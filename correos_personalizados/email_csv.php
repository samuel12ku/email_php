<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== 0) {
        exit("❌ Error al subir el archivo CSV.");
    }

    $titulo = $_POST['titulo'];
    $contenido_base = $_POST['contenido'];
    $orden = $_POST['orden'];

    $archivo_tmp = $_FILES['csv']['tmp_name'];
    $handle = fopen($archivo_tmp, 'r');

    if ($handle === false) {
        exit("❌ No se pudo abrir el archivo CSV.");
    }

    // Leer encabezado
    fgetcsv($handle);

    $enviados = 0;
    $fallidos = [];

    while (($datos = fgetcsv($handle)) !== false) {
        if (count($datos) < 3) continue;

        list($nombre, $correo, $saludo) = $datos;

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $fallidos[] = "$correo (correo inválido)";
            continue;
        }

        $mail = new PHPMailer(true);

        try {
            // Config SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; //smtp.gmail.com
            $mail->SMTPAuth = true;
            $mail->Username = 'correopruebas0701@gmail.com'; // Cambiar
            $mail->Password = 'hplo gshr scrl fkno'; // Cambiar por tu App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // importancia 
            $mail->AddCustomHeader("X-MSMail-Priority: High");
            $mail->AddCustomHeader("Importance: High");
            $mail->AddCustomHeader("X-Priority: 1");

            $mail->setFrom('correopruebas0101@gmail.com', 'Correos deseados');
            $mail->addAddress($correo, $nombre);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // Imagen embebida
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $ruta = $_FILES['imagen']['tmp_name'];
                $nombre_img = $_FILES['imagen']['name'];
                $mime = mime_content_type($ruta);
                $mail->addEmbeddedImage($ruta, 'imagen_cuerpo', $nombre_img, 'base64', $mime);
            } 

            // Procesar texto y enlaces
            $saludo_limpio = rtrim($saludo, ", \t\n\r\0\x0B");
            $contenido_html = nl2br($contenido_base); // saltos de línea
            $contenido_html = preg_replace(
                '/(https?:\/\/[^\s<]+)/',
                '<a href="$1" target="_blank">$1</a>',
                $contenido_html
            );

            // Cuerpo del mensaje según orden
            if ($orden === 'imagen_texto') {
                $mensaje = "
                    <div style='font-family:Arial,sans-serif; font-size:16px; color:#222'>
                        <img src='cid:imagen_cuerpo' style='max-width:100%; height:auto; margin-bottom:20px; display:block;'>
                        <p><strong>$saludo_limpio</strong></p>
                        <p>$contenido_html</p>
                    </div>";
            } else {
                $mensaje = "
                    <div style='font-family:Arial,sans-serif; font-size:16px; color:#222'>
                        <p><strong>$saludo_limpio</strong></p>
                        <p>$contenido_html</p>
                        <img src='cid:imagen_cuerpo' style='max-width:100%; height:auto; margin-top:20px; display:block;'>
                    </div>";
            }

            $mail->Subject = $titulo;
            $mail->Body = $mensaje;

            $mail->send();
            $enviados++;
        } catch (Exception $e) {
            $fallidos[] = "$correo ({$e->getMessage()})";
        }
    }

    fclose($handle);

    echo "✅ Correos enviados: $enviados<br>";
    if (!empty($fallidos)) {
        echo "❌ Fallaron:<br>" . implode('<br>', $fallidos);
    }
}
?>
