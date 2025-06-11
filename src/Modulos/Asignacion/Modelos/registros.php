<?php
# src/Modulos/Asignacion/Modelos/registros.php (Modelo donde hace las consultas con variables y seguras para inyectar al las vistas)
declare(strict_types=1);

namespace App\Modulos\Asignacion\Modelos;

use PDO;

class registros {
    protected PDO $db;

    # Llama instancia singleton del DB para inicair querys
    public function __construct() {
        $this->db = \App\Comunes\DB\conexion::instanciaDB();
    }

    # Querys 
    public function getRegistros(): array {
        $stmt1 = $this->db->query("SELECT COUNT(nombre) FROM entidades WHERE tipo_entidad = 'BANCO'");
        $bancos = $stmt1->fetch(PDO::FETCH_ASSOC)['total_bancos'] ?? 0;

        $stmt2 = $this->db->query("SELECT COUNT(nombre) FROM entidades WHERE tipo_entidad = 'CAMARA_COMERCIO'");
        $camarac = $stmt2->fetch(PDO::FETCH_ASSOC)['total_cc'] ?? 0;

        $stmt3 = $this->db->query("SELECT COUNT(nombre) FROM entidades WHERE tipo_entidad = 'TRANSITO'");
        $transito = $stmt3->fetch(PDO::FETCH_ASSOC)['total_transito'] ?? 0;

        return [
            'totalBancos' => intval($bancos),
            'totalCc' => intval($camarac),
            'totalTransito' => intval($transito),
        ];
    }

    public function insertarRegistros(string $nombre, string $correo, string $rol, int $estado): bool {
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

    public function actualizarRegistros(int $id, string $nombre, string $correo, string $rol, int $estado): bool {
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

    public function archivarRegitros(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM funcionarios WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function activarRegistros(int $id, int $nuevoEstado): bool {
        $stmt = $this->db->prepare("UPDATE funcionarios SET estado = :estado WHERE id = :id");
        return $stmt->execute([
            'id'     => $id,
            'estado' => $nuevoEstado,
        ]);
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