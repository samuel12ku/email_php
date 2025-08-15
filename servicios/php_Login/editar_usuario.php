<?php 
session_start();
include "../conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit;
}

$conexion   = ConectarDB();
$id_usuario = $_SESSION['usuario_id'];
$rolSesion  = $_SESSION['rol'] ?? null;

// 1) Elegimos tabla y PK según el rol en sesión (si existe)
if ($rolSesion === 'orientador') {
    $tabla_origen = 'orientadores';
    $pk = 'id_orientador';
} else {
    // Por defecto (o si rol no viene), asumimos emprendedor
    $tabla_origen = 'orientacion_rcde2025_valle';
    $pk = 'id';
}

// 2) Intentamos obtener el registro en la tabla esperada
$stmt = $conexion->prepare("SELECT * FROM $tabla_origen WHERE $pk = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// 3) Fallback: si no se encontró, buscamos en la otra tabla
if (!$usuario) {
    if ($tabla_origen === 'orientadores') {
        $tabla_origen = 'orientacion_rcde2025_valle';
        $pk = 'id';
    } else {
        $tabla_origen = 'orientadores';
        $pk = 'id_orientador';
    }

    $stmt = $conexion->prepare("SELECT * FROM $tabla_origen WHERE $pk = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
}

// 4) Si tampoco existe, cerramos sesión por seguridad
if (!$usuario) {
    session_destroy();
    header("Location: ../../login.php");
    exit;
}

// 5) Aseguramos el rol a usar después (para el formulario / actualización)
$rol = $rolSesion ?: ($tabla_origen === 'orientadores' ? 'orientador' : 'emprendedor');
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
            <!-- ID y tabla para saber de dónde actualizar -->
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($id_usuario) ?>">
            <input type="hidden" name="tabla_origen" value="<?= htmlspecialchars($tabla_origen) ?>">
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
