<?php
# src/Modulos/Controladores/controlador_base.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

abstract class controlador_base {
    
    /**
     * Método de redirección común para todos los controladores
     */
    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Método abstracto que deben implementar todos los controladores
     */
    abstract public function handle(string $uri, string $method): void;
    
    /**
     * Método para enviar respuesta JSON
     */
    protected function sendJsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Método para mostrar una vista
     */
    protected function renderView(string $viewPath, array $data = []): void {
        extract($data);
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("Vista no encontrada: {$viewPath}");
        }
    }

    /**
     * Método para manejar errores
     */
    protected function handleError(string $message, int $statusCode = 500): void {
        http_response_code($statusCode);
        echo "Error: {$message}";
        exit;
    }
}