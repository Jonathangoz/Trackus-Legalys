<?php
/**
 * Script de Inicio de Sesión
 * 
 * Este script maneja la autenticación de usuarios verificando las credenciales 
 * contra la base de datos. Si tiene éxito, crea una sesión y genera un token 
 * firmado y encriptado para el usuario.
 */

session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /**
     * @var string $correo Dirección de correo electrónico del usuario desde la solicitud POST.
     * @var string $contraseña Contraseña del usuario desde la solicitud POST.
     * @var string $nombres Nombre del usuario desde la solicitud POST.
     * @var string $apellidos Apellido del usuario desde la solicitud POST.
     * @var string $tiporol Tipo de rol del usuario desde la solicitud POST.
     */
    $correo = trim($_POST['correo']);
    $contraseña = trim($_POST['contrasenia']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $tiporol = trim($_POST['tipo_rol']);

    // Validar los campos de entrada
    if (empty($correo) || empty($contraseña)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: ../loggin.php");
        exit;
    }

    try {
        /**
         * Preparar y ejecutar una consulta para obtener los datos del usuario por correo electrónico.
         * 
         * @var PDOStatement $stmt Declaración preparada para la consulta SQL.
         */
        $stmt = $connect->prepare("SELECT id_funcionario, correo, contrasenia, nombres, apellidos, tipo_rol FROM funcionarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        // Verificar si el usuario existe
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña
            if (password_verify($contraseña, $user['contrasenia'])) {
                /**
                 * Generar un token seguro para la sesión.
                 * 
                 * @var string $token Token generado aleatoriamente.
                 * @var string $secretkey Contraseña del usuario utilizada como clave secreta.
                 * @var string $signature Firma HMAC para el token.
                 * @var string $signedToken Token firmado con la firma HMAC.
                 * @var string $iv Vector de inicialización para la encriptación AES.
                 * @var string $encryptedToken Token encriptado usando AES-256-CBC.
                 */
                $token = bin2hex(random_bytes(64));
                $secretkey = $contraseña;
                $signature = hash_hmac('sha256', $token, $secretkey);
                $signedToken = $token . '.' . $signature;
                $iv = random_bytes(32);
                $encryptedToken = openssl_encrypt($token, 'AES-256-CBC', $secretkey, 0, $iv);

                // Establecer variables de sesión
                $_SESSION['loggedin'] = true;
                $_SESSION['id_funcionario'] = $user['id_funcionario'];
                $_SESSION['correo'] = $user['correo'];
                $_SESSION['nombres'] = $user['nombres'];
                $_SESSION['apellidos'] = $user['apellidos'];
                $_SESSION['tipo_rol'] = $user['tipo_rol'];

                echo "Autenticación exitosa. Redirigiendo al index...";
                header("Location: ../index.php");
                exit;
            } else {
                // Manejar contraseña incorrecta
                $_SESSION['error'] = "Contraseña incorrecta.";
                echo "Error: " . $_SESSION['error'];
                header("Location: ../loggin.php");
                exit;
            }
        } else {
            // Manejar usuario no encontrado
            $_SESSION['error'] = "Usuario no encontrado.";
            echo "Error: " . $_SESSION['error'];
            header("Location: ../loggin.php");
            exit;
        }
    } catch (PDOException $e) {
        // Manejar errores de base de datos
        $_SESSION['error'] = "Error: " . $e->getMessage();
        echo "Error en la consulta: " . $_SESSION['error'];
        header("Location: ../loggin.php");
        exit;
    }
}
