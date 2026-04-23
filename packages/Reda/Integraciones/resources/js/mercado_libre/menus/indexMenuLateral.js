export const indexMenuLateral = () =>
{
    (function( $ ) {
        "use strict";

        function injectMenu() {
            console.log("Iniciando inyección del menú de Mercado Libre...");
            // Obtener la ruta actual
            const path = window.location.pathname;

            // Obtener los slugs de la ruta
            const slugs = path.split('/').filter(slug => slug !== '');

            const iconML = '<i class="fas fa-store"></i>';

            // Verificar si el primer slug es "admin" y el segundo slug no es "agent"
            if (slugs[0] === 'admin' && slugs[1] !== 'agent') {
                const propertyCollapse = $('#propertySpecifications');
                if (propertyCollapse.length) {
                    // Inyectar el elemento en el menú
                    const menuItemHtml = `
                        <li class="nav-item">
                            <a data-toggle="collapse" href="#mlMenuAdmin" aria-expanded="false">
                                ${iconML}
                                <p>${window.RedaIntegraciones["Mercado Libre"] || "Mercado Libre"}</p>
                                <b class="caret"></b>
                            </a>
                            <div class="collapse" id="mlMenuAdmin">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="/admin/mercado-libre/importadores">
                                            <span class="sub-item">${window.RedaIntegraciones["Importador"] || "Importador"}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>`;
                    propertyCollapse.closest('li.nav-item').after(menuItemHtml);
                    console.log("Menú de Mercado Libre inyectado correctamente.");
                }
            }
            // Verificar si el primer slug es "user" y el segundo slug no es "agent"
            else if (slugs[0] === 'user' && slugs[1] !== 'agent') {
                const propertySpecification = $('#propertySpecification');
                if (propertySpecification.length) {
                    // Inyectar el elemento en el menú
                    const menuItemHtml = `
                        <li class="nav-item">
                            <a data-toggle="collapse" href="#mlMenuAgencia" aria-expanded="false">
                                ${iconML}
                                <p>${window.RedaIntegraciones["Mercado Libre"] || "Mercado Libre"}</p>
                                <b class="caret"></b>
                            </a>
                            <div class="collapse" id="mlMenuAgencia">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="/user/mercado-libre/configuraciones">
                                            <span class="sub-item">${window.RedaIntegraciones["Configuración"] || "Configuración"}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>`;

                    propertySpecification.closest('li.nav-item').after(menuItemHtml);
                    console.log("Menú de Mercado Libre inyectado correctamente.");
                }
            }
            // Verificar si el segundo slug es "agent"
            else if (slugs[1] === 'agent') {
                const propertyManagement = $('#propertyManagement');
                if (propertyManagement.length) {
                    // Obtener los dos primeros slugs de la ruta
                    const firstSlug = slugs[0];
                    const secondSlug = slugs[1];

                    // Formar el href completo con la ruta base
                    const basePath = 'https://dev2.carmetric.net';
                    const href = `${basePath}/${firstSlug}/${secondSlug}/mercado-libre/configuraciones`;

                    // Inyectar el elemento en el menú
                    const menuItemHtml = `
                        <li class="nav-item">
                            <a data-toggle="collapse" href="#mlMenuAgente" aria-expanded="false">
                                ${iconML}
                                <p>${window.RedaIntegraciones["Mercado Libre"] || "Mercado Libre"}</p>
                                <b class="caret"></b>
                            </a>
                            <div class="collapse" id="mlMenuAgente">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="${href}">
                                            <span class="sub-item">${window.RedaIntegraciones["Configuración"] || "Configuración"}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>`;

                    /* Se comentó porque el usuario no debe tener acceso a esta opción, pero se mantiene el código como referencia para añadir alguna opción en el futuro
                    propertyManagement.closest('li.nav-item').after(menuItemHtml);
                    console.log("Menú de Mercado Libre inyectado correctamente.");
                    */
                }
            }
        }

        $(function() {
            injectMenu();
        });
    })(jQuery);
}
indexMenuLateral();
