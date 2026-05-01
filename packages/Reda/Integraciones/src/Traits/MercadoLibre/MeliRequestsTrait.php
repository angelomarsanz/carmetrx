<?php

namespace Reda\Integraciones\Traits\MercadoLibre;

use Illuminate\Support\Facades\Log;

trait MeliRequestsTrait
{
    /**
     * Función genérica mejorada para capturar errores de validación de Mercado Libre.
     */
    protected function enviarSolicitudMeli($punto_final, $metodo = 'GET', $datos = [], $requiere_autenticacion = false, $token_acceso = null, $es_oauth = false)
    {
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
}
