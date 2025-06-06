<?php
// src/Comunes/utilidades/loggers.php
//declare(strict_types=1);

namespace App\Comunes\utilidades;


use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Formatter\LineFormatter;

class loggers {
    /**
     * Inicializa y devuelve un Logger configurado según .env
     */
    public static function createLogger(): Logger {
        // 1. Nombre del canal (puedes cambiar “app” por lo que prefieras)
        $logger = new Logger('Mensajes');
        #ErrorHandler::register($logger);

        // 2. Obtener configuración desde variables de entorno
        $channel   = $_ENV['LOG_CHANNEL'] ?? 'single';
        $levelName = $_ENV['LOG_LEVEL']   ?? 'debug';
        $logPath   = $_ENV['LOG_PATH']    ?? __DIR__ . '/../../../logs';
        $maxFiles  = (int) ($_ENV['LOG_MAX_FILES'] ?? 7);

        // Convertir el nombre de nivel a constante Monolog (int)
        $level = Logger::toMonologLevel($levelName);

        // 3. Según el canal, agregamos distintos handlers
        switch ($channel) {
            case 'daily':
                $rotating = new RotatingFileHandler(
                    rtrim($logPath, '/\\') . '/Mensajes.log',
                    $maxFiles,
                    $level
                );
                $formatter = new LineFormatter(
                    "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",    // formato por defecto
                    "d/m/Y h:i A",    // fecha por defecto
                    true,    // permitir “multiline”
                    true     // eliminar espacios extra
                );
                $rotating->setFormatter($formatter);
                $logger->pushHandler($rotating);
                break;

            case 'single':
            default:
                $stream = new StreamHandler(
                    rtrim($logPath, '/\\') . '/Mensajes.log',
                    $level
                );
                $formatter = new LineFormatter(
                    "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",    // formato por defecto
                    "d/m/Y h:i A",    // fecha por defecto
                    true,    // permitir “multiline”
                    true     // eliminar espacios extra
                );
                $stream->setFormatter($formatter);
                $logger->pushHandler($stream);
                break;
        }

        // 4. Agregar handler para la consola del navegador
        //    Solo en entorno de desarrollo. Si en producción, pudes saltarlo.
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            $browserHandler = new BrowserConsoleHandler($level);
            $logger->pushHandler($browserHandler);
        }

        return $logger;
    }
}
