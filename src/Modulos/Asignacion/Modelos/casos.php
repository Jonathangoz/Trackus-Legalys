<?php
# src/Modulos/Asignacion/Modelos/registros.php (Modelo donde hace las consultas con variables y seguras para inyectar al las vistas)
declare(strict_types=1);

namespace App\Modulos\Asignacion\Modelos;

use PDO;

class casos {
    protected PDO $db;

    # Llama instancia singleton del DB para inicair querys
    public function __construct() {
        $this->db = \App\Comunes\DB\conexion::instanciaDB();
    }

    # Querys 
    public function getCaso(): array {
        $stmt1 = $this->db->query("SELECT COUNT(nombre) FROM entidades WHERE tipo_entidad = 'BANCO'");
        $bancos = $stmt1->fetch(PDO::FETCH_ASSOC)['total_bancos'] ?? 0;

        return [
            'totalBancos' => intval($bancos),

        ];
    }

    public function insertCaso(string $nombre, string $correo, string $rol, int $estado): bool {
        $sql = "INSERT INTO casos ( radicado, nombre, correo, rol, estado, creado_en)
                VALUES (:nombre, :correo, :rol, :estado, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado,
        ]);
    }

    // Método para crear un nuevo caso
    public function crearCaso(array $datos): bool { 
        $sql = "INSERT INTO casos_coactivo (
            radicado, 
            tipo_persona, 
            nombre_completo, 
            identificacion, 
            tipo_tramite, 
            estado, 
            descripcion, 
            monto_original, 
            intereses, 
            costos, 
            monto_total, 
            fecha_creacion, 
            fecha_asignacion, 
            fecha_limite_pago, 
            fecha_cierre,
            numero_factura
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['radicado'],
                $datos['tipo_persona'],
                $datos['nombre'],
                $datos['identificacion'],
                $datos['tipo_tramite'],
                $datos['estado'],
                $datos['descripcion'],
                $datos['monto_original'],
                $datos['intereses'],
                $datos['costos'],
                $datos['monto_total'],
                $datos['fecha_creacion'],
                $datos['fecha_asignacion'],
                $datos['fecha_limite_pago'],
                $datos['fecha_cierre'],
                $datos['numero_factura'] ?? null
            ]);
    }

    public function actualizarCaso(int $id, string $nombre, string $correo, string $rol, int $estado): bool {
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

    public function actualizarCasos(int $id, int $nuevoEstado): bool {
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