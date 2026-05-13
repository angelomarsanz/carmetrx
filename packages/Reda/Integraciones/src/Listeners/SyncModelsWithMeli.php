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
            return;
        }

        $meliBrandId = $marcaMeli->datos_meli['meli_id'];

        // Recuperamos los registros completos para poder decodificar el JSON en memoria
        $modelosMeliExistentesIds = ModeloAutoMeli::whereHas('userCarModel', function($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            })
            ->get()
            ->map(function ($modelo) {
                // Obtenemos el meli_id desde el array datos_meli
                return $modelo->datos_meli['meli_id'] ?? null;
            })
            ->filter()
            ->toArray();

        // Cargamos los IDs vinculados para evitar el error 1062 de duplicidad
        $idsYaVinculados = ModeloAutoMeli::pluck('user_car_model_id')->toArray();

        // Solicitar modelos a Mercado Libre
        $url = "catalog_domains/MLU-CARS_AND_VANS/attributes/MODEL/top_values";
        $datos = [
            "known_attributes" => [
                ["id" => "BRAND", "value_id" => $meliBrandId]
            ]
        ];

        // Verificación de conexión y tokens [cite: 18]
        $respuestaVerificarUsuarioConectado = (new UsuarioController())->verificarUsuarioConectado(null, true);
        if (!$respuestaVerificarUsuarioConectado['success']) return;

        $idUsuario = $respuestaVerificarUsuarioConectado['id_usuario_conectado'];
        $nombreTabla = $this->usuarioTabla($respuestaVerificarUsuarioConectado['tipo_agencia_agente']);
        $datosUsuarioConectado = [
            'id_usuario_agencia' => $respuestaVerificarUsuarioConectado['id_usuario_agencia'],
            'tipo_agencia_agente' => $respuestaVerificarUsuarioConectado['tipo_agencia_agente']
        ];

        $respuestaVerificarTokenMeli = (new ConfiguracionController())->verificarTokenMeli(null, $datosUsuarioConectado, true);
        if (!$respuestaVerificarTokenMeli['success']) return;

        $token = $respuestaVerificarTokenMeli['token_meli'];

        $res = $this->enviarSolicitudMeli($url, 'POST', $datos, true, $token, false, 'sync_models_with_meli', $idUsuario, null, $nombreTabla);

        Log::info("SyncModelsWithMeli, res: " . print_r($res, true));

        if ($res['success'] && is_array($res['respuesta'])) {
            foreach ($res['respuesta'] as $modeloMeli) {
                $meliId = $modeloMeli['id'];
                $nombre = $modeloMeli['name'];

                Log::info("SyncModelsWithMeli, meliId: " . $meliId . ", nombre: " . $nombre);

                // Si ya conocemos este ID de MeLi, no hacemos NADA
                if (in_array($meliId, $modelosMeliExistentesIds)) {
                    continue;
                }

                try {
                    DB::transaction(function () use ($brandId, $nombre, $meliId, $modeloMeli, &$idsYaVinculados) {
                        // MySQL encontrará "Sedan" aunque busquemos "Sedán" por la colación
                        $nuevoModelo = UserCarModel::firstOrCreate(
                            ['brand_id' => $brandId, 'name' => $nombre],
                            ['language_id' => 180]
                        );

                        // Si el ID ya fue vinculado en esta ejecución (caso Sedan/Sedán), saltar
                        if (in_array($nuevoModelo->id, $idsYaVinculados)) {
                            return;
                        }

                        ModeloAutoMeli::create([
                            'user_car_model_id' => $nuevoModelo->id,
                            'datos_meli' => [
                                'meli_id' => $meliId,
                                'nombre_meli' => $nombre
                            ],
                            'respuesta_meli' => $modeloMeli
                        ]);

                        // Actualizamos el array en memoria para el siguiente ciclo
                        $idsYaVinculados[] = $nuevoModelo->id;
                    });
                } catch (QueryException $e) {
                    // Si el error es de duplicidad (1062), lo ignoramos silenciosamente
                    if ($e->errorInfo[1] == 1062) {
                        Log::info("SyncModelsWithMeli: Se ignoró vínculo duplicado para $nombre (ID Carmetric: " . ($nuevoModelo->id ?? 'N/A') . ")");
                        continue;
                    }
                    // Si es otro tipo de error, lo relanzamos
                    throw $e;
                }
            }
        }
    }
}
