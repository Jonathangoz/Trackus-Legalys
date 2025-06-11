<?php
# src/Modulos/Dashboard/modelos/DashboardModel.php
#declare(strict_types=1);

namespace App\Modulos\Dashboard\Modelos;

use PDO;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;
use Monolog\Level;
use Throwable;

class ModeloDashboard {
    protected PDO $db;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        $this->logger = loggers::createLogger();
        $this->logger->info("💼 ModeloDashboard::__construct() inicializado");
        try {
            $this->db = \App\Comunes\DB\conexion::instanciaDB();
            $this->logger->info("✔ Conexión a la base de datos establecida");
        } catch (Throwable $e) {
            $this->logger->error("❌ Error al obtener instancia de DB: " . $e->getMessage());
            throw $e;
        }
    }

    public function getEntidades(): array {
        $this->logger->info("📊 ModeloDashboard::getEntidades() invocado");

        try {
            // BANCO
            $this->logger->debug("Ejecutando COUNT de entidades tipo 'BANCO'");
            $stmt1 = $this->db->query("SELECT COUNT(nombre) AS total_bancos FROM entidades WHERE tipo_entidad = 'BANCO'");
            $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
            $bancos = intval($row1['total_bancos'] ?? 0);
            $this->logger->debug("Resultado COUNT BANCO: {$bancos}");

            // CAMARA DE COMERCIO
            $this->logger->debug("Ejecutando COUNT de entidades tipo 'CAMARA_COMERCIO'");
            $stmt2 = $this->db->query("SELECT COUNT(nombre) AS total_cc FROM entidades WHERE tipo_entidad = 'CAMARA_COMERCIO'");
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            $camarac = intval($row2['total_cc'] ?? 0);
            $this->logger->debug("Resultado COUNT CAMARA_COMERCIO: {$camarac}");

            // TRANSITO
            $this->logger->debug("Ejecutando COUNT de entidades tipo 'TRANSITO'");
            $stmt3 = $this->db->query("SELECT COUNT(nombre) AS total_transito FROM entidades WHERE tipo_entidad = 'TRANSITO'");
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            $transito = intval($row3['total_transito'] ?? 0);
            $this->logger->debug("Resultado COUNT TRANSITO: {$transito}");

            $resultado = [
                'totalBancos'     => $bancos,
                'totalCc'         => $camarac,
                'totalTransito'   => $transito,
            ];
            $this->logger->info("✅ getEntidades() completado correctamente", $resultado);
            return $resultado;
        } catch (Throwable $e) {
            $this->logger->error("❌ Error en getEntidades(): " . $e->getMessage());
            return [
                'totalBancos'   => 0,
                'totalCc'       => 0,
                'totalTransito' => 0,
            ];
        }
    }

    public function getAllFuncionarios(): array {
        $this->logger->info("👥 ModeloDashboard::getAllFuncionarios() invocado");

        try {
            $stmt = $this->db->query("SELECT * FROM funcionarios ORDER BY id DESC");
            $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = count($todos);
            $this->logger->info("✅ getAllFuncionarios() devolvió {$count} filas");
            return $todos;
        } catch (Throwable $e) {
            $this->logger->error("❌ Error en getAllFuncionarios(): " . $e->getMessage());
            return [];
        }
    }

    public function getFuncionarioById(int $id): ?array {
        $this->logger->info("🔍 ModeloDashboard::getFuncionarioById() invocado", ['id' => $id]);

        try {
            $stmt = $this->db->prepare("SELECT * FROM funcionarios WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {
                $this->logger->info("✅ Funcionario encontrado con id={$id}");
                return $fila;
            } else {
                $this->logger->warning("⚠️ No se encontró funcionario con id={$id}");
                return null;
            }
        } catch (Throwable $e) {
            $this->logger->error("❌ Error en getFuncionarioById(id={$id}): " . $e->getMessage());
            return null;
        }
    }

    public function insertarFuncionario(string $nombre, string $correo, string $rol, int $estado): bool {
        $this->logger->info("➕ ModeloDashboard::insertarFuncionario() invocado", [
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado,
        ]);

        try {
            $sql = "INSERT INTO funcionarios (nombre, correo, rol, estado, creado_en)
                    VALUES (:nombre, :correo, :rol, :estado, NOW())";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                'nombre' => $nombre,
                'correo' => $correo,
                'rol'    => $rol,
                'estado' => $estado,
            ]);

            if ($resultado) {
                $lastId = intval($this->db->lastInsertId());
                $this->logger->info("✅ Funcionario insertado correctamente con id={$lastId}");
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                $this->logger->warning("⚠️ No se pudo insertar funcionario: " . implode(" | ", $errorInfo));
                return false;
            }
        } catch (Throwable $e) {
            $this->logger->error("❌ Excepción en insertarFuncionario(): " . $e->getMessage());
            return false;
        }
    }

    public function actualizarFuncionario(int $id, string $nombre, string $correo, string $rol, int $estado): bool {
        $this->logger->info("✏️ ModeloDashboard::actualizarFuncionario() invocado", [
            'id'     => $id,
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado,
        ]);

        try {
            $sql = "UPDATE funcionarios
                    SET nombre = :nombre, correo = :correo, rol = :rol, estado = :estado, actualizado_en = NOW()
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                'id'     => $id,
                'nombre' => $nombre,
                'correo' => $correo,
                'rol'    => $rol,
                'estado' => $estado,
            ]);

            if ($resultado) {
                $this->logger->info("✅ Funcionario con id={$id} actualizado correctamente");
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                $this->logger->warning("⚠️ No se pudo actualizar funcionario id={$id}: " . implode(" | ", $errorInfo));
                return false;
            }
        } catch (Throwable $e) {
            $this->logger->error("❌ Excepción en actualizarFuncionario(id={$id}): " . $e->getMessage());
            return false;
        }
    }

    public function eliminarFuncionario(int $id): bool {
        $this->logger->info("🗑️ ModeloDashboard::eliminarFuncionario() invocado", ['id' => $id]);

        try {
            $stmt = $this->db->prepare("DELETE FROM funcionarios WHERE id = :id");
            $resultado = $stmt->execute(['id' => $id]);

            if ($resultado) {
                $this->logger->info("✅ Funcionario con id={$id} eliminado correctamente");
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                $this->logger->warning("⚠️ No se pudo eliminar funcionario id={$id}: " . implode(" | ", $errorInfo));
                return false;
            }
        } catch (Throwable $e) {
            $this->logger->error("❌ Excepción en eliminarFuncionario(id={$id}): " . $e->getMessage());
            return false;
        }
    }

    public function activarFuncionario(int $id, int $nuevoEstado): bool {
        $this->logger->info("🔄 ModeloDashboard::activarFuncionario() invocado", [
            'id'         => $id,
            'nuevoEstado'=> $nuevoEstado,
        ]);

        try {
            $stmt = $this->db->prepare("UPDATE funcionarios SET estado = :estado WHERE id = :id");
            $resultado = $stmt->execute([
                'id'     => $id,
                'estado' => $nuevoEstado,
            ]);

            if ($resultado) {
                $this->logger->info("✅ Estado de funcionario id={$id} cambiado a {$nuevoEstado}");
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                $this->logger->warning("⚠️ No se pudo activar/desactivar funcionario id={$id}: " . implode(" | ", $errorInfo));
                return false;
            }
        } catch (Throwable $e) {
            $this->logger->error("❌ Excepción en activarFuncionario(id={$id}): " . $e->getMessage());
            return false;
        }
    }

    public function getLogAuditoria(): array {
        $this->logger->info("📜 ModeloDashboard::getLogAuditoria() invocado");

        try {
            $stmt = $this->db->query("SELECT * FROM auditoria ORDER BY timestamp DESC LIMIT 100");
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = count($logs);
            $this->logger->info("✅ getLogAuditoria() devolvió {$count} registros");
            return $logs;
        } catch (Throwable $e) {
            $this->logger->error("❌ Error en getLogAuditoria(): " . $e->getMessage());
            return [];
        }
    }

    public function getEstadisticas(): array {
        $this->logger->info("📈 ModeloDashboard::getEstadisticas() invocado");

        try {
            $stmt = $this->db->query("
                SELECT 
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) AS tramites_activos,
                    SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) AS tramites_finalizados,
                    COUNT(*) AS tramites_total
                FROM tramites
            ");
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fila === false) {
                $this->logger->warning("⚠️ getEstadisticas() no devolvió ningún resultado");
                return [];
            }

            $estadisticas = [
                'tramites_activos'     => intval($fila['tramites_activos'] ?? 0),
                'tramites_finalizados' => intval($fila['tramites_finalizados'] ?? 0),
                'tramites_total'       => intval($fila['tramites_total'] ?? 0),
            ];
            $this->logger->info("✅ getEstadisticas() completado", $estadisticas);
            return $estadisticas;
        } catch (Throwable $e) {
            $this->logger->error("❌ Excepción en getEstadisticas(): " . $e->getMessage());
            return [];
        }
    }
}