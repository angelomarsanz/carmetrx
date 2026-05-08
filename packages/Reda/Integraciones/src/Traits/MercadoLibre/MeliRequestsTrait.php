<?php

namespace Reda\Integraciones\Traits\MercadoLibre;

use Reda\Integraciones\Http\Controllers\General\UsuarioController;
use Illuminate\Support\Facades\Log;
use DateTime;

trait MeliRequestsTrait
{
    public function obtenerOrigenPrefijoBase()
    {
        // Obtener el host, path y slugs
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $protocolo . "://" . $_SERVER['HTTP_HOST'];
        $path = strtok($_SERVER['REQUEST_URI'], '?');

        $slugs = array_values(array_filter(explode('/', $path), function($slug) {return $slug !== ''; }));

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
     * Función genérica mejorada para capturar errores de validación de Mercado Libre.
     */
    protected function enviarSolicitudMeli($punto_final, $metodo = 'GET', $datos = [], $requiere_autenticacion = false, $token_acceso = null, $es_oauth = false, $nombreFuncion = null, $idUsuario = null, $idPropiedad = null, $nombreTabla = null)
    {

        $parametrosRecibidos = [
            'punto_final' => $punto_final,
            'metodo' => $metodo,
            'datos' => $datos,
            'requiere_autenticacion' => $requiere_autenticacion,
            'token_acceso' => $token_acceso,
            'es_oauth' => $es_oauth,
            'nombreFuncion' => $nombreFuncion,
            'idUsuario' => $idUsuario,
            'idPropiedad' => $idPropiedad,
            'nombreTabla' => $nombreTabla];

        Log::info("enviarSolicitudMeli, parametrosRecibidos: " . print_r($parametrosRecibidos, true));
        
        
        $vectorAtributosDatosMeli = [ 'solicitud_'.$nombreFuncion => $datos];

        $respuestaActualizarDatosMeliUsuario = $this->actualizarDatosMeli($vectorAtributosDatosMeli, $idUsuario, $idPropiedad, $nombreTabla, 'envios_meli'); // Guardamos la solicitud en la columna 'envios_meli' para análisis posterior

        $url_base = 'https://api.mercadolibre.com/';
        $url_completa = $url_base . ltrim($punto_final, '/');

        $contentType = $es_oauth ? 'application/x-www-form-urlencoded' : 'application/json';
        $encabezados = [
            'Content-Type: ' . $contentType,
            'Accept: application/json'
        ];

        if ($requiere_autenticacion) {
            if (empty($token_acceso)) {
                return [
                    'success' => false,
                    'codigo_respuesta' => 1,
                    'codigo_http' => 401,
                    'mensaje_respuesta' => __('Token de acceso no proporcionado'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas' => ''
                ];
            }
            $encabezados[] = 'Authorization: Bearer ' . $token_acceso;
        }

        $manejador_curl = curl_init();
        curl_setopt($manejador_curl, CURLOPT_URL, $url_completa);
        curl_setopt($manejador_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($manejador_curl, CURLOPT_CUSTOMREQUEST, strtoupper($metodo));

        if (strtoupper($metodo) === 'GET' && !empty($datos)) {
            $url_completa .= '?' . http_build_query($datos);
            curl_setopt($manejador_curl, CURLOPT_URL, $url_completa);
        } elseif (!empty($datos)) {
            $postFields = $es_oauth ? http_build_query($datos) : json_encode($datos);
            curl_setopt($manejador_curl, CURLOPT_POSTFIELDS, $postFields);
        }

        curl_setopt($manejador_curl, CURLOPT_HTTPHEADER, $encabezados);

        $respuesta_raw = curl_exec($manejador_curl);
        $codigo_http   = curl_getinfo($manejador_curl, CURLINFO_HTTP_CODE);
        $error_curl    = curl_error($manejador_curl);
        curl_close($manejador_curl);

        $vectorAtributosDatosMeli = [ 'respuesta_'.$nombreFuncion =>
            [
                'respuesta_raw' => $respuesta_raw,
                'codigo_http' => $codigo_http,
                'error_curl' => $error_curl,
            ]
        ];

        $respuestaActualizarDatosMeliUsuario = $this->actualizarDatosMeli($vectorAtributosDatosMeli, $idUsuario, $idPropiedad, $nombreTabla, 'respuesta_meli'); // Guardamos la respuesta en la columna 'respuesta_meli' para análisis posterior

        $respuesta_decodificada = json_decode($respuesta_raw, true);
        $exito_http = ($respuesta_raw !== false && $codigo_http >= 200 && $codigo_http < 300);

        if ($exito_http) {
            return [
                'success' => true,
                'codigo_respuesta' => 0,
                'codigo_http' => $codigo_http,
                'mensaje_respuesta' => __('Proceso exitoso'),
                'respuesta' => $respuesta_decodificada ?: $respuesta_raw,
                'error_curl' => $error_curl,
                'causas' => ''

            ];
        }

        // --- LÓGICA DE EXTRACCIÓN DE CAUSAS (Validation Errors) ---
        $errores_detallados = [];
        $mensaje_principal = __('Mercado Libre retornó un error');

        if ($respuesta_raw === false) {
            $mensaje_principal = __('Error de conexión con Mercado Libre: ') . $error_curl;
        } elseif (is_array($respuesta_decodificada)) {
            // Mensaje de alto nivel (ej: "Validation error")
            if (isset($respuesta_decodificada['message'])) {
                $mensaje_principal = $respuesta_decodificada['message'];
            }

            // Extraemos las causas específicas del array 'cause'
            if (isset($respuesta_decodificada['cause']) && is_array($respuesta_decodificada['cause'])) {
                foreach ($respuesta_decodificada['cause'] as $causa) {
                    // Mercado Libre a veces envía el mensaje directo o dentro de un objeto
                    if (is_array($causa) && isset($causa['message'])) {
                        $errores_detallados[] = $causa['message'];
                    } elseif (is_string($causa)) {
                        $errores_detallados[] = $causa;
                    }
                }
            }
        }

        return [
            'success' => false,
            'codigo_respuesta' => 2,
            'codigo_http' => $codigo_http,
            'mensaje_respuesta' => $mensaje_principal,
            'respuesta' => $respuesta_decodificada ?: $respuesta_raw,
            'error_curl' => $error_curl,
            'causas' => $errores_detallados
        ];
    }
    public function fechaHoraActual()
    {
        setlocale(LC_TIME, 'es_UY', 'es_UY.UTF-8', 'es_UY.UTF-8');
        date_default_timezone_set('America/Montevideo');

        $fechaHoraActualFormato = date("Y-m-d H:i:s");

        $fechaHoraActualObjeto = new DateTime('now');

        return [
            // Corregimos el nombre de la variable aquí
            'fecha_hora_actual_formato' => $fechaHoraActualFormato,
            'fecha_hora_actual_objeto' => $fechaHoraActualObjeto
        ];
    }
    public function actualizarDatosMeli($vectorAtributosDatosMeli = null, $idUsuario = null, $idPropiedad = null, $nombreTabla = null, $columnaActualizar = null)
    {
        try {
            $datosFaltantes = [];

            if (empty($vectorAtributosDatosMeli)) {
                $datosFaltantes[] =  'vectorAtributosDatosMeli';
            }

            if (empty($idUsuario) && empty($idPropiedad)) {
                $datosFaltantes[] = __('idUsuario o idPropiedad (se requiere al menos uno)');
            }

            if (empty($nombreTabla)) {
                $datosFaltantes[] = 'nombreTabla';
            }

            if (empty($columnaActualizar)) {
                $datosFaltantes[] = 'columnaActualizar';
            }

            // Si el vector tiene elementos, retornamos la respuesta única con la lista
            if (!empty($datosFaltantes)) {
                return [
                    'success' => false,
                    'codigo_respuesta' => 2,
                    'codigo_http' => 422,
                    'mensaje_respuesta' => __('Error: Faltan los siguientes datos:'),
                    'respuesta' => '',
                    'error_curl' => '',
                    'causas' => $datosFaltantes
                ];
            }
            // 2. Determinar dinámicamente el Modelo según el tipo de usuario
            $modeloClase = null;
            $nombreColumnaId = null;

            switch ($nombreTabla) {
                case 'admins_melis':
                    $modeloClase = \Reda\Integraciones\Models\MercadoLibre\AdminMeli::class;
                    $nombreColumnaId = 'admin_id';
                    break;
                case 'users_melis':
                    $modeloClase = \Reda\Integraciones\Models\MercadoLibre\UserMeli::class;
                    $nombreColumnaId = 'user_id';
                    break;
                case 'agents_melis':
                    $modeloClase = \Reda\Integraciones\Models\MercadoLibre\AgentMeli::class;
                    $nombreColumnaId = 'user_agent_id';
                    break;
                case 'propiedades_melis':
                    $modeloClase = \Reda\Integraciones\Models\MercadoLibre\PropiedadMeli::class;
                    break;
                default:
                    return [
                        'success' => false,
                        'codigo_respuesta' => 3,
                        'codigo_http' => 400,
                        'mensaje_respuesta' => __('Error: Nombre de tabla no reconocida (') . $nombreTabla . ').',
                        'respuesta' => '',
                        'error_curl' => '',
                        'causas' => ''
                    ];
            }

            // 3. Buscamos el registro o preparamos uno nuevo usando la clase detectada
            // firstOrNew busca por user_id; si no lo halla, prepara el objeto

            if ($idPropiedad == null) {
                $registroMeli = $modeloClase::firstOrNew([$nombreColumnaId => $idUsuario]);
            } else {
                $registroMeli = $modeloClase::firstOrNew(['property_id' => $idPropiedad]);
            }

            // 4. Lógica de actualización del JSON (aprovechando el cast 'array' de los modelos)
            $datosActuales = $registroMeli->$columnaActualizar ?? [];

            // Fusionamos los atributos nuevos con los existentes
            $datosActualizados = array_merge($datosActuales, $vectorAtributosDatosMeli);

            // 5. Guardar cambios
            $registroMeli->$columnaActualizar = $datosActualizados;
            $registroMeli->save();

            return [
                'success' => true,
                'codigo_respuesta' => 0,
                'codigo_http' => 200,
                'mensaje_respuesta' => __('Atributos actualizados correctamente en ') . class_basename($modeloClase),
                'respuesta' => '',
                'error_curl' => '',
                'causas' => ''
            ];

        } catch (\Exception $e) {
            // Logueamos el error para depuración
            \Log::error("Error en actualizarDatosMeli: " . $e->getMessage());

            return [
                'success' => false,
                'codigo_respuesta' => 4,
                'codigo_http' => 500,
                'mensaje_respuesta' => _('Error interno al procesar la actualización: ') . $e->getMessage(),
                'respuesta' => '',
                'error_curl' => '',
                'causas' => ''
            ];
        }
    }
    public function usuarioTabla($tipoUsuario)
    {
        switch ($tipoUsuario) {
            case 'admin':
                return 'admins_melis';
            case 'estate_agency':
                return 'users_melis';
            case 'estate_agent':
                return 'agents_melis';
            default:
                return null;
        }
    }
}
