document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('userTrigger').addEventListener('click', loadUserModal);
});

function loadUserModal(e) {
  // Si fuera un <a>, también haríamos e.preventDefault() aquí
  fetch('/admin/componentes/modalDashboard.php')
    .then(res => res.text())
    .then(html => {
      const container = document.getElementById('userModalContainer');
      container.innerHTML = html;
      const modal = container.querySelector('.modal-fade');
      const closeBtn = container.querySelector('.close-btn');

      // Mostrar modal
      modal.style.display = 'block';

      // Cerrar al hacer clic en la X
      closeBtn.addEventListener('click', () => container.innerHTML = '');

      // Cerrar al hacer clic fuera del contenido
      modal.addEventListener('click', ev => {
        if (ev.target === modal) container.innerHTML = '';
      });
    })
    .catch(console.error);
}
    function closeModal() {
        const modal = document.getElementById('modalUser');
        const backdrop = document.getElementById('contentmodal');
        
        modal.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
            document.body.style.overflow = '';
        }, 150);
    }

    // PASSWORD TOGGLE FUNCTIONALITY

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // FORM VALIDATION

    function validatePasswords() {
        const password = document.getElementById('userPassword').value;
        const confirmPassword = document.getElementById('userConfirmPassword').value;
        const errorDiv = document.getElementById('passwordError');
        
        if (password !== confirmPassword) {
            errorDiv.classList.remove('d-none');
            return false;
        } else {
            errorDiv.classList.add('d-none');
            return true;
        }
    }

    // SAVE USER FUNCTIONALITY

    function saveUser() {
        const form = document.getElementById('userForm');
        const formData = new FormData(form);
        
        // Validate required fields
        const requiredFields = ['userName', 'userIdentification', 'userEmail', 'userRole', 'userPassword'];
        let isValid = true;
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                field.style.borderColor = '#dc3545';
                isValid = false;
            } else {
                field.style.borderColor = '#ced4da';
            }
        });
        
        // Validate passwords match
        if (!validatePasswords()) {
            isValid = false;
        }
        
        if (isValid) {
            // Here you would typically send the data to your server
            alert('Usuario guardado correctamente');
            closeModal();
            form.reset();
        } else {
            alert('Por favor, complete todos los campos requeridos correctamente');
        }
    }

    // EVENT LISTENERS

    document.addEventListener('DOMContentLoaded', function() {
        // Password validation on input
        document.getElementById('userConfirmPassword').addEventListener('input', validatePasswords);
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        
        // Form submission
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            saveUser();
        });
    });


/*
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
    window.location.href = 'login.html';
    }
});

// Otras funcionalidades pueden ir aquí
// Por ejemplo, para el perfil:
document.querySelector('.dropdown-item[href="#"]').addEventListener('click', function(e) {
    e.preventDefault();
    // Lógica para mostrar el perfil
    console.log('Mostrar perfil del usuario');
});
}); */