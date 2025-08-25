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
            <div style="position:relative;">
                <input type="password" id="contrasena" name="contrasena" title="solo se permiten numeros minimo 6 maximo 10" required class="form-control"
                    pattern="[0-9]{6,10}" minlength="6" maxlength="10" style="padding-right:38px;" />
                <button type="button" id="mostrarConstrasena" 
                    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; padding:0;">
                    <!-- OJO ABIERTO SVG (por defecto visible) -->
                    <svg id="ojoAbierto" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#19985B">
                        <ellipse cx="12" cy="12" rx="8" ry="6" stroke-width="2"/>
                        <circle cx="12" cy="12" r="2" fill="#19985B"/>
                    </svg>
                    <!-- OJO CERRADO SVG (por defecto oculto) -->
                    <svg id="ojoCerrado" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#A0A0A0" style="display:none;">
                        <ellipse cx="12" cy="12" rx="8" ry="6" stroke-width="2"/>
                        <line x1="5" y1="19" x2="19" y2="5" stroke="#A0A0A0" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje-error" id="errorMensaje">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn-enviar">Ingresar</button>
        <a href="servicios/php_login/verificar_identidad.php" class="btn btn-primary">Verificar identidad(si no tienes Contraseña)</a>
        <a href="formulario_emprendedores/registro_emprendedores.php" class="btn btn-primary">¿No tienes cuenta?, registrate aqui</a>

    </form>
</div>

<script src="componentes/js/login.js"></script>
</body>
</html>
