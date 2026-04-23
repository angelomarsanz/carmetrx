// packages/Reda/Integraciones/resources/js/vistas/mercado_libre/configuraciones/indexConfiguracionesMercadoLibre.js

$(function() {
    // 1. Verificar si el div específico de esta vista existe en la página.
    const containerId = '#indexConfiguracionesMercadoLibre';
    if ($(containerId).length) {

        // --- Aquí va todo el código de la vista de Index Configuraciones ---

        console.log('Script para "Index Configuraciones" cargado.');

        // 2. Crear el botón y el modal
        const modalId = 'modalIndexConfiguraciones';

        // HTML del Modal
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">${window.RedaIntegraciones["Configuración conexión con Mercado Libre"] || "Configuración conexión con Mercado Libre"}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    ${window.RedaIntegraciones["Configuración conexión con Mercado Libre"] || "Configuración conexión con Mercado Libre"}
                  </div>
                </div>
              </div>
            </div>
        `;

        // 3. Crear contenido inicial dinámico con el botón modificado
        const initialContentHtml = `
            <div class="card">
              <div class="card-header">
                ${window.RedaIntegraciones["Configuración conexión con Mercado Libre"] || "Configuración conexión con Mercado Libre"}
              </div>
              <div class="card-body">
                <h5 class="card-title">${window.RedaIntegraciones["Contenido dinámico"] || "Contenido dinámico"}</h5>
                <p class="card-text">${window.RedaIntegraciones["Este contenido ha sido insertado dinámicamente con JavaScript"] || "Este contenido ha sido insertado dinámicamente con JavaScript."}</p>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#${modalId}">
                  ${window.RedaIntegraciones["Configuración conexión con Mercado Libre"] || "Configuración conexión con Mercado Libre"}
                </button>
              </div>
            </div>
        `;

        // 4. Añadir el contenido al contenedor y el modal al body.
        $(containerId).html(initialContentHtml);
        $('body').append(modalHtml);
        alert(window.RedaIntegraciones["Contenido dinámico para Index Configuraciones ha sido cargado y el modal está listo"] || "Contenido dinámico para Index Configuraciones ha sido cargado y el modal está listo.");
    }
});
