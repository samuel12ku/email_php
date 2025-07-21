<?php
// 1. CONFIGURACIÓN DE ACCESO A LA BD
$host = 'localhost';
$db   = 'fondo_emprender';
$user = 'root';         // <-- cámbialo
$pass = '';             // <-- cámbialo
$charset = 'utf8mb4';

// 2. CONEXIÓN CON PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    die("Error de conexión: " . $e->getMessage());
}

// 3. RECIBIR Y VALIDAR DATOS
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

// 4. INSERTAR EN LA TABLA
$sql = "INSERT INTO ruta_emprendedora (
            nombres, apellidos, departamento, municipio, pais, tipo_id, numero_id,
            fecha_nacimiento, fecha_orientacion, genero, nacionalidad, pais_origen,
            correo, clasificacion, discapacidad, tipo_emprendedor, nivel_formacion,
            celular, programa, situacion_negocio, ficha, programa_formacion,
            centro_orientacion, orientador
        ) VALUES (
            :nombres, :apellidos, :departamento, :municipio, :pais, :tipo_id, :numero_id,
            :fecha_nacimiento, :fecha_orientacion, :genero, :nacionalidad, :pais_origen,
            :correo, :clasificacion, :discapacidad, :tipo_emprendedor, :nivel_formacion,
            :celular, :programa, :situacion_negocio, :ficha, :programa_formacion,
            :centro_orientacion, :orientador
        )";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nombres'            => $nombres,
    ':apellidos'          => $apellidos,
    ':departamento'       => $departamento,
    ':municipio'          => $municipio,
    ':pais'               => $pais,
    ':tipo_id'            => $tipo_id,
    ':numero_id'          => $numero_id,
    ':fecha_nacimiento'   => $fecha_nacimiento,
    ':fecha_orientacion'  => $fecha_orientacion,
    ':genero'             => $genero,
    ':nacionalidad'       => $nacionalidad,
    ':pais_origen'        => $pais_origen,
    ':correo'             => $correo,
    ':clasificacion'      => $clasificacion,
    ':discapacidad'       => $discapacidad,
    ':tipo_emprendedor'   => $tipo_emprendedor,
    ':nivel_formacion'    => $nivel_formacion,
    ':celular'            => $celular,
    ':programa'           => $programa,
    ':situacion_negocio'  => $situacion_negocio,
    ':ficha'              => $ficha,
    ':programa_formacion' => $programa_formacion,
    ':centro_orientacion' => $centro_orientacion,
    ':orientador'         => $orientador,
]);

// 5. RESPONDER AL USUARIO
header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'msg' => 'Registro guardado con éxito']);
?>