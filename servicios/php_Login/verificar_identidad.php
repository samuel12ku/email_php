<?php
session_start();
include "../conexion.php";

$conexion = ConectarDB();
$numero_id = $_POST['numero_id'] ?? '';
$usuario = null;
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($numero_id)) {
        $stmt = $conexion->prepare("SELECT * FROM orientacion_rcde2025_valle WHERE numero_id = ?");
        $stmt->bind_param("s", $numero_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $usuario = $res->fetch_assoc();

        if ($usuario) {
            // ✅ Guardamos sesión, pero sin redirigir aún
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol'] = 'emprendedor';
        } else {
            $mensaje = " No se encontró tu documento en nuestra base de datos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Identidad</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">

    <h2>Verificar Identidad</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="numero_id" class="form-label">Ingrese tu documento de identidad</label>
            <input type="text" class="form-control" name="numero_id" required>
        </div>
        <button type="submit" class="btn btn-success">Revisar</button>
        <a href="../../login.php">Volver al Inicio</a>
    </form>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger mt-3"><?= $mensaje ?></div>
    <?php endif; ?>

    <!-- Modal Bootstrap -->
    <?php if ($usuario): ?>
    <div class="modal fade show" id="usuarioModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Usuario encontrado</h5>
                </div>
                <div class="modal-body">
                    <p>Te encuentras en nuestra base de datos. Verifica si tus datos son correctos:</p>
                    <ul>
                        <li><b>Nombres:</b> <?= $usuario['nombres'] ?></li>
                        <li><b>Apellidos:</b> <?= $usuario['apellidos'] ?></li>
                        <li><b>Documento:</b> <?= $usuario['numero_id'] ?></li>
                        <li><b>Celular:</b> <?= $usuario['celular'] ?></li>
                        <li><b>Correo:</b> <?= $usuario['correo'] ?></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <a href="editar_usuario_primera_vez.php?numero_id=<?= urlencode($usuario['numero_id']) ?>" class="btn btn-primary">Actualizar mis datos</a>
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function cerrarModal() {
        document.getElementById('usuarioModal').style.display = 'none';
        document.body.classList.remove('modal-open');
        let backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    }
    </script>
</body>
</html>
