<?php
# src/Comunes/utilidades/loggers.php (verifica y crea log ya sea diario, mensual, importante para warning y error del sistema)
declare(strict_types=1);

namespace App\Comunes\utilidades;


use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Formatter\LineFormatter;

class loggers {

    # Inicializa y devuelve un Logger configurado según .env
    public static function createLogger(): Logger {
        # Nombre del canal (puedes cambiar “Mensajes” por lo que prefieras)
        $logger = new Logger('Mensajes');
        #ErrorHandler::register($logger);

        # Obtener configuración desde variables de entorno
        $channel   = $_ENV['LOG_CHANNEL'] ?? 'single';
        $levelName = $_ENV['LOG_LEVEL']   ?? 'debug';
        $logPath   = $_ENV['LOG_PATH']    ?? __DIR__ . '/../../../logs'; # Ruta donde se almacenaran los log generados del sistema.
        $maxFiles  = (int) ($_ENV['LOG_MAX_FILES'] ?? 7);

        # Convertir el nombre de nivel a constante Monolog (int)
        $level = Logger::toMonologLevel($levelName);

        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }

        # Según el canal, agregamos distintos handlers
        switch ($channel) {
            case 'daily':
                $rotating = new RotatingFileHandler(
                    rtrim($logPath, '/\\') . '/log.log',
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
                    rtrim($logPath, '/\\') . '/Advertencias.log',
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

        # Agregar handler para la consola del navegador, Solo en entorno de desarrollo. en producción, se pude saltarl.
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            $browserHandler = new BrowserConsoleHandler($level);
            $logger->pushHandler($browserHandler);
        }
        return $logger;
    }
}