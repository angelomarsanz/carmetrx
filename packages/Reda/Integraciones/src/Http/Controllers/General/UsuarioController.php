<?php
namespace Reda\Integraciones\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Importas el modelo original de Laravel, no necesitas crear uno nuevo
use App\Models\User;
use App\Models\User\Agent\Agent;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    /**
     * Obtiene el origen y prefijo base de la URL, similar a obtenerOrigenPrefijoBase.js pero en PHP.
     * @param Request $request
     * @return array ['origin' => ..., 'prefijo' => ...]
     */
    public function obtenerOrigenPrefijoBasePHP(Request $request)
    {
        // Obtener el path y el host
        $path = $request->getPathInfo();
        $host = $request->getSchemeAndHttpHost();
        $slugs = array_values(array_filter(explode('/', $path), function($slug) { return $slug !== ''; }));

        $prefijoBase = '';

        // Caso 1: Administrador (/admin/...)
        if (isset($slugs[0]) && $slugs[0] === 'admin' && (!isset($slugs[1]) || $slugs[1] !== 'agent')) {
            $prefijoBase = '/admin';
        }
        // Caso 2: Agencia/User (/user/...)
        else if (isset($slugs[0]) && $slugs[0] === 'user' && (!isset($slugs[1]) || $slugs[1] !== 'agent')) {
            $prefijoBase = '/user';
        }
        // Caso 3: Agente (/{username}/agent/...)
        else if (isset($slugs[1]) && $slugs[1] === 'agent') {
            $prefijoBase = '/' . $slugs[0] . '/' . $slugs[1];
        }

        return [
            'origin' => $host,
            'prefijo' => $prefijoBase
        ];
    }

    /**
     * Verificar si el usuario está autenticado y devolver sus datos
     */
    // Agregar el request->prefijo
    /**
     * Verifica si el usuario está autenticado y devuelve datos según el contexto de la llamada.
     * Si $returnArray es true, retorna un array asociativo (llamada interna).
     * Si $returnArray es false (por defecto), retorna un JSON (llamada Ajax o HTTP normal).
     */
    public function verificarUsuarioConectado(Request $request, $returnArray = false)
    {
        $id_usuario_administrador_conectado = 0;
        $id_usuario_agencia_conectado = 0;
        $id_usuario_agente_conectado = 0;
        $rol_usuario_conectado = 0; // 0 = No autenticado, 2 = Agente, 3 = Usuario Agencia, 5 = Administrador
        $tipo_agencia_agente = ''; // Puede ser vacío si no aplica, 'estate_agent', 'estate_agency' y "admin"

        // Preferimos el parámetro `prefijo` si viene desde el cliente.
        // Si no está, usamos la lógica PHP equivalente a obtenerOrigenPrefijoBase.js
        if ($request->has('origin') && $request->has('prefijo')) {
            $origin = $request->input('origin');
            $prefijo = $request->input('prefijo');
        } else {
            $origenPrefijo = self::obtenerOrigenPrefijoBasePHP($request);
            $origin = $origenPrefijo['origin'];
            $prefijo = $origenPrefijo['prefijo'];
        }

        $respuesta = [
            'codigo_respuesta' => 0,
            'mensaje_respuesta' => '',
            'id_usuario_administrador_conectado' => 0,
            'id_usuario_agencia_conectado' => 0,
            'id_usuario_agente_conectado' => 0,
            'rol_usuario_conectado' => 0,
            'tipo_agencia_agente' => ''
        ];

        if ($prefijo === '/user') {
            $user = Auth::user();
            if (!$user) {
                $respuesta = [
                    'codigo_respuesta' => 2,
                    'mensaje_respuesta' => __('Usuario no autenticado'),
                    'id_usuario_administrador_conectado' => 0,
                    'id_usuario_agencia_conectado' => 0,
                    'id_usuario_agente_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => ''
                ];
                return $returnArray ? $respuesta : response()->json($respuesta, 401);
            }
            $respuesta = [
                'codigo_respuesta' => 0,
                'mensaje_respuesta' => __('Verificación exitosa'),
                'id_usuario_administrador_conectado' => 0,
                'id_usuario_agencia_conectado' => $user->id,
                'id_usuario_agente_conectado' => $user->id,
                'rol_usuario_conectado' => 3,
                'tipo_agencia_agente' => 'estate_agency'
            ];
        } elseif ($prefijo && strpos($prefijo, '/agent') !== false) {
            $agente = $this->obtenerAgente();
            if (is_array($agente) && isset($agente['codigo_respuesta']) && $agente['codigo_respuesta'] !== 0) {
                return $returnArray ? $agente : response()->json($agente, 401);
            }
            $respuesta = [
                'codigo_respuesta' => 0,
                'mensaje_respuesta' => __('Verificación exitosa'),
                'id_usuario_administrador_conectado' => $agente['id_usuario_administrador_conectado'],
                'id_usuario_agencia_conectado' => $agente['id_usuario_agencia_conectado'],
                'id_usuario_agente_conectado' => $agente['id_agente_conectado'],
                'rol_usuario_conectado' => $agente['rol_usuario_conectado'],
                'tipo_agencia_agente' => $agente['tipo_agencia_agente']
            ];
        } elseif ($prefijo === '/admin') {
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                $respuesta = [
                    'codigo_respuesta' => 0,
                    'mensaje_respuesta' => __('Verificación exitosa'),
                    'id_usuario_administrador_conectado' => $admin->id,
                    'id_usuario_agencia_conectado' => 0,
                    'id_usuario_agente_conectado' => 0,
                    'rol_usuario_conectado' => 5,
                    'tipo_agencia_agente' => 'admin'
                ];
            } else {
                $respuesta = [
                    'codigo_respuesta' => 2,
                    'mensaje_respuesta' => __('Usuario no autenticado'),
                    'id_usuario_administrador_conectado' => 0,
                    'id_usuario_agencia_conectado' => 0,
                    'id_usuario_agente_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => ''
                ];
                return $returnArray ? $respuesta : response()->json($respuesta, 401);
            }
        } else {
            $respuesta = [
                'codigo_respuesta' => 3,
                'mensaje_respuesta' => __('Valor inválido para prefijo'),
                'id_usuario_administrador_conectado' => 0,
                'id_usuario_agencia_conectado' => 0,
                'id_usuario_agente_conectado' => 0,
                'rol_usuario_conectado' => 0,
                'tipo_agencia_agente' => ''
            ];
            return $returnArray ? $respuesta : response()->json($respuesta, 400);
        }

        return $returnArray ? $respuesta : response()->json($respuesta, 200);
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
                        'codigo_respuesta' => 0,
                        'mensaje_respuesta' => __('Verificación exitosa'),
                        'id_usuario_administrador_conectado' => 0,
                        'id_usuario_agencia_conectado' => $agent->user_id,
                        'id_usuario_agente_conectado' => $agent->id,
                        'rol_usuario_conectado' => 2, 
                        'tipo_agencia_agente' => 'estate_agent',
                    ];
                }
                // Si no coincide el tenant, enviar vector con código de error
                return [
                    'codigo_respuesta' => 1,
                    'mensaje_respuesta' => __('El agente no pertenece al tenant actual')
                ];
            }

            // Si no existe getUser(), enviar vector con código de error
            return [
                'codigo_respuesta' => 2,
                'mensaje_respuesta' => __('No se pudo obtener el tenant actual')
            ];
        } catch (\Exception $e) {
            // En caso de error, enviar vector con código de error y mensaje_respuesta
            return [
                'codigo_respuesta' => 3,
                'mensaje_respuesta' => __('Error al obtener el agente: ') . $e->getMessage()
            ];
        }
    }
}
