<?php
// src/Log/loggers.php (procesos de log, que guarda los log en la carpeta logs duracion el mismo dependiendo de como se define en .env)
declare(strict_types=1);

namespace App\Log;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class loggers
{
    /**
     * Inicializa y devuelve un Logger configurado según .env
     *
     * @return Logger
     */
    public static function createLogger(): Logger
    {
        // 1. Nombre del canal (puedes cambiar “app” por lo que prefieras)
        $logger = new Logger('app');

        // 2. Obtener configuración desde variables de entorno
        $channel   = $_ENV['LOG_CHANNEL'] ?? 'single';
        $levelName = $_ENV['LOG_LEVEL']   ?? 'debug';
        $logPath   = $_ENV['LOG_PATH']    ?? __DIR__ . '/../logs';
        $maxFiles  = (int) ($_ENV['LOG_MAX_FILES'] ?? 7);

        // Convertir el nombre de nivel a constante Monolog (int)
        $level = Logger::toMonologLevel($levelName);

        // 3. Según el canal, agregamos distintos handlers
        switch ($channel) {
            case 'daily':
                // RotatingFileHandler crea un archivo nuevo por día y rota
                // Nombre: /var/log/mi_aplicacion/app-YYYY-MM-DD.log
                $rotating = new RotatingFileHandler(
                    rtrim($logPath, '/\\') . '/app.log',
                    $maxFiles,
                    $level
                );
                // Opcional: formateo más legible
                $formatter = new LineFormatter(
                    null,             // formato por defecto: "[%datetime%] %channel%.%level_name%: %message% %context%"
                    null,             // fecha por defecto: "Y-m-d H:i:s"
                    true,             // permitir "multiline"
                    true              // eliminar espacios en blancos extra
                );
                $rotating->setFormatter($formatter);
                $logger->pushHandler($rotating);
                break;

            case 'single':
                // Simplemente un solo StreamHandler
                $stream = new StreamHandler(
                    rtrim($logPath, '/\\') . '/app.log',
                    $level
                );
                $logger->pushHandler($stream);
                break;

            default:
                // Por defecto, mismo comportamiento que 'single'
                $stream = new StreamHandler(
                    rtrim($logPath, '/\\') . '/app.log',
                    $level
                );
                $logger->pushHandler($stream);
                break;
        }

        return $logger;
    }
}
