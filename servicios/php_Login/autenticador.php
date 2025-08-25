<?php
session_start();
include "../conexion.php";//no incluir conexion arriba si no depues de recoger los datos
//hacer la filtracion correcto 

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

/* 1️⃣ Buscar en tabla orientadores */
$stmt = $conexiondb->prepare("SELECT * FROM orientadores WHERE numero_id = ?");
$stmt->bind_param("i", $num_documento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $hashBD = $usuario['contrasena'];

    if (password_verify($contrasena, $hashBD) || $contrasena === $hashBD) {
        $_SESSION['usuario_id'] = $usuario['id_orientador'];
        $_SESSION['nombre'] = $usuario['nombres'];
        $_SESSION['apellido'] = $usuario['apellidos'];
        $_SESSION['rol'] = 'orientador';

        header("Location: ../php/panel_orientador.php");
        exit;
    } else {
        header("Location: ../../index.php?error=" . urlencode("Contraseña incorrecta") . "&documento=" . urlencode($num_documento));
        exit;
    }
}

/* 2️⃣ Buscar en tabla orientacion_rcde2025_valle (emprendedores) */
$stmt = $conexiondb->prepare("SELECT * FROM orientacion_rcde2025_valle WHERE numero_id = ?");
$stmt->bind_param("i", $num_documento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $hashBD = $usuario['contrasena'];

    if (password_verify($contrasena, $hashBD) || $contrasena === $hashBD) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombres'];
        $_SESSION['apellido'] = $usuario['apellidos'];
        $_SESSION['rol'] = 'emprendedor';

        // Validar campos obligatorios vacíos
        $campos_obligatorios = ['correo', 'celular', 'nombres', 'apellidos', 'numero_id'];
        $faltantes = [];

        foreach ($campos_obligatorios as $campo) {
            if (empty($usuario[$campo])) {
                $faltantes[] = ucfirst($campo);
            }
        }

        if (!empty($faltantes)) {
            echo "
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>Actualización requerida</title>
                <style>
                    body {
                        margin: 0;
                        padding: 0;
                        height: 100vh;
                        background: linear-gradient(to bottom right, #e6f9e6, #cceccc);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-family: 'Work Sans', sans-serif;
                    }
                    .aviso {
                        background: white;
                        padding: 30px 40px;
                        border-radius: 12px;
                        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
                        text-align: center;
                    }
                    .aviso h2 {
                        color: #39A900;
                        margin-bottom: 10px;
                    }
                    .aviso p {
                        margin-bottom: 20px;
                        color: #333;
                    }
                    .aviso ul {
                        text-align: left;
                        margin-bottom: 20px;
                        color: #444;
                    }
                    .aviso a {
                        padding: 10px 20px;
                        background: #39A900;
                        color: white;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: bold;
                        display: inline-block;
                    }
                    .aviso a:hover {
                        background: #007832;
                    }
                </style>
            </head>
            <body>
                <div class='aviso'>
                    <h2>✏️ Debes completar tus datos</h2>
                    <p>Faltan los siguientes campos obligatorios:</p>
                    <ul>";
                    foreach ($faltantes as $campo) {
                        echo "<li>• $campo</li>";
                    }
                    echo "</ul>
                    <a href='../php_login/editar_usuario.php'>Actualizar mis datos</a>
                </div>
            </body>
            </html>
            ";
            exit;
        }

        // Si todo está completo, redirige al panel
        if ($usuario['estado_proceso'] !== 'pendiente') {
            header("Location: ../../dashboard.php");
            exit;
        }

        // Si aún está pendiente, mostrar elección visual
        echo "
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Bienvenido</title>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    height: 100vh;
                    background: linear-gradient(to bottom right, #92d892ff, #78a178ff);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: 'Work Sans', sans-serif;
                }
                .eleccion {
                    background: white;
                    padding: 30px 40px;
                    border-radius: 12px;
                    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .eleccion h2 {
                    color: #39A900;
                    margin-bottom: 10px;
                }
                .eleccion p {
                    margin-bottom: 25px;
                    color: #333;
                    font-size: 1.1rem;
                }
                .eleccion a {
                    padding: 12px 22px;
                    margin: 10px;
                    background: #39A900;
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: bold;
                    display: inline-block;
                    transition: background 0.2s ease;
                }
                .eleccion a:hover {
                    background: #007832;
                }
                @media (max-width: 600px) {
                    body {
                        padding: 10px;
                    }
                    .eleccion {
                        padding: 25px 20px;
                        width: 100%;
                        max-width: 320px;
                        margin: auto;
                    }
                    .eleccion h2 {
                        font-size: 1.4rem;
                    }
                    .eleccion a {
                        display: block;
                        margin: 10px 0;
                        font-size: 1rem;
                        padding: 20px;
                    }
                }
            </style>
        </head> 
        <body>
            <div class='eleccion'>
                <h2>👋 ¡Bienvenido/a, {$usuario['nombres']} {$usuario['apellidos']}!</h2>
                <p>Estuviste en la orientación y queremos saber si deseas continuar con el proceso de emprendimiento.</p>
                <a href='../php/continuar_proceso.php'> Sí, deseo continuar</a>
                <a href='../../index.php'> No deseo continuar por ahora</a>
            </div>
        </body>
        </html>
        ";
        exit;

    } else {
        header("Location: ../../index.php?error=" . urlencode("Contraseña incorrecta") . "&documento=" . urlencode($num_documento));
        exit;
    }
}

/* 3️⃣ Si no encontró en ninguna tabla */
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
        <a href='../../index.php' style='background:#ccc; color:#333;'>Volver al inicio</a>
    </div>
</body>
</html>
";
