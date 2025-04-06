<?php
session_start();

// Regenerar el ID de la sesión para evitar fixation
session_regenerate_id(true);

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión completamente
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();

// Cabeceras para evitar caché en navegadores
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Redirección final
header("Location: ../loggin.php");
exit();
