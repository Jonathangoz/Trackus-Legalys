<?php
// src/Modulos/Dashboard/Controladores/control_Dashboard.php
#declare(strict_types=1);

namespace App\Modulos\Deudores\Controladores;

use App\Modulos\Controladores\controlador_base;
use App\Modulos\Deudores\Modelos\obligados_pagos;
use App\Modulos\Deudores\Modelos\deudores;
use App\Comunes\seguridad\autenticacion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Deudores extends controlador_base {
    protected deudores $modeloDeudores;
    protected obligados_pagos $modeloObligadosPagos;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        // Inicializar modelo y logger
        $this->modeloDeudores = new deudores();
        $this->modeloObligadosPagos = new obligados_pagos();
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que inicien en /dashboard
     * @param string $uri
     * @param string $method
     */
    public function handle(string $uri, string $method): void {
        # Para evitar distinciones de mayúsculas/minúsculas:
        $path = strtolower($uri);
        $this->logger->info("🏷️  control_deudores::handle() invocado para: {$method} {$path}");

        // Verificar autenticación y rol ADMIN_TRAMITE (redundante pero seguro)
        # Revisar si esta logueado y tiene el rol correcto
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado en Deudores, redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # Revisar el rol del usuario
        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ABOGADO') {
            $this->logger->warning("🚫 Usuario sin rol ABOGADO en Deudores. Rol actual: {$rol}, redirigiendo a /login");
            $this->redirect('/login');
            return;
        }

        #Redirige al modelo segun metodo, uri y lo redirecciona a la funcion principal del modelo, recorriendo lo necesario.
        $rutas = "{$method} " . strtolower(rtrim($uri, '/'));
        switch ($rutas) {
            case 'GET /deudores':
            case 'POST /deudores':
                $this->listarDeudores();
                return;

            case 'GET /deudores/obligados':
            case 'POST /deudores/obligados':
                $this->listarUsuarios();
                return;

        default:
            $this->logger->warning("❓ cobrocoactivo::handle(): ruta no encontrada ({$uri})");
            http_response_code(404);
            echo "Ruta no encontrada: {$uri}";
            return;
        }
    }

    /** GET /deudores */
    protected function listarDeudores(): void {
        $datosDeu = [
            'deudores' => $this->modeloDeudores->getDeudores(),
        ];
        extract($datosDeu);
        require_once __DIR__ . '/../Vistas/Deudores.php';
    }

    /** GET /deudores/obligados */
    protected function listarUsuarios(): void {
        $datosUsuario = [
            'usuarios' => $this->modeloObligadosPagos->getUsuarios(),
        ];
        extract($datosUsuario);
        require_once __DIR__ . '/../Vistas/usuario.php';
    }

    /** POST /dashboard/funcionarios */
 /*   protected function crear(): void {

        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $rol    = trim($_POST['rol'] ?? '');
        $estado = isset($_POST['estado']) ? 1 : 0;

        $errores = [];
        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido.';
        }
        if ($rol === '') {
            $errores[] = 'El rol es obligatorio.';
        }

        if (!empty($errores)) {
            $this->logger->warning("⚠️ crear(): errores de validación", ['errores' => $errores]);
            $_SESSION['dashboard_errors'] = $errores;
            header('Location: /dashboard/funcionarios/crear');
            exit;
        }

        $this->modelo->insertarFuncionario($nombre, $correo, $rol, $estado);
        $_SESSION['dashboard_message'] = 'Funcionario creado correctamente.';
        header('Location: /dashboard/funcionarios');
        exit;
    }

    /** GET /dashboard/funcionarios/editar?id=XX */
 /*   protected function editarForm(): void {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->logger->warning("⚠️ editarForm(): ID inválido (<=0), redirigiendo");
            header('Location: /dashboard/funcionarios');
            exit;
        }
        $func = $this->modelo->getFuncionarioById($id);
        if (!$func) {
            $this->logger->warning("⚠️ editarForm(): funcionario no encontrado para ID={\$id}");
            header('Location: /dashboard/funcionarios');
            exit;
        }
        require __DIR__ . '/../vistas/dashboard.php';
    }

    /** POST /dashboard/funcionarios/editar */
  /*  protected function editar(): void {

        $id     = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $rol    = trim($_POST['rol'] ?? '');
        $estado = isset($_POST['estado']) ? 1 : 0;

        $errores = [];
        if ($id <= 0) {
            $errores[] = 'ID de funcionario inválido.';
        }
        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido.';
        }
        if ($rol === '') {
            $errores[] = 'El rol es obligatorio.';
        }

        if (!empty($errores)) {
            $this->logger->warning("⚠️ editar(): errores de validación", ['errores' => $errores]);
            $_SESSION['dashboard_errors'] = $errores;
            header("Location: /dashboard/funcionarios/editar?id={$id}");
            exit;
        }

        $this->modelo->actualizarFuncionario($id, $nombre, $correo, $rol, $estado);
        $_SESSION['dashboard_message'] = 'Funcionario actualizado correctamente.';
        header('Location: /dashboard/funcionarios');
        exit;
    }

    /** POST /dashboard/funcionarios/eliminar */
 /*  protected function eliminar(): void {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->modelo->eliminarFuncionario($id);
            $_SESSION['dashboard_message'] = 'Funcionario eliminado correctamente.';
        } else {
            $this->logger->warning("⚠️ eliminar(): ID inválido (<=0)");
        }
        header('Location: /dashboard/funcionarios');
        exit;
    }

    /** POST /dashboard/funcionarios/activar */
  /*  protected function activar(): void {
        $id     = intval($_POST['id'] ?? 0);
        $estado = ($_POST['estado'] ?? '0') === '1' ? 1 : 0;
        if ($id > 0) {
            $this->modelo->activarFuncionario($id, $estado);
            if ($estado) {
                $_SESSION['dashboard_message'] = 'Funcionario activado correctamente.';
            } else {
                $_SESSION['dashboard_message'] = 'Funcionario desactivado correctamente.';
            }
        } else {
            $this->logger->warning("⚠️ activar(): ID inválido (<=0)");
        }
        header('Location: /dashboard/funcionarios');
        exit;
    } */
}