<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . "../../conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    echo "Invitado";
    exit;
}

$conexion = ConectarDB();
$id_usuario = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT nombres, apellidos FROM usuarios WHERE id_usuarios = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    echo htmlspecialchars($usuario['nombres'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($usuario['apellidos'], ENT_QUOTES, 'UTF-8');
} else {
    echo "Usuario no encontrado";
}

