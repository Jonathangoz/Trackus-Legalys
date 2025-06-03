<?php
// src/Modulos/Dashboard/Controladores/API/control_query.php
declare(strict_types=1);

namespace App\Modulos\Dashboard\Controladores;

use App\Modulos\Controladores\controlador_base;
use App\Comunes\seguridad\autenticacion as SeguridadAutenticacion;
use App\Comunes\seguridad\encriptacion as SeguridadEncriptacion;
use App\Modulos\CobroCoactivo\Modelos\obligados_pagos;
use App\Modulos\Dashboard\Modelos\activar_inhabilitar;
use App\Comunes\DB\conexion;

class control_query extends controlador_base
{
    /**
     * Maneja todas las llamadas AJAX/JSON a /api
     */
    public function handle(): void
    {
        // 1) Forzar respuesta JSON
        header('Content-Type: application/json; charset=UTF-8');

        // 2) Obtener token cifrado de la cookie
        $encryptedToken = $_COOKIE['auth_token'] ?? '';
        if (empty($encryptedToken)) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado (cookie ausente).']);
            return;
        }

        // 2.5) Verificar en PostgreSQL que el token exista, no expiró y no fue revocado
        $db = conexion::getInstance();
        $sql = "SELECT user_id
                FROM user_tokens
                WHERE token = :token
                  AND expires_at > NOW()
                  AND revoked = FALSE
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(['token' => $encryptedToken]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (! $row) {
            http_response_code(401);
            echo json_encode(['error' => 'Token no encontrado o expirado (DB).']);
            return;
        }

        // 3) Descifrar y verificar HMAC + expiración en sesión
        $tokenOriginal = SeguridadEncriptacion::decryptAndVerifyWithExpiry($encryptedToken);
        if ($tokenOriginal === null) {
            http_response_code(401);
            echo json_encode(['error' => 'Sesión expirada o token inválido.']);
            return;
        }

        // 4) Validar sesión PHP y rol
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
                'cookie_secure'   => true,
            ]);
        }
        if (! SeguridadAutenticacion::checkUserIsLogged()) {
            http_response_code(401);
            echo json_encode(['error' => 'Sesión no válida.']);
            return;
        }

        // 5) Leer JSON del cuerpo
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        if (! is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON mal formado.']);
            return;
        }

        // 6) Validar CSRF para POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfForm = $data['csrf_token'] ?? '';
            $csrfSes  = $_SESSION['csrf_token'] ?? '';
            if (! hash_equals($csrfSes, $csrfForm)) {
                http_response_code(400);
                echo json_encode(['error' => 'Token CSRF inválido.']);
                return;
            }
        }

        // 7) Dispatch según acción
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

    private function listarDeudores(): void
    {
        try {
            $deudores = obligados_pagos::allActivos();
            echo json_encode(['deudores' => $deudores]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Error interno al listar deudores.',
                'details' => $e->getMessage()
            ]);
        }
    }

    private function activarDeudor(int $id): void
    {
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID deudor inválido.']);
            return;
        }
        try {
            $ok = obligados_pagos::activate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo activar el deudor.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Error interno al activar deudor.',
                'details' => $e->getMessage()
            ]);
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
            $ok = obligados_pagos::deactivate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo desactivar el deudor.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Error interno al desactivar deudor.',
                'details' => $e->getMessage()
            ]);
        }
    }

    private function listarFuncionarios(): void
    {
        try {
            $funcionarios = activar_inhabilitar::allActivos();
            echo json_encode(['funcionarios' => $funcionarios]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Error interno al listar funcionarios.',
                'details' => $e->getMessage()
            ]);
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
            $ok = activar_inhabilitar::activate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo activar el funcionario.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Error interno al activar funcionario.',
                'details' => $e->getMessage()
            ]);
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
            $ok = activar_inhabilitar::deactivate($id);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'No se pudo desactivar el funcionario.']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Error interno al desactivar funcionario.',
                'details' => $e->getMessage()
            ]);
        }
    }
}
