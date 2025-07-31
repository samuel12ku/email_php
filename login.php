<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fondo Emprender</title>
    <link rel="stylesheet" href="componentes/login.css">
    <style>
        .mensaje-error {
            color: red;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="dashboard-contenedor">
    <div class="dashboard-header">
        <img src="componentes/img/logoFondoEmprender.svg" alt="Logo SENA" class="dashboard-logo">
        <h2>Iniciar Sesión</h2>
    </div>

    <form action="servicios/php_Login/autenticador.php" method="post" class="persona-form">
        <div class="form-grupo">
            <label for="numeroDocumento">Número de documento</label>
            <input type="text" id="numeroDocumento" name="numeroDocumento" pattern="[A-Z0-9]{1,10}" maxlength="20" min="10" title="Ingrese un número de documento válido" required class="form-control" value="<?= isset($_GET['documento']) ? htmlspecialchars($_GET['documento']) : '' ?>" />
        </div>

        <div class="form-grupo">
            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required class="form-control" pattern="[0-9]{4,10}" minlength="8" maxlength="10" />
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje-error" id="errorMensaje">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn-enviar">Ingresar</button>
    </form>
</div>

<script>
    // Ocultar el mensaje de error al empezar a escribir la contraseña
    const inputContrasena = document.getElementById('contrasena');
    const mensajeError = document.getElementById('errorMensaje');

    if (inputContrasena && mensajeError) {
        inputContrasena.addEventListener('input', () => {
            mensajeError.style.display = 'none';
        });
    }

    // Eliminar parámetros de error de la URL para evitar que se mantenga al refrescar
    if (window.location.search.includes('error') || window.location.search.includes('documento')) {
        const url = new URL(window.location);
        url.searchParams.delete('error');
        url.searchParams.delete('documento');
        window.history.replaceState({}, document.title, url.pathname);
    }
</script>
</body>
</html>
