<?php
// 1. CONFIGURACIÓN
$host = 'localhost';
$user = 'root';      // <-- tu usuario
$pass = '';          // <-- tu contraseña
$db   = 'fondo_emprender';

// 2. CONEXIÓN
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    http_response_code(500);
    die('Error de conexión: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// 3. RECOGER DATOS
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

// 4. CONSULTA PREPARADA
$sql = "INSERT INTO ruta_emprendedora_2025 (
            nombres, apellidos, departamento, municipio, pais, tipo_id, numero_id,
            fecha_nacimiento, fecha_orientacion, genero, nacionalidad, pais_origen,
            correo, clasificacion, discapacidad, tipo_emprendedor, nivel_formacion,
            celular, programa, situacion_negocio, ficha, programa_formacion,
            centro_orientacion, orientador
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    http_response_code(500);
    die('Error preparando la consulta: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    'ssssssssssssssssssssssss',
    $nombres, $apellidos, $departamento, $municipio, $pais, $tipo_id, $numero_id,
    $fecha_nacimiento, $fecha_orientacion, $genero, $nacionalidad, $pais_origen,
    $correo, $clasificacion, $discapacidad, $tipo_emprendedor, $nivel_formacion,
    $celular, $programa, $situacion_negocio, $ficha, $programa_formacion,
    $centro_orientacion, $orientador
);

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

// 5. RESPUESTA (igual que antes: JSON para el JS)
header('Content-Type: application/json');
if ($ok) {
    echo json_encode(['status' => 'ok', 'msg' => 'Registro guardado con éxito']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'No se pudo guardar el registro']);
}
?>