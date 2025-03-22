<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SENA - Recuperar Contraseña</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
  <link href="assets/css/styles2.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center items-center p-4">
  <div class="card bg-white p-8 w-full max-w-md mx-auto shadow-lg rounded-lg">
    <div class="logo-section text-center mb-10">
        <div class="img">
         <img src="assets/img/Logosimbolo-SENA-PRINCIPAL.png" alt="Logo SENA" aria-label="Logo SENA">
        </div>
      <h2 class="text-xl text-gray-700 font-medium">Trackus Legalys</h2>
    </div>
    
    <form class="space-y-6" aria-label="Formulario de recuperación de contraseña" action="recover.php" method="POST">
  <div>
    <label class="block text-gray-700 text-sm font-semibold mb-2" for="recovery-email">Correo</label>
    <input 
      class="form-control w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-sena-green focus:ring-2 focus:ring-sena-green" 
      id="recovery-email"
      name="correo"
      type="email" 
      placeholder="Ingrese su Correo"
      required
      aria-required="true">
  </div>
  
  <div class="flex items-center justify-between gap-4">
    <button 
      class="btn-gradient text-white font-semibold py-2 px-4 rounded-lg w-1/2 focus:outline-none focus:ring-2 focus:ring-sena-green" 
      type="submit"
      aria-label="Aceptar">
      Aceptar
    </button>
    <button 
      class="btn-gradient text-white font-semibold py-2 px-4 rounded-lg w-1/2 focus:outline-none focus:ring-2 focus:ring-sena-green" 
      type="button"
      onclick="window.location.href='loggin.php'"
      aria-label="Cancelar">
      Cancelar
    </button>
  </div>
</form>
    
    <div class="mt-10 text-center">
      <div class="border-t border-gray-200 pt-4">
            <p class="text-sm text-gray-600">
                © 2025 SENA - Trackus Legalys
            </p>
      </div>
    </div>
  </div>

  <script src="assets/js/visualpass.js"></script>
  <script src="assets/js/confirm-password.js"></script>
</body>
</html>