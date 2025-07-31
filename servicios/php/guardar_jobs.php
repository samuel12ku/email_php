<?php
require_once '../conexion.php';
$conn = ConectarDB();

$actors = $_POST['actor'] ?? [];
$job1   = $_POST['job_1'] ?? [];
$job2   = $_POST['job_2'] ?? [];
$job3   = $_POST['job_3'] ?? [];

$stmt = $conn->prepare(
    "INSERT INTO jobs_to_be_done (actor, job_1, job_2, job_3)
     VALUES (?, ?, ?, ?)"
);
if (!$stmt) {
    http_response_code(500);
    exit("Error preparando consulta");
}

$insertados = 0;
for ($i = 0; $i < count($actors); $i++) {
    $stmt->bind_param('ssss', $actors[$i], $job1[$i], $job2[$i], $job3[$i]);
    if ($stmt->execute()) $insertados++;
}
$stmt->close();
$conn->close();
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
        <a class="btn" href="../../dashboard.php">Volver</a>
    </div>
</body>
</html>