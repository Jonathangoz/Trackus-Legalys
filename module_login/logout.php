<?php
// Inicia la sesión para poder acceder a los datos de la misma
session_start();

// Regenera el ID de la sesión para prevenir ataques de fijación de sesión
session_regenerate_id(true);

// Destruye completamente la sesión actual
if (ini_get("session.use_cookies")) {
    // Obtiene los parámetros de la cookie de sesión
    $params = session_get_cookie_params();
    // Establece una cookie con un tiempo de expiración pasado para eliminarla
    setcookie(
        session_name(), // Nombre de la cookie de sesión
        '',             // Valor vacío
        time() - 4200, // Tiempo de expiración en el pasado
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruye los datos de la sesión en el servidor
session_destroy();

// Configura las cabeceras HTTP para evitar que el navegador almacene en caché la página
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Redirige al usuario a la página de inicio de sesión
header("Location: ../index.html");
exit();
