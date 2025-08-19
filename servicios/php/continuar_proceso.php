<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include "../conexion.php";
include "../../correos_masivos/correo_util.php"; // Función enviarCorreo

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$conexion = ConectarDB();
$usuario_id = (int)$_SESSION['usuario_id']; // asegurar entero

// 1) Traer TODOS los datos que vas a usar
$stmt = $conexion->prepare("
    SELECT 
        nombres,
        apellidos,
        numero_id,
        celular,
        correo,
        orientador_id
    FROM orientacion_rcde2025_valle
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    echo "No se encontró al emprendedor en la base de datos.";
    exit;
}

$nombre_emprendedor = $usuario['nombres'];
$apellidos          = $usuario['apellidos'];
$cedula             = $usuario['numero_id'];
$celular            = $usuario['celular'];
$correo             = $usuario['correo'];
$orientador_id      = (int)$usuario['orientador_id'];

// 2) Obtener correo del orientador
if ($orientador_id <= 0) {
    echo "No hay un orientador asignado a este emprendedor.";
    exit;
}

$stmt = $conexion->prepare("
    SELECT correo 
    FROM orientadores 
    WHERE id_orientador = ? 
    LIMIT 1
");
$stmt->bind_param("i", $orientador_id);
$stmt->execute();
$result = $stmt->get_result();
$orientador = $result->fetch_assoc();
$stmt->close();

if (!$orientador) {
    echo "No se encontró el correo del orientador.";
    exit;
}

$correo_orientador = $orientador['correo'];

// 3) Actualizar estado del emprendedor
$stmt = $conexion->prepare("
    UPDATE orientacion_rcde2025_valle 
       SET estado_proceso = 'interesado' 
     WHERE id = ?
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->close();

// 4) Enviar correo al orientador
$para    = $correo_orientador;
$asunto  = "Un emprendedor desea continuar el proceso";
$mensaje = "{$nombre_emprendedor} {$apellidos} con el número de documento {$cedula} ha indicado que desea continuar con el proceso de emprendimiento, medios de contacto: correo {$correo} y celular {$celular}.";

$exito = enviarCorreo($para, $asunto, $mensaje);

if ($exito) {
    $_SESSION['mostrar_modal_confirmacion'] = true;
    header("Location: ../../dashboard.php");
    exit;
} else {
    echo "<p>Hubo un error al enviar el correo. Intenta nuevamente más tarde.</p>";
}
