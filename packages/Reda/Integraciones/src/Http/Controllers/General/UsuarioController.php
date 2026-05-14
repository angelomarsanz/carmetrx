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
                    'message' => 'Usuario no autenticado',
                    'mensaje_usuario' => __('Usuario no autenticado'),
                    'respuesta' => '',
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => '',
                    'code' => 401
                ];
            }
            else {
                $respuesta = [
                    'success' => true,
                    'message' => 'Verificación exitosa',
                    'mensaje_usuario' => __('Verificación exitosa'),
                    'respuesta' => '',
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => $user->id,
                    'id_usuario_agente' => $user->id,
                    'id_usuario_conectado' => $user->id,
                    'rol_usuario_conectado' => 3,
                    'tipo_agencia_agente' => 'estate_agency',
                    'code' => 200
                ];
                $idUsuarioConectado = $user->id;
            }
        } elseif ($prefijo && strpos($prefijo, '/agent') !== false) {
            $agente = $this->obtenerAgente();
            if (is_array($agente) && isset($agente['message']) && $agente['message'] !== 'Verificación exitosa') {
                $respuesta = $agente;
            }
            else {
                $respuesta = [
                    'success' => true,
                    'message' => 'Verificación exitosa',
                    'mensaje_usuario' => __('Verificación exitosa'),
                    'respuesta' => '',
                    'id_usuario_administrador' => $agente['id_usuario_administrador'],
                    'id_usuario_agencia' => $agente['id_usuario_agencia'],
                    'id_usuario_agente' => $agente['id_usuario_agente'],
                    'id_usuario_conectado' => $agente['id_usuario_conectado'],
                    'rol_usuario_conectado' => $agente['rol_usuario_conectado'],
                    'tipo_agencia_agente' => $agente['tipo_agencia_agente'],
                    'code' => 200
                ];
                $idUsuarioConectado = $agente['id_usuario_agente'];
            }
        } elseif ($prefijo === '/admin') {
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                $respuesta = [
                    'success' => true,
                    'message' => 'Verificación exitosa',
                    'mensaje_usuario' => __('Verificación exitosa'),
                    'respuesta' => '',
                    'id_usuario_administrador' => $admin->id,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => $admin->id,
                    'rol_usuario_conectado' => 5,
                    'tipo_agencia_agente' => 'admin',
                    'code' => 200
                ];
                $idUsuarioConectado = $admin->id;
            } else {
                $respuesta = [
                    'success' => false,
                    'message' => 'Usuario no autenticado',
                    'mensaje_usuario' => __('Usuario no autenticado'),
                    'respuesta' => '',
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => '',
                    'code' => 401
                ];
            }
        } else {
            $respuesta = [
                'success' => false,
                'message' => 'Valor inválido para prefijo',
                'mensaje_usuario' => __('Valor inválido para prefijo'),
                'respuesta' => '',
                'id_usuario_administrador' => 0,
                'id_usuario_agencia' => 0,
                'id_usuario_agente' => 0,
                'id_usuario_conectado' => 0,
                'rol_usuario_conectado' => 0,
                'tipo_agencia_agente' => '',
                'code' => 400
            ];
        }

        if ($respuesta['id_usuario_conectado'] != 0 && $respuesta['tipo_agencia_agente'] != '') {
            $vectorAtributosDatosMeli = [
                'verificar_usuario_conectado' => [
                    'message' => $respuesta['message'],
                    'fecha_hora' => $this->fechaHoraActual()['fecha_hora_actual_formato'],
                    'code' => $respuesta['code']
                ],
            ];

            $nombreTabla = $this->usuarioTabla($respuesta['tipo_agencia_agente']);

            $respuestaActualizarDatosMeli = $this->actualizarDatosMeli($vectorAtributosDatosMeli, $respuesta['id_usuario_conectado'], null, $nombreTabla, 'datos_meli');

            if ($respuestaActualizarDatosMeli['success'] == false) 
            {
                $respuesta = $respuestaActualizarDatosMeli;
            }
        }

        Log::info("verificarUsuarioConectado, respuesta: " . print_r($respuesta, true));

        return $returnArray ? $respuesta : response()->json($respuesta, $respuesta['code']);
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
                        'message' => 'Verificación exitosa',
                        'mensaje_usuario' => __('Verificación exitosa'),
                        'respuesta' => '',
                        'id_usuario_administrador' => 0,
                        'id_usuario_agencia' => $agent->user_id,
                        'id_usuario_agente' => $agent->id,
                        'id_usuario_conectado' => $agent->id,
                        'rol_usuario_conectado' => 2,
                        'tipo_agencia_agente' => 'estate_agent',
                        'code' => 200
                    ];
                }
                // Si no coincide el tenant, enviar vector con código de error
                return [
                    'success' => false,
                    'message' => 'El agente no pertenece al tenant actual',
                    'mensaje_usuario' => __('El agente no pertenece al tenant actual'),
                    'respuesta' => '',
                    'code' => 400
                ];
            }

            // Si no existe getUser(), enviar vector con código de error
            return [
                'success' => false,
                'message' => 'No se pudo obtener el tenant actual',
                'mensaje_usuario' => __('No se pudo obtener el tenant actual'),
                'respuesta' => '',
                'code' => 404
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener el agente',
                'mensaje_usuario' => __('Error al obtener el agente: ') . $e->getMessage(),
                'respuesta' => '',
                'code' => 404                
            ];
        }
    }
}