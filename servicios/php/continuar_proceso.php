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
$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del emprendedor desde la tabla orientacion_rcde2025_valle
$stmt = $conexion->prepare("SELECT nombres, orientador_id FROM orientacion_rcde2025_valle WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo "No se encontró al emprendedor en la base de datos.";
    exit;
}

$nombre_emprendedor = $usuario['nombres'];
$orientador_id = $usuario['orientador_id'];

// Obtener correo del orientador desde la tabla orientadores
$stmt = $conexion->prepare("SELECT correo FROM orientadores WHERE id_orientador = ?");
$stmt->bind_param("i", $orientador_id);
$stmt->execute();
$result = $stmt->get_result();
$orientador = $result->fetch_assoc();

if (!$orientador) {
    echo "No se encontró el correo del orientador.";
    exit;
}

// Actualizar el estado del emprendedor a 'interesado'
$stmt = $conexion->prepare("UPDATE orientacion_rcde2025_valle SET estado_proceso = 'interesado' WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

// Enviar correo al orientador
$para = $orientador['correo'];
$asunto = "Un emprendedor desea continuar el proceso";
$mensaje = "$nombre_emprendedor ha indicado que desea continuar con el proceso de emprendimiento.";

$exito = enviarCorreo($para, $asunto, $mensaje);

if ($exito) {
    $_SESSION['mostrar_modal_confirmacion'] = true;
    header("Location: ../../dashboard.php");
    exit;
} else {
    echo "<p>Hubo un error al enviar el correo. Intenta nuevamente más tarde.</p>";
}
