$(function () {
    function injectMenu() {
        console.log("Iniciando inyección del menú de Mercado Libre...");
        // Obtener la ruta actual
        const path = window.location.pathname;

        // Obtener los slugs de la ruta
        const slugs = path.split('/').filter(slug => slug !== '');

        // Verificar si el primer slug es "admin" y el segundo slug no es "agent"
        if (slugs[0] === 'admin' && slugs[1] !== 'agent') {
            const propertyCollapse = $('#propertySpecifications');
            if (propertyCollapse.length) {
                // Inyectar el elemento en el menú
                const menuItemHtml = `
                    <li class="nav-item">
                        <a href="/admin/garantias">
                            <i class="fas fa-shield-alt"></i>
                            <p>Garantías</p>
                        </a>
                    </li>`;
                propertyCollapse.closest('li.nav-item').after(menuItemHtml);
                console.log("Menú de Garantías inyectado correctamente.");
            }
        }
        // Verificar si el primer slug es "user" y el segundo slug no es "agent"
        else if (slugs[0] === 'user' && slugs[1] !== 'agent') {
            const propertySpecification = $('#propertySpecification');
            if (propertySpecification.length) {
                // Inyectar el elemento en el menú
                const menuItemHtml = `
                    <li class="nav-item">
                        <a href="/user/garantias">
                            <i class="fas fa-shield-alt"></i>
                            <p>Garantías</p>
                        </a>
                    </li>`;
                propertySpecification.closest('li.nav-item').after(menuItemHtml);
                console.log("Menú de Garantías inyectado correctamente.");
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
                            <i class="fas fa-shield-alt"></i>
                            <p>Garantías</p>
                        </a>
                    </li>`;
                propertyManagement.closest('li.nav-item').after(menuItemHtml);
                console.log("Menú de Garantías inyectado correctamente.");
            }
        }
    }
    // Ejecutar al cargar
    injectMenu();    
});