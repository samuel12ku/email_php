<?php
// 1. Configuración de acceso a la BD
$host = 'localhost';
$user = 'root';     // cambiar si es necesario
$pass = '';         // cambiar si es necesario
$db   = 'fondo_emprender';

// 2. Conexión MySQLi
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    http_response_code(500);
    exit("Error de conexión: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

/* -------------------------------------------------------------
   3. Recibir todos los campos del formulario
------------------------------------------------------------- */
$nombres            = $_POST['nombres']            ?? '';
$apellidos          = $_POST['apellidos']          ?? '';
$departamento       = $_POST['departamento']       ?? '';
$municipio          = $_POST['municipio']          ?? '';
$pais               = $_POST['pais']               ?? '';
$tipo_id            = $_POST['tipo_id']            ?? '';
$numero_id          = $_POST['numero_id']          ?? '';
$fecha_nacimiento   = $_POST['fecha_nacimiento']   ?? '';
$fecha_orientacion  = $_POST['fecha_orientacion']  ?? '';
$genero             = $_POST['genero']             ?? '';
$nacionalidad       = $_POST['nacionalidad']       ?? '';
$pais_origen        = $_POST['pais_origen']        ?? null;
$correo             = $_POST['correo']             ?? '';
$clasificacion      = $_POST['clasificacion']      ?? null;
$discapacidad       = $_POST['discapacidad']       ?? null;
$tipo_emprendedor   = $_POST['tipo_emprendedor']   ?? '';
$nivel_formacion    = $_POST['nivel_formacion']    ?? '';
$celular            = $_POST['celular']            ?? '';
$programa           = $_POST['programa']           ?? '';
$situacion_negocio  = $_POST['situacion_negocio']  ?? '';
$ficha              = $_POST['ficha']              ?? '';
$programa_formacion = $_POST['programa_formacion'] ?? '';
$centro_orientacion = $_POST['centro_orientacion'] ?? '';
$orientador         = $_POST['orientador']         ?? '';

/* -------------------------------------------------------------
   4. Guardar en la tabla
------------------------------------------------------------- */
$sql = "INSERT INTO ruta_emprendedora
        (nombres, apellidos, departamento, municipio, pais, tipo_id, numero_id,
         fecha_nacimiento, fecha_orientacion, genero, nacionalidad, pais_origen,
         correo, clasificacion, discapacidad, tipo_emprendedor, nivel_formacion,
         celular, programa, situacion_negocio, ficha, programa_formacion,
         centro_orientacion, orientador)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    'ssssssssssssssssssssssss',
    $nombres, $apellidos, $departamento, $municipio, $pais, $tipo_id, $numero_id,
    $fecha_nacimiento, $fecha_orientacion, $genero, $nacionalidad, $pais_origen,
    $correo, $clasificacion, $discapacidad, $tipo_emprendedor, $nivel_formacion,
    $celular, $programa, $situacion_negocio, $ficha, $programa_formacion,
    $centro_orientacion, $orientador
);

$exito = mysqli_stmt_execute($stmt);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Datos enviados!</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body{
            font-family:'Sora',sans-serif;
            background:linear-gradient(135deg,#e8f5e9,#c8e6c9);
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
        <h1>¡Datos enviados con éxito!</h1>
        <p>Gracias por registrar tu información.</p>
        <a class="btn" href="../../dashboard.html">Volver</a>
    </div>
</body>
</html>