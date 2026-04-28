<?php
namespace Reda\Integraciones\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Importas el modelo original de Laravel, no necesitas crear uno nuevo
use App\Models\User;
use App\Models\User\Agent\Agent;
use Reda\Integraciones\Models\MercadoLibre\UserMeli;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Reda\Integraciones\Http\Controllers\MercadoLibre\ConfiguracionController;

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
            'id_usuario_administrador' => 0,
            'id_usuario_agencia' => 0,
            'id_usuario_agente' => 0,
            'id_usuario_conectado' => 0,
            'rol_usuario_conectado' => 0,
            'tipo_agencia_agente' => ''
        ];

        $codigoRespuestaJson = 200;

        if ($prefijo === '/user') {
            $user = Auth::user();
            if (!$user) {
                $respuesta = [
                    'codigo_respuesta' => 2,
                    'mensaje_respuesta' => __('Usuario no autenticado'),
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => ''
                ];
                $codigoRespuestaJson = 401;
            }
            else {
                $respuesta = [
                    'codigo_respuesta' => 0,
                    'mensaje_respuesta' => __('Verificación exitosa'),
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
                $codigoRespuestaJson = 401;
            }
            else {
                $respuesta = [
                    'codigo_respuesta' => 0,
                    'mensaje_respuesta' => __('Verificación exitosa'),
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
                    'codigo_respuesta' => 0,
                    'mensaje_respuesta' => __('Verificación exitosa'),
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
                    'codigo_respuesta' => 2,
                    'mensaje_respuesta' => __('Usuario no autenticado'),
                    'id_usuario_administrador' => 0,
                    'id_usuario_agencia' => 0,
                    'id_usuario_agente' => 0,
                    'id_usuario_conectado' => 0,
                    'rol_usuario_conectado' => 0,
                    'tipo_agencia_agente' => ''
                ];
                $codigoRespuestaJson = 401;
            }
        } else {
            $respuesta = [
                'codigo_respuesta' => 3,
                'mensaje_respuesta' => __('Valor inválido para prefijo'),
                'id_usuario_administrador' => 0,
                'id_usuario_agencia' => 0,
                'id_usuario_agente' => 0,
                'id_usuario_conectado' => 0,
                'rol_usuario_conectado' => 0,
                'tipo_agencia_agente' => ''
            ];
            $codigoRespuestaJson = 400;
        }

        if ($respuesta['id_usuario_conectado'] != 0 && $respuesta['tipo_agencia_agente'] != '') {
            $vectorAtributosDatosMeli = [
                'codigo_respuesta_verificar_usuario_conectado' => $respuesta['codigo_respuesta'],
                'mensaje_respuesta_verificar_usuario_conectado' => $respuesta['mensaje_respuesta'],
                'fecha_hora_verificar_usuario_conectado' => (new ConfiguracionController())->fechaHoraActual()
            ];

            $respuestaActualizarDatosMeliUsuario = $this->actualizarDatosMeliUsuario($vectorAtributosDatosMeli, $respuesta['id_usuario_conectado'], $respuesta['tipo_agencia_agente']);
        }

        return $returnArray ? $respuesta : response()->json($respuesta, $codigoRespuestaJson);
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
                        'id_usuario_administrador' => 0,
                        'id_usuario_agencia' => $agent->user_id,
                        'id_usuario_agente' => $agent->id,
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
    public function actualizarDatosMeliUsuario($vectorAtributos = [], $idUsuarioConectado = null, $tipoUsuario = null)
    {
        try {
            // 1. Verificación de parámetros obligatorios
            if (empty($idUsuarioConectado)) {
                Log::error("[Carmetric] Intento de actualización sin ID de usuario.");
                return [
                    'codigo_respuesta' => 1,
                    'mensaje_respuesta' => 'Error: El ID del usuario es obligatorio.'
                ];
            }

            if (empty($vectorAtributos)) {
                return [
                    'codigo_respuesta' => 1,
                    'mensaje_respuesta' => 'Error: El vector de atributos está vacío.'
                ];
            }

            // 2. Buscamos el registro o creamos una instancia nueva si no existe
            // firstOrNew busca por user_id; si no lo halla, prepara un objeto nuevo con ese user_id
            $userMeli = UserMeli::firstOrNew(['user_id' => $idUsuarioConectado]);

            // 3. Lógica de actualización del JSON
            // Gracias al cast 'array' en el modelo, $userMeli->datos_meli ya es un vector PHP
            $datosActuales = $userMeli->datos_meli ?? [];

            // Fusionamos: los valores de $vectorAtributos sustituyen a los actuales o se agregan
            $datosActualizados = array_merge($datosActuales, $vectorAtributos);

            // 4. Guardar cambios
            $userMeli->datos_meli = $datosActualizados;
            $userMeli->save();

            Log::info("[Carmetric] Columna datos_meli actualizada con éxito para usuario ID: " . $idUsuarioConectado);

            return [
                'codigo_respuesta' => 0,
                'mensaje_respuesta' => 'Atributos actualizados correctamente.',
                'datos_guardados' => $datosActualizados
            ];

        } catch (\Exception $e) {
            Log::error("[Carmetric] Error crítico en actualizarDatosMeliUsuario: " . $e->getMessage());
            return [
                'codigo_respuesta' => 2,
                'mensaje_respuesta' => 'Error interno al procesar la actualización.'
            ];
        }
    }
}

