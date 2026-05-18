<?php

namespace Reda\Integraciones\Listeners;

use Reda\Integraciones\Events\StatesRequested;
use Reda\Integraciones\Models\MercadoLibre\PaisMeli;
use Reda\Integraciones\Models\MercadoLibre\EstadoMeli;
use App\Models\User\Property\State;
use App\Models\User\Property\StateContent;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Reda\Integraciones\Http\Controllers\General\UsuarioController;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ConfiguracionController;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class SyncStatesWithMeli
{
    use MeliRequestsTrait;

    public function handle(StatesRequested $event)
    {
        $countryId = $event->country_id;

        // 1. Verificar si este país tiene un ID de Mercado Libre vinculado
        $paisMeli = PaisMeli::where('user_country_id', $countryId)->first();

        if (!$paisMeli || !isset($paisMeli->datos_meli['meli_id'])) {
            Log::info("SyncStatesWithMeli, no existe el país con el countryId: ".$countryId);
            return;
        }

        $meliCountryId = $paisMeli->datos_meli['meli_id'];

        // 2. OPTIMIZACIÓN EN MEMORIA A: Recuperamos los IDs de MeLi ya procesados para este país
        $estadosMeliExistentesIds = EstadoMeli::whereHas('state', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            })
            ->get()
            ->map(function ($estado) {
                return $estado->datos_meli['meli_id'] ?? null;
            })
            ->filter()
            ->toArray();

        // 3. OPTIMIZACIÓN EN MEMORIA B: Corregido para evitar llamar a StateContent::state()
        // Buscamos directamente desde State usando su relación 'contents' para armar el mapa de nombres
        $estadosLocalesExistentes = [];
        
        $estadosConContenido = State::where('user_id', 0)
            ->where('country_id', $countryId)
            ->with(['contents' => function($query) {
                $query->where('language_id', 180);
            }])
            ->get();

        foreach ($estadosConContenido as $stateObj) {
            $content = $stateObj->contents->first();
            if ($content && !empty($content->name)) {
                $nombreKey = mb_strtolower(trim($content->name));
                $estadosLocalesExistentes[$nombreKey] = $stateObj->id;
            }
        }

        // 4. Control de IDs locales vinculados en esta sesión concurrente
        $idsYaVinculados = EstadoMeli::pluck('user_state_id')->toArray();

        // 5. Solicitar estados a Mercado Libre
        $url = "classified_locations/states/{$meliCountryId}";

        // Verificación de tokens y sesión de MeLi
        $respuestaVerificarUsuarioConectado = (new UsuarioController())->verificarUsuarioConectado(null, true);

        if (!$respuestaVerificarUsuarioConectado['success']) {
            Log::info("SyncStatesWithMeli, respuestaVerificarUsuarioConectado, false: " . print_r($respuestaVerificarUsuarioConectado, true));
            return;
        }

        $idUsuario = $respuestaVerificarUsuarioConectado['id_usuario_conectado'];
        $nombreTabla = $this->usuarioTabla($respuestaVerificarUsuarioConectado['tipo_agencia_agente']);
        $datosUsuarioConectado = [
            'id_usuario_agencia' => $respuestaVerificarUsuarioConectado['id_usuario_agencia'],
            'tipo_agencia_agente' => $respuestaVerificarUsuarioConectado['tipo_agencia_agente']
        ];

        $respuestaVerificarTokenMeli = (new ConfiguracionController())->verificarTokenMeli(null, $datosUsuarioConectado, true);

        if (!$respuestaVerificarTokenMeli['success']) {
            Log::info("SyncStatesWithMeli, respuestaVerificarTokenMeli, false: " . print_r($respuestaVerificarTokenMeli, true));
            return;
        }

        $token = $respuestaVerificarTokenMeli['token_meli'];

        // Petición HTTP GET a la API
        $res = $this->enviarSolicitudMeli($url, 'GET', [], true, $token, false, 'sync_states_with_meli', $idUsuario, null, $nombreTabla);

        if ($res['success'] && isset($res['respuesta']['cities']) && is_array($res['respuesta']['cities'])) {
            foreach ($res['respuesta']['cities'] as $estadoMeli) {
                $meliId = $estadoMeli['id'];
                $nombre = $estadoMeli['name'];
                $nombreKey = mb_strtolower(trim($nombre));
                // Si el ID de MeLi ya existe en nuestra tabla de integración, saltamos de inmediato
                if (in_array($meliId, $estadosMeliExistentesIds)) {
                    continue;
                }

                try {
                    // Usamos la transacción exclusivamente para las escrituras necesarias
                    DB::transaction(function () use ($countryId, $nombre, $nombreKey, $meliId, $estadoMeli, &$idsYaVinculados, &$estadosLocalesExistentes) {
                        
                        // Búsqueda 100% en memoria usando el array que preparamos al inicio
                        if (array_key_exists($nombreKey, $estadosLocalesExistentes)) {
                            $stateId = $estadosLocalesExistentes[$nombreKey];
                            Log::info("SyncStatesWithMeli [MEMORIA]: Se reutilizó State ID local existente: {$stateId} para el nombre: {$nombre}");
                        } else {
                            // Si no existe en memoria, procedemos a crearlo en las dos tablas locales
                            $nuevoEstado = State::create([
                                'user_id'    => 0,
                                'country_id' => $countryId
                            ]);

                            $stateId = $nuevoEstado->id;

                            StateContent::create([
                                'user_id'     => 0,
                                'state_id'    => $stateId,
                                'language_id' => 180,
                                'name'        => $nombre,
                                'slug'        => Str::slug($nombre)
                            ]);

                            // Actualizamos el mapa en memoria por si acaso MeLi devolviera nombres repetidos en la misma respuesta
                            $estadosLocalesExistentes[$nombreKey] = $stateId;
                        }

                        // Si el ID local ya fue amarrado a MeLi en esta iteración actual, frenamos
                        if (in_array($stateId, $idsYaVinculados)) {
                            Log::info("SyncStatesWithMeli, id vinculado anteriormente: ".$stateId);
                            return;
                        }

                        // Guardamos el puente definitivo de integración
                        EstadoMeli::create([
                            'user_state_id'   => $stateId,
                            'datos_meli'      => [
                                'meli_id'     => $meliId,
                                'nombre_meli' => $nombre
                            ],
                            'respuesta_meli'  => $estadoMeli
                        ]);

                        // Actualizamos el stack de control en memoria
                        $idsYaVinculados[] = $stateId;
                    });
                } catch (QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        Log::info("SyncStatesWithMeli: Se manejó pacíficamente conflicto único para el estado: {$nombre}");
                        continue;
                    }
                    throw $e;
                }
            }
        }
        else
        {
            Log::info("SyncStatesWithMeli, no se encontraron estados en la respuesta de MeLi para el país con meliCountryId: ".$meliCountryId);
        }
    }
}