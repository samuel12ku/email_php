<?php
include "../conexion.php";
$conexion = ConectarDB();

$id_usuario = $_POST['id_usuarios'];
$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
$numero_id = $_POST['numero_id'];
$correo = $_POST['correo'];
$celular = $_POST['celular'];

// Obtener el numero_id anterior
$stmt0 = $conexion->prepare("SELECT numero_id FROM usuarios WHERE id_usuarios = ?");
$stmt0->bind_param("i", $id_usuario);
$stmt0->execute();
$stmt0->bind_result($numero_id_anterior);
$stmt0->fetch();
$stmt0->close();

// Cifrar la nueva contraseña igual al nuevo número de documento
$contrasena_hash = password_hash($numero_id, PASSWORD_DEFAULT);

// Actualizar tabla usuarios
$stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ?, contrasena = ? WHERE id_usuarios = ?");
$stmt->bind_param("ssssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $contrasena_hash, $id_usuario);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

if ($stmt->execute()) {
    // Actualizar tabla ruta_emprendedora usando el numero_id anterior
    $stmt2 = $conexion->prepare("UPDATE ruta_emprendedora SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ? WHERE numero_id = ?");
    $stmt2->bind_param("ssssss", $nombres, $apellidos, $numero_id, $correo, $celular, $numero_id_anterior);
    $stmt2->execute();

        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Gracias!</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body{
            font-family:'Sora',sans-serif;
            background:linear-gradient(135deg,#f1f8e9,#c8e6c9);
            color:#2e7d32;
            display:flex;
            align-items:center;
            justify-content:center;
            height:100vh;
            margin:0;
            text-align:center;
        }
        .card{
            background:#fff;
            padding:50px 60px;
            border-radius:14px;
            box-shadow:0 10px 25px rgba(0,0,0,.08);
            max-width:480px;
        }
        .card h1{margin:0 0 15px;font-size:1.8rem}
        .card p{margin:0 0 25px}
        .btn{
            display:inline-block;
            padding:12px 26px;
            background:#39a900;
            color:#fff;
            border-radius:6px;
            text-decoration:none;
            transition:.3s;
        }
        .btn:hover{background:#2e7d32}
    </style>
</head>
<body>
    <div class="card">
        <h1>¡Guardado con éxito!</h1>
        <p>Tus datos se han actualizado correctamente.</p>
        <a class="btn" href="../../dashboard.php">Volver al panel</a>
    </div>
</body>
</html>
    <?php
} else {
    echo "Error al actualizar: " . $stmt->error;
}
?>