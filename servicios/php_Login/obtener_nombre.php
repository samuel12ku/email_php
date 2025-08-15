<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . "../../conexion.php";

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    echo "Invitado";
    exit;
}

$conexion = ConectarDB();
$id_usuario = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['rol'];

// Elegir tabla según el rol
if ($rol_usuario === 'emprendedor') {
    $tabla = "orientacion_rcde2025_valle";
    $id_campo = "id_usuarios";
} elseif ($rol_usuario === 'orientador') {
    $tabla = "orientadores";
    $id_campo = "id_orientador";
} else {
    echo "Invitado";
    exit;
}

// Consultar nombres y apellidos
$stmt = $conexion->prepare("SELECT nombres, apellidos FROM $tabla WHERE $id_campo = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    echo htmlspecialchars($usuario['nombres'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($usuario['apellidos'], ENT_QUOTES, 'UTF-8');
} else {
    echo "Usuario no encontrado";
}
