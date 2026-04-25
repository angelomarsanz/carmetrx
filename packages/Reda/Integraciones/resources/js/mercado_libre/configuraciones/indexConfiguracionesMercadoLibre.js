import { verificarUsuarioConectado } from "../generales/verificarUsuarioConectado";
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

            var gifEspere =
                `<img src='https://dev-backend.ofiliaria.com/public/imagenes/loading.gif' 
                alt='Por favor espere' style="width: 90px; height: 90px" />`;

            var verificandoUsuarioConectado = `
                <div class="alert alert-info d-flex align-items-center" role="alert">   
                    <strong>${window.RedaIntegraciones["Verificando usuario conectado"] || "Verificando usuario conectado"}</strong>
                </div>
                <div class="d-flex justify-content-center">
                    ${gifEspere}
                </div>
            `;

            var usuarioAdministradorNoConfiguraMeli = `
                <div class="alert alert-warning d-flex align-items-center" role="alert">   
                    <strong>${window.RedaIntegraciones["El usuario con perfil de Administrador no puede configurar conexión con Mercado Libre"] || "El usuario con perfil de Administrador no puede configurar conexión con Mercado Libre."}</strong>
                </div>
            `;

            var usuarioVerificadoCorrectamente = `
                <div class="alert alert-success d-flex align-items-center" role="alert">   
                    <strong>${window.RedaIntegraciones["Usuario verificado correctamente"] || "Usuario verificado correctamente"}</strong>
                </div>
                <div class="alert alert-info d-flex align-items-center" role="alert">   
                    <strong>${window.RedaIntegraciones["Verificando token de Mercado Libre"] || "Verificando token de Mercado Libre"}</strong>
                </div>
                <div class="d-flex justify-content-center">
                    ${gifEspere}
                </div>
            `;
            
            var errorVerificandoUsuario = `
                <div class="alert alert-danger d-flex align-items-center" role="alert">   
                    <strong>${window.RedaIntegraciones["Error al verificar el usuario conectado"] || "Error al verificar el usuario conectado"}</strong>
                </div>
            `;

            var conexionMeliVerificadaExitosamente = `
                <div class="alert alert-success d-flex align-items-center" role="alert">   
                    <strong>${window.RedaIntegraciones["Conexión con Mercado Libre verificada exitosamente"] || "Conexión con Mercado Libre verificada exitosamente"}</strong>
                </div>
            `;
            
            var configurarConexionMeli = `
                <div class="alert alert-warning d-flex align-items-center" role="alert">   
                    <strong>${window.RedaIntegraciones["No se encontró un token de Mercado Libre válido para el usuario conectado. Por favor, configura la conexión con Mercado Libre."] || "No se encontró un token de Mercado Libre válido para el usuario conectado. Por favor, configura la conexión con Mercado Libre."}</strong>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="/mercado-libre/configuraciones/solicitar-token" class="btn btn-primary">
                        ${window.RedaIntegraciones["Configurar conexión con Mercado Libre"] || "Configurar conexión con Mercado Libre"}
                    </a>
                </div>
            `;

            async function buscarTokenBaseDatos() {
                $(containerId).html(verificandoUsuarioConectado);
                const respuestaVerificarUsuarioConectado = await verificarUsuarioConectado();
                let codigo_respuesta_verificar_usuario = respuestaVerificarUsuarioConectado.codigo_respuesta;
                let mensaje_respuesta_verificar_usuario = respuestaVerificarUsuarioConectado.mensaje;
                if (codigo_respuesta_verificar_usuario == 0)
                {
                  let rol_usuario_conectado = respuestaVerificarUsuarioConectado.rol_usuario_conectado;
                  if (rol_usuario_conectado == 'admin')
                  {
                    $(containerId).html(usuarioAdministradorNoConfiguraMeli);
                    return;
                  }
                  else
                  {
                    $(containerId).html(usuarioVerificadoCorrectamente);
                    let datos_usuario_conectado = {
                      id_usuario_administrador_conectado : respuestaVerificarUsuarioConectado.id_usuario_administrador_conectado,
                      id_usuario_agencia_conectado : respuestaVerificarUsuarioConectado.id_usuario_agencia_conectado,
                      id_usuario_agente_conectado : respuestaVerificarUsuarioConectado.id_usuario_agente_conectado,
                      rol_usuario_conectado : respuestaVerificarUsuarioConectado.rol_usuario_conectado,
                      tipo_agencia_agente : respuestaVerificarUsuarioConectado.tipo_agencia_agente
                    };
                    const respuestaVerificarToken = await verificarTokenMeli(datos_usuario_conectado);
                    let codigo_respuesta = respuestaVerificarToken.codigo_respuesta;
                    let mensaje_respuesta = respuestaVerificarToken.mensaje_respuesta;
                    token_meli = respuestaVerificarToken.token_meli;
                    refresh_token_meli = respuestaVerificarToken.refresh_token_meli;
                    if (codigo_respuesta == 0)
                    {
                      $(containerId).html(conexionMeliVerificadaExitosamente);
                      console.log('Token de Mercado Libre: ' + token_meli);
                      console.log('Refresh Token de Mercado Libre: ' + refresh_token_meli);
                    }
                    else  
                    {
                      $(containerId).html(`
                        <div class="alert alert-warning" role="alert">
                            ${mensaje_respuesta}
                        </div>
                      `);
                    }
                  }
                }
                else
                {                  
                  $(containerId).html(errorVerificandoUsuario);
                }
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