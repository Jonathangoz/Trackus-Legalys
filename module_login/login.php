<?php
session_start();
require 'conexion.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $correo = trim($_POST['correo']);
    $contraseña = trim($_POST['contrasenia']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $tiporol = trim($_POST['tipo_rol']);

    if (empty($correo) || empty($contraseña)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: ../public/loggin.php");
        exit;
    }

    try {
        $stmt = $connect->prepare("SELECT 'funcionario' AS tipo_usuario, id_funcionario AS id, nombres, apellidos, correo, contrasenia, tipo_rol 
            FROM funcionarios WHERE correo = :correo
            UNION ALL
            SELECT 'usuario' AS tipo_usuario, id_usuario AS id, nombres, apellidos, correo, contrasenia, tipo_rol 
            FROM usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($contraseña,  $user['contrasenia'])) {

                $token = bin2hex(random_bytes(64));
                // firma del token con HMAC
                $secretkey = "Jonathan94Goz@";
                $signature = hash_hmac('sha256', $token, $secretkey);
                $signedToken = $token . '.' . $signature;
                // encriptacion del token (cifrado simetrico con AES)
                $iv = random_bytes(16); // vector de inicializacion (iv)
                $encryptedToken = openssl_encrypt($token, 'AES-256-CBC', $secretkey, 0, $iv);

    
                $_SESSION['loggedin'] = true;
                $_SESSION['id_funcionario'] = $user['id_funcionario'];
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['correo'] = $user['correo'];
                $_SESSION['nombres'] = $user['nombres'];
                $_SESSION['apellidos'] = $user['apellidos'];
                $_SESSION['tipo_rol'] = $user['tipo_rol'];
                echo "Autenticación exitosa. Redirigiendo al index...";
                switch ($user['tipo_rol']) {
                    case 'ADMIN':
                        header("Location: ../public/dashboard.php");
                        break;
                    case 'ADMIN_TRAMITE':
                        header("Location: ../moduler/view_process/dashboard_process.php");
                        break;
                    case 'ABOGADO_1':
                        header("Location: ../moduler/view_lawyer/dashboard_lawyer.php");
                        break;
                    case 'ABOGADO_2':
                        header("Location: ../moduler/view_lawyer/dashboard_lawyer.php");
                        break;
                    case 'ABOGADO_3':
                        header("Location: ../moduler/view_lawyer/dashboard_lawyer.php");
                        break;
                    case 'USUARIOS':
                        header("Location: ../moduler/view_user/dashboard_user.php");
                        break;
                    default:
                        $_SESSION['error'] = "Rol no Reconicido.";
                        echo "Error: " . $_SESSION['error'];
                        header("Location: ../public/loggin.php");
                        break;
                }
                exit;
            } else {
                $_SESSION['error'] = "Contraseña incorrecta.";
                echo "Error: " . $_SESSION['error'];
                header("Location: ../public/loggin.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Usuario no encontrado.";
            echo "Error: " . $_SESSION['error'];
            header("Location: ../public/loggin.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        echo "Error en la consulta: " . $_SESSION['error'];
        header("Location: ../public/loggin.php");
        exit;
    }
}