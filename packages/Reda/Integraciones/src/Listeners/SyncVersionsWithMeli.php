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

        Log::info("SyncVersionsWithMeli, modelId: ".$modelId);

        // 1. Obtener el ID de Mercado Libre del modelo para poder consultar sus versiones
        $modeloMeli = ModeloAutoMeli::where('user_car_model_id', $modelId)->first();

        if (!$modeloMeli || !isset($modeloMeli->datos_meli['meli_id'])) {
            return;
        }

        $meliModelId = $modeloMeli->datos_meli['meli_id'];

        $versionesMeliExistentesIds = VersionAutoMeli::whereHas('userCarVersion', function($q) use ($modelId) {
                $q->where('model_id', $modelId);
            })
            ->get()
            ->map(fn($v) => $v->datos_meli['meli_id'] ?? null)
            ->filter()
            ->toArray();

        // Obtenemos los IDs internos (user_car_version_id) ya ocupados en la tabla de integración
        // para evitar el error 1062 si MeLi manda versiones duplicadas (ej: "XLi" vs "XLI")
        $idsYaVinculados = VersionAutoMeli::pluck('user_car_version_id')->toArray();

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

        Log::info("SyncVersionsWithMeli, res: " . print_r($res, true));

        if ($res['success'] && is_array($res['respuesta'])) {
            // Necesitamos el brand_id del modelo para crear registros en la tabla original
            $brandId = DB::table('user_car_model')->where('id', $modelId)->value('brand_id');

            foreach ($res['respuesta'] as $versionMeli) {
                $meliVersionId = $versionMeli['id'];
                $nombreVersion = $versionMeli['name'];

                // Si ya procesamos este ID de MeLi anteriormente, saltar
                if (in_array($meliVersionId, $versionesMeliExistentesIds)) {
                    Log::info("SyncVersionsWithMeli: Saltando versión duplicada versiones_autos_meli: {$meliVersionId}");
                    continue;
                }

                try {
                    DB::transaction(function () use ($brandId, $modelId, $nombreVersion, $meliVersionId, $versionMeli, &$idsYaVinculados) {
                        // A. Crear o buscar en la tabla original (user_car_version)
                        // MySQL ignorará tildes/mayúsculas según la colación de la tabla
                        $nuevaVersion = UserCarVersion::firstOrCreate(
                            [
                                'brand_id' => $brandId,
                                'model_id' => $modelId,
                                'name'     => $nombreVersion
                            ],
                            ['language_id' => 180]
                        );

                        // B. Evitar duplicar el vínculo si el ID interno ya se usó en este bucle
                        if (in_array($nuevaVersion->id, $idsYaVinculados)) {
                            Log::info("SyncVersionsWithMeli: Saltando versión duplicada visual ($nombreVersion) para ID interno: {$nuevaVersion->id}");
                            return;
                        }

                        // C. Crear la vinculación en la tabla de integración
                        VersionAutoMeli::create([
                            'user_car_version_id' => $nuevaVersion->id,
                            'datos_meli' => [
                                'meli_id' => $meliVersionId,
                                'nombre_meli' => $nombreVersion
                            ],
                            'respuesta_meli' => $versionMeli
                        ]);

                        Log::info("SyncVersionsWithMeli, Versión añadida meli_id: ".$meliVersionId." nombre_meli: ".$nombreVersion);

                        // Actualizar el array de control por referencia
                        $idsYaVinculados[] = $nuevaVersion->id;
                    });
                } catch (QueryException $e) {
                    // Manejo del error 1062 (Duplicate entry)
                    if ($e->errorInfo[1] == 1062) {
                        Log::warning("SyncVersionsWithMeli: Registro duplicado detectado por BD para $nombreVersion. Ignorado.");
                        continue;
                    }
                    throw $e;
                }
            }
        }
    }
}
