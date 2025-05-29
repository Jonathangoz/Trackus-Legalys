// script.js
 document.addEventListener('DOMContentLoaded', () => {
            console.log('Carga exitosamente!');

            // Sidebar toggle functionality
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');
            const toggleIcon = document.getElementById('toggleIcon');

            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');

                // Selecciona el elemento <i> dentro del botón
                let toggleIcon = toggleBtn.querySelector('i');

                // Cambia las clases del icono en lugar de su textContent
                if (sidebar.classList.contains('collapsed')) {
                    // Si el sidebar está colapsado (cerrado), muestra la flecha para abrir
                    toggleIcon.classList.remove('fa-chevron-left');
                    toggleIcon.classList.add('fa-chevron-right');
                } else {
                    // Si el sidebar está abierto, muestra la flecha para cerrar
                    toggleIcon.classList.remove('fa-chevron-right');
                    toggleIcon.classList.add('fa-chevron-left');
                }
            });

            // Navigation links active state
            const navLinks = document.querySelectorAll('.nav-link');
            
            function setActiveLink() {
                const scrollPos = window.scrollY + 100;
                
                navLinks.forEach(link => {
                    const section = document.querySelector(link.getAttribute('href'));
                    if (section) {
                        const sectionTop = section.offsetTop;
                        const sectionHeight = section.offsetHeight;
                        
                        if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                            navLinks.forEach(l => l.classList.remove('active'));
                            link.classList.add('active');
                        }
                    }
                });
            }

            window.addEventListener('scroll', setActiveLink);

});