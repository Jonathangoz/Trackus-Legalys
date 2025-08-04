<?php
# public/logging.pph (formulario inicio de sesion correo y contraseña)
require_once __DIR__ . '/../vendor/autoload.php';

use App\Comunes\middleware\mantenimiento;
use App\Comunes\seguridad\autenticacion;
use App\Comunes\utilidades\loggers;
use App\Comunes\seguridad\csrf;

# Verificar mantenimiento
mantenimiento::check();

# crear llamado para logger
$logger = loggers::createLogger();

# Si el método es POST, procesamos:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (autenticacion::login($email, $password)) {
        // PRG: redirige a dashboard
        header('Location: /dashboard.php');
        exit;
    } else {
        header('Location: /login.php?error=1');
        exit;
    }
}

$logger->debug("Renderizando vista logging.php", [
    'SESSION_ID' => session_id(),
    'CSRF_TOKEN' => $_SESSION['csrf_token'] ?? null
]);

# inyectar tokencsrf
csrf::generarToken();

// Si llegamos aquí, es GET: mostramos formulario
$error = isset($_GET['error']) ? "Credenciales incorrectas" : "";

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
  <title>SENA Cobro Coactivo - Inicio de Sesion</title>
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
  <link href="assets/CSS/styles3.css" rel="stylesheet">

</head>
<body class="bg-gray-50 min-h-screen flex flex-col justify-center items-center p-4">
  <div class="card bg-white p-8 w-full max-w-md mx-auto shadow-lg rounded-lg">
    <div class="logo-section text-center mb-10">
        <div class="img">
         <img src="assets/images/Logosimbolo-SENA-PRINCIPAL.png" alt="Logo SENA" aria-label="Logo SENA">
        </div>
      <h2 class="text-xl text-gray-700 font-medium">Gestion Juridica</h2>
    </div>
        
    <form class="space-y-6" aria-label="Formulario de inicio de sesión" action="/login" method="POST">
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
            name="password" 
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
      <div class="flex flex-row gap-5 mt-6 justify-center">
      <?php csrf::insertarInput(); ?>
        <button 
          class="btn-gradient text-white text-sm font-semibold py-2 px-12 rounded-lg focus:outline-none focus:ring-2 focus:ring-sena-green" 
          type="submit"
          aria-label="Iniciar Sesión">
          Iniciar Sesión
        </button>
        <button 
          class="btn-gradient text-white text-sm font-semibold py-2 px-16 rounded-lg focus:outline-none focus:ring-2 focus:ring-sena-green" 
          type="button" 
          onclick="window.location.href='index.html'"
          aria-label="Regresar">
          Regresar
        </button>
      </div>

        <?php

          if (isset($_SESSION['login_errors'])) {
            echo "<p class='text-center mb-10' style='color: red;'>" . $_SESSION['login_errors'] . "</p>";
            unset($_SESSION['login_errors']); // Limpiar el mensaje de error después de mostrarlo
          }

        ?>

    </form>
    
    <div class="mt-6 text-center">
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