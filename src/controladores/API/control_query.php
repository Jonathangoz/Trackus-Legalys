<?php
// src/Controllers/ApiController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\CryptoService;
use App\Models\Deudor;
use App\Models\Funcionario;

class ApiController extends BaseController
{
    /**
     * Maneja todas las llamadas a /api
     * Espera un JSON en el body con al menos { action: '...' }.
     * Verifica X-Auth-Token y CSRF (en POST), luego despacha a la acción correspondiente.
     */
    public function handle(): void
    {
        // 1) Verificar que sea aplicación/json
        header('Content-Type: application/json; charset=UTF-8');

        // 2) Verificar que venga un token de autenticación firmado
        $headers = getallheaders();
        $authHeader = $headers['X-Auth-Token'] ?? '';
        if (empty($authHeader) || CryptoService::verifySignedToken($authHeader) === null) {
            http_response_code(401);
            echo json_encode(['error' => 'Token de autenticación inválido.']);
            return;
        }

        // 3) Validar que la sesión PHP siga activa y el token sea el mismo
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict',
            'cookie_secure'   => false, // true si usas HTTPS
        ]);
        if (! AuthService::checkUserIsLogged()) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no autenticado.']);
            return;
        }

        // 4) Leer el JSON del body
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        if (! is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON mal formado.']);
            return;
        }

        // 5) (Opcional) verificar CSRF en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfForm = $data['csrf_token'] ?? '';
            $csrfSes  = $_SESSION['csrf_token'] ?? '';
            if (! hash_equals($csrfSes, $csrfForm)) {
                http_response_code(400);
                echo json_encode(['error' => 'Token CSRF inválido.']);
                return;
            }
        }

        // 6) Dispatch según action
        $action = $data['action'] ?? '';
        switch ($action) {
            case 'listar_deudores':
                $this->listarDeudores();
                break;

            case 'activar_deudor':
                $id = intval($data['id'] ?? 0);
                $this->activarDeudor($id);
                break;

            case 'desactivar_deudor':
                $id = intval($data['id'] ?? 0);
                $this->desactivarDeudor($id);
                break;

            case 'listar_funcionarios':
                $this->listarFuncionarios();
                break;

            case 'activar_funcionario':
                $id = intval($data['id'] ?? 0);
                $this->activarFuncionario($id);
                break;

            case 'desactivar_funcionario':
                $id = intval($data['id'] ?? 0);
                $this->desactivarFuncionario($id);
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Acción no reconocida.']);
                break;
        }
    }

    /**
     * Ejemplo: retorna JSON con todos los deudores activos.
     */
    private function listarDeudores(): void
    {
        try {
            $deudores = Deudor::allActivos(); // Debe devolver array (prepared statement en el modelo)
            echo json_encode(['deudores' => $deudores]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno.', 'details' => $e->getMessage()]);
        }
    }

    /**
     * Ejemplo: activa un deudor vía función almacenada o query preparada.
     */
    private function activarDeudor(int $id): void
    {
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID deudor inválido.']);
            return;
        }

        try {
            // Suponiendo que el modelo Deudor tenga método activate()
            $ok = Deudor::activate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo activar el deudor.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno.', 'details' => $e->getMessage()]);
        }
    }

    private function desactivarDeudor(int $id): void
    {
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID deudor inválido.']);
            return;
        }

        try {
            $ok = Deudor::deactivate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo desactivar el deudor.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno.', 'details' => $e->getMessage()]);
        }
    }

    /**
     * Ejemplo: lista todos los funcionarios
     */
    private function listarFuncionarios(): void
    {
        try {
            $funcionarios = Funcionario::allActivos();
            echo json_encode(['funcionarios' => $funcionarios]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno.', 'details' => $e->getMessage()]);
        }
    }

    private function activarFuncionario(int $id): void
    {
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID funcionario inválido.']);
            return;
        }

        try {
            $ok = Funcionario::activate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo activar el funcionario.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno.', 'details' => $e->getMessage()]);
        }
    }

    private function desactivarFuncionario(int $id): void
    {
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID funcionario inválido.']);
            return;
        }

        try {
            $ok = Funcionario::deactivate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo desactivar el funcionario.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno.', 'details' => $e->getMessage()]);
        }
    }
}
