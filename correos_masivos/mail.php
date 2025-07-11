<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $destinatario = trim($_POST['destinatario']); // un solo correo
    $orden = $_POST['orden'] ?? 'imagen_texto'; // valor por defecto si no llega


    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        exit("❌ Correo inválido: $destinatario");
    }

    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // correo de gmail
        $mail->Username = 'samuelscarpetta1203@gmail.com';
        // contraseña de aplicación  
        $mail->Password = 'ubte psxi wdyj kkoo';
        $mail->Port = 587;

        // Remitente y destinatario real (visible)
        $mail->setFrom('k3vinch3nl1@gmail.com', 'Kevin Chen Li');
        $mail->addAddress($destinatario);

        // HTML y codificación
        $mail->isHTML(true);    
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Insertar imagen en el cuerpo (embebida, no adjunta)
        $cid = 'imagen_cuerpo';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $ruta = $_FILES['imagen']['tmp_name'];
            $mime = mime_content_type($ruta);
            $mail->addStringEmbeddedImage(file_get_contents($ruta), $cid, '', 'base64', $mime);
        }

        // Procesar texto con saltos de línea y enlaces
        $contenido_html = nl2br($contenido);
        $contenido_html = preg_replace(
            '/(https?:\/\/[^\s<]+)/',
            '<a href="$1" target="_blank">$1</a>',
            $contenido_html
        );

        // Mensaje con imagen si fue cargada
        $mensaje = "<div style='font-family:Arial,sans-serif; font-size:16px; color:#222'>";

        if ($orden === 'imagen_texto' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $mensaje .= "<img src='cid:$cid' style='max-width:100%; height:auto; margin-bottom:20px; display:block;'>";
            $mensaje .= "<p>$contenido_html</p>";
        } elseif ($orden === 'texto_imagen' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $mensaje .= "<p>$contenido_html</p>";
            $mensaje .= "<img src='cid:$cid' style='max-width:100%; height:auto; margin-top:20px; display:block;'>";
        } else {
            $mensaje .= "<p>$contenido_html</p>"; // Por si no hay imagen
        }

        $mensaje .= "</div>";


        // Asunto y cuerpo
        $mail->Subject = $titulo;
        $mail->Body = $mensaje;

        $mail->send();
        echo "✅ ¡Correo enviado a $destinatario!";
    } catch (Exception $e) {
        echo "❌ Error al enviar: {$mail->ErrorInfo}";
    }
}
