document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordFields = ['password', 'new-password'];
    passwordFields.forEach(function(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        if (passwordInput) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    });
    this.textContent = this.textContent === '👁️' ? '👁️‍🗨️' : '👁️';
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
  const passwordFields = ['confirm-password'];
  passwordFields.forEach(function(fieldId) {
      const passwordInput = document.getElementById(fieldId);
      if (passwordInput) {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
      }
  });
  this.textContent = this.textContent === '👁️' ? '👁️‍🗨️' : '👁️';
});