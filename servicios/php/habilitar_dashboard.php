<?php
session_start();
require_once "../conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'orientador') {
  $_SESSION['mensaje_error'] = 'No autorizado.';
  header("Location: lista_emprendedores.php"); exit;
}
$conexion = ConectarDB();
$numero_id = $_POST['numero_id'] ?? '';
$numero_id = trim($numero_id);

if ($numero_id === '') {
  $_SESSION['mensaje_error'] = 'Falta número de documento.';
  header("Location: lista_emprendedores.php"); exit;
}

$stmt = $conexion->prepare("UPDATE orientacion_rcde2025_valle SET acceso_panel = 1 WHERE numero_id = ?");
$stmt->bind_param("s", $numero_id);
$ok = $stmt->execute();
$stmt->close();

$_SESSION[$ok ? 'mensaje_exito' : 'mensaje_error'] = $ok ? 'Acceso habilitado.' : 'No se pudo habilitar el acceso.';
header("Location: lista_emprendedores.php"); exit;
