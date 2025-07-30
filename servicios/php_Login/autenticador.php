<?php
session_start();
include "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    echo "Tu petición ha sido rechazada";
    exit;
}

if (
    empty($_POST['numeroDocumento']) ||
    empty($_POST['contrasena'])
) {
    echo "Hay datos errados";
    exit;
}

$num_documento = filter_var($_POST['numeroDocumento'], FILTER_VALIDATE_INT);
$contrasena = $_POST['contrasena'];

$conexiondb = ConectarDB();

// Buscar usuario por documento
$stmt = $conexiondb->prepare("SELECT * FROM usuarios WHERE numero_id = ?");
$stmt->bind_param("i", $num_documento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $hashBD = $usuario['contrasena'];

    // Verifica contraseña
    if (
        password_verify($contrasena, $hashBD) ||
        $contrasena === $hashBD // por compatibilidad si hay registros sin hash
    ) {
        $_SESSION['usuario_id'] = $usuario['id_usuarios'];
        $_SESSION['nombre'] = $usuario['nombres'];
        $_SESSION['apellido'] = $usuario['apellidos'];
        $_SESSION['rol'] = $usuario['rol'];

        if ($usuario['rol'] === 'orientador') {
            header("Location: ../php/panel_orientador.php");
        } else {
            header("Location: ../php_login/perfil_usuario.php"); // emprendedor
        }
        exit;


        header("Location: ../php_login/perfil_usuario.php");
        exit;
    } else {
        echo "Contraseña incorrecta.";
        exit;
    }
} else {
    // Usuario no encontrado
    echo "
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Usuario no registrado</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                padding: 40px;
                text-align: center;
            }
            .mensaje {
                background: #fff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                display: inline-block;
            }
            a {
                display: inline-block;
                margin-top: 10px;
                padding: 10px 20px;
                background: #39A900;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
            }
            a:hover {
                background: #007832;
            }
        </style>
    </head>
    <body>
        <div class='mensaje'>
            <h2>Usted no se encuentra registrado en la base de datos</h2>
            <p>¿Desea registrarse?</p>
            <a href='../../formulario_emprendedores/registro_emprendedores.html'>Registrarse aquí</a>
            <br><br>
            <a href='../../login.html' style='background:#ccc; color:#333;'>Volver al inicio</a>
        </div>
    </body>
    </html>
    ";
}
