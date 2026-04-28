<?php
namespace Reda\Integraciones\Http\Controllers\MercadoLibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Reda\Integraciones\Models\MercadoLibre\UserMeli;
use Illuminate\Support\Facades\Log;
use Reda\Integraciones\Http\Controllers\General\UsuarioController;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('reda-integraciones::mercado_libre.configuraciones.index');
    }

    public function verificarTokenMeli(Request $request, $datosUsuarioConectado = null, $retornaArray = false)
    {
        if ($datosUsuarioConectado == null) {
            $datosUsuarioConectado = $request->input('datos_usuario_conectado');
            Log::info("Contenido de datosUsuarioConectado: " . print_r($datosUsuarioConectado, true));
        }

        $respuesta = [
            'codigo_respuesta' => 0,
            'mensaje_respuesta' => '',
            'token_meli' => '',
            'refresh_token_meli' => ''
        ];

        $userId = $datosUsuarioConectado['id_usuario_agencia'] ?? null;

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
                else {
                    $respuesta = [
                        'codigo_respuesta' => 2,
                        'mensaje_respuesta' => __('No se encontró el token de Mercado Libre para este usuario'),
                        'token_meli' => '',
                        'refresh_token_meli' => ''
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

        if (isset($datosUsuarioConectado['id_usuario_conectado']) && $datosUsuarioConectado['id_usuario_conectado'] != 0)
        {
            if (isset($datosUsuarioConectado['tipo_agencia_agente']) && $datosUsuarioConectado['tipo_agencia_agente'] != '')
            {
                $vectorAtributosDatosMeli = [
                    'codigo_respuesta_verificar_token_meli' => $respuesta['codigo_respuesta'],
                    'mensaje_respuesta_verificar_token_meli' => $respuesta['mensaje_respuesta'],
                    'fecha_hora_verificar_token_meli' => $this->fechaHoraActual()
                ];

                (new UsuarioController())->actualizarDatosMeliUsuario($vectorAtributosDatosMeli, $datosUsuarioConectado['id_usuario_conectado'], $datosUsuarioConectado['tipo_agencia_agente']);
            }
        }

        if ($retornaArray) {
            return $respuesta;
        }
        else {
            return response()->json($respuesta, 200);
        }
    }
    public function fechaHoraActual()
    {
		setlocale(LC_TIME, 'es_UY', 'es_UY.UTF-8', 'es_UY.UTF-8');
		date_default_timezone_set('America/Montevideo');
		return $fechaHoraActual = date("Y-m-d H:i:s");
    }
}
