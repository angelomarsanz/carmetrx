<?php

namespace Reda\Integraciones\Listeners;

use Reda\Integraciones\Events\VersionsRequested;
use Reda\Integraciones\Models\MercadoLibre\ModeloAutoMeli;
use Reda\Integraciones\Models\MercadoLibre\VersionAutoMeli;
use App\Models\User\UserCarVersion;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;
use Illuminate\Support\Facades\DB;
use Reda\Integraciones\Http\Controllers\General\UsuarioController;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ConfiguracionController;
use Illuminate\Support\Facades\Log;

class SyncVersionsWithMeli
{
    use MeliRequestsTrait;

    public function handle(VersionsRequested $event)
    {
        $modelId = $event->model_id;

        // 1. Obtener el ID de Mercado Libre del modelo para poder consultar sus versiones
        $modeloMeli = ModeloAutoMeli::where('user_car_model_id', $modelId)->first();

        if (!$modeloMeli || !isset($modeloMeli->datos_meli['meli_id'])) {
            return;
        }

        $meliModelId = $modeloMeli->datos_meli['meli_id'];

        // 2. ¿Ya existen versiones para este modelo? 
        // Siguiendo tu lógica, si ya hay registros evitaremos llamadas innecesarias a la API
        $existeCarga = VersionAutoMeli::whereHas('userCarVersion', function($q) use ($modelId) {
            $q->where('model_id', $modelId);
        })->exists();

        if ($existeCarga) {
            return;
        }

        // 3. Verificación de Usuario y Token
        $respuestaVerificarUsuarioConectado = (new UsuarioController())->verificarUsuarioConectado(null, true);

        if (!$respuestaVerificarUsuarioConectado['success']) {
            Log::info("SyncVersionsWithMeli, respuestaVerificarUsuarioConectado: " . print_r($respuestaVerificarUsuarioConectado, true));
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
            Log::info("SyncVersionsWithMeli, respuestaVerificarTokenMeli: " . print_r($respuestaVerificarTokenMeli, true));
            return;
        }

        $token = $respuestaVerificarTokenMeli['token_meli'];

        // 4. Consultar Versiones (Attribute TRIM) en Meli
        $url = "catalog_domains/MLU-CARS_AND_VANS/attributes/TRIM/top_values";
        $datos = [
            'known_attributes' => [
                ['id' => 'MODEL', 'value_id' => $meliModelId]
            ]
        ];

        $res = $this->enviarSolicitudMeli($url, 'POST', $datos, true, $token, false, 'sync_versions_with_meli', $idUsuario, null, $nombreTabla);

        if ($res['success'] && is_array($res['respuesta'])) {
            
            // Necesitamos el brand_id del modelo para llenar user_car_version correctamente
            $brandId = DB::table('user_car_model')->where('id', $modelId)->value('brand_id');

            foreach ($res['respuesta'] as $versionMeli) {
                $nombreVersion = $versionMeli['name'];

                DB::transaction(function () use ($brandId, $modelId, $nombreVersion, $versionMeli) {
                    // A. Crear o buscar en la tabla original (user_car_version)
                    $nuevaVersion = UserCarVersion::firstOrCreate(
                        [
                            'brand_id' => $brandId, 
                            'model_id' => $modelId, 
                            'name'     => $nombreVersion
                        ],
                        ['language_id' => 180]
                    );

                    // B. Crear o actualizar la vinculación en versiones_autos_melis
                    VersionAutoMeli::updateOrCreate(
                        ['user_car_version_id' => $nuevaVersion->id],
                        [
                            'datos_meli' => [
                                'meli_id' => $versionMeli['id'],
                                'nombre_meli' => $nombreVersion
                            ],
                            'respuesta_meli' => $versionMeli
                        ]
                    );
                });
            }
        }
    }
}