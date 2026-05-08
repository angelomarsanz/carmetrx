<?php

namespace Reda\Integraciones\Listeners;

use Reda\Integraciones\Events\ModelsRequested;
use Reda\Integraciones\Models\MercadoLibre\MarcaAutoMeli;
use Reda\Integraciones\Models\MercadoLibre\ModeloAutoMeli;
use App\Models\User\UserCarModel;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;
use Illuminate\Support\Facades\DB;
use Reda\Integraciones\Http\Controllers\General\UsuarioController;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ConfiguracionController;
use Illuminate\Support\Facades\Log;

class SyncModelsWithMeli
{
    use MeliRequestsTrait;

    public function handle(ModelsRequested $event)
    {
        $brandId = $event->brand_id;

        // 1. Verificar si esta marca tiene un ID de Mercado Libre vinculado
        $marcaMeli = MarcaAutoMeli::where('user_car_brand_id', $brandId)->first();

        if (!$marcaMeli || !isset($marcaMeli->datos_meli['meli_id'])) {
            return; // No podemos hacer nada si la marca no es de Meli
        }

        $meliBrandId = $marcaMeli->datos_meli['meli_id'];

        // 2. ¿Ya tenemos modelos para esta marca?
        // Si ya hay muchos, quizás no necesitemos llamar a la API cada vez
        $existeCarga = ModeloAutoMeli::whereHas('userCarModel', function($q) use ($brandId) {
            $q->where('brand_id', $brandId);
        })->exists();

        if ($existeCarga) return; // Ya sincronizado anteriormente

        // 3. Solicitar modelos a Mercado Libre
        // Usamos el endpoint de top_values para MODEL
        $url = "catalog_domains/MLU-CARS_AND_VANS/attributes/MODEL/top_values?BRAND={$meliBrandId}";

        // Obtener datos de conexión para el Trait
        $respuestaVerificarUsuarioConectado = (new UsuarioController())->verificarUsuarioConectado(null, true);

        if (!$respuestaVerificarUsuarioConectado['success']) {
            Log::info("SyncModelsWithMeli, respuestaVerificarUsuarioConectado: " . print_r($respuestaVerificarUsuarioConectado, true));
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
            Log::info("SyncModelsWithMeli, respuestaVerificarTokenMeli: " . print_r($respuestaVerificarTokenMeli, true));
            return;
        }

        $token = $respuestaVerificarTokenMeli['token_meli'];

        $res = $this->enviarSolicitudMeli($url, 'GET', [], true, $token, false, 'sync_models_with_meli', $idUsuario, null, $nombreTabla);

        if ($res['success'] && is_array($res['respuesta'])) {
            foreach ($res['respuesta'] as $modeloMeli) {
                $nombre = $modeloMeli['name'];

                DB::transaction(function () use ($brandId, $nombre, $modeloMeli) {
                    // Crear en la tabla original
                    $nuevoModelo = UserCarModel::firstOrCreate(
                        ['brand_id' => $brandId, 'name' => $nombre],
                        ['language_id' => 180]
                    );

                    // Vincular en nuestra tabla de integración
                    ModeloAutoMeli::updateOrCreate(
                        ['user_car_model_id' => $nuevoModelo->id],
                        [
                            'datos_meli' => [
                                'meli_id' => $modeloMeli['id'],
                                'nombre_meli' => $nombre
                            ],
                            'respuesta_meli' => $modeloMeli
                        ]
                    );
                });
            }
        }
    }
}
