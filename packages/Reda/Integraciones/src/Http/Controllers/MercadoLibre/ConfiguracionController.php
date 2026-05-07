<?php
namespace Reda\Integraciones\Http\Controllers\MercadoLibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Reda\Integraciones\Models\MercadoLibre\UserMeli;
use Illuminate\Support\Facades\Log;
use Reda\Integraciones\Http\Controllers\General\UsuarioController;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;
use DateTime;

class ConfiguracionController extends Controller
{
    use MeliRequestsTrait;

    public function index()
    {
        $clientIdMeli = env('CLIENT_ID_MELI');

        return view('reda-integraciones::mercado_libre.configuraciones.index', compact('clientIdMeli'));
    }

    public function verificarTokenMeli(?Request $request, $datosUsuarioConectado = null, $retornaArray = false)
    {
        setlocale(LC_TIME, 'es_UY', 'es_UY.UTF-8', 'es_UY.UTF-8');
		date_default_timezone_set('America/Montevideo');

        if ($request) {
            $datosUsuarioConectado = $datosUsuarioConectado ?? $request->input('datos_usuario_conectado');
        }

        $idUsuario = $datosUsuarioConectado['id_usuario_agencia'] ?? null;
        $tipoUsuario = $datosUsuarioConectado['tipo_agencia_agente'] ?? null;

        if (!$idUsuario) {
            $respuesta = [
                'success' => false,
                'codigo_respuesta' => 1,
                'codigo_http' => 422,
                'mensaje_respuesta' => __('ID de usuario no proporcionado para verificar el token de Mercado Libre'),
                'respuesta' => '',
                'error_curl' => '',
                'causas' => '',
                'token_meli' => '',
                'refresh_token_meli' => ''
            ];
        }
        else {
            // Buscamos en la tabla users_melis usando el modelo que configuramos
            $userMeli = UserMeli::where('user_id', $idUsuario)->first();

            if ($userMeli && isset($userMeli->datos_meli)) {
                $datos = $userMeli->datos_meli;

                // Verificamos que el JSON contenga los atributos necesarios
                $hasToken = isset($datos['token_meli']);
                $hasRefresh = isset($datos['refresh_token_meli']);
                $hasDate = isset($datos['fecha_hora_token_meli']);

                if ($hasToken && $hasRefresh && $hasDate) {
                    $fechaHoraTokenMeliObjeto = new DateTime($datos['fecha_hora_token_meli']);
                    $fechaHoraActualObjeto = $this->fechaHoraActual()['fecha_hora_actual_objeto'];
                    $intervaloDiferencia = $fechaHoraTokenMeliObjeto->diff($fechaHoraActualObjeto);
                    $codigoRetornoTokenMeli = 0; // 0 = válido, 1 = expirado

                    if ($intervaloDiferencia->y > 0)
                    {
                        $codigoRetornoTokenMeli = 1;
                    }
                    elseif ($intervaloDiferencia->m > 0)
                    {
                        $codigoRetornoTokenMeli = 1;
                    }
                    elseif ($intervaloDiferencia->d > 0)
                    {
                        $codigoRetornoTokenMeli = 1;
                    }
                    elseif ($intervaloDiferencia->h > 4)
                    {
                        $codigoRetornoTokenMeli = 1;
                    }

                    if ($codigoRetornoTokenMeli == 0)
                    {
                        $respuesta = [
                            'success' => true,
                            'codigo_respuesta' => 0,
                            'codigo_http' => 200,
                            'mensaje_respuesta' => __('Token recuperado con éxito'),
                            'respuesta' => '',
                            'error_curl' => '',
                            'causas' => '',
                            'token_meli' => $datos['token_meli'],
                            'refresh_token_meli' => $datos['refresh_token_meli'],
                        ];
                    }
                    else
                    {
                        $respuesta = $this->obtenerTokenMeli(null, 'refresh_token', $datos['refresh_token_meli'], true, $idUsuario, $tipoUsuario);
                    }
                }
                else {
                    $respuesta = [
                        'success' => false,
                        'codigo_respuesta' => 2,
                        'codigo_http' => 404,
                        'mensaje_respuesta' => __('No se encontró el token de Mercado Libre para este usuario'),
                        'respuesta' => '',
                        'error_curl' => '',
                        'causas' => '',
                        'token_meli' => '',
                        'refresh_token_meli' => ''
                    ];
                }
            }
            else {
                $respuesta = [
                    'success' => false,
                    'codigo_respuesta' => 2,
                    'codigo_http' => 404,
                    'mensaje_respuesta' => __('No se encontró el token de Mercado Libre para este usuario'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas' => '',
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
                    'verificar_token_meli' => [
                        'codigo_respuesta' => $respuesta['codigo_respuesta'],
                        'mensaje_respuesta' => $respuesta['mensaje_respuesta'],
                        'fecha_hora' => $this->fechaHoraActual()['fecha_hora_actual_formato']
                    ]
                ];

                $nombreTabla = $this->usuarioTabla($datosUsuarioConectado['tipo_agencia_agente']);

                $respuestaActualizarDatosMeli = $this->actualizarDatosMeli($vectorAtributosDatosMeli, $datosUsuarioConectado['id_usuario_conectado'], null, $nombreTabla, 'datos_meli');

                if ($respuestaActualizarDatosMeli['success'] == false)
                {
                    $respuesta = $respuestaActualizarDatosMeli;
                }
            }
        }

        if ($retornaArray) {
            return $respuesta;
        }
        else {
            return response()->json($respuesta, $respuesta['codigo_http']);
        }
    }
    public function obtenerTokenMeli(?Request $request, $accion = null, $codigoRefreshToken = null, $retornaArray = false, $idUsuario = null, $tipoUsuario = null)
    {
        $respuesta = [];
        if ($request)
        {
            $accion = $request->input('accion');
            $codigoRefreshToken = $request->input('codigo_refresh_token');
            $idUsuario = $request->input('id_usuario');
            $tipoUsuario = $request->input('tipo_usuario');
        }

        $datos = [
            'grant_type'    => $accion,
            'client_id'     => env('CLIENT_ID_MELI'),
            'client_secret' => env('CLIENT_SECRET_MELI'),
        ];

        if ($accion === "authorization_code") {
            $datos['code'] = $codigoRefreshToken; // Para el primer intercambio
            $datos['redirect_uri'] = url('/user/mercado-libre/configuraciones');
        } elseif ($accion === "refresh_token") {
            $datos['refresh_token'] = $codigoRefreshToken; // Para renovar (lo que pide el error)
        }

        $nombreTabla = $this->usuarioTabla($tipoUsuario);

        $respuestaEnviarSolicitudMeli = $this->enviarSolicitudMeli('oauth/token', 'POST', $datos, false, null, true, 'obtener_token_meli', $idUsuario, null, $nombreTabla);

        if ($respuestaEnviarSolicitudMeli['success']) {
            $tokenMeli = $respuestaEnviarSolicitudMeli['respuesta']['access_token'];
            $refreshTokenMeli = $respuestaEnviarSolicitudMeli['respuesta']['refresh_token'];
            $respuesta = $this->guardarTokenMeli(null, $tokenMeli, $refreshTokenMeli, $retornaArray = true);
        }
        else
        {
            $respuesta = $respuestaEnviarSolicitudMeli;
        }

        if ($retornaArray)
        {
            return $respuesta;
        }
        else
        {
            return response()->json($respuesta, $respuesta['codigo_http']);
        }
    }

	public function guardarTokenMeli(?Request $request, $tokenMeli = null, $refreshTokenMeli = null, $retornaArray = false)
	{
        $respuestaVerificarUsuarioConectado = (new UsuarioController())->verificarUsuarioConectado(null, true);
        $idUsuarioAgencia = $respuestaVerificarUsuarioConectado['id_usuario_agencia'];

        if ($request !== null && $tokenMeli == null)
        {
            $tokenMeli = $request->input('token_meli');
            $refreshTokenMeli = $request->input('refresh_token_meli');
        }

        $vectorAtributosDatosMeli = [
            'token_meli' => $tokenMeli,
            'refresh_token_meli' => $refreshTokenMeli,
            'fecha_hora_token_meli' => $this->fechaHoraActual()['fecha_hora_actual_formato']
        ];

        $nombreTabla = $this->usuarioTabla('estate_agency');

        $respuestaActualizarDatosMeli = $this->actualizarDatosMeli($vectorAtributosDatosMeli, $idUsuarioAgencia, null, $nombreTabla, 'datos_meli');

		if ($retornaArray == true)
		{
			return $respuestaActualizarDatosMeli;
		}
		else
		{
			return response()->json($respuestaActualizarDatosMeli, $respuestaActualizarDatosMeli['codigo_http']);
		}
	}
}
