$(function () {
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
                            <ul class="nav">
                                <li>
                                    <a href="/admin/mercado-libre/importadores">
                                        <span class="sub-item">${window.RedaIntegraciones["Importador"] || "Importador"}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/admin/mercado-libre/configuraciones">
                                        <span class="sub-item">${window.RedaIntegraciones["Configuración"] || "Configuración"}</span>
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
                        <a data-toggle="collapse" href="#mlMenuAdmin" aria-expanded="false">
                            ${iconML}
                            <p>${window.RedaIntegraciones["Mercado Libre"] || "Mercado Libre"}</p>
                            <b class="caret"></b>
                        </a>
                        <div class="collapse" id="mlMenuAdmin">
                            <ul class="nav">
                                <li>
                                    <a href="/admin/mercado-libre/importadores">
                                        <span class="sub-item">${window.RedaIntegraciones["Importador"] || "Importador"}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/admin/mercado-libre/configuraciones">
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
                const basePath = 'https://dev2.ofiliaria.com';
                const href = `${basePath}/${firstSlug}/${secondSlug}/garantias`;

                // Inyectar el elemento en el menú
                const menuItemHtml = `
                    <li class="nav-item">
                        <a href="${href}">
                            ${iconML}
                            <p>${window.RedaIntegraciones["Mercado Libre"] || "Mercado Libre"}</p>
                        </a>
                    </li>`;
                propertyManagement.closest('li.nav-item').after(menuItemHtml);
                console.log("Menú de Mercado Libre inyectado correctamente.");
            }
        }
    }
    // Ejecutar al cargar
    injectMenu();
});
