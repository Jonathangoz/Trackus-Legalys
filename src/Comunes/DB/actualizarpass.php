<?php
// archivo: actualizarpassword.php
// Ejecútalo desde la línea de comandos (CLI):
// php actualizarpassword.php <correo_o_correo_institucional> <nueva_contraseña>

#declare(strict_types=1);

// 1) Autoload de tu proyecto (PSR-4) o incluye manualmente la clase de conexión.
//    Ajusta esta ruta a donde esté tu autoloader o la definición de App\Comunes\DB\conexion:
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Comunes\DB\conexion;

// 2) Validar que reciba exactamente 2 argumentos (además del nombre del script)
if ($argc !== 3) {
    fwrite(STDERR, "Uso: php {$argv[0]} <correo_o_correo_institucional> <nueva_contraseña>\n");
    exit(1);
}

$email       = trim($argv[1]);
$newPassword = $argv[2];

// 3) Validar formato básico de correo (opcional, pero recomendado)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Error: '{$email}' no tiene formato de correo válido.\n");
    exit(2);
}

// 4) Generar el hash con BCrypt usando password_hash()
$passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
if ($passwordHash === false) {
    fwrite(STDERR, "Error: no se pudo generar el hash de la contraseña.\n");
    exit(3);
}

try {
    // 5) Obtener la instancia de conexión (ajusta si tu clase de conexión difiere)
    $db = conexion::instanciaDB(); // Debe devolver un objeto PDO con conexión a PostgreSQL

    // Para acumular cuántas filas actualizamos
    $totalActualizadas = 0;

    // 6) Intentar actualizar en funcionarios (coincida ya sea con correo_institucional o con correo)
    $sqlF = "
        UPDATE funcionarios
           SET password_hash = :passHash
         WHERE correo_institucional = :email
            OR correo = :email
    ";
    $stmtF = $db->prepare($sqlF);
    $stmtF->execute([
        ':passHash' => $passwordHash,
        ':email'    => $email,
    ]);
    $filasF = $stmtF->rowCount();
    $totalActualizadas += $filasF;

    // 7) Intentar actualizar en deudores (coincida con correo)
    $sqlD = "
        UPDATE deudores
           SET password_hash = :passHash
         WHERE correo = :email
    ";
    $stmtD = $db->prepare($sqlD);
    $stmtD->execute([
        ':passHash' => $passwordHash,
        ':email'    => $email,
    ]);
    $filasD = $stmtD->rowCount();
    $totalActualizadas += $filasD;

    // 8) Mostrar el resultado
    echo "Hash generado: {$passwordHash}\n";
    echo "Filas actualizadas en 'funcionarios': {$filasF}\n";
    echo "Filas actualizadas en 'deudores'    : {$filasD}\n";
    echo "Total filas modificadas             : {$totalActualizadas}\n";

    if ($totalActualizadas === 0) {
        echo "Advertencia: no se encontró ningún registro con ese correo.\n";
        exit(4);
    }

    echo "🔐 La contraseña se ha actualizado correctamente.\n";
    exit(0);

} catch (PDOException $e) {
    fwrite(STDERR, "Error PDO al actualizar contraseña: " . $e->getMessage() . "\n");
    exit(5);
}
