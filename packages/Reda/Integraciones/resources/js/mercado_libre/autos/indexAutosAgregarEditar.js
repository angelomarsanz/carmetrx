import { verificarUsuarioConectado } from "../generales/verificarUsuarioConectado";
import { verificarTokenMeli } from "../generales/verificarTokenMeli";
import { obtenerTokenMeli } from "../generales/obtenerTokenMeli";
import { listarCausasError } from "../generales/listarCausasError";

export const indexAutosAgregarEditar = () => {
    (function ($) {
        "use strict";
        const containerId = '#propertyForm';
        if ($(containerId).length) {
            console.log('Script para "IndexAutosAgregarEditar" cargado.');

            var gifEspere =
                `<img src='https://dev-backend.ofiliaria.com/public/imagenes/loading.gif'
                alt='Por favor espere' style="width: 90px; height: 90px" />`;

            $(function () {
                $(document).ajaxSuccess(function(event, xhr, settings) {
                    // 1. Verificamos la URL de forma más precisa
                    // Esto asegura que solo actúe sobre la ruta de obtención de modelos
                    if (settings.url.match(/\/get-models(\?|$)/)) {
                        
                        try {
                            // 2. Intentamos obtener los datos ya parseados por jQuery o los parseamos manualmente
                            let data = xhr.responseJSON || JSON.parse(xhr.responseText);
                            
                            // 3. Verificamos si la propiedad 'models' existe y está vacía
                            if (data && data.models && data.models.length === 0) {
                                
                                // 4. Lanzamos la notificación
                                // Agregué 'allow_dismiss: true' para que el usuario pueda cerrarla
                                $.notify({
                                    icon: 'fa fa-exclamation-triangle',
                                    title: '<strong>' + 'Atención' + '</strong>',
                                    message: window.RedaIntegraciones["No se encontraron modelos para esta marca"] || "No se encontraron modelos para esta marca.",
                                }, {
                                    type: 'warning',
                                    placement: { from: 'top', align: 'right' },
                                    time: 1000,
                                    delay: 5000,
                                    allow_dismiss: true,
                                    z_index: 2000 // Asegura que esté por encima de modales si los hay
                                });
                            }
                        } catch (e) {
                            // No hacemos log para no ensuciar la consola del usuario final
                        }
                    }
                    if (settings.url.match(/\/get-versions(\?|$)/)) {
                        try {
                            let data = xhr.responseJSON || JSON.parse(xhr.responseText);
                            if (data && data.versions && data.versions.length === 0) {
                                $.notify({
                                    icon: 'fa fa-exclamation-triangle',
                                    title: '<strong>Atención</strong>',
                                    message: window.RedaIntegraciones["No se encontraron versiones para este modelo"] || "No se encontraron versiones para este modelo.",
                                }, {
                                    type: 'warning',
                                    placement: { from: 'top', align: 'right' },
                                    delay: 5000,
                                    z_index: 2000
                                });
                            }
                        } catch (e) {}
                    }
                    if (settings.url.match(/\/get-states-cities(\?|$)/)) {
                        try {
                            let data = xhr.responseJSON || JSON.parse(xhr.responseText);
                            if (data && data.versions && data.versions.length === 0) {
                                $.notify({
                                    icon: 'fa fa-exclamation-triangle',
                                    title: '<strong>Atención</strong>',
                                    message: window.RedaIntegraciones["No se encontraron ciudades para este estado"] || "No se encontraron ciudades para este estado.",
                                }, {
                                    type: 'warning',
                                    placement: { from: 'top', align: 'right' },
                                    delay: 5000,
                                    z_index: 2000
                                });
                            }
                        } catch (e) {}
                    }
                    if (settings.url.match(/\/get-cities(\?|$)/)) {
                        try {
                            let data = xhr.responseJSON || JSON.parse(xhr.responseText);
                            if (data && data.versions && data.versions.length === 0) {
                                $.notify({
                                    icon: 'fa fa-exclamation-triangle',
                                    title: '<strong>Atención</strong>',
                                    message: window.RedaIntegraciones["No se encontraron zonas para esta ciudad"] || "No se encontraron zonas para esta ciudad.",
                                }, {
                                    type: 'warning',
                                    placement: { from: 'top', align: 'right' },
                                    delay: 5000,
                                    z_index: 2000
                                });
                            }
                        } catch (e) {}
                    }
                });
            });
        }
    })(jQuery);
}
indexAutosAgregarEditar();
