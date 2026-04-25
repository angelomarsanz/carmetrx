import { verificarTokenMeli } from "../generales/verificarTokenMeli";

export const indexConfiguracionesMercadoLibre = () =>
{
    (function( $ ) {
        "use strict";
        const containerId = '#indexConfiguracionesMercadoLibre';
        if ($(containerId).length) {
            console.log('Script para "Index Configuraciones" cargado.');
            var token_meli = "";
            var refresh_token_meli = ""; 
            const modalId = 'modalIndexConfiguraciones';

            const verificandoConexionActiva = `
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">${window.RedaIntegraciones["Verificando conexión"] || "Verificando conexión"}</h5>
                    <p class="card-text">${window.RedaIntegraciones["Verificando la conexión con Mercado Libre"] || "Verificando la conexión con Mercado Libre"}</p>
                  </div>
                </div>
            `;

            async function buscarTokenBaseDatos() {
                $(containerId).html(verificandoConexionActiva);
                const respuestaVerificarToken = await verificarTokenMeli();
                token_meli = respuestaVerificarToken.token_meli;
                refresh_token_meli = respuestaVerificarToken.refresh_token_meli;
                let usuario_duenio_token = respuestaVerificarToken.usuario_duenio_token;
                let codigo_respuesta = respuestaVerificarToken.codigo_respuesta;
                let mensaje_respuesta = respuestaVerificarToken.mensaje_respuesta;
                console.log('indexMercadoLibre, buscarTokenBaseDatos, token_meli', token_meli);
                console.log('indexMercadoLibre, buscarTokenBaseDatos, refresh_token_meli', refresh_token_meli);
            }  

            $(function() {
              if ($("#codigo_temporal").val() != "error")
              {
                  console.log('indexMercadoLibre, ir a solicitarTokenML');
                  solicitarTokenML();
              }
              else
              {
                  console.log('indexMercadoLibre, ir a buscarTokenBaseDatos');
                  buscarTokenBaseDatos();
              }  
            });
        }
    })(jQuery);
}
indexConfiguracionesMercadoLibre();