document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const newPassword = document.getElementById('new-password');
    const confirmPassword = document.getElementById('confirm-password');
    const passwordError = document.getElementById('password-error');

    function validatePasswords() {
      if (newPassword.value !== confirmPassword.value) {
        passwordError.classList.remove('hidden');
        passwordError.textContent = 'Las contraseñas no coinciden.';
        return false;
      } else {
        passwordError.classList.add('hidden');
        return true;
      }
    }

    form.addEventListener('submit', function (event) {
      if (!validatePasswords()) {
        event.preventDefault();
      }
    });

    confirmPassword.addEventListener('input', validatePasswords);
    newPassword.addEventListener('input', validatePasswords);
  });