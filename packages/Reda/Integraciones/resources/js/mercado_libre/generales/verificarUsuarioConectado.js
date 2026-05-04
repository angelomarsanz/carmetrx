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
                let respuesta = {
                    'codigo_respuesta' : data.codigo_respuesta,
                    'mensaje_respuesta' : window.RedaIntegraciones[data.mensaje_respuesta] || data.mensaje_respuesta,
                    'id_usuario_administrador' : data.id_usuario_administrador,
                    'id_usuario_agencia' : data.id_usuario_agencia,
                    'id_usuario_agente' : data.id_usuario_agente,
                    'id_usuario_conectado' : data.id_usuario_conectado,
                    'rol_usuario_conectado' : data.rol_usuario_conectado,
                    'tipo_agencia_agente' : data.tipo_agencia_agente,
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
