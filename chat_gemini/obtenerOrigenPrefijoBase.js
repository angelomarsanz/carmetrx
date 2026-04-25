export const obtenerOrigenPrefijoBase = () => {
    (function( $ ) {
        const path = window.location.pathname;
        const slugs = path.split('/').filter(slug => slug !== '');
        const origin = window.location.origin;

        let prefijoBase = '';

        // Caso 1: Administrador (/admin/...)
        if (slugs[0] === 'admin' && slugs[1] !== 'agent') {
            prefijoBase = '/admin';
        }
        // Caso 2: Agencia/User (/user/...)
        else if (slugs[0] === 'user' && slugs[1] !== 'agent') {
            prefijoBase = '/user';
        }
        // Caso 3: Agente (/{username}/agent/...)
        else if (slugs[1] === 'agent') {
            prefijoBase = `/${slugs[0]}/${slugs[1]}`;
        }

        return {
            origin: origin,
            prefijo: prefijoBase
        };
    })(jQuery);
};