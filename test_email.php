<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'includes/mail_config.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP_DEBUG;
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress('cliente@ejemplo.com'); // cualquier email, da igual

    $mail->Subject = 'Prueba de correo - Autos Costa Sol';
    $mail->isHTML(true);
    $mail->Body    = '<h2>📩 Mailtrap funciona correctamente</h2><p>Este correo ha sido enviado desde tu proyecto.</p>';

    $mail->send();
    echo "✔️ Correo enviado correctamente (revisa Mailtrap)";
} catch (Exception $e) {
    echo "❌ Error enviando correo: " . $mail->ErrorInfo;
}
