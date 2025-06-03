<?php
// src/Mantenimiento/mantenimiento.php
declare(strict_types=1);

namespace App\Mantenimiento;

class mantenimiento {
    
    public static function check() {
        // Cargar configuración si no está cargada
        if (!function_exists('is_maintenance_mode')) {
            require_once __DIR__ . '/../../config/env.php';
        }
        
        // Verificar si está en modo mantenimiento
        if (is_maintenance_mode()) {
            self::showMaintenancePage();
            exit;
        }
    }
    
    private static function showMaintenancePage() {
        http_response_code(503);
        header('Retry-After: 3600'); // Reintentar en 1 hora
        
        $message = env('MAINTENANCE_MESSAGE', 'Estamos realizando mejoras. Volvemos pronto.');
        
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mantenimiento - <?= env('APP_NAME', 'Mi Aplicación') ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                }
                
                .maintenance-container {
                    text-align: center;
                    max-width: 600px;
                    padding: 2rem;
                }
                
                .maintenance-icon {
                    font-size: 4rem;
                    margin-bottom: 1rem;
                    opacity: 0.8;
                }
                
                .maintenance-title {
                    font-size: 2.5rem;
                    font-weight: 300;
                    margin-bottom: 1rem;
                }
                
                .maintenance-message {
                    font-size: 1.2rem;
                    opacity: 0.9;
                    margin-bottom: 2rem;
                    line-height: 1.6;
                }
                
                .maintenance-footer {
                    opacity: 0.7;
                    font-size: 0.9rem;
                }
                
                @keyframes pulse {
                    0%, 100% { opacity: 0.5; }
                    50% { opacity: 1; }
                }
                
                .pulse {
                    animation: pulse 2s infinite;
                }
            </style>
        </head>
        <body>
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
    
    /**
     * Verificar si una IP está en la lista de permitidas
     */
    private static function isIpAllowed($ip) {
        $allowedIps = env('MAINTENANCE_ALLOWED_IPS', '');
        if (empty($allowedIps)) {
            return false;
        }
        
        $ips = array_map('trim', explode(',', $allowedIps));
        return in_array($ip, $ips);
    }
    
    /**
     * Obtener la IP real del cliente
     */
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