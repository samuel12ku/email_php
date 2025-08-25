<?php
include "../conexion.php";
$conn = ConectarDB();

// =======================
// REGLAS DE IDENTIFICACIÓN
// =======================
$reglas = [
    'TI'  => ['min' => 6, 'max' => 10, 'soloNumeros' => true ],
    'CC'  => ['min' => 6, 'max' => 10, 'soloNumeros' => true ],
    'CE'  => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
    'PEP' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
    'PAS' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
    'PPT' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
];

// =======================
// VARIABLES DEL FORM
// =======================

// Nivel formación y carrera
$nivel_formacion = mb_strtoupper(trim($_POST['nivel_formacion'] ?? ''), 'UTF-8');
$carrera = '';
switch ($nivel_formacion) {
    case 'TÉCNICO':    $carrera = trim($_POST['carrera_tecnico']   ?? ''); break;
    case 'TECNÓLOGO':  $carrera = trim($_POST['carrera_tecnologo'] ?? ''); break;
    case 'OPERARIO':   $carrera = trim($_POST['carrera_operario']  ?? ''); break;
    case 'AUXILIAR':   $carrera = trim($_POST['carrera_auxiliar']  ?? ''); break;
    case 'PROFESIONAL': $carrera = trim($_POST['carrera_profesional'] ?? ''); break;
}

// ------- Variables del formulario (strings normalizados) -------
$nombres    = mb_convert_case(trim($_POST['nombres']   ?? ''), MB_CASE_TITLE, "UTF-8");
$apellidos  = mb_convert_case(trim($_POST['apellidos'] ?? ''), MB_CASE_TITLE, "UTF-8");

// Tipo y número de identificación
$tipo_id   = mb_strtoupper(trim($_POST['tipo_id']   ?? ''), 'UTF-8');
$numero_id = mb_strtoupper(trim($_POST['numero_id'] ?? ''), 'UTF-8');

error_log("DEBUG tipo_id recibido (raw): " . $_POST['tipo_id']);
error_log("DEBUG tipo_id procesado: " . $tipo_id);


// Validación identificación
if (!isset($reglas[$tipo_id])) {
    http_response_code(422);
    exit("Tipo de identificación no válido. Tipos permitidos: " . implode(", ", array_keys($reglas)));
}
$rg  = $reglas[$tipo_id];
$len = mb_strlen($numero_id, 'UTF-8');

if ($len < $rg['min'] || $len > $rg['max']) {
    http_response_code(422);
    exit("Número de identificación inválido: debe tener entre {$rg['min']} y {$rg['max']} caracteres.");
}
if ($rg['soloNumeros'] && !preg_match('/^\d+$/', $numero_id)) {
    http_response_code(422);
    exit("Número de identificación inválido: solo se permiten dígitos.");
}
if (!$rg['soloNumeros'] && !preg_match('/^[A-Za-z0-9]+$/', $numero_id)) {
    http_response_code(422);
    exit("Número de identificación inválido: solo letras y/o números, sin espacios ni símbolos.");
}

// Correo y celular
$correo  = filter_var(strtolower(trim($_POST['correo'] ?? '')), FILTER_SANITIZE_EMAIL);
$celular = (string)trim($_POST['celular'] ?? '');

// Sexo / género
$sexo = ucfirst(mb_strtolower(trim($_POST['sexo'] ?? ($_POST['genero'] ?? '')), 'UTF-8'));

// Ubicación
$departamento = (($_POST['departamento'] ?? '') === 'Otro' && !empty($_POST['departamento_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['departamento_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['departamento'] ?? ''), 'UTF-8'));

$municipio          = ucfirst(mb_strtolower(trim($_POST['municipio']        ?? ''), 'UTF-8'));
$fecha_nacimiento   = (string)($_POST['fecha_nacimiento'] ?? '');
// $fecha_expedicion   = (string)($_POST['fecha_expedicion'] ?? ''); // nueva columna a guardar

// Tiempos
date_default_timezone_set('America/Bogota');
$hora_inicio      = $_POST['ts_inicio'] ?? date('Y-m-d H:i:s');
$hora_fin         = date('Y-m-d H:i:s');
$ts_inicio        = $hora_inicio;
$fecha_registro   = date('Y-m-d H:i:s');

// Nacionalidad
$pais_origen  = (string)($_POST['pais_origen'] ?? '');
$nacionalidad = ucfirst(mb_strtolower(trim($_POST['nacionalidad'] ?? ''), 'UTF-8'));
$pais         = $pais_origen;

// Otros datos
$clasificacion       = ucfirst(mb_strtolower(trim($_POST['clasificacion'] ?? ''), 'UTF-8'));
$discapacidad        = ucfirst(mb_strtolower(trim($_POST['discapacidad'] ?? ''), 'UTF-8'));
$tipo_emp_post       = trim($_POST['tipo_emprendedor'] ?? '');
$tipo_emp_otro       = trim($_POST['tipo_emprendedor_otro'] ?? '');
$tipo_emprendedor    = ($tipo_emp_post !== '' && strcasecmp($tipo_emp_post, 'otro') !== 0)
                        ? ucfirst(mb_strtolower($tipo_emp_post, 'UTF-8'))
                        : ucfirst(mb_strtolower($tipo_emp_otro, 'UTF-8'));

$programa = (($_POST['programa'] ?? '') === 'Otro' && !empty($_POST['programa_especial_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['programa_especial_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['programa'] ?? ''), 'UTF-8'));

$situacion_negocio = (($_POST['situacion_negocio'] ?? '') === 'Otro' && !empty($_POST['situacion_negocio_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['situacion_negocio_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['situacion_negocio'] ?? ''), 'UTF-8'));

$ejercer_actividad   = mb_strtoupper(trim($_POST['ejercer_actividad_proyecto'] ?? ''), 'UTF-8');
$empresa_formalizada = mb_strtoupper(trim($_POST['empresa_formalizada'] ?? ''), 'UTF-8');
$ficha               = ucfirst(mb_strtolower(trim($_POST['ficha'] ?? ''), 'UTF-8'));

// Centro de orientación (QR o manual)
$centro_orientacion_qr = trim($_POST['centro_orientacion'] ?? '');
$centro_orientacion    = $centro_orientacion_qr !== ''
    ? mb_strtoupper($centro_orientacion_qr, 'UTF-8')
    : mb_strtoupper(trim($_POST['centro_orientacion'] ?? ''), 'UTF-8');

// Orientador
$orientador_nombre = preg_replace('/\s+/', ' ', trim($_POST['orientador'] ?? ''));
if ($orientador_nombre === '') {
    http_response_code(422);
    exit("Debes seleccionar un orientador.");
}
$orientador_id = (int)($_POST['orientador_id_prefill'] ?? 0);

// =======================
// CHEQUEO DUPLICADOS
// =======================
$duplicados = [];

$chk1 = $conn->prepare("SELECT 1 FROM orientacion_rcde2025_valle WHERE numero_id = ? LIMIT 1");
$chk1->bind_param("s", $numero_id);
$chk1->execute(); $chk1->store_result();
if ($chk1->num_rows > 0) { $duplicados[] = "El número de identificación ya está registrado."; }
$chk1->close();

$chk2 = $conn->prepare("SELECT 1 FROM orientacion_rcde2025_valle WHERE correo = ? LIMIT 1");
$chk2->bind_param("s", $correo);
$chk2->execute(); $chk2->store_result();
if ($chk2->num_rows > 0) { $duplicados[] = "El correo ya está registrado."; }
$chk2->close();

if (!empty($duplicados)) {
    http_response_code(409);
    exit("No se pudo guardar: datos duplicados → " . implode(", ", $duplicados));
}

// =======================
// DEFAULTS
// =======================
$rol                = 'emprendedor';
$estado_proceso_def = 'pendiente';
$acceso_panel_def   = 0;
$contrasena_hash    = '';

// =======================
// BUSCAR ORIENTADOR_ID
// =======================
$orientador_id = 0;
$orientador_nombre_normal = preg_replace('/\s+/', ' ', trim($orientador_nombre));

if ($q1 = $conn->prepare("SELECT id_orientador FROM orientadores WHERE LOWER(TRIM(CONCAT(nombres,' ',apellidos))) = LOWER(?) LIMIT 1")) {
    $q1->bind_param("s", $orientador_nombre_normal);
    $q1->execute();
    $q1->bind_result($tmpId1);
    if ($q1->fetch()) { $orientador_id = (int)$tmpId1; }
    $q1->close();
}

// =======================
// INSERT FINAL
// =======================
$sql = "INSERT INTO orientacion_rcde2025_valle
        (hora_inicio, hora_fin, nombres, apellidos, tipo_id, numero_id, correo,
         celular, pais, nacionalidad, departamento, municipio, fecha_nacimiento,
         fecha_orientacion, sexo, clasificacion, discapacidad, tipo_emprendedor,
         nivel_formacion, ficha, carrera, programa, situacion_negocio, centro_orientacion,
        fecha_registro, orientador_id, orientador, pais_origen, rol,
         ejercer_actividad_proyecto, empresa_formalizada, contrasena, estado_proceso, acceso_panel)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// 24 's' (hasta fecha_registro), 1 'i' (orientador_id), 7 's' (incluye estado_proceso), 1 'i' (acceso_panel)
$types = str_repeat('s', 25) . 'i' . str_repeat('s', 7) . 'i';
 

$stmt->bind_param(
    $types,
    $hora_inicio, $hora_fin, $nombres, $apellidos, $tipo_id, $numero_id, $correo,
    $celular, $pais, $nacionalidad, $departamento,
    $municipio, $fecha_nacimiento, $ts_inicio,
    $sexo, $clasificacion, $discapacidad, $tipo_emprendedor, $nivel_formacion,
    $ficha, $carrera, $programa, $situacion_negocio, $centro_orientacion,
    $fecha_registro, $orientador_id, $orientador_nombre, $pais_origen, $rol,
    $ejercer_actividad, $empresa_formalizada, $contrasena_hash, $estado_proceso_def, $acceso_panel_def
);

$exito = $stmt->execute();
$stmt->close();

if ($exito) {
    echo "✅ Registro exitoso de $nombres $apellidos";
} else {
    $err = mysqli_error($conn);
    $conn->close();
    echo "❌ Error al guardar en la base de datos. Detalle: " . htmlspecialchars($err);
}

