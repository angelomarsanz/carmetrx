<?php
namespace Reda\Integraciones\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Importas el modelo original de Laravel, no necesitas crear uno nuevo
use App\Models\User;
use App\Models\User\Agent\Agent;
use Reda\Integraciones\Models\MercadoLibre\AdminMeli;
use Reda\Integraciones\Models\MercadoLibre\UserMeli;
use Reda\Integraciones\Models\MercadoLibre\AgentMeli;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ConfiguracionController;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;

class UsuarioController extends Controller
{
    use MeliRequestsTrait;

    /**
     * Verificar si el usuario está autenticado y devolver sus datos
     */
    // Agregar el request->prefijo
    /**
     * Verifica si el usuario está autenticado y devuelve datos según el contexto de la llamada.
     * Si $returnArray es true, retorna un array asociativo (llamada interna).
     * Si $returnArray es false (por defecto), retorna un JSON (llamada Ajax o HTTP normal).
     */
    public function verificarUsuarioConectado(?Request $request, $returnArray = false)
    {
        // Preferimos el parámetro `prefijo` si viene desde el cliente.
        // Si no está, usamos la lógica PHP equivalente a obtenerOrigenPrefijoBase.js
        if ($request) { 
            $origin = $request->input('origin');
            $prefijo = $request->input('prefijo');
        } else {
            $origenPrefijo = $this->obtenerOrigenPrefijoBase();
            $origin = $origenPrefijo['origin'];
            $prefijo = $origenPrefijo['prefijo'];
        }

        if ($prefijo === '/user') {
            $user = Auth::user();
            if (!$user) {
                $respuesta = [
                    'success' => false,
                    'codigo_respuesta' => 1,
                    'codigo_http' => 401,
                    'mensaje_respuesta' => __('Usuario no autenticado'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas'  => '',
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => ''
                ];
            }
            else {
                $respuesta = [
                    'success' => true,
                    'codigo_respuesta' => 0,
                    'codigo_http' => 200,
                    'mensaje_respuesta' => __('Verificación exitosa'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas'  => '',
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => $user->id,
                    'id_usuario_agente' => $user->id,
                    'id_usuario_conectado' => $user->id,
                    'rol_usuario_conectado' => 3,
                    'tipo_agencia_agente' => 'estate_agency'
                ];
                $idUsuarioConectado = $user->id;
            }
        } elseif ($prefijo && strpos($prefijo, '/agent') !== false) {
            $agente = $this->obtenerAgente();
            if (is_array($agente) && isset($agente['codigo_respuesta']) && $agente['codigo_respuesta'] !== 0) {
                $respuesta = $agente;
            }
            else {
                $respuesta = [
                    'success' => true,
                    'codigo_respuesta' => 0,
                    'codigo_http' => 200,
                    'mensaje_respuesta' => __('Verificación exitosa'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas'  => '',
                    'id_usuario_administrador' => $agente['id_usuario_administrador'],
                    'id_usuario_agencia' => $agente['id_usuario_agencia'],
                    'id_usuario_agente' => $agente['id_agente_conectado'],
                    'id_usuario_conectado' => $agente['id_agente_conectado'],
                    'rol_usuario_conectado' => $agente['rol_usuario_conectado'],
                    'tipo_agencia_agente' => $agente['tipo_agencia_agente']
                ];
                $idUsuarioConectado = $agente['id_usuario_agente'];
            }
        } elseif ($prefijo === '/admin') {
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                $respuesta = [
                    'success' => true,
                    'codigo_respuesta' => 0,
                    'codigo_http' => 200,
                    'mensaje_respuesta' => __('Verificación exitosa'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas'  => '',
                    'id_usuario_administrador' => $admin->id,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => $admin->id,
                    'rol_usuario_conectado' => 5,
                    'tipo_agencia_agente' => 'admin'
                ];
                $idUsuarioConectado = $admin->id;
            } else {
                $respuesta = [
                    'success' => false,
                    'codigo_respuesta' => 2,
                    'codigo_http' => 401,
                    'mensaje_respuesta' => __('Usuario no autenticado'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas'  => '',
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => ''
                ];
            }
        } else {
            $respuesta = [
                'success' => false,
                'codigo_respuesta' => 3,
                'codigo_http' => 400,
                'mensaje_respuesta' => __('Valor inválido para prefijo'),
                'respuesta' => '',
                'error_curl' => '',
                'causas'  => '',
                'id_usuario_administrador' => 0,
                'id_usuario_agencia' => 0,
                'id_usuario_agente' => 0,
                'id_usuario_conectado' => 0,
                'rol_usuario_conectado' => 0,
                'tipo_agencia_agente' => ''
            ];
        }

        Log::info("verificarUsuarioConectado, respuesta: " . print_r($respuesta, true));

        if ($respuesta['id_usuario_conectado'] != 0 && $respuesta['tipo_agencia_agente'] != '') {
            $vectorAtributosDatosMeli = [
                'verificar_usuario_conectado' => [
                    'codigo_respuesta' => $respuesta['codigo_respuesta'],
                    'mensaje_respuesta' => $respuesta['mensaje_respuesta'],
                    'fecha_hora' => $this->fechaHoraActual()['fecha_hora_actual_formato']
                ],
            ];

            $nombreTabla = $this->usuarioTabla($respuesta['tipo_agencia_agente']);

            $respuestaActualizarDatosMeli = $this->actualizarDatosMeli($vectorAtributosDatosMeli, $respuesta['id_usuario_conectado'], null, $nombreTabla, 'datos_meli');

            if ($respuestaActualizarDatosMeli['success'] == false) 
            {
                $respuesta = $respuestaActualizarDatosMeli;
            }
        }

        return $returnArray ? $respuesta : response()->json($respuesta, $respuesta['codigo_http']);
    }
    public static function obtenerAgente()
    {
        try {
            if (!Auth::guard('agent')->check()) {
                return null;
            }

            $agent = Auth::guard('agent')->user();

            // Comprobar que el agente pertenece al tenant actual de la URL
            if (function_exists('getUser')) {
                $tenant = getUser();
                if ($tenant && isset($agent->user_id) && $agent->user_id == $tenant->id) {
                    // retornar tanto el id del usuario como el id del agente
                    return [
                        'success' => true,
                        'codigo_respuesta' => 0,
                        'codigo_http' => 200,
                        'mensaje_respuesta' => __('Verificación exitosa'),
                        'respuesta' => '',
                        'error_curl' => '',
                        'causas'  => '',
                        'id_usuario_administrador' => 0,
                        'id_usuario_agencia' => $agent->user_id,
                        'id_usuario_agente' => $agent->id,
                        'rol_usuario_conectado' => 2,
                        'tipo_agencia_agente' => 'estate_agent',
                    ];
                }
                // Si no coincide el tenant, enviar vector con código de error
                return [
                    'success' => false,
                    'codigo_respuesta' => 1,
                    'codigo_http' => 400,
                    'mensaje_respuesta' => __('El agente no pertenece al tenant actual'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas' => ''
                ];
            }

            // Si no existe getUser(), enviar vector con código de error
            return [
                'success' => false,
                'codigo_respuesta' => 2,
                'codigo_http' => 404,
                'mensaje_respuesta' => __('No se pudo obtener el tenant actual'),
                'respuesta' => '',
                'error_curl' => '',
                'causas' => ''
            ];
        } catch (\Exception $e) {
            // En caso de error, enviar vector con código de error y mensaje_respuesta
            return [
                'success' => false,
                'codigo_respuesta' => 3,
                'codigo_http' => 404,
                'mensaje_respuesta' => __('Error al obtener el agente: ') . $e->getMessage(),
                'respuesta' => '',
                'error_curl' => '',
                'causas' => ''                
            ];
        }
    }
}