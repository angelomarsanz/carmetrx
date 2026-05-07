<?php
namespace Reda\Integraciones\Http\Controllers\MercadoLibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Reda\Integraciones\Models\MercadoLibre\UserMeli;
use Illuminate\Support\Facades\Log;
use Reda\Integraciones\Http\Controllers\General\UsuarioController;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;
use DateTime;
use App\Models\User\UserCarBrand;
use Reda\Integraciones\Models\MercadoLibre\MarcaAutoMeli;
use Illuminate\Support\Facades\DB;

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
    public function sincronizarMarcasMeli(Request $request, $token = null)
    {
        // 1. Obtención del Token (Prioriza parámetro de ruta, luego Query String)
        $tokenMeli = $token ?? $request->query('token');

        if (!$tokenMeli) {
            return response()->json(['success' => false, 'mensaje' => 'Token no proporcionado.'], 400);
        }

        // 2. Obtener datos de conexión para el Trait
        $respuestaVerificarUsuarioConectado = (new UsuarioController())->verificarUsuarioConectado(null, true);

        if (!$respuestaVerificarUsuarioConectado['success']) {
            return response()->json(['success' => false, 'mensaje' => __('No se pudo verificar el usuario conectado'), 'respuestaVerificarUsuarioConectado' => $respuestaVerificarUsuarioConectado], $respuestaVerificarUsuarioConectado['codigo_http']);
        }

        $idUsuario = $respuestaVerificarUsuarioConectado['id_usuario_conectado'];
        $nombreTabla = $this->usuarioTabla($respuestaVerificarUsuarioConectado['tipo_agencia_agente']);

        // 3. Consultar marcas en Mercado Libre Uruguay (MLU)
        $respuestaMeli = $this->enviarSolicitudMeli(
            'categories/MLU1744/attributes',
            'GET',
            [],
            true,
            $tokenMeli,
            false,
            'sincronizar_marcas_meli',
            $idUsuario,
            null,
            $nombreTabla
        );

        if (!$respuestaMeli['success']) return response()->json($respuestaMeli, 500);

        // 4. Extraer el listado de marcas de la API
        $marcasMeli = collect($respuestaMeli['respuesta'])->firstWhere('id', 'BRAND')['values'] ?? [];

        // --- CARGA DE DATOS EXISTENTES PARA EVITAR DUPLICADOS ---
        
        // Nombres de marcas ya existentes en minúsculas para comparar: ['toyota' => id, ...]
        $marcasExistentesDB = UserCarBrand::all()->pluck('id', 'name')->mapWithKeys(function ($id, $name) {
            return [strtolower(trim($name)) => $id];
        });

        // IDs de marcas que ya tienen relación con Meli: [1, 2, 5, ...]
        $relacionesExistentesMeli = MarcaAutoMeli::all()->pluck('user_car_brand_id')->toArray();

        $nuevasMarcasBase = 0;
        $nuevasRelacionesMeli = 0;
        $yaExistentes = 0;

        // 5. Procesamiento
        foreach ($marcasMeli as $marca) {
            $nombreLimpio = strtolower(trim($marca['name']));

            DB::transaction(function () use ($marca, $nombreLimpio, &$marcasExistentesDB, &$relacionesExistentesMeli, &$nuevasMarcasBase, &$nuevasRelacionesMeli, &$yaExistentes) {
                
                // PASO A: Asegurar la existencia en la tabla original (user_car_brand)
                if ($marcasExistentesDB->has($nombreLimpio)) {
                    $brandId = $marcasExistentesDB->get($nombreLimpio);
                } else {
                    $nuevaMarca = UserCarBrand::create([
                        'name' => $marca['name'],
                        'language_id' => 180 // Español
                    ]);
                    $brandId = $nuevaMarca->id;
                    $marcasExistentesDB->put($nombreLimpio, $brandId);
                    $nuevasMarcasBase++;
                }

                // PASO B: Verificar si ya existe la relación en nuestra tabla marcas_autos_melis
                if (in_array($brandId, $relacionesExistentesMeli)) {
                    $yaExistentes++;
                    return; // Ya está sincronizada, saltamos a la siguiente
                }

                // PASO C: Solo si no existía la relación, la creamos
                MarcaAutoMeli::create([
                    'user_car_brand_id' => $brandId,
                    'datos_meli' => [
                        'meli_id' => $marca['id'],
                        'nombre_meli' => $marca['name']
                    ],
                    'respuesta_meli' => $marca 
                ]);
                
                $relacionesExistentesMeli[] = $brandId;
                $nuevasRelacionesMeli++;
            });
        }

        return response()->json([
            'success' => true,
            'mensaje' => "Sincronización semestral finalizada.",
            'resultado' => [
                'marcas_nuevas_en_proyecto' => $nuevasMarcasBase,
                'nuevas_vinculaciones_con_meli' => $nuevasRelacionesMeli,
                'marcas_que_ya_estaban_al_dia' => $yaExistentes
            ]
        ]);
    }
}