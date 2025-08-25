<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$conexion = ConectarDB();
$id_usuario = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuarios = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inicio del emprendedor</title>
</head>
<body>
    <h2>¡Bienvenido, <?php echo $usuario['nombres']; ?>!</h2>

    <?php
    if ($usuario['estado_proceso'] == 'pendiente') {
        echo '<form method="post" action="continuar_proceso.php">';
        echo '<input type="submit" name="continuar" value="Quiero continuar el proceso">';
        echo '</form>';
    }
    ?>
</body>
</html>
