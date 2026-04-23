<?php
namespace Reda\Integraciones\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Importas el modelo original de Laravel, no necesitas crear uno nuevo
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// Usa el controlador de Agente para obtener el agente autenticado
use Reda\Integraciones\Http\Controllers\General\AgenteController;

class UsuarioController extends Controller
{
    /**
     * Verificar si el usuario está autenticado y devolver sus datos
     */
    // Agregar el request->inicio_ruta
    public function verificarUsuarioConectado(Request $request)
    {
		$id_usuario_agencia = 0;
		$id_usuario_conectado = 0;
		$rol_usuario_conectado = 0;
		$tipo_agencia_agente = '';

        // Preferimos el parámetro `inicio_ruta` si viene desde el cliente.
        // Si no está, intentamos inferirlo desde la cabecera Referer o la ruta actual.
        if ($request->has('inicio_ruta')) {
            $inicio_ruta = $request->input('inicio_ruta');
        } else {
            $referer = $request->headers->get('referer');
            if ($referer) {
                $parsed = parse_url($referer);
                $inicio_ruta = $parsed['path'] ?? '/';
            } else {
                // Último recurso: usar la ruta actual del request
                $inicio_ruta = $request->getPathInfo() ?? '/';
            }
        }

        if ($inicio_ruta === '/user/') {
            // Obtener el usuario que está logueado actualmente
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'codigo_retorno' => 2,
                    'mensaje' => 'Usuario no autenticado'
                ], 401);
            }

            $id_usuario_agencia = $user->id;
            $id_usuario_conectado = $user->id;
            $rol_usuario_conectado = 3;
            $tipo_agencia_agente = 'estate_agency';

        } elseif ($inicio_ruta && strpos($inicio_ruta, '/agent/') !== false) {
            // Lógica para agente dentro del contexto tenant
            $agente = AgenteController::obtenerAgente();

            if (is_array($agente) && isset($agente['codigo_retorno']) && $agente['codigo_retorno'] !== 0) {
                return response()->json($agente, 401);
            }

            $id_usuario_agencia = $agente['id_usuario_agencia'];
            $id_usuario_conectado = $agente['id_agente'];
            $rol_usuario_conectado = 2;
            $tipo_agencia_agente = 'estate_agent';
        }
        else {
            return response()->json([
                'codigo_retorno' => 3,
                'mensaje' => 'Valor inválido para inicio_ruta'
            ], 400);
        }

        return response()->json([
			'codigo_retorno' => 0,
			'mensaje' => 'Verificación exitosa',
			'id_usuario_agencia' => $id_usuario_agencia,
			'id_usuario_conectado' => $id_usuario_conectado, // Cuando es agencia, el id del usuario conectado es el mismo
			'rol_usuario_conectado' => $rol_usuario_conectado, // 2 = Agente ó 3 = Agencia
			'tipo_agencia_agente' => $tipo_agencia_agente
        ], 200);
    }
}
