<?php
/**
 * recover.php
 * 
 * Este archivo maneja la lógica para la recuperación de contraseñas de usuarios.
 * Permite a los usuarios restablecer su contraseña proporcionando un correo electrónico válido.
 * 
 * Flujo del script:
 * 1. Verifica si la solicitud es de tipo POST.
 * 2. Valida que los campos requeridos (correo, nueva contraseña y confirmación) estén presentes.
 * 3. Valida el formato del correo electrónico y verifica que el dominio exista.
 * 4. Busca el correo en la base de datos para asegurarse de que el usuario existe.
 * 5. Si el usuario existe, actualiza la contraseña en la base de datos utilizando un hash seguro.
 * 6. Redirige al usuario con mensajes de éxito o error según corresponda.
 * 
 * Variables de sesión utilizadas:
 * - $_SESSION['error3']: Almacena mensajes de error relacionados con la recuperación de contraseña.
 * - $_SESSION['success']: Almacena el mensaje de éxito cuando la contraseña se actualiza correctamente.
 * 
 * Dependencias:
 * - Requiere el archivo 'module_login/conexion.php' para la conexión a la base de datos.
 * 
 * Notas:
 * - Se utiliza PDO para consultas seguras a la base de datos.
 * - Las contraseñas se almacenan utilizando password_hash para mayor seguridad.
 */

session_start();
require 'module_login/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST['correo']) && isset($_POST['contrasenia']) && isset($_POST['confirmar'])) {
        $correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
        $nuevacontraseña = trim($_POST['contrasenia']);
        $confirmarContraseña = trim($_POST['confirmar']);

        if (!$correo) {
            $_SESSION['error3'] = "Correo electrónico inválido.";
            header("Location: account_recovery.php");
            exit;
        }

        $dominio = explode('@', $correo)[1] ?? '';
        if (!checkdnsrr($dominio, 'MX')) {
            $_SESSION['error3'] = "El dominio del correo no existe";
            header("Location: account_recovery.php");
            exit;
        }

        $connect->exec("SET NAMES 'utf8'");
        $stmt = $connect->prepare("SELECT * FROM funcionarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $funcionarios = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$funcionarios) {
            $_SESSION['error3'] = "No se encontró ningún usuario con ese correo.";
            header("Location: account_recovery.php");
            exit;
        }

        try {
            $nueva_contraseña = password_hash($nuevacontraseña, PASSWORD_DEFAULT);
            $updateStmt = $connect->prepare("UPDATE funcionarios SET contrasenia = :contrasenia WHERE correo = :correo");
            $updateStmt->bindParam(':contrasenia', $nueva_contraseña);
            $updateStmt->bindParam(':correo', $correo);
            $updateStmt->execute();



            if ($updateStmt->rowCount() > 0) {
                $_SESSION['success'] = "Contraseña actualizada correctamente.";
                header("Location: loggin.php");
                exit;
            } else {
                $_SESSION['error3'] = "No se encontró ningún usuario con ese correo.";
                header("Location: account_recovery.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error3'] = "Error en la consulta: " . $e->getMessage();
            header("Location: account_recovery.php");
            exit;
        }
     
    } else {
        $_SESSION['error3'] = "Todos los campos son obligatorios.";
        header("Location: account_recovery.php");
       exit;
    } 
}