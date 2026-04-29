import { obtenerOrigenPrefijoBase } from "./obtenerOrigenPrefijoBase.js";

export const obtenerTokenMeli = (codigo_temporal) => {
    const origenPrefijoBase = obtenerOrigenPrefijoBase();
    const origin = origenPrefijoBase.origin;
    const prefijo = origenPrefijoBase.prefijo;
    const url = `${origin}${prefijo}/mercado-libre/configuraciones/obtener-token-meli`;
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
                'codigo_temporal': codigo_temporal
            },
            success: function(data)
            {
                let respuesta = {
                    codigo_respuesta : data.codigo_respuesta,
                    mensaje_respuesta : window.RedaIntegraciones[data.mensaje_respuesta] || data.mensaje_respuesta,
                    token_meli : data.token_meli,
                    refresh_token_meli : data.refresh_token_meli,
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
                    token_meli : '',
                    refresh_token_meli : ''
                }
                resolve(respuesta);
            }
        });
        })(jQuery);
    });
  }
