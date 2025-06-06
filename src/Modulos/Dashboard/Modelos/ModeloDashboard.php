<?php
// src/Modulos/Dashboard/modelos/DashboardModel.php
declare(strict_types=1);

namespace App\Modulos\Dashboard\Modelos;

use PDO;

class ModeloDashboard {
    protected PDO $db;

    public function __construct() {
        $this->db = \App\Comunes\DB\conexion::instanciaDB();
    }

    public function getResumen(): array {
        $stmt1 = $this->db->query("SELECT COUNT(*) AS total_func FROM funcionarios");
        $func = $stmt1->fetch(PDO::FETCH_ASSOC)['total_func'] ?? 0;

        $stmt2 = $this->db->query("SELECT COUNT(*) AS total_proc FROM procesos");
        $proc = $stmt2->fetch(PDO::FETCH_ASSOC)['total_proc'] ?? 0;

        $stmt3 = $this->db->query("SELECT COUNT(*) AS total_deud FROM deudores");
        $deud = $stmt3->fetch(PDO::FETCH_ASSOC)['total_deud'] ?? 0;

        return [
            'totalFunc' => intval($func),
            'totalProc' => intval($proc),
            'totalDeud' => intval($deud),
        ];
    }

    public function getAllFuncionarios(): array {
        $stmt = $this->db->query("SELECT * FROM funcionarios ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFuncionarioById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM funcionarios WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function insertarFuncionario(string $nombre, string $correo, string $rol, int $estado): bool {
        $sql = "INSERT INTO funcionarios (nombre, correo, rol, estado, creado_en)
                VALUES (:nombre, :correo, :rol, :estado, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado,
        ]);
    }

    public function actualizarFuncionario(int $id, string $nombre, string $correo, string $rol, int $estado): bool {
        $sql = "UPDATE funcionarios
                SET nombre = :nombre, correo = :correo, rol = :rol, estado = :estado, actualizado_en = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'     => $id,
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado,
        ]);
    }

    public function eliminarFuncionario(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM funcionarios WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function activarFuncionario(int $id, int $nuevoEstado): bool {
        $stmt = $this->db->prepare("UPDATE funcionarios SET estado = :estado WHERE id = :id");
        return $stmt->execute([
            'id'     => $id,
            'estado' => $nuevoEstado,
        ]);
    }

    public function getLogAuditoria(): array {
        $stmt = $this->db->query("SELECT * FROM auditoria ORDER BY timestamp DESC LIMIT 100");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEstadisticas(): array {
        $stmt = $this->db->query("
            SELECT 
                SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) AS tramites_activos,
                SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) AS tramites_finalizados,
                COUNT(*) AS tramites_total
            FROM tramites
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}

