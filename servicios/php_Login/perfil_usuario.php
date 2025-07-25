<?php 
session_start();
include "../conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.html");
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
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="../../componentes/editar_perfil.css"> 
</head>
<body>
    <div class="form-container">
        <h2>Editar Perfil</h2>
        <form action="actualizar_usuario.php" method="POST">
            <input type="hidden" name="id_usuarios" value="<?= $usuario['id_usuarios'] ?>">

            <label>Nombre:</label>
            <input type="text" name="nombres" value="<?= htmlspecialchars($usuario['nombres']) ?>" required>

            <label>Apellido:</label>
            <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>

            <label>Número de Documento:</label>
            <input type="text" name="numero_id" value="<?= htmlspecialchars($usuario['numero_id']) ?>" required>

            <label>Email:</label>
            <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>

            <label>Teléfono:</label>
            <input type="text" name="celular" value="<?= htmlspecialchars($usuario['celular']) ?>">

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
