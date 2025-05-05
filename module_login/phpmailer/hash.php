<?php
require 'conexion.php'; // Asegúrate de incluir tu archivo de conexión a la base de datos

try {
    // Obtener todas las contraseñas en texto plano
    $stmt = $connect->query("SELECT id_funcionario, contraseña FROM funcionarios");
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($funcionarios as $funcionario) {
        $id_funcionario = $funcionario['id_funcionario'];
        $contraseña_plana = $funcionario['contraseña'];

        // Generar el hash de la contraseña
        $hashed_password = password_hash($contraseña_plana, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $update_stmt = $connect->prepare("UPDATE funcionarios SET contraseña = :hashed_password WHERE id_funcionario = :id_funcionario");
        $update_stmt->bindParam(':hashed_password', $hashed_password);
        $update_stmt->bindParam(':id_funcionario', $id_funcionario);
        $update_stmt->execute();
    }

    echo "Todas las contraseñas han sido actualizadas a hashes.";
} catch (PDOException $e) {
    die("Error al actualizar las contraseñas: " . $e->getMessage());
}