<?php
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

// Configuración SendGrid
$mail->isSMTP();
$mail->Host = 'smtp.sendgrid.net';
$mail->SMTPAuth = true;
$mail->Username = 'apikey'; // Literalmente la palabra "apikey"
$mail->Password = 'SG.XXXXXXXX'; // Tu API Key de SendGrid
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Configurar email
$mail->setFrom('no-reply@tudominio.com', 'Tu Sitio Web');
$mail->addAddress('destinatario@example.com');
$mail->Subject = 'Asunto del correo';
$mail->Body = 'Contenido del correo';

// Enviar
try {
    $mail->send();
    echo 'Correo enviado exitosamente';
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}