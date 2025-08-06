<?php 
session_start();
include "../conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit;
}

$conexion = ConectarDB();
$id_usuario = $_SESSION['usuario_id'];

// Obtener datos del usuario
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuarios = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Guardamos el rol actual para usarlo en el formulario
$rol = $usuario['rol'];
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
            <input type="hidden" name="rol" value="<?= htmlspecialchars($rol) ?>">

            <label>Nombre:</label>
            <input type="text" name="nombres" value="<?= htmlspecialchars($usuario['nombres']) ?>" required>

            <label>Apellido:</label>
            <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>

            <label>Número de Documento:</label>
            <input type="text" id="numero_id" name="numero_id" value="<?= htmlspecialchars($usuario['numero_id']) ?>" readonly>

            <?php if ($rol === 'orientador'): ?>
                <label>Contraseña:</label>
                <input type="password" name="contrasena" placeholder="Dejar en blanco para no cambiar">
            <?php endif; ?>

            <label>Email:</label>
            <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>

            <label>Teléfono:</label>
            <input type="text" name="celular" pattern="[0-9]{10}" title="Ingrese un número de teléfono válido de 10 dígitos" minlength="10" maxlength="10" value="<?= htmlspecialchars($usuario['celular']) ?>">

            <div class="botones-acciones">
            <button class="boton-guardar" type="submit">Guardar Cambios</button>
            <?php
                $cancel_url = ($rol === 'orientador') ? '../php/panel_orientador.php' : '../../dashboard.php';
            ?>
            
            <button type="button" class="boton-cancelar" class="boton" id="btn-cancelar" onclick="window.location.href='<?= $cancel_url ?>'">Cancelar</button>
            </div>

        </form>
    </div>
</body>
</html>
