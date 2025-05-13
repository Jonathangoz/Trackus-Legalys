document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar el div del avatar del usuario
    const userAvatar = document.querySelector('.user-avatar');
    const userInfo = document.querySelector('.user-info');
    
    // Verificar si ya existe un menú desplegable
    let dropdownMenu = userInfo.querySelector('.dropdown-menu');
    
    // Si no existe, creamos el menú desplegable
    if (!dropdownMenu) {
        // Crear el menú desplegable
        dropdownMenu = document.createElement('div');
        dropdownMenu.className = 'dropdown-menu';
        dropdownMenu.style.display = 'none';
        
        // Crear las opciones del menú
        const menuItems = [
            { text: 'Ver Perfil', icon: '👤', action: viewProfile },
            { text: 'Configuración', icon: '⚙️', action: openSettings },
            { text: 'Cerrar Sesión', icon: '🚪', action: logout }
        ];
        
        // Crear y añadir cada opción al menú
        menuItems.forEach(item => {
            const menuItem = document.createElement('div');
            menuItem.className = 'dropdown-item';
            menuItem.innerHTML = `<span class="dropdown-icon">${item.icon}</span> ${item.text}`;
            menuItem.addEventListener('click', item.action);
            dropdownMenu.appendChild(menuItem);
        });
        
        // Añadir el menú al div de información del usuario
        userInfo.appendChild(dropdownMenu);
    }
    
    // Función para alternar la visualización del menú desplegable
    function toggleDropdown() {
        if (dropdownMenu.style.display === 'none') {
            dropdownMenu.style.display = 'block';
        } else {
            dropdownMenu.style.display = 'none';
        }
    }
    
    // Añadir evento de clic al avatar para mostrar/ocultar el menú
    userAvatar.addEventListener('click', function(event) {
        event.stopPropagation();
        toggleDropdown();
    });
    
    // Cerrar el menú al hacer clic en cualquier parte fuera de él
    document.addEventListener('click', function(event) {
        if (!userInfo.contains(event.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
    
    // Funciones para las acciones del menú
    function viewProfile() {
        console.log('Ver perfil');
        // Aquí puedes añadir código para navegar a la página del perfil
        alert('Navegando al perfil de usuario');
        // window.location.href = 'profile.html';
    }
    
    function openSettings() {
        console.log('Abrir configuración');
        // Aquí puedes añadir código para navegar a la configuración
        alert('Abriendo configuración de usuario');
        // window.location.href = 'settings.html';
    }
    
    function logout() {
        console.log('Cerrar sesión');
        if (confirm('¿Está seguro que desea cerrar sesión?')) {
            // Aquí puedes añadir código para cerrar la sesión
            alert('Sesión cerrada exitosamente');
            // window.location.href = 'login.html';
        }
    }
});


