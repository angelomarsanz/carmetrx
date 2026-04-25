import { obtenerOrigenPrefijoBase } from "./obtenerOrigenPrefijoBase.js";

export const verificarTokenMeli = (datos_usuario_conectado) => {
    const origenPrefijoBase = obtenerOrigenPrefijoBase();
    const origin = origenPrefijoBase.origin;
    const prefijo = origenPrefijoBase.prefijo;
    const url = `${origin}${prefijo}/mercado-libre/configuraciones/verificar-token-meli`;
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
                let codigo_retorno = data.codigo_respuesta;
                if ( codigo_retorno == 0) 
                {
                    let respuesta = {
                        codigo_respuesta : codigo_retorno,
                        mensaje_respuesta : 'El usuario tiene token',
                        token_meli : data.token_meli,
                        refresh_token_meli : data.refresh_token_meli,
                    }
                    resolve(respuesta);
                } 
                else  
                {
                    let respuesta = {
                        codigo_respuesta : codigo_retorno,
                        mensaje_respuesta : 'Hubo un error',
                        token_meli : 'Hubo un error',
                        refresh_token_meli : 'Hubo un error'
                    }
                    resolve(respuesta);
                }
            },
            error: function (x, xs, xt) 
            {
                let respuesta = {
                    codigo_respuesta : 5,
                    mensaje_respuesta : 'Error en el servidor de Ofiliaria: ' + xt,
                    token_meli : 'Error en el servidor de Ofiliaria: ' + xt,
                    refresh_token_meli : 'Error en el servidor de Ofiliaria: ' + xt
                }
                console.log('error', JSON.stringify(x));
                resolve(respuesta);
            }
        });
        })(jQuery);
    });
  }