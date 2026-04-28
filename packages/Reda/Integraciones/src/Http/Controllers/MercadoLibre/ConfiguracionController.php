<?php
namespace Reda\Integraciones\Http\Controllers\MercadoLibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Reda\Integraciones\Models\MercadoLibre\UserMeli;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('reda-integraciones::mercado_libre.configuraciones.index');
    }

    public function verificarTokenMeli(Request $request, $datosUsuarioConectado = null)
    {
        $indicadorAjax = 0;
        if ($datosUsuarioConectado == null) {
            $indicadorAjax = 1;
            $datosUsuarioConectado = $request->input('datos_usuario_conectado');
        }

        Log::info("Contenido de datosUsuarioConectado: " . print_r($datosUsuarioConectado, true));

        $userId = $datosUsuarioConectado['id_usuario_agencia_conectado'] ?? null;

        if (!$userId) {
            $respuesta = [
                'codigo_respuesta' => 1,
                'mensaje_respuesta' => __('ID de usuario no proporcionado para verificar el token de Mercado Libre'),
                'token_meli' => '',
                'refresh_token_meli' => ''
            ];
        }
        else {
            // Buscamos en la tabla users_melis usando el modelo que configuramos
            $userMeli = UserMeli::where('user_id', $userId)->first();

            if ($userMeli && isset($userMeli->datos_meli)) {
                $datos = $userMeli->datos_meli;

                // Verificamos que el JSON contenga los atributos necesarios
                $hasToken = isset($datos['token_meli']);
                $hasRefresh = isset($datos['refresh_token_meli']);
                $hasDate = isset($datos['fecha_hora_token_meli']);

                if ($hasToken && $hasRefresh && $hasDate) {
                    $respuesta = [
                        'codigo_respuesta' => 0,
                        'mensaje_respuesta' => __('Token recuperado con éxito'),
                        'token_meli' => $datos['token_meli'],
                        'refresh_token_meli' => $datos['refresh_token_meli'],
                    ];
                }
            }
            else {
                $respuesta = [
                    'codigo_respuesta' => 2,
                    'mensaje_respuesta' => __('No se encontró el token de Mercado Libre para este usuario'),
                    'token_meli' => '',
                    'refresh_token_meli' => ''
                ];
            }
        }
        if ($indicadorAjax) {
            return response()->json($respuesta, 200);
        }
        return $respuesta;
    }
}
