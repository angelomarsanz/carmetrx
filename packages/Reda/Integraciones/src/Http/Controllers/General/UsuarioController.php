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
     * Verificar si el usuario está autenticado y devolver sus datos
     */
    // Agregar el request->prefijo
    public function verificarUsuarioConectado(Request $request)
    {
        $id_usuario_administrador_conectado = 0;
        $id_usuario_agencia_conectado = 0;
        $id_usuario_agente_conectado = 0;
        $rol_usuario_conectado = 0; // 0 = No autenticado, 2 = Agente, 3 = Usuario Agencia, 5 = Administrador
        $tipo_agencia_agente = ''; // Puede ser vacío si no aplica, 'estate_agent', 'estate_agency' y "admin"

        // Preferimos el parámetro `prefijo` si viene desde el cliente.
        // Si no está, intentamos inferirlo desde la cabecera Referer o la ruta actual.
        if ($request->has('prefijo')) {
            // Imprimir en el log de laravel el valor del prefijo recibido desde el cliente
            \Log::info('Valor de prefijo recibido desde el cliente: ' . $request->input('prefijo'));
            $prefijo = $request->input('prefijo');
        } else {
            $referer = $request->headers->get('referer');
            if ($referer) {
                $parsed = parse_url($referer);
                $prefijo = $parsed['path'] ?? '/';
            } else {
                // Último recurso: usar la ruta actual del request
                $prefijo = $request->getPathInfo() ?? '/';
            }
            // Imprimir en el log de laravel el valor del prefijo inferido
            \Log::info('Valor de prefijo inferido: ' . $prefijo);
        }

        if ($prefijo === '/user') {
            // Obtener el usuario que está logueado actualmente
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'codigo_respuesta' => 2,
                    'mensaje_respuesta' => 'Usuario no autenticado'
                ], 401);
            }

            $id_usuario_administrador_conectado = 0;
            $id_usuario_agencia_conectado = $user->id;
            $id_usuario_agente_conectado = $user->id;          
            $rol_usuario_conectado = 3;
            $tipo_agencia_agente = 'estate_agency';

        } elseif ($prefijo && strpos($prefijo, '/agent') !== false) {
            // Lógica para agente dentro del contexto tenant
            $agente = $this->obtenerAgente();

            if (is_array($agente) && isset($agente['codigo_respuesta']) && $agente['codigo_respuesta'] !== 0) {
                return response()->json($agente, 401);
            }

            $id_usuario_administrador_conectado = $agente['id_usuario_administrador_conectado'];
            $id_usuario_agencia_conectado = $agente['id_usuario_agencia_conectado'];
            $id_usuario_agente_conectado = $agente['id_agente_conectado'];
            $rol_usuario_conectado = $agente['rol_usuario_conectado'];
            $tipo_agencia_agente = $agente['tipo_agencia_agente'];

        } elseif ($prefijo === '/admin') {
            // Verificar si el usuario administrador está autenticado
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                $id_usuario_administrador_conectado = $admin->id;
                $id_usuario_agencia_conectado = 0;
                $id_usuario_agente_conectado = 0;
                $rol_usuario_conectado = 5; 
                $tipo_agencia_agente = 'admin';
            } else {
                return response()->json([
                    'codigo_respuesta' => 2,
                    'mensaje_respuesta' => 'Administrador no autenticado'
                ], 401);
            }
        } else {
            return response()->json([
                'codigo_respuesta' => 3,
                'mensaje_respuesta' => 'Valor inválido para prefijo'
            ], 400);
        }

        return response()->json([
			'codigo_respuesta' => 0,
			'mensaje_respuesta' => 'Verificación exitosa',
            'id_usuario_administrador_conectado' => $id_usuario_administrador_conectado,
			'id_usuario_agencia_conectado' => $id_usuario_agencia_conectado,
			'id_usuario_agente_conectado' => $id_usuario_agente_conectado, // Cuando es agencia, el id del usuario conectado es el mismo
			'rol_usuario_conectado' => $rol_usuario_conectado, 
			'tipo_agencia_agente' => $tipo_agencia_agente
        ], 200);
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
                        'mensaje_respuesta' => 'Agente autenticado correctamente',
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
                    'mensaje_respuesta' => 'El agente no pertenece al tenant actual'
                ];
            }

            // Si no existe getUser(), enviar vector con código de error
            return [
                'codigo_respuesta' => 2,
                'mensaje_respuesta' => 'No se pudo obtener el tenant actual'
            ];
        } catch (\Exception $e) {
            // En caso de error, enviar vector con código de error y mensaje_respuesta
            return [
                'codigo_respuesta' => 3,
                'mensaje_respuesta' => 'Error al obtener el agente: ' . $e->getMessage()
            ];
        }
    }
}
