import { obtenerOrigenPrefijoBase } from "./obtenerOrigenPrefijoBase.js";

export const verificarTokenMeli = (datos_usuario_conectado) => {
    const origenPrefijoBase = obtenerOrigenPrefijoBase();
    const origin = origenPrefijoBase.origin;
    const prefijo = origenPrefijoBase.prefijo;
    const url = `${origin}${prefijo}/mercado-libre/configuraciones/verificar-token-meli`;
    console.log('url', url);
    console.log('datos_usuario_conectado', datos_usuario_conectado);
    return new Promise((resolve) => {
        (function( $ ) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            data: {
                'datos_usuario_conectado': datos_usuario_conectado
            },
            success: function(data)
            {
                let respuesta = {
                    'codigo_respuesta' : data.codigo_respuesta,
                    'mensaje_respuesta' : window.RedaIntegraciones[data.mensaje_respuesta] || data.mensaje_respuesta,
                    'token_meli' : data.token_meli,
                    'refresh_token_meli' : data.refresh_token_meli,
                }
                resolve(respuesta);
            },
            error: function (x, xs, xt) {
                let responseTextObjeto = JSON.parse(x.responseText);
                console.log('mensaje de error', responseTextObjeto);

                // 1. Intentamos obtener el JSON que el servidor envió junto con el error 400
                let respuestaServidor = {};
                try {
                    // x.responseText contiene el cuerpo del JSON enviado por Laravel
                    respuestaServidor = responseTextObjeto;
                } catch (e) {
                    respuestaServidor = {};
                }

                const mensajeErrorBase = window.RedaIntegraciones["Error en el servidor de Carmetric"] || "Error en el servidor de Carmetric";
                const detalleError = responseTextObjeto.message ? `<br />${responseTextObjeto.message}` : '';


                // 2. Construimos la respuesta usando los datos reales del servidor si existen
                let respuesta = {
                    'success': false,
                    'codigo_respuesta': respuestaServidor.codigo_respuesta || 99, // 99 si no hay código interno
                    'codigo_http': x.status !== 0 ? x.status : 504,
                    'mensaje_respuesta': respuestaServidor.mensaje_respuesta ? (window.RedaIntegraciones[respuestaServidor.mensaje_respuesta] || respuestaServidor.mensaje_respuesta) : `${mensajeErrorBase}.${detalleError}`, 
                    'respuesta': respuestaServidor.respuesta || '',
                    'error_curl' : respuestaServidor.error_curl || '',
                    'causas': respuestaServidor.causas || (responseTextObjeto.trace ? [`${responseTextObjeto.file}, linea ${responseTextObjeto.line}`, `${responseTextObjeto.trace[0].file}, linea ${responseTextObjeto.trace[0].line}`] : [])
                };
                resolve(respuesta);
            }

        });
        })(jQuery);
    });
  }
