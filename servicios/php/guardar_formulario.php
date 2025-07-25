<?php
// Configuración
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'fondo_emprender';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    http_response_code(500);
    exit('Error de conexión');
}
mysqli_set_charset($conn, 'utf8mb4');

// Primero defines
$nivel_formacion = isset($_POST['nivel_formacion']) ? mb_strtoupper(trim($_POST['nivel_formacion']), 'UTF-8') : '';
$carrera = null;

switch ($nivel_formacion) {
  case 'TÉCNICO':
    $carrera = isset($_POST['carrera_tecnico']) ? trim($_POST['carrera_tecnico']) : null;
    break;
  case 'TECNÓLOGO':
    $carrera = isset($_POST['carrera_tecnologo']) ? trim($_POST['carrera_tecnologo']) : null;
    break;
  case 'OPERARIO':
    $carrera = isset($_POST['carrera_operario']) ? trim($_POST['carrera_operario']) : null;
    break;
  case 'AUXILIAR':
    $carrera = isset($_POST['carrera_auxiliar']) ? trim($_POST['carrera_auxiliar']) : null;
    break;
  default:
    $carrera = null;
}







$nombres            = ucfirst(mb_strtolower(trim($_POST['nombres']), 'UTF-8'));
$apellidos          = ucfirst(mb_strtolower(trim($_POST['apellidos']), 'UTF-8'));
$departamento       = ($_POST['departamento'] === 'Otro' && !empty($_POST['departamento_otro']))
? ucfirst(mb_strtolower(trim($_POST['departamento_otro']), 'UTF-8'))
: ucfirst(mb_strtolower(trim($_POST['departamento']), 'UTF-8'));
$municipio          = ucfirst(mb_strtolower(trim($_POST['municipio']), 'UTF-8'));
$tipo_id            = mb_strtoupper(trim($_POST['tipo_id']), 'UTF-8');
$numero_id          = mb_strtoupper(trim($_POST['numero_id']), 'UTF-8');
$correo = filter_var(strtolower(trim($_POST['correo'])), FILTER_SANITIZE_EMAIL);
$pais = isset($_POST['pais']) && $_POST['pais'] !== ''
    ? ucfirst(mb_strtolower(trim($_POST['pais']), 'UTF-8'))
    : '';
$fecha_nacimiento   = $_POST['fecha_nacimiento']   ?? '';
$fecha_expedicioncc   = $_POST['fecha_expedicion']   ?? '';
$fecha_orientacion  = $_POST['fecha_orientacion']  ?? '';
$pais_origen = empty($_POST['pais_origen']) ? null : $_POST['pais_origen'];
$celular = trim($_POST['celular']);
$genero             = ucfirst(mb_strtolower(trim($_POST['genero']), 'UTF-8'));
$nacionalidad       =ucfirst(mb_strtolower(trim($_POST['nacionalidad']), 'UTF-8'));
$clasificacion      = isset($_POST['clasificacion']) ? ucfirst(mb_strtolower(trim($_POST['clasificacion']), 'UTF-8')) : null;
$discapacidad       = isset($_POST['discapacidad']) ? ucfirst(mb_strtolower(trim($_POST['discapacidad']), 'UTF-8')) : null;
$tipo_emprendedor   = ucfirst(mb_strtolower(trim($_POST['tipo_emprendedor']), 'UTF-8'));
// $nivel_formacion    = ucfirst(mb_strtolower(trim($_POST['nivel_formacion']), 'UTF-8'));
$programa           = ($_POST['programa'] === 'Otro' && !empty($_POST['programa_especial_otro']))
                      ? ucfirst(mb_strtolower(trim($_POST['programa_especial_otro']),'UTF-8'))
                      : ucfirst(mb_strtolower(trim($_POST['programa']), 'UTF-8'));
$situacion_negocio  = ($_POST['situacion_negocio'] === 'Otro' && !empty($_POST['situacion_negocio_otro']))
                      ? ucfirst(mb_strtolower(trim($_POST['situacion_negocio_otro']), 'UTF-8'))
                      : ucfirst(mb_strtolower(trim($_POST['situacion_negocio']), 'UTF-8'));
$ficha              = ucfirst(mb_strtolower(trim($_POST['ficha']), 'UTF-8'));
$centro_orientacion = mb_strtoupper(trim($_POST['centro_orientacion']), 'UTF-8');
$orientador         = ucfirst(mb_strtolower(trim($_POST['orientador']), 'UTF-8'));

/* Preparar e insertar */
$sql = "INSERT INTO ruta_emprendedora
        (nombres, apellidos, departamento, municipio, pais, tipo_id, numero_id,
        fecha_nacimiento, fecha_expedicion, fecha_orientacion, genero, nacionalidad, pais_origen,
        correo, clasificacion, discapacidad, tipo_emprendedor, nivel_formacion,
        carrera, celular, programa, situacion_negocio, ficha, /*programa_formacion,*/
        centro_orientacion, orientador)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    'sssssssssssssssssssssssss',
    $nombres, $apellidos, $departamento, $municipio, $pais, $tipo_id, $numero_id,
    $fecha_nacimiento,$fecha_expedicioncc, $fecha_orientacion, $genero, $nacionalidad, $pais_origen,
    $correo, $clasificacion, $discapacidad, $tipo_emprendedor, $nivel_formacion,
    $carrera, $celular, $programa, $situacion_negocio, $ficha, //$programa_formacion,
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
        body{font-family:'Sora',sans-serif;background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;text-align:center}
        .card{background:#fff;padding:50px 60px;border-radius:14px;box-shadow:0 10px 25px rgba(0,0,0,.08);max-width:480px}
        .card h1{margin:0 0 15px;font-size:1.8rem}.card p{margin:0 0 25px}
        .btn{display:inline-block;padding:12px 26px;background:#39a900;color:#fff;border-radius:6px;text-decoration:none;transition:.3s}
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