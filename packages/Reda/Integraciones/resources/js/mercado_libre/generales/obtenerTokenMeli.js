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
                resolve(data);
            },
            error: function (x, xs, xt)
            {
                console.log('error', JSON.stringify(x));

                // Si x.status es 0, significa que no hubo respuesta del servidor (error de red)
                const statusCode = x.status !== 0 ? x.status : 504;
                const mensajeErrorBase = window.RedaIntegraciones["Error en el servidor de Carmetric"] || "Error en el servidor de Carmetric";
                let respuesta = {
                    'success' : false,
                    'codigo_respuesta' : 99,
                    'codigo_http' : statusCode,
                    'mensaje_respuesta' : `${mensajeErrorBase}. ${xt}`,,
                    'respuesta' : '',
                    'error_curl' : '',
                    'causas' : ''
                    }
                resolve(respuesta);
            }
        });
        })(jQuery);
    });
  }
