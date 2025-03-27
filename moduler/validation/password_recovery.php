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
        $correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);

        if (!$correo) {
            $_SESSION['error'] = "Correo electrónico inválido.";
            header("Location: account_recovery.php");
            exit;
        }

        $dominio = explode('@', $correo)[1] ?? '';
        if (!checkdnsrr($dominio, 'MX')) {
            $_SESSION['error'] = "El dominio del correo no existe";
            header("Location: account_recovery.php");
            exit;
        }

        $connect->exec("SET NAMES 'utf8'");
        $stmt = $connect->prepare("SELECT * FROM funcionarios WHERE correo = :correo /*AND STATUS = 1*/");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $funcionarios = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$funcionarios) {
            $_SESSION['error'] = "No se encontró ningún usuario con ese correo.";
            header("Location: account_recovery.php");
            exit;
        }
    }

    if ($correo) {
        $token = bin2hex(random_bytes(64));
        $expiration = time() + 900; // 15 minutos
        $tokenData = $token . '.' . $expiration;
        // firma del token con HMAC
        $secretkey = $contraseña;
        $signature = hash_hmac('sha256', $token, $secretkey);
        $signedToken = $token . '.' . $signature;
        // encriptacion del token (cifrado simetrico con AES)
        $iv = random_bytes(16); // vector de inicializacion (iv)
        $encryptedToken = openssl_encrypt($token, 'AES-256-CBC', $secretkey, 0, $iv);

        // Configurar PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Configuración SMTP (Ejemplo para Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jonathan942115@gmail.com'; // Tu email
            $mail->Password = 'aboq yedx fwxp xjhj'; // Usa una "Contraseña de aplicación" de Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
/*
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jonathan_goz_18@hotmail.com'; // Tu email
            $mail->Password = 'aboq yedx fwxp xjhj'; $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
*/
            // Configurar email
            $mail->setFrom('noreply@gmail.com', 'pruebas');
            $mail->addAddress($correo, 'pruebas');
            $mail->addReplyTo('support@gmail.com', 'pruebas');
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Subject = 'Restablecer contraseña';
            
            $reset_link="http://localhost/tl/tl/account_recovery.php?token=$token";
            
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

            $mail->send();
            $_SESSION['message'] = "Correo enviado correctamente";
        } catch (Exception $e) {
            $_SESSION['error2'] = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
        
        header("Location: loggin.php");
        exit();
    }
}