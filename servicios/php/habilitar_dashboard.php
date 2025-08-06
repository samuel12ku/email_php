<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'orientador') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['numero_id'])) {
    $numero_id = $_POST['numero_id'];
    $conexion = ConectarDB();

    $stmt = $conexion->prepare("UPDATE usuarios SET acceso_panel = 1 WHERE numero_id = ?");
    $stmt->bind_param("s", $numero_id);

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Se habilitó el acceso correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Ocurrió un error al habilitar el acceso.";
    }

    header("Location: lista_emprendedores.php");
    exit;
}
?>
