<?php
namespace Reda\Integraciones\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Agent\Agent;

class AgenteController extends Controller
{
    /**
     * Devuelve el agente autenticado (guard 'agent') solo si pertenece
     * al tenant (agencia) actual obtenido con getUser().
     *
     * Retorna el modelo Agent o null.
     */
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
                        'codigo_retorno' => 0,
                        'mensaje' => 'Agente autenticado correctamente',
                        'id_usuario_agencia' => $agent->user_id,
                        'id_agente' => $agent->id
                    ];
                }
                // Si no coincide el tenant, enviar vector con código de error
                return [
                    'codigo_retorno' => 1,
                    'mensaje' => 'El agente no pertenece al tenant actual'
                ];
            }

            // Si no existe getUser(), enviar vector con código de error
            return [
                'codigo_retorno' => 2,
                'mensaje' => 'No se pudo obtener el tenant actual'
            ];
        } catch (\Exception $e) {
            // En caso de error, enviar vector con código de error y mensaje
            return [
                'codigo_retorno' => 3,
                'mensaje' => 'Error al obtener el agente: ' . $e->getMessage()
            ];
        }
    }
}
