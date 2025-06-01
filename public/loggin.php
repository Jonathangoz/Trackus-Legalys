<?php

use App\Mantenimiento\mantenimiento;

require __DIR__ . '/vendor/autoload.php';

// Cargar variables desde .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 2) Obtener una instancia del Logger
$logger = \App\Log\loggers::createLogger();

// 3) Empezar a usarlo
$logger->info('Aplicación iniciada correctamente.');
$logger->warning('Algo inusual ocurrió, pero no es crítico.');
$logger->error('¡Ha ocurrido un error crítico!', ['usuario' => 'juan123', 'acción' => 'login']);

require_once 'config/env.php';
require_once 'Mantenimiento/mantenimiento.php';

// Verificar mantenimiento
mantenimiento::check();

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'cookie_secure'   => false, // pon true si usas HTTPS
    ]);
}

// Generar o reusar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Capturar errores y old values de sesiones anteriores
$errors = $_SESSION['login_errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SENA - Trackus Legalys</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
  <link href="assets/css/styles3.css" rel="stylesheet">
  <link rel="icon" href="assets/img/sena.ico" type="image/x-icon"/>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center items-center p-4">
  <div class="card bg-white p-8 w-full max-w-md mx-auto shadow-lg rounded-lg">
    <div class="logo-section text-center mb-10">
        <div class="img">
         <img src="assets/img/Logosimbolo-SENA-PRINCIPAL.png" alt="Logo SENA" aria-label="Logo SENA">
        </div>
      <h2 class="text-xl text-gray-700 font-medium">Trackus Legalys</h2>
    </div>
    
    
    <form class="space-y-6" aria-label="Formulario de inicio de sesión" action="module_login/login.php" method="POST">
      <div>
        <label class="block text-gray-700 text-sm font-semibold mb-2" for="username">Email</label>
        <input 
          class="form-control w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-sena-green focus:ring-2 focus:ring-sena-green" 
          id="username"
          name="correo"
          type="text" 
          placeholder="Ingrese su Correo"
          required
          aria-required="true">
      </div>
      
      <div class="relative">
        <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
          Contraseña
        </label>
        <div class="relative">
          <input 
            class="form-control w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:border-sena-green focus:ring-2 focus:ring-sena-green" 
            id="password"
            name="contrasenia" 
            type="password" 
            placeholder="Ingrese su contraseña"
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
      
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input 
            id="remember" 
            type="checkbox" 
            class="h-4 w-4 text-sena-green focus:ring-2 focus:ring-sena-green border-gray-300 rounded"
            aria-label="Recordarme">
          <label for="remember" class="ml-2 block text-sm text-gray-700">
            Recordarme
          </label>
        </div>
              
        <a class="inline-block align-baseline font-medium text-sm text-sena-green hover:underline" href="../module_login/validation/verification.php" aria-label="¿Olvidó su contraseña?">
          ¿Olvidó su contraseña?
        </a>
      </div>
      
      <button 
        class="btn-gradient text-white font-semibold py-3 px-4 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-sena-green" 
        type="submit"
        href="index.php"
        aria-label="Iniciar Sesión">
        Iniciar Sesión
      </button>
      <?php

        session_start();
        if (isset($_SESSION['error'])) {
            echo "<p class='text-center mb-10' style='color: red;'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']); // Limpiar el mensaje de error después de mostrarlo
        }

        if (isset($_SESSION['message'])) {
            echo "<p class='text-center mb-10' style='color: green;'>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }

      ?>
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
</body>
</html> 