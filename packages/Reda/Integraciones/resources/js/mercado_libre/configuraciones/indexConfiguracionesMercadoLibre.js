import { verificarUsuarioConectado } from "../generales/verificarUsuarioConectado";
import { verificarTokenMeli } from "../generales/verificarTokenMeli";
import { obtenerTokenMeli } from "../generales/obtenerTokenMeli";
import { listarCausasError } from "../generales/listarCausasError";

export const indexConfiguracionesMercadoLibre = () => {
    (function ($) {
        "use strict";
        const containerId = '#indexConfiguracionesMercadoLibre';
        if ($(containerId).length) {
            console.log('Script para "Index Configuraciones" cargado.');
            var client_id_meli = $(containerId).data('client-id-meli');
            const origin = window.location.origin;
            var url_meli = origin + '/user/mercado-libre/configuraciones'
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

            var usuarioAgenciaUnicamenteConfiguraMeli = `
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <strong>${window.RedaIntegraciones["El usuario con rol de agencia es el único que puede configurar la conexión con Mercado Libre"] || "El usuario con rol de agencia es el único que puede configurar la conexión con Mercado Libre."}</strong>
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

            var conexionMeliVerificadaExitosamente = `
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <strong>${window.RedaIntegraciones["Conexión con Mercado Libre verificada exitosamente"] || "Conexión con Mercado Libre verificada exitosamente"}</strong>
                </div>
            `;

            var configurarConexionMeli = `
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <strong>${window.RedaIntegraciones["No se encontró un token de Mercado Libre válido para el usuario conectado Por favor, configura la conexión con Mercado Libre."] || "No se encontró un token de Mercado Libre válido para el usuario conectado. Por favor, configura la conexión con Mercado Libre."}</strong>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="#" id="opcion_configurar_conexion_meli" class="btn btn-primary">
                        ${window.RedaIntegraciones["Configurar conexión con Mercado Libre"] || "Configurar conexión con Mercado Libre"}
                    </a>
                </div>
            `;

            var conexionMeliConfiguradaExitosamente = `
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <strong>${window.RedaIntegraciones["Conexión con Mercado Libre configurada exitosamente"] || "Conexión con Mercado Libre configurada exitosamente"}</strong>
                </div>
            `;

            async function solicitarTokenML() {
                $(containerId).html(verificandoUsuarioConectado);
                const respuestaVerificarUsuarioConectado = await verificarUsuarioConectado();
                if (respuestaVerificarUsuarioConectado.codigo_respuesta == 0) {
                    if (respuestaVerificarUsuarioConectado.tipo_agencia_agente != 'estate_agency') {
                        $(containerId).html(usuarioAgenciaUnicamenteConfiguraMeli);
                        return;
                    }
                    else {
                        // Mostramos un mensaje de "Procesando" mientras esperamos la respuesta
                        $(containerId).html(`
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <strong>${window.RedaIntegraciones["Procesando vinculación con Mercado Libre"] || "Procesando vinculación con Mercado Libre..."}</strong>
                            </div>
                            <div class="d-flex justify-content-center">${gifEspere}</div>
                        `);

                        const respuestaObtenerTokenMeli = await obtenerTokenMeli($("#codigo_temporal").val(), respuestaVerificarUsuarioConectado.id_usuario_conectado, respuestaVerificarUsuarioConectado.tipo_agencia_agente);

                        if (respuestaObtenerTokenMeli.success && respuestaObtenerTokenMeli.codigo_respuesta == 0) {
                            // --- CASO ÉXITO ---
                            $(containerId).html(conexionMeliConfiguradaExitosamente);
                        }
                        else {
                            // --- CASO ERROR (códigos 1, 2, 99) ---
                            let htmlCausas = listarCausasError(respuestaObtenerTokenMeli.causas);
                            let errorObteniendoTokenMeli = `
                                <div class="alert alert-danger" role="alert">
                                    <div class="mb-2">
                                        <strong>${window.RedaIntegraciones["No se pudo obtener el token de Mercado Libre"] || "No se pudo obtener el token de Mercado Libre"}</strong>
                                    </div>
                                    <div class="small">
                                        <span class="d-block">
                                            <strong>${window.RedaIntegraciones["Código de respuesta"] || "Código de respuesta"}:</strong> ${respuestaObtenerTokenMeli.codigo_respuesta}
                                        </span>
                                        <span class="d-block">
                                            <strong>${window.RedaIntegraciones["Mensaje de respuesta"] || "Mensaje de respuesta"}:</strong> ${respuestaObtenerTokenMeli.mensaje_respuesta}
                                        </span>
                                        ${htmlCausas}
                                    </div>
                                </div>
                            `;

                            $(containerId).html(errorObteniendoTokenMeli);
                        }

                    }
                }
                else {
                    const causasHtml = listarCausasError(respuestaVerificarUsuarioConectado.causas);
                    let errorVerificandoUsuario = `
                    <div class="alert alert-danger" role="alert">
                        <div class="mb-2">
                            <strong>${window.RedaIntegraciones["Error al verificar el usuario conectado"] || "Error al verificar el usuario conectado"}</strong>
                        </div>
                        <div class="small">
                            <span class="d-block">
                                <strong>${window.RedaIntegraciones["Código de respuesta"] || "Código de respuesta"}:</strong> ${respuestaVerificarUsuarioConectado.codigo_respuesta}
                            </span>
                            <span class="d-block">
                                <strong>${window.RedaIntegraciones["Mensaje de respuesta"] || "Mensaje de respuesta"}:</strong> ${respuestaVerificarUsuarioConectado.mensaje_respuesta}
                            </span>
                            ${causasHtml}
                        </div>
                    </div>
                `;
                    $(containerId).html(errorVerificandoUsuario);
                }
            }

            async function buscarTokenBaseDatos() {
                $(containerId).html(verificandoUsuarioConectado);
                const respuestaVerificarUsuarioConectado = await verificarUsuarioConectado();
                if (respuestaVerificarUsuarioConectado.codigo_respuesta == 0) {
                    if (respuestaVerificarUsuarioConectado.tipo_agencia_agente != 'estate_agency') {
                        $(containerId).html(usuarioAgenciaUnicamenteConfiguraMeli);
                        return;
                    }
                    else {
                        $(containerId).html(usuarioVerificadoCorrectamente);
                        let datos_usuario_conectado = {
                            id_usuario_administrador: respuestaVerificarUsuarioConectado.id_usuario_administrador,
                            id_usuario_agencia: respuestaVerificarUsuarioConectado.id_usuario_agencia,
                            id_usuario_agente: respuestaVerificarUsuarioConectado.id_usuario_agente,
                            id_usuario_conectado: respuestaVerificarUsuarioConectado.id_usuario_conectado,
                            rol_usuario_conectado: respuestaVerificarUsuarioConectado.rol_usuario_conectado,
                            tipo_agencia_agente: respuestaVerificarUsuarioConectado.tipo_agencia_agente
                        };
                        const respuestaVerificarToken = await verificarTokenMeli(datos_usuario_conectado);
                        let codigo_respuesta_verificar_token = respuestaVerificarToken.codigo_respuesta;
                        let mensaje_respuesta_verificar_token = respuestaVerificarToken.mensaje_respuesta;
                        token_meli = respuestaVerificarToken.token_meli;
                        refresh_token_meli = respuestaVerificarToken.refresh_token_meli;
                        if (codigo_respuesta_verificar_token == 0) {
                            $(containerId).html(conexionMeliVerificadaExitosamente);
                        }
                        else if (codigo_respuesta_verificar_token == 2) {
                            $(containerId).html(configurarConexionMeli);
                        }
                        else {
                            let htmlCausas = listarCausasError(respuestaVerificarToken.causas);
                            let errorVerificandoTokenMeli = `
                                <div class="alert alert-danger" role="alert">
                                    <div class="mb-2">
                                    <strong>${window.RedaIntegraciones["No se encontró un token de Mercado Libre válido para el usuario conectado"] || "No se encontró un token de Mercado Libre válido para el usuario conectado."}</strong>
                                    </div>
                                    <div class="small">
                                        <span class="d-block">
                                            <strong>${window.RedaIntegraciones["Código de respuesta"] || "Código de respuesta"}:</strong> ${respuestaVerificarToken.codigo_respuesta}
                                        </span>
                                        <span class="d-block">
                                            <strong>${window.RedaIntegraciones["Mensaje de respuesta"] || "Mensaje de respuesta"}:</strong> ${respuestaVerificarToken.mensaje_respuesta}
                                        </span>
                                        ${htmlCausas}
                                    </div>
                                </div>
                            `;
                            $(containerId).html(errorVerificandoTokenMeli);
                        }
                    }
                }
                else {
                    const causasHtml = listarCausasError(respuestaVerificarUsuarioConectado.causas);
                    let errorVerificandoUsuario = `
                    <div class="alert alert-danger" role="alert">
                        <div class="mb-2">
                            <strong>${window.RedaIntegraciones["Error al verificar el usuario conectado"] || "Error al verificar el usuario conectado"}</strong>
                        </div>
                        <div class="small">
                            <span class="d-block">
                                <strong>${window.RedaIntegraciones["Código de respuesta"] || "Código de respuesta"}:</strong> ${respuestaVerificarUsuarioConectado.codigo_respuesta}
                            </span>
                            <span class="d-block">
                                <strong>${window.RedaIntegraciones["Mensaje de respuesta"] || "Mensaje de respuesta"}:</strong> ${respuestaVerificarUsuarioConectado.mensaje_respuesta}
                            </span>
                            ${causasHtml}
                        </div>
                    </div>
                `;
                    $(containerId).html(errorVerificandoUsuario);
                }
            }

            $(function () {
                if ($("#codigo_temporal").val() != "error") {
                    console.log('indexMercadoLibre, ir a solicitarTokenML');
                    solicitarTokenML();
                }
                else {
                    console.log('indexMercadoLibre, ir a buscarTokenBaseDatos');
                    buscarTokenBaseDatos();
                }
                $(containerId).on('click', '#opcion_configurar_conexion_meli', function (event) {
                    event.preventDefault();
                    // Redirección a Mercado Libre
                    const authUrl = `https://auth.mercadolibre.com.uy/authorization?response_type=code&client_id=${client_id_meli}&redirect_uri=${url_meli}`;
                    window.location.href = authUrl;
                });
            });
        }
    })(jQuery);
}
indexConfiguracionesMercadoLibre();
