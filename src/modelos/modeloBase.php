<?php
// src/Models/User.php
declare(strict_types=1);

namespace App\modelos;

use App\DB\conexion;
use PDO;
use PDOException;

class modeloBase
{
    public int    $id;
    public string $tipo_usuario;   // 'funcionario' o 'usuario'
    public string $correo;
    public string $password_hash;  // columna "contrasenia"
    public string $nombres;
    public string $apellidos;
    public string $tipo_rol;

    /**
     * Construye la instancia a partir de un array (fila de BD).
     */
    public function __construct(array $row)
    {
        $this->tipo_usuario  = $row['tipo_usuario']; // 'funcionario' o 'usuario'
        $this->id            = (int) $row['id'];
        $this->correo        = $row['correo'];
        $this->password_hash = $row['contrasenia'];
        $this->nombres       = $row['nombres'];
        $this->apellidos     = $row['apellidos'];
        $this->tipo_rol      = $row['tipo_rol'];
    }

    /**
     * Busca un usuario activo (funcionario o usuario) por correo.
     * Retorna null si no existe ninguno.
     */
    public static function findByEmail(string $correo): ?self
    {
        $db = conexion::getInstance();
        // Usamos UNION ALL para unir ambas tablas, limitando a 1 fila
        $sql = "
            SELECT 'funcionario' AS tipo_usuario,
                   id_funcionario AS id,
                   nombres,
                   apellidos,
                   correo,
                   contrasenia,
                   tipo_rol
              FROM funcionarios
             WHERE correo = :correo
               AND activo = TRUE
            UNION ALL
            SELECT 'usuario' AS tipo_usuario,
                   id_usuario AS id,
                   nombres,
                   apellidos,
                   correo,
                   contrasenia,
                   tipo_rol
              FROM usuarios
             WHERE correo = :correo
               AND activo = TRUE
             LIMIT 1
        ";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(['correo' => $correo]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (! $row) {
                return null;
            }
            return new self($row);
        } catch (PDOException $e) {
            // Puedes loguear el error aquí, pero no exponerlo al usuario.
            error_log("User::findByEmail error: " . $e->getMessage());
            return null;
        }
    }
}
