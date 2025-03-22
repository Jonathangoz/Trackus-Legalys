<?php
session_start();
require 'vendor/autoload.php'; // Carga PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Verificar si el email existe (igual que antes)
    // ... [tu código existente] ...

    if ($user) {
        // Generar token (igual que antes)
        // ... [tu código existente] ...

        // Configurar PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Configuración SMTP (Ejemplo para Gmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tucorreo@gmail.com'; // Tu email
            $mail->Password = 'tupassword'; // Usa una "Contraseña de aplicación" de Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configurar email
            $mail->setFrom('no-reply@tudominio.com', 'Nombre de tu sitio');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Restablecer contraseña';
            
            $reset_link = "https://tudominio.com/reset_password.php?token=$token";
            
            $mail->Body = "
                <h1>Restablece tu contraseña</h1>
                <p>Haz clic en el siguiente enlace:</p>
                <a href='$reset_link'>Restablecer contraseña</a>
                <p>Si no solicitaste esto, ignora este correo.</p>
            ";

            $mail->send();
            $_SESSION['message'] = "Correo enviado correctamente";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
        
        header("Location: login.php");
        exit();
    }
}
