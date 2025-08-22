<?php

session_start();
include "../conexion.php";

$conexion = ConectarDB();


if (!isset($_GET['numero_id'])) {
    die("Error: no se especificó un documento válido.");
}

$numero_id = $_GET['numero_id'];

// Consultar datos del usuario
$stmt = $conexion->prepare("SELECT * FROM orientacion_rcde2025_valle WHERE numero_id = ?");
$stmt->bind_param("s", $numero_id);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

$mensaje = "";
$mensajebueno = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];

    $contrasena= $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    if ($contrasena !== $confirmar_contrasena) {
        $mensaje = "❌ Las contraseñas no coinciden.";
    } else {
        // Encriptar contraseña
        $passwordHash = password_hash($contrasena, PASSWORD_DEFAULT);

        $update = $conexion->prepare("UPDATE orientacion_rcde2025_valle 
            SET nombres=?, apellidos=?, celular=?, correo=?, contrasena=? 
            WHERE numero_id=?");
        $update->bind_param("ssssss", $nombres, $apellidos, $celular, $correo, $passwordHash, $numero_id);

        if ($update->execute()) {
            
            $mensajebueno = "success";
        } else {
            $mensaje = "❌ Error al actualizar usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - Primera Vez</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-3 text-center">Completa tu registro</h3>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if ($mensajebueno): ?>
            <div class="alert alert-success"><?= $mensajebueno ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombres" class="form-control" value="<?= htmlspecialchars($usuario['nombres']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Apellidos</label>
                <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Celular</label>
                <input type="text" name="celular" class="form-control" value="<?= htmlspecialchars($usuario['celular']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label">Crea tu contraseña</label>
                <input type="password" name="contrasena" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" name="confirmar_contrasena" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="verificar_identidad.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        <?php if ($mensajebueno === "success"): ?>
        <!-- Modal -->
        <div id="successModal" style="
            position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.5); display:flex; justify-content:center; align-items:center;
        ">
            <div style="background:#fff; padding:20px; border-radius:10px; text-align:center; max-width:400px;">
                <h2 style="color:green;">✅ Datos guardados exitosamente</h2>
                <p>Puedes volver al login para iniciar sesión.</p>
                <a href="../../login.php" 
                    style="display:inline-block; margin-top:15px; padding:10px 20px; background:#007BFF; color:white; border-radius:5px; text-decoration:none;">
                    volver al Login
                </a>
            </div>
        </div>
        <?php endif; ?>
        </form>
    </div>
</div>

</body>
</html>
