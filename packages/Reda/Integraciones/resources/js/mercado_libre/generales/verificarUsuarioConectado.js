import { obtenerOrigenPrefijoBase } from "./obtenerOrigenPrefijoBase.js";

export const verificarUsuarioConectado = () => {
    const origenPrefijoBase = obtenerOrigenPrefijoBase();
    const origin = origenPrefijoBase.origin;
    const prefijo = origenPrefijoBase.prefijo;
    console.log('verificarUsuarioConectado, origin: ' + origin);
    console.log('verificarUsuarioConectado, prefijo: ' + prefijo);
    const url = `${origin}${prefijo}/general/usuario/verificar-usuario-conectado`;
    return new Promise((resolve) => {
        (function( $ ) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            data: {
                'origin': origin,
                'prefijo': prefijo
            },
            success: function(data)
            {
                resolve(data);
            },
            error: function (x, xs, xt) {
                // 1. Intentamos obtener el JSON que el servidor envió junto con el error 400
                let respuestaServidor = {};
                try {
                    // x.responseText contiene el cuerpo del JSON enviado por Laravel
                    respuestaServidor = JSON.parse(x.responseText);
                } catch (e) {
                    respuestaServidor = {};
                }
                console.log('respuestaServidor', respuestaServidor);

                const mensajeErrorBase = window.RedaIntegraciones["Error en el servidor de Carmetric"] || "Error en el servidor de Carmetric";
                const detalleError = respuestaServidor.message ? `<br />${respuestaServidor.message}` : '';

                // 2. Construimos la respuesta usando los datos reales del servidor si existen
                let respuesta = {
                    'success': false,
                    'message': respuestaServidor.message || 'Error en el servidor',
                    'mensaje_usuario': respuestaServidor.mensaje_usuario ? respuestaServidor.mensaje_usuario : `${mensajeErrorBase}.${detalleError}`, 
                    'respuesta': respuestaServidor.respuesta || '',
                    'error_curl' : respuestaServidor.error_curl || '',
                    'causas': respuestaServidor.causas || (respuestaServidor.trace ? [`${respuestaServidor.file}, linea ${respuestaServidor.line}`, `${respuestaServidor.trace[0].file}, linea ${respuestaServidor.trace[0].line}`] : []),
                    'code': x.status !== 0 ? x.status : 504
                };

                resolve(respuesta);
            }
        });
        })(jQuery);
    });
  }
