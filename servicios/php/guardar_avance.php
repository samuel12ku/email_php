<?php
session_start();
include_once "../conexion.php";

if (!isset($_SESSION['usuario_id']) || !isset($_POST['fase'])) {
    exit("Acceso denegado");
}

$usuario_id = intval($_SESSION['usuario_id']);
$fase = intval($_POST['fase']);

$conexion = ConectarDB();

// Solo insertamos si no existe ese avance aún
$stmt = $conexion->prepare("INSERT IGNORE INTO progreso_herramientas (usuario_id, fase) VALUES (?, ?)");
$stmt->bind_param("ii", $usuario_id, $fase);
$ok = $stmt->execute();

if ($ok) {
    echo "OK";
} else {
    echo "Error: " . $stmt->error;
}
