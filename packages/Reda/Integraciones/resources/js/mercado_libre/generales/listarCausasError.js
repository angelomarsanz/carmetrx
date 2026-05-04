export const listarCausasError = (causas) => {
    return (function( $ ) {
        if (!Array.isArray(causas) || causas.length === 0) return '';

        const etiquetaCausas = window.RedaIntegraciones["Causas"] || "Causas";
        
        // Iteramos sobre el vector para construir la lista <li>
        const itemsLista = causas.map(causa => `<li>${causa}</li>`).join('');

        return `
            <div class="mt-2">
                <strong>${etiquetaCausas}:</strong>
                <ul class="mb-0">
                    ${itemsLista}
                </ul>
            </div>
        `;
    })(jQuery);
};