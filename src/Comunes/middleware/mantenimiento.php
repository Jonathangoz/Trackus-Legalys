<?php
# src/Comunes/middleware/mantenimiento.php (vista Modo Mantenimiento)
declare(strict_types=1);

namespace App\Comunes\middleware;

class mantenimiento {
    
    public static function check() {
        # Cargar configuración si no está cargada
        if (!function_exists('modoMantenimiento')) {
            require_once __DIR__ . '/../../../config/env.php';
        }
        
        # Verificar si está en modo mantenimiento
        if (modoMantenimiento()) {
            self::showMaintenancePage();
            exit;
        }
    }

# vista modo mantenimiento
private static function showMaintenancePage() {
    http_response_code(503);
    $message = env('MAINTENANCE_MESSAGE', 'Estamos Realizando Mejoras, Volvemos Pronto.');
        
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - <?= env('APP_NAME', 'Mi Aplicación') ?></title>
</head>
<body>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            cursor: default;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(160deg, #39a900 15%, #f7f7f8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
        }
        
        .maintenance-container {
            text-align: center;
            padding: 2rem;
        }
        
        .maintenance-icon {
            font-size: 8rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
        
        .maintenance-title {
            font-size: 4rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .maintenance-message {
            font-size: 2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .maintenance-footer {
            opacity: 0.7;
            font-size: 1.5rem;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
    </style>
    <div class="maintenance-container">
        <div class="maintenance-icon pulse">🔧</div>
        <h1 class="maintenance-title">Sitio en Mantenimiento</h1>
        <p class="maintenance-message"><?= htmlspecialchars($message) ?></p>
        <div class="maintenance-footer">
            <p>Gracias por tu paciencia</p>
            <p><strong><?= env('APP_NAME', 'Mi Aplicación') ?></strong></p>
        </div>
    </div>
</body>
</html>
<?php
}
    

    # Verificar si una IP está en la lista de permitidas (para dar una excepcion a una ip en concreto)
    private static function isIpAllowed($ip) {
        $allowedIps = env('MAINTENANCE_ALLOWED_IPS', '');
        if (empty($allowedIps)) {
            return false;
        }
        
        $ips = array_map('trim', explode(',', $allowedIps));
        return in_array($ip, $ips);
    }
    
    # Obtener la IP real del cliente
    private static function getRealIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return null;
    }
}