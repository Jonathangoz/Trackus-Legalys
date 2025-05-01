<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SENA - Recuperar Contraseña</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
  <link href="../../assets/css/styles2.css" rel="stylesheet">
  <link rel="icon" href="../../assets/img/sena.ico" type="image/x-icon"/>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center items-center p-4">
  <div class="card bg-white p-8 w-full max-w-md mx-auto shadow-lg rounded-lg">
    <div class="logo-section text-center mb-10">
        <div class="img">
         <img src="../../assets/img/Logosimbolo-SENA-PRINCIPAL.png" alt="Logo SENA" aria-label="Logo SENA">
        </div>
      <h2 class="text-xl text-gray-700 font-medium">Trackus Legalys</h2>
    </div>
    
    <form class="space-y-6" aria-label="Formulario de recuperación de contraseña" action="password_recovery.php" method="POST">
  <div>
    <label class="block text-gray-700 text-center font-semibold mb-2" for="recovery-email">Correo para Recuperación de Contraseña</label>
    <input 
      class="form-control w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-sena-green focus:ring-2 focus:ring-sena-green" 
      id="recovery-email"
      name="correo"
      type="email"
      placeholder="Ingrese un Correo, se enviara link de Recuperación"
      required
      aria-required="true">
  </div>
  
  <div class="flex items-center justify-between gap-4">
    <button 
      class="btn-gradient text-white font-semibold py-2 px-4 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-sena-green" 
      type="submit"
      aria-label="Recuperar Contraseña">
      Recuperar Contraseña
    </button>
  </div>
</form>
    <?php
        session_start();
        if (isset($_SESSION['message'])) {
            echo "<p class='text-center mb-10' style='color: green;'>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error2'])) {
            echo "<p class='text-center mb-10' style='color: red;'>" . $_SESSION['error2'] . "</p>";
            unset($_SESSION['error2']);
        }
      ?>
    
    <div class="mt-10 text-center">
      <div class="border-t border-gray-200 pt-4">
            <p class="text-sm text-gray-600">
                © 2025 SENA - Trackus Legalys
            </p>
      </div>
    </div>
  </div>

  <script src="../../assets/js/visualpass.js"></script>
  <script src="../../assets/js/confirm-password.js"></script>
</body>
</html>