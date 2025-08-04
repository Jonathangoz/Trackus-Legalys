document.addEventListener('DOMContentLoaded', function() {
    // Obtenemos el contenedor principal del componente "Hover Menu"
    const mobileHoverMenuContainer = document.getElementById('mobile-hover-menu');

    // Solo ejecutamos este script si el contenedor existe en el DOM
    if (mobileHoverMenuContainer) {
        // Seleccionamos todos los divs con la clase 'group' dentro de nuestro componente
        const navGroups = mobileHoverMenuContainer.querySelectorAll('.group');
        let currentlyOpenLabel = null; // Esta variable nos ayudará a saber qué etiqueta está abierta

        // Función para cerrar todas las etiquetas de texto
        function closeAllLabels() {
            navGroups.forEach(group => {
                const label = group.querySelector('span'); // Busca la etiqueta (span) dentro de cada grupo
                if (label && label.classList.contains('scale-100')) {
                    label.classList.remove('scale-100');
                    label.classList.add('scale-0');
                }
            });
            currentlyOpenLabel = null; // Reinicia la referencia de la etiqueta abierta
        }

        // Añadimos un 'click' listener a cada 'group' (cada icono)
        navGroups.forEach(group => {
            const label = group.querySelector('span'); // La etiqueta de texto asociada a este icono

            if (label) { // Nos aseguramos de que haya una etiqueta
                group.addEventListener('click', function(event) {
                    // Solo activamos este comportamiento si la pantalla es <= 480px (móvil)
                    if (window.innerWidth <= 480) {
                        // Si se hace clic en la etiqueta que ya está abierta, la cerramos
                        if (label === currentlyOpenLabel) {
                            label.classList.remove('scale-100');
                            label.classList.add('scale-0');
                            currentlyOpenLabel = null;
                        } else {
                            // Si se hace clic en una nueva etiqueta, cerramos todas las demás primero
                            closeAllLabels();

                            // Luego, abrimos la etiqueta actual
                            label.classList.remove('scale-0');
                            label.classList.add('scale-100');
                            currentlyOpenLabel = label; // Establecemos esta como la etiqueta abierta
                        }
                    }
                });
            }
        });

        // Opcional: Cerrar cualquier etiqueta abierta si el usuario hace clic fuera del menú
        document.addEventListener('click', function(event) {
            // Solo si estamos en vista móvil y hay una etiqueta abierta
            if (window.innerWidth <= 480 && currentlyOpenLabel) {
                // Si el clic no fue dentro de nuestro componente "Hover Menu"
                if (!mobileHoverMenuContainer.contains(event.target)) {
                    closeAllLabels();
                }
            }
        });

        // Opcional: Si el usuario redimensiona la pantalla de móvil a escritorio,
        // cerramos cualquier etiqueta que pudiera haber quedado abierta
        window.addEventListener('resize', function() {
            if (window.innerWidth > 480 && currentlyOpenLabel) {
                closeAllLabels();
            }
        });
    }
});