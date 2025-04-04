<?php
session_start();
include 'module_login/conexion.php';

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    
    if (isset($_POST['correo'])) {
        // Validar el formato del correo electrónico
        $correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);

        if (!$correo) {
            // Redirigir si el correo es inválido
            $_SESSION['error'] = "Correo electrónico inválido.";
            header("Location: account_recovery.php");
            exit;
        }

        // Verificar si el dominio del correo existe
        $dominio = explode('@', $correo)[1] ?? '';
        if (!checkdnsrr($dominio, 'MX')) {
            $_SESSION['error'] = "El dominio del correo no existe";
            header("Location: account_recovery.php");
            exit;
        }

        // Consultar la base de datos para encontrar al usuario con el correo proporcionado
        $connect->exec("SET NAMES 'utf8'");
        $stmt = $connect->prepare("SELECT * FROM funcionarios WHERE correo = :correo /*AND STATUS = 1*/");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $funcionarios = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$funcionarios) {
            // Redirigir si no se encuentra un usuario
            $_SESSION['error'] = "No se encontró ningún usuario con ese correo.";
            header("Location: account_recovery.php");
            exit;
        }
    }

    if ($correo) {
        // Generar un token seguro para la recuperación de contraseña
        $token = bin2hex(random_bytes(64));
        $expiration = time() + 900; // Tiempo de expiración del token (15 minutos)
        $tokenData = $token . '.' . $expiration;

        // Firmar el token usando HMAC
        $secretkey = $contraseña;
        $signature = hash_hmac('sha256', $token, $secretkey);
        $signedToken = $token . '.' . $signature;

        // Encriptar el token usando AES-256-CBC
        $iv = random_bytes(16); // Vector de inicialización
        $encryptedToken = openssl_encrypt($token, 'AES-256-CBC', $secretkey, 0, $iv);

        // Configurar PHPMailer para enviar el correo de recuperación
        $mail = new PHPMailer(true);
        
        try {
            // Configuración de SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jonathan942115@gmail.com'; // Tu correo
            $mail->Password = 'aboq yedx fwxp xjhj'; // Contraseña de aplicación de Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuración del correo
            $mail->setFrom('noreply@gmail.com', 'pruebas');
            $mail->addAddress($correo, 'pruebas');
            $mail->addReplyTo('support@gmail.com', 'pruebas');
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Subject = 'Restablecer contraseña';
            $mail->AltBody = 'Restablecer contraseña'; // Texto alternativo para clientes de correo que no soportan HTML

            // Generar el enlace de restablecimiento de contraseña
            $reset_link = "http://localhost/tl/tl/account_recovery.php?token=$signedToken&iv=" . bin2hex($iv) . "&email=" . urlencode($correo);
            
            // Contenido del cuerpo del correo
            $mail->Body = "
                <html>
                <head>
                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
                </head>
                <body>
                    <h1>Restablece tu contraseña</h1>
                    <p>Haz clic en el siguiente enlace:</p>
                    <a href=\"$reset_link\">Restablecer contraseña</a>
                    <p>Si no solicitaste esto, ignora este correo.</p>
                </body>
                </html>
            ";

            // Enviar el correo
            $mail->send();
            $_SESSION['message'] = "Correo enviado correctamente";
        } catch (Exception $e) {
            // Manejar errores al enviar el correo
            $_SESSION['error2'] = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
        
        // Redirigir a la página de inicio de sesión después de enviar el correo
        header("Location: loggin.php");
        exit();
    }
}