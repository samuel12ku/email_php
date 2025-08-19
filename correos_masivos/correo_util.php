<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function enviarCorreo($destinatario, $asunto, $contenido, $orden = 'texto') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'correopruebas0701@gmail.com';
        $mail->Password = 'hplo gshr scrl fkno';  // contraseña de aplicación
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';

        $mail->setFrom('LuzrecursosH@gmail.com', 'Fondo emprender CAB');
        $mail->addAddress($destinatario);

        // Opcional: prioridad
        $mail->AddCustomHeader("X-MSMail-Priority: High");
        $mail->AddCustomHeader("Importance: High");
        $mail->AddCustomHeader("X-Priority: 1");

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Procesar contenido con formato HTML simple
        $contenido_html = nl2br($contenido);
        $contenido_html = preg_replace(
            '/(https?:\/\/[^\s<]+)/',
            '<a href="$1" target="_blank">$1</a>',
            $contenido_html
        );

        $mensaje = "<div style='font-family:Arial,sans-serif; font-size:16px; color:#222'>";
        $mensaje .= "<p>$contenido_html</p>";
        $mensaje .= "</div>";

        $mail->Subject = $asunto;
        $mail->Body = $mensaje;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}
