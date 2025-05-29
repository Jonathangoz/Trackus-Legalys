<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SENA - Recuperar Contraseña</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
  <link href="../../public/CSS/styles2.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center items-center p-4">
  <div class="card bg-white p-8 w-full max-w-md mx-auto shadow-lg rounded-lg">
    <div class="logo-section text-center mb-10">
        <div class="img">
         <img src="../../public/img/Logosimbolo-SENA-PRINCIPAL.png" alt="Logo SENA" aria-label="Logo SENA">
        </div>
      <h2 class="text-xl text-gray-700 font-medium">Trackus Legalys</h2>
    </div>
    
    <form class="space-y-6" aria-label="Formulario de recuperación de contraseña" action="recover.php" method="POST">
  
  <div class="relative">
    <label class="block text-gray-700 text-sm font-semibold mb-2" for="recovery-email">Ingrese el Correo Usado en Trackus Legalys</label>
    <input 
      class="form-control w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-sena-green focus:ring-2 focus:ring-sena-green" 
      id="recovery-email"
      name="correo"
      type="email"
      placeholder="Ingrese su Correo"
      required
      aria-required="true">
  </div>
  
  <div class="relative">
    <label class="block text-gray-700 text-sm font-semibold mb-2" for="new-password">Nueva Contraseña</label>
    <div class="relative">
      <input 
        class="form-control w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-sena-green focus:ring-2 focus:ring-sena-green" 
        id="new-password"
        name="contrasenia"
        type="password" 
        placeholder="Ingrese su Nueva Contraseña"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[ñ-Ñ])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
        title="La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y caracteres especiales(@,?,=,*,etc)."
        required
        aria-required="true">
      <span 
        id="togglePassword" 
        class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-500 hover:text-gray-700"
        aria-label="Mostrar/Ocultar contraseña">
        👁️‍🗨️
      </span>
    </div>
  </div>
  
  <div class="relative">
    <label class="block text-gray-700 text-sm font-semibold mb-2" for="confirm-password">Confirmar Contraseña</label>
    <div class="relative">
      <input 
        class="form-control w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-sena-green focus:ring-2 focus:ring-sena-green" 
        id="confirm-password"
        name="confirmar" 
        type="password" 
        placeholder="Confirme su Nueva Contraseña"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[ñ-Ñ])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
        title="La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y caracteres especiales(@,?,=,*,etc)."
        required
        aria-required="true">
      <span 
        id="toggleConfirmPassword" 
        class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-500 hover:text-gray-700"
        aria-label="Mostrar/Ocultar contraseña">
        👁️‍🗨️
      </span>
    </div>
    <!-- Mensaje de error si las contraseñas no coinciden -->
    <p id="password-error" class="text-red-500 text-sm mt-2 hidden"></p>
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
      onclick="window.location.href='../../public/logging.php'"
      aria-label="Cancelar">
      Cancelar
    </button>
  </div>
</form>
    <div class="mt-6 text-center">
      <div class="border-t border-gray-200 pt-4">
            <p class="text-sm text-gray-600">
                © 2025 SENA - Trackus Legalys
            </p>
      </div>
    </div>
  </div>
  <script src="../../public/js/visualpass.js"></script>
  <script src="../../public/js/confirm-password.js"></script>
</body>
</html>