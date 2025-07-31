<?php
session_start();
include "../conexion.php"; // Asegúrate que la ruta es correcta

$conexion = ConectarDB();



if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];      

$nombres = isset($_POST['nombres']) ? trim($_POST['nombres']) : '';
$apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
$numero_id = isset($_POST['numero_id']) ? trim($_POST['numero_id']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$celular = isset($_POST['celular']) ? trim($_POST['celular']) : '';

// Obtener el numero_id anterior
$stmt0 = $conexion->prepare("SELECT numero_id FROM usuarios WHERE id_usuarios = ?");
$stmt0->bind_param("i", $id_usuario);
$stmt0->execute();
$stmt0->bind_result($numero_id_anterior);
$stmt0->fetch();
$stmt0->close();

// Obtener el rol actualizado del usuario
$stmt3 = $conexion->prepare("SELECT rol FROM usuarios WHERE id_usuarios = ?");
$stmt3->bind_param("i", $id_usuario);
$stmt3->execute();
$stmt3->bind_result($rol);
$stmt3->fetch();
$stmt3->close();

// Verificar si es orientador y si envió una nueva contraseña
$contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : null;

if ($rol === 'orientador' && !empty($contrasena)) {
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Actualizar tabla usuarios con contraseña
    $stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ?, contrasena = ? WHERE id_usuarios = ?");
    $stmt->bind_param("ssssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $contrasena_hash, $id_usuario);
} else {
    // Actualizar tabla usuarios sin contraseña
    $stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ? WHERE id_usuarios = ?");
    $stmt->bind_param("sssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $id_usuario);
}

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

if ($stmt->execute()) {     
    // Solo actualizar ruta_emprendedora si es emprendedor
    if ($rol === 'emprendedor') {
        $stmt2 = $conexion->prepare("UPDATE ruta_emprendedora SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ? WHERE numero_id = ?");
        $stmt2->bind_param("ssssss", $nombres, $apellidos, $numero_id, $correo, $celular, $numero_id_anterior);
        $stmt2->execute();
        $stmt2->close();
    }

    if ($rol === 'orientador') {
    $_SESSION['nombre'] = $nombres;
    $_SESSION['apellido'] = $apellidos;
}


    // Redirección según el rol
    $ruta_redireccion = ($rol === 'orientador') ? "../php/panel_orientador.php" : "../../dashboard.php";
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <title>¡Gracias!</title>
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Work-sans', sans-serif;
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
            .card h1 { margin: 0 0 15px; font-size: 1.8rem; }
            .card p { margin: 0 0 25px; }
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
    echo "❌ Error al actualizar los datos: " . $stmt->error;
}

?>
