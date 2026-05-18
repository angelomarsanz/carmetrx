<?php

namespace Reda\Integraciones\Listeners;

use Reda\Integraciones\Events\CitiesRequested;
use Reda\Integraciones\Models\MercadoLibre\EstadoMeli;
use Reda\Integraciones\Models\MercadoLibre\CiudadMeli;
use App\Models\User\Property\State;
use App\Models\User\Property\City;
use App\Models\User\Property\CityContent;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Reda\Integraciones\Http\Controllers\General\UsuarioController;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ConfiguracionController;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class SyncCitiesWithMeli
{
    use MeliRequestsTrait;

    public function handle(CitiesRequested $event)
    {
        $stateId = $event->state_id;
        Log::info("SyncCitiesWithMeli, stateId: ".$stateId);

        // 1. Obtener el estado local y verificar si tiene un ID de Mercado Libre vinculado
        $state = State::find($stateId);
        if (!$state) {
            Log::info("SyncCitiesWithMeli: No se encontró el estado local con ID: " . $stateId);
            return;
        }

        // Necesitamos el country_id del estado para guardarlo en la tabla user_cities
        $countryId = $state->country_id;

        $estadoMeli = EstadoMeli::where('user_state_id', $stateId)->first();
        if (!$estadoMeli || !isset($estadoMeli->datos_meli['meli_id'])) {
            Log::info("SyncCitiesWithMeli: El estado local ID {$stateId} no está mapeado en MeLi.");
            return;
        }

        Log::info("SyncCitiesWithMeli, datos_meli: " . print_r($estadoMeli->datos_meli, true));

        $meliStateId = $estadoMeli->datos_meli['meli_id'];

        // 2. OPTIMIZACIÓN EN MEMORIA A: IDs de MeLi ya vinculados para este estado específico
        $ciudadesMeliExistentesIds = CiudadMeli::whereHas('city', function($q) use ($stateId) {
                $q->where('state_id', $stateId);
            })
            ->get()
            ->map(function ($ciudad) {
                return $ciudad->datos_meli['meli_id'] ?? null;
            })
            ->filter()
            ->toArray();

        // 3. OPTIMIZACIÓN EN MEMORIA B: Ciudades locales existentes en este estado (Key en minúsculas)
        $ciudadesLocalesExistentes = [];
        
        $ciudadesConContenido = City::where('user_id', 0)
            ->where('state_id', $stateId)
            ->with(['contents' => function($query) {
                $query->where('language_id', 180);
            }])
            ->get();

        foreach ($ciudadesConContenido as $cityObj) {
            $content = $cityObj->contents->first();
            if ($content && !empty($content->name)) {
                $nombreKey = mb_strtolower(trim($content->name));
                $ciudadesLocalesExistentes[$nombreKey] = $cityObj->id;
            }
        }

        // 4. Control de IDs amarrados en la sesión concurrente actual
        $idsYaVinculados = CiudadMeli::pluck('user_city_id')->toArray();

        // Endpoint correcto de Mercado Libre para traer ciudades de un estado
        $url = "classified_locations/cities/{$meliStateId}";

        // Verificación de autenticación y tokens
        $respuestaVerificarUsuarioConectado = (new UsuarioController())->verificarUsuarioConectado(null, true);

        if (!$respuestaVerificarUsuarioConectado['success']) {
            Log::info("SyncCitiesWithMeli, respuestaVerificarUsuarioConectado, false: " . print_r($respuestaVerificarUsuarioConectado, true));
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

        // Petición HTTP a la API de MeLi
        $res = $this->enviarSolicitudMeli($url, 'GET', [], true, $token, false, 'sync_cities_with_meli', $idUsuario, null, $nombreTabla);

        if ($res['success'] && isset($res['respuesta']['neighborhoods']) && is_array($res['respuesta']['neighborhoods'])) {
            foreach ($res['respuesta']['neighborhoods'] as $ciudadMeli) {
                $meliId = $ciudadMeli['id'];
                $nombre = $ciudadMeli['name'];
                $nombreKey = mb_strtolower(trim($nombre));

                // Si el ID de MeLi ya está registrado en la tabla pivote, saltamos
                if (in_array($meliId, $ciudadesMeliExistentesIds)) {
                    continue;
                }

                try {
                    DB::transaction(function () use ($stateId, $countryId, $nombre, $nombreKey, $meliId, $ciudadMeli, &$idsYaVinculados, &$ciudadesLocalesExistentes) {
                        
                        // Búsqueda ultrarrápida en la caché de memoria
                        if (array_key_exists($nombreKey, $ciudadesLocalesExistentes)) {
                            $cityId = $ciudadesLocalesExistentes[$nombreKey];
                            Log::info("SyncCitiesWithMeli [MEMORIA]: Se reutilizó State ID local existente: {$cityId} para el nombre: {$nombre}");
                        } else {
                            // Creación estructural en user_cities (user_id => 0 según el estándar global del sistema)
                            $nuevaCiudad = City::create([
                                'user_id'    => 0,
                                'country_id' => $countryId, // Inyectado para cumplir el esquema SQL
                                'state_id'   => $stateId,
                                'status'     => 1
                            ]);

                            $cityId = $nuevaCiudad->id;

                            // Creación del contenido traducido en user_city_contents
                            CityContent::create([
                                'user_id'     => 0,
                                'city_id'     => $cityId,
                                'language_id' => 180,
                                'name'        => $nombre,
                                'slug'        => Str::slug($nombre) // El mutador 'slug' del modelo aplicará make_slug automáticamente
                            ]);

                            // Registramos en la lista de memoria por si el JSON repite el nombre
                            $ciudadesLocalesExistentes[$nombreKey] = $cityId;
                        }

                        // Evitar duplicados concurrentes en la misma respuesta
                        if (in_array($cityId, $idsYaVinculados)) {
                            return;
                        }

                        // Guardamos el puente en ciudades_melis
                        CiudadMeli::create([
                            'user_city_id'   => $cityId,
                            'datos_meli'     => [
                                'meli_id'     => $meliId,
                                'nombre_meli' => $nombre
                            ],
                            'respuesta_meli' => $ciudadMeli
                        ]);

                        $idsYaVinculados[] = $cityId;
                    });
                } catch (QueryException $e) {
                    // Control pacífico si otra petición paralela insertó el índice único user_city_id
                    if ($e->errorInfo[1] == 1062) {
                        Log::info("SyncCitiesWithMeli: Conflicto único controlado pacíficamente para la ciudad: {$nombre}");
                        continue;
                    }
                    throw $e;
                }
            }
        } else {
            Log::info("SyncCitiesWithMeli: No se encontraron ciudades para el estado MeLi ID: " . $meliStateId);
        }
    }
}