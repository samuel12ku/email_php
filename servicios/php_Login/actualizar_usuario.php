<?php
session_start();
include "../conexion.php";

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    header("Location: ../../login.php");
    exit;
}

$conexion = ConectarDB();
$id_usuario = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Sanitizar entradas
$nombres    = trim($_POST['nombres'] ?? '');
$apellidos  = trim($_POST['apellidos'] ?? '');
$numero_id  = trim($_POST['numero_id'] ?? '');
$correo     = trim($_POST['correo'] ?? '');
$celular    = trim($_POST['celular'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

// Actualizar según el rol y si hay contraseña
if ($rol === 'orientador') {
    if (!empty($contrasena)) {
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("UPDATE orientadores  SET nombres=?, apellidos=?, numero_id=?, correo=?, celular=?, contrasena=?  WHERE id_orientador=?"
);
        $stmt->bind_param("ssssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $contrasena_hash, $id_usuario);
    } else {
        $stmt = $conexion->prepare("UPDATE orientadores SET nombres=?, apellidos=?, numero_id=?, correo=?, celular=? WHERE id_orientador=?");
        $stmt->bind_param("sssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $id_usuario);
    }
} else { // emprendedor
    if (!empty($contrasena)) {
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("UPDATE orientacion_rcde2025_valle SET nombres=?, apellidos=?, numero_id=?, correo=?, celular=?, contrasena=? WHERE id=?");
        $stmt->bind_param("ssssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $contrasena_hash, $id_usuario);
    } else {
        $stmt = $conexion->prepare("UPDATE orientacion_rcde2025_valle SET nombres=?, apellidos=?, numero_id=?, correo=?, celular=? WHERE id=?");
        $stmt->bind_param("sssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $id_usuario);
    }
}

// Ejecutar
if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

if ($stmt->execute()) {
    // Actualizar sesión si es orientador
    if ($rol === 'orientador') {
        $_SESSION['nombre']  = $nombres;
        $_SESSION['apellido'] = $apellidos;
    }

    $ruta_redireccion = ($rol === 'orientador') ? "../php/panel_orientador.php" : "../../login.php";
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Datos actualizados</title>
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Work Sans', sans-serif;
                background: linear-gradient(135deg, #f1f8e9, #c8e6c9);
                color: #2e7d32;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                text-align: center;
            }
            .card {
                background: #fff;
                padding: 50px 60px;
                border-radius: 14px;
                box-shadow: 0 10px 25px rgba(0,0,0,.08);
                max-width: 480px;
            }
            .card h1 { margin-bottom: 15px; font-size: 1.8rem; }
            .btn {
                display: inline-block;
                padding: 12px 26px;
                background: #39a900;
                color: #fff;
                border-radius: 6px;
                text-decoration: none;
                transition: .3s;
            }
            .btn:hover { background: #2e7d32; }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>¡Guardado con éxito!</h1>
            <p>Tus datos se han actualizado correctamente.</p>
            <a class="btn" href="<?= $ruta_redireccion ?>">Ir al panel</a>
        </div>
    </body>
    </html>
    <?php
    exit;
} else {
    echo " Error al actualizar: " . $stmt->error;
}
?>
