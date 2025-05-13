 // ABRIR / CERRAR MODAL
 const openBtn = document.getElementById('openModal');
 const modal   = document.getElementById('userModal');
 const closeBtn = document.getElementById('closeModal');
 const cancelBtn = document.getElementById('cancelBtn');

 openBtn.addEventListener('click', () => modal.classList.add('show'));
 [closeBtn, cancelBtn].forEach(btn =>
   btn.addEventListener('click', () => modal.classList.remove('show'))
 );
 // Cerrar al hacer clic fuera del diálogo
 modal.addEventListener('click', e => {
   if (e.target === modal) modal.classList.remove('show');
 });

 // TOGGLE VISIBILIDAD CONTRASEÑAS
 document.querySelectorAll('.toggle-password').forEach(btn => {
   btn.addEventListener('click', () => {
     const input = document.getElementById(btn.dataset.target);
     input.type = input.type === 'password' ? 'text' : 'password';
   });
 });

 // VALIDACIÓN BÁSICA DE CONTRASEÑAS
 const form = document.getElementById('userForm');
 const save = document.getElementById('saveUser');
 save.addEventListener('click', () => {
   const pw  = form.userPassword.value;
   const cpw = form.userConfirmPassword.value;
   const err = document.getElementById('passwordError');
   if (pw === cpw) {
     err.style.display = 'none';
     // aquí podrías enviar el formulario...
     form.submit();
   } else {
     err.style.display = 'block';
   }
 });