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
                'prefijo': prefijo 
            },
            success: function(data) 
            {
                let codigo_respuesta = data.codigo_respuesta;
                if ( codigo_respuesta == 0) 
                {
                    let respuesta = {
                        codigo_respuesta : codigo_respuesta,
                        mensaje_respuesta : data.mensaje_respuesta,
                        id_usuario_administrador_conectado : data.id_usuario_administrador_conectado,
                        id_usuario_agencia_conectado : data.id_usuario_agencia_conectado,
                        id_usuario_agente_conectado : data.id_usuario_agente_conectado,
                        rol_usuario_conectado : data.rol_usuario_conectado,
                        tipo_agencia_agente : data.tipo_agencia_agente
                    }
                    resolve(respuesta);
                } 
                else  
                {
                    let respuesta = {
                        codigo_respuesta : codigo_respuesta,
                        mensaje_respuesta : data.mensaje_respuesta,
                        id_usuario_administrador_conectado : 0,
                        id_usuario_agencia_conectado : 0,
                        id_usuario_agente_conectado : 0,
                        rol_usuario_conectado : '',
                        tipo_agencia_agente : ''
                    }
                    resolve(respuesta);
                }
            },
            error: function (x, xs, xt) 
            {
                let respuesta = {
                    codigo_respuesta : 5,
                    mensaje_respuesta : 'Error en el servidor de Carmetric: ' + xt,
                    id_usuario_administrador_conectado : 0,
                    id_usuario_agencia_conectado : 0,
                    id_usuario_agente_conectado : 0,
                    rol_usuario_conectado : '',
                    tipo_agencia_agente : ''
                }
                console.log('error', JSON.stringify(x));
                resolve(respuesta);
            }
        });
        })(jQuery);
    });
  }