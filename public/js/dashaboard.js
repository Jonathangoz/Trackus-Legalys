
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar en móviles
    const sidebarToggle = document.createElement('button');
    sidebarToggle.className = 'navbar-toggler position-fixed d-md-none';
    sidebarToggle.style.top = '10px';
    sidebarToggle.style.left = '10px';
    sidebarToggle.style.zIndex = '1000';
    sidebarToggle.setAttribute('data-bs-toggle', 'collapse');
    sidebarToggle.setAttribute('data-bs-target', '#sidebar');
    sidebarToggle.innerHTML = '<i class="bi bi-list"></i>';
    document.body.appendChild(sidebarToggle);
    
    // Mostrar/ocultar contraseña
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // Validación de contraseñas
    const password = document.getElementById('userPassword');
    const confirmPassword = document.getElementById('userConfirmPassword');
    const passwordError = document.getElementById('passwordError');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            passwordError.classList.remove('d-none');
            confirmPassword.classList.add('is-invalid');
            return false;
        } else {
            passwordError.classList.add('d-none');
            confirmPassword.classList.remove('is-invalid');
            return true;
        }
    }
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);

    // Guardar usuario
    document.getElementById('saveUser').addEventListener('click', function() {
        const form = document.getElementById('userForm');
        
        // Validar formulario
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        // Validar contraseñas
        if (!validatePassword()) {
            return;
        }
        
        // Obtener datos del formulario
        const userData = {
            name: document.getElementById('userName').value,
            identification: document.getElementById('userIdentification').value,
            email: document.getElementById('userEmail').value,
            phone: document.getElementById('userPhone').value,
            role: document.getElementById('userRole').value,
            password: password.value
        };
        
        console.log('Datos del usuario:', userData);
        
        // Simular envío exitoso
        alert('Usuario creado exitosamente');
        const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
        modal.hide();
        
        // Resetear formulario
        form.reset();
        form.classList.remove('was-validated');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Cerrar sesión
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('¿Está seguro que desea cerrar sesión?')) {
        // Aquí iría la lógica para cerrar sesión
        // Por ejemplo: 
        // fetch('/logout', { method: 'POST' })
        //   .then(() => window.location.href = '/login')
        
        // Simulación:
        alert('Sesión cerrada exitosamente');
        window.location.href = 'logging.html';
      }
    });
    
    // Otras funcionalidades pueden ir aquí
    // Por ejemplo, para el perfil:
    document.querySelector('.dropdown-item[href="#"]').addEventListener('click', function(e) {
      e.preventDefault();
      // Lógica para mostrar el perfil
      console.log('Mostrar perfil del usuario');
    });
  });