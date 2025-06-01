<?php
session_start();
require '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST['correo']) && isset($_POST['contrasenia']) && isset($_POST['confirmar'])) {
        $correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
        $nuevacontraseña = trim($_POST['contrasenia']);
        $confirmarContraseña = trim($_POST['confirmar']);

        if (!$correo) {
            $_SESSION['error3'] = "Correo electrónico inválido.";
            header("Location: verification.php");
            exit;
        }

        $dominio = explode('@', $correo)[1] ?? '';
        if (!checkdnsrr($dominio, 'MX')) {
            $_SESSION['error3'] = "El dominio del correo no existe";
            header("Location: verification.php");
            exit;
        }

        $connect->exec("SET NAMES 'utf8'");
        $stmt = $connect->prepare("SELECT 'funcionario' AS tipo FROM funcionarios WHERE correo = :correo 
                                    UNION ALL 
                                    SELECT 'usuario' FROM usuarios WHERE correo = :correo 
                                    LIMIT 1");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount() == 0) { // Si no hay resultados
            $_SESSION['error3'] = "No se encontró ningún usuario con ese correo.";
            header("Location: verification.php");
        exit;
        }

        try {
            $connect->beginTransaction();

            // Hashear la nueva contraseña
            $nueva_contraseña = password_hash($nuevacontraseña, PASSWORD_DEFAULT);

            // 1. Actualizar en funcionarios (si existe el correo)
            $updateFuncionarios = $connect->prepare("UPDATE funcionarios SET contrasenia = :contrasenia WHERE correo = :correo 
                                                    AND EXISTS (SELECT 1 FROM funcionarios WHERE correo = :correo)");
            $updateFuncionarios->bindParam(':contrasenia', $nueva_contraseña);
            $updateFuncionarios->bindParam(':correo', $correo);
            $updateFuncionarios->execute();

            // 2. Actualizar en usuarios (si existe el correo)
            $updateUsuarios = $connect->prepare("UPDATE usuarios SET contrasenia = :contrasenia WHERE correo = :correo 
                                                AND EXISTS (SELECT 1 FROM usuarios WHERE correo = :correo)");
            $updateUsuarios->bindParam(':contrasenia', $nueva_contraseña);
            $updateUsuarios->bindParam(':correo', $correo);
            $updateUsuarios->execute();

            if ($updateUsuarios->rowCount() > 0) {
                $_SESSION['success'] = "Contraseña actualizada correctamente.";
                header("Location: ../../logging.php");
            } elseif ($updateFuncionarios->rowCount() > 0) {
                $_SESSION['success'] = "Contraseña actualizada correctamente.";
                header("Location: ../../logging.php");
            } else {
                $_SESSION['error3'] = "No se encontró ningún usuario con ese correo.";
                header("Location: verification.php");
                exit;
            }

            $connect->commit();
            echo "Contraseña actualizada";
        } catch (PDOException $e) {
            $connect->rollBack(); // Revierte cambios en caso de error
            echo "Error: " . $e->getMessage();
            $_SESSION['error3'] = "Error en la consulta: " . $e->getMessage();
            header("Location: verification.php");
            exit;
        }
     
    } else {
        $_SESSION['error3'] = "Todos los campos son obligatorios.";
        header("Location: verification.php");
       exit;
    } 
}
