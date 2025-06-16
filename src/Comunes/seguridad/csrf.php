<?php
# src/Comunes/seguridad/csrf.php (token csrf ayuda contra los ataques de inyeccion html y robo de sesiones)
namespace App\Comunes\seguridad;

use App\Comunes\seguridad\encriptacion;
use Monolog\Logger;


class csrf {

    # Genera un token CSRF y lo guarda en $_SESSION['csrf_token'], si aún no existe. Devuelve siempre el valor final del token.
    # genera el token base64_encode unico por sesion y por usuario.
    public static function generarToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] ??= encriptacion::tokenRandom();
        }
        return $_SESSION['csrf_token'];
    }

    # para futuras actualizaciones cambiar la funcion para mayor seguridad
    /*public static function generarToken(): string {
        // Regenerar token cada hora por seguridad adicional
        $tokenKey = 'csrf_token';
        $timeKey = 'csrf_token_time';
        
        if (empty($_SESSION[$tokenKey]) || 
            (time() - ($_SESSION[$timeKey] ?? 0)) > 3600) {
            $_SESSION[$tokenKey] = encriptacion::tokenRandom();
            $_SESSION[$timeKey] = time();
        }
        
        return $_SESSION[$tokenKey];
    } */

    # Valida que, si el método es POST, el token enviado por formulario coincida exactamente con el guardado en sesión. 
    # Si no coincide, lanza excepción.
    public static function validarCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            # En caso de que no venga definido, asigno cadena vacía
            $tokenPost = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
            $tokenSession = $_SESSION['csrf_token'] ?? '';

            // Validar que ambos tokens no estén vacíos
            if (empty($tokenPost) || empty($tokenSession)) {
                throw new \Exception("CSRF token requerido");
            }

            # hash_equals previene timing attacks
            if (!hash_equals($tokenPost, $tokenSession)) {
                throw new \Exception("CSRF token inválido");    
            }
        }
    }

    # Imprime un <input type="hidden"> con el token CSRF generado y escapado.
    # Usarlo dentro del <form> para que se envíe en cada petición POST.
        public static function insertarInput(): void {
            $token = self::generarToken();

            // Escapar con htmlspecialchars antes de imprimirlo.
            $tokenEscapado = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
            echo '<input type="hidden" name="csrf_token" value="'.$tokenEscapado.'" />';
        }
    
}