<?php
// 1. Configuración de conexión
$host = 'localhost';
$user = 'root';     // cambia si tu usuario es distinto
$pass = '';         // cambia si tienes contraseña
$db   = 'fondo_emprender';  // <<-- pon aquí el nombre real de tu base

// 2. Conexión MySQLi
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die('Error de conexión: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// 3. Recibir campos del formulario
$nombre            = $_POST['nombre']            ?? '';
$descriptor        = $_POST['descriptor']        ?? '';
$citas             = $_POST['citas']             ?? '';
$quien             = $_POST['quien']             ?? '';
$metas             = $_POST['metas']             ?? '';
$actitud           = $_POST['actitud']           ?? '';
$comportamiento    = $_POST['comportamiento']    ?? '';
$modas             = $_POST['modas']             ?? '';
$beneficios        = $_POST['beneficios']        ?? '';
$decisiones_tiempo = $_POST['decisiones_tiempo'] ?? '';
$decisiones_base   = $_POST['decisiones_base']   ?? '';
$job_funcional     = $_POST['job_funcional']     ?? '';
$job_emocional     = $_POST['job_emocional']     ?? '';
$job_social        = $_POST['job_social']        ?? '';

// 4. Sentencia preparada
$sql = "INSERT INTO tarjeta_persona
        (nombre, descriptor, citas, quien, metas, actitud, comportamiento,
        modas, beneficios, decisiones_tiempo, decisiones_base,
        job_funcional, job_emocional, job_social)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssssssssssssss',
    $nombre, $descriptor, $citas, $quien, $metas, $actitud, $comportamiento,
    $modas, $beneficios, $decisiones_tiempo, $decisiones_base,
    $job_funcional, $job_emocional, $job_social
);

// 5. Ejecutar y responder
$exito = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if ($exito) {
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
        <p>Tus datos fueron almacenados correctamente.</p>
        <a class="btn" href="../../dashboard.html">Volver</a>
    </div>
</body>
</html>
    <?php
} else {
    echo "No se pudo guardar la tarjeta.";
}
?>