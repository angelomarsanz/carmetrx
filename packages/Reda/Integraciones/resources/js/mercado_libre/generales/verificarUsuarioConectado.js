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
                    codigo_respuesta : data.codigo_respuesta,
                    mensaje_respuesta : window.RedaIntegraciones[data.mensaje_respuesta] || data.mensaje_respuesta,
                    id_usuario_administrador : data.id_usuario_administrador,
                    id_usuario_agencia : data.id_usuario_agencia,
                    id_usuario_agente : data.id_usuario_agente,
                    id_usuario_conectado : data.id_usuario_conectado,
                    rol_usuario_conectado : data.rol_usuario_conectado,
                    tipo_agencia_agente : data.tipo_agencia_agente
                }
                resolve(respuesta);
            },
            error: function (x, xs, xt)
            {
                console.log('error', JSON.stringify(x));
                const mensajeErrorBase = window.RedaIntegraciones["Error en el servidor de Carmetric"] || "Error en el servidor de Carmetric";
                let respuesta = {
                    codigo_respuesta : 99,
                    mensaje_respuesta : `${mensajeErrorBase}. ${xt}`,
                    id_usuario_administrador : 0,
                    id_usuario_agencia : 0,
                    id_usuario_agente : 0,
                    id_usuario_conectado : 0,
                    rol_usuario_conectado : '',
                    tipo_agencia_agente : ''
                }
                resolve(respuesta);
            }
        });
        })(jQuery);
    });
  }
