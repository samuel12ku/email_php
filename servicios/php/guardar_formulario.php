<?php

include_once "../conexion.php";
$conn = ConectarDB();

mysqli_set_charset($conn, 'utf8mb4');

// ------- Nivel de formación y carrera -------
$nivel_formacion = isset($_POST['nivel_formacion']) ? mb_strtoupper(trim($_POST['nivel_formacion']), 'UTF-8') : '';
$carrera = '';
switch ($nivel_formacion) {
    case 'TÉCNICO':
    case 'TECNICO':    $carrera = trim($_POST['carrera_tecnico']   ?? ''); break;
    case 'TECNÓLOGO':
    case 'TECNOLOGO':  $carrera = trim($_POST['carrera_tecnologo'] ?? ''); break;
    case 'OPERARIO':   $carrera = trim($_POST['carrera_operario']  ?? ''); break;
    case 'AUXILIAR':   $carrera = trim($_POST['carrera_auxiliar']  ?? ''); break;
}

// ------- Variables del formulario (strings normalizados) -------
$nombres    = mb_convert_case(trim($_POST['nombres']   ?? ''), MB_CASE_TITLE, "UTF-8");
$apellidos  = mb_convert_case(trim($_POST['apellidos'] ?? ''), MB_CASE_TITLE, "UTF-8");

// Tipo y número de identificación con validación
$tipo_id   = mb_strtoupper(trim($_POST['tipo_id']   ?? ''), 'UTF-8');
$numero_id = mb_strtoupper(trim($_POST['numero_id'] ?? ''), 'UTF-8');

// Reglas de número de identificación
$reglas = [
    'TI'  => ['min' => 6, 'max' => 10, 'soloNumeros' => true ],
    'CC'  => ['min' => 6, 'max' => 10, 'soloNumeros' => true ],
    'CE'  => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
    'PEP' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
    'PPT' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
    'PAS' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
];
if (!isset($reglas[$tipo_id])) { http_response_code(422); exit('Tipo de identificación inválido.'); }
$rg  = $reglas[$tipo_id];
$len = mb_strlen($numero_id, 'UTF-8');
if ($len < $rg['min'] || $len > $rg['max'])                   { http_response_code(422); exit("Número de identificación inválido: debe tener entre {$rg['min']} y {$rg['max']} caracteres."); }
if ($rg['soloNumeros'] && !preg_match('/^\d+$/', $numero_id)) { http_response_code(422); exit("Número de identificación inválido: solo se permiten dígitos."); }
if (!$rg['soloNumeros'] && !preg_match('/^[A-Za-z0-9]+$/', $numero_id)) { http_response_code(422); exit("Número de identificación inválido: solo letras y/o números, sin espacios ni símbolos."); }

$correo  = filter_var(strtolower(trim($_POST['correo'] ?? '')), FILTER_SANITIZE_EMAIL);
$celular = (string)trim($_POST['celular'] ?? '');

// Aceptar 'sexo' o 'genero' desde el front
$sexo = ucfirst(mb_strtolower(trim($_POST['sexo'] ?? $_POST['genero'] ?? ''), 'UTF-8'));

$departamento = (($_POST['departamento'] ?? '') === 'Otro' && !empty($_POST['departamento_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['departamento_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['departamento']       ?? ''), 'UTF-8'));

$municipio          = ucfirst(mb_strtolower(trim($_POST['municipio']        ?? ''), 'UTF-8'));
$fecha_nacimiento   = (string)($_POST['fecha_nacimiento'] ?? '');
$fecha_expedicion   = (string)($_POST['fecha_expedicion'] ?? ''); // nueva columna a guardar

// Tiempos
date_default_timezone_set('America/Bogota');
$ts_inicio      = $_POST['ts_inicio']   ?? date('Y-m-d H:i:s'); // fecha_orientacion (inicio de diligenciamiento)
$fecha_registro = date('Y-m-d H:i:s');

// Momento en que empezó a diligenciar (puedes usar ts_inicio si ya lo tienes)
$hora_inicio = $_POST['ts_inicio'] ?? date('Y-m-d H:i:s');

// Momento en que guardó (cuando ejecuta este PHP)
$hora_fin = date('Y-m-d H:i:s');

// País y nacionalidad
$pais_origen  = (string)($_POST['pais_origen']  ?? '');
$nacionalidad = ucfirst(mb_strtolower(trim($_POST['nacionalidad'] ?? ''), 'UTF-8'));
$pais         = $pais_origen;

$clasificacion       = ucfirst(mb_strtolower(trim($_POST['clasificacion']      ?? ''), 'UTF-8'));
$discapacidad        = ucfirst(mb_strtolower(trim($_POST['discapacidad']       ?? ''), 'UTF-8'));
// Tipo de emprendedor con soporte "Otro"
$tipo_emp_post = trim($_POST['tipo_emprendedor'] ?? '');
$tipo_emp_otro = trim($_POST['tipo_emprendedor_otro'] ?? '');

if ($tipo_emp_post !== '' && strcasecmp($tipo_emp_post, 'otro') !== 0) {
    $tipo_emprendedor = ucfirst(mb_strtolower($tipo_emp_post, 'UTF-8'));
} else {
    // Si seleccionó "Otro" o viene vacío, usa el input
    $tipo_emprendedor = ucfirst(mb_strtolower($tipo_emp_otro, 'UTF-8'));
}
$programa = (($_POST['programa'] ?? '') === 'Otro' && !empty($_POST['programa_especial_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['programa_especial_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['programa']                ?? ''), 'UTF-8'));
$situacion_negocio = (($_POST['situacion_negocio'] ?? '') === 'Otro' && !empty($_POST['situacion_negocio_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['situacion_negocio_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['situacion_negocio']      ?? ''), 'UTF-8'));

$ejercer_actividad   = mb_strtoupper(trim($_POST['ejercer_actividad_proyecto'] ?? ''), 'UTF-8');
$empresa_formalizada = mb_strtoupper(trim($_POST['empresa_formalizada']        ?? ''), 'UTF-8');
$ficha               = ucfirst(mb_strtolower(trim($_POST['ficha']              ?? ''), 'UTF-8'));
// $centro_orientacion  = mb_strtoupper(trim($_POST['centro_orientacion']         ?? ''), 'UTF-8');

// // Orientador (obligatorio por nombre) y búsqueda de orientador_id
// $orientador_nombre = preg_replace('/\s+/', ' ', trim($_POST['orientador'] ?? ''));
// Centro: si vino desde QR (hidden), úsalo
$centro_orientacion_qr = trim($_POST['centro_orientacion'] ?? ''); // el hidden que agregamos
if ($centro_orientacion_qr !== '') {
    $centro_orientacion = mb_strtoupper($centro_orientacion_qr, 'UTF-8');
} else {
    $centro_orientacion = mb_strtoupper(trim($_POST['centro_orientacion'] ?? ''), 'UTF-8');
}

// Orientador (nombre completo) priorizando lo del QR
$orientador_nombre_qr = preg_replace('/\s+/', ' ', trim($_POST['orientador'] ?? ''));
if ($orientador_nombre_qr !== '') {
    $orientador_nombre = $orientador_nombre_qr;
} else {
    $orientador_nombre = preg_replace('/\s+/', ' ', trim($_POST['orientador'] ?? ''));
}

// (Opcional) Si mandaste orientador_id_prefill en hidden:
$orientador_id = (int)($_POST['orientador_id_prefill'] ?? 0);
// Si no vino, realiza tu búsqueda por nombre como ya lo haces.

if ($orientador_nombre === '') {
    http_response_code(422);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Falta el orientador</title>
        <style>
            body{
                font-family:sans-serif;
                background:#fff7f7;
                color:#b71c1c;
                display:flex;
                align-items:center;
                justify-content:center;
                height:100vh;
                margin:0
                }
            .card{
                background:#fff;
                padding:28px 32px;
                border-radius:10px;
                box-shadow:0 10px 25px rgba(0,0,0,.08);
                max-width:640px;
                text-align:center
                }
            .btn{
                display:inline-block;
                margin-top:14px;
                padding:10px 18px;
                background:#b71c1c;
                color:#fff;
                border-radius:6px;
                text-decoration:none
                }
        </style>
    </head>
    <body><div class="card"><h2>Debes seleccionar un orientador</h2><a class="btn" href="javascript:history.back()">Volver</a></div></body></html>
    <?php
    exit;
}



// ------- Chequeo de duplicados en la MISMA tabla de orientación -------
$duplicados = [];

// ¿Número de identificación ya existe?
$chk1 = $conn->prepare("SELECT 1 FROM orientacion_rcde2025_valle WHERE numero_id = ? LIMIT 1");
$chk1->bind_param("s", $numero_id);
$chk1->execute(); $chk1->store_result();
if ($chk1->num_rows > 0) { $duplicados[] = "El número de identificación ya está registrado."; }
$chk1->close();

// ¿Correo ya existe?
$chk2 = $conn->prepare("SELECT 1 FROM orientacion_rcde2025_valle WHERE correo = ? LIMIT 1");
$chk2->bind_param("s", $correo);
$chk2->execute(); $chk2->store_result();
if ($chk2->num_rows > 0) { $duplicados[] = "El correo ya está registrado."; }
$chk2->close();

if (!empty($duplicados)) {
    http_response_code(409); ?>
    <!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Datos duplicados</title>
    <style>body{font-family:sans-serif;background:#fff7f7;color:#b71c1c;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}.card{background:#fff;padding:28px 32px;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,.08);max-width:680px}.btn{display:inline-block;margin-top:14px;padding:10px 18px;background:#b71c1c;color:#fff;border-radius:6px;text-decoration:none}ul{margin:8px 0 0 18px}</style>
    </head><body><div class="card"><h2>No pudimos guardar: se encontraron datos duplicados</h2><ul>
    <?php foreach ($duplicados as $msg) { echo "<li>".htmlspecialchars($msg)."</li>"; } ?>
    </ul><a class="btn" href="javascript:history.back()">Volver y corregir</a></div></body></html><?php
    exit;
}

// ------- Defaults solicitados -------
$rol                = 'emprendedor';
$contrasena_hash    = password_hash($numero_id, PASSWORD_DEFAULT);
$estado_proceso_def = 'pendiente';
$acceso_panel_def   = 0; // INT

// Buscar orientador_id a partir del nombre completo elegido en el form
// En orientadores: nombres = "Celiced", apellidos = "Castaño Barco"
$orientador_id = 0;

// Normaliza espacios a uno solo
$orientador_nombre_normal = preg_replace('/\s+/', ' ', trim($orientador_nombre));

// 1) Match por nombre completo: CONCAT(nombres, ' ', apellidos)
if ($q1 = $conn->prepare(" SELECT id_orientador FROM orientadores WHERE CONCAT_WS(' ', TRIM(nombres), TRIM(apellidos)) COLLATE utf8mb4_spanish_ci = ? LIMIT 1
")) {
    $q1->bind_param("s", $orientador_nombre_normal);
    $q1->execute();
    $q1->bind_result($tmpId1);
    if ($q1->fetch()) { $orientador_id = (int)$tmpId1; }
    $q1->close();
}

// 2) Fallback: primer token como 'nombres', resto como 'apellidos'
if ($orientador_id === 0) {
    $partes = explode(' ', $orientador_nombre_normal, 2);
    $nombre_primero = $partes[0] ?? '';
    $apellidos_rest = $partes[1] ?? '';
    if ($nombre_primero !== '' && $apellidos_rest !== '') {
        if ($q2 = $conn->prepare(" SELECT id_orientador FROM orientadores WHERE TRIM(nombres)   COLLATE utf8mb4_spanish_ci = ? AND TRIM(apellidos) COLLATE utf8mb4_spanish_ci = ? LIMIT 1
        ")) {
            $q2->bind_param("ss", $nombre_primero, $apellidos_rest);
            $q2->execute();
            $q2->bind_result($tmpId2);
            if ($q2->fetch()) { $orientador_id = (int)$tmpId2; }
            $q2->close();
        }
    }
}


$orientador_id = $orientador_id ?? 0;  // si no lo encuentra, 0

// ------- INSERT principal (ORDEN EXACTO requerido) -------
// Incluye: fecha_expedicion, fecha_registro y orientador_id ANTES de 'orientador'
$sql = "INSERT INTO orientacion_rcde2025_valle
        (hora_inicio, hora_fin, nombres, apellidos, tipo_id, numero_id, correo,
         celular, pais, nacionalidad, departamento, municipio, fecha_nacimiento,
         fecha_orientacion, sexo, clasificacion, discapacidad, tipo_emprendedor,
         nivel_formacion, ficha, carrera, programa, situacion_negocio, centro_orientacion,
         fecha_expedicion, fecha_registro, orientador_id, orientador, pais_origen, rol,
         ejercer_actividad_proyecto, empresa_formalizada, contrasena, estado_proceso, acceso_panel)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// 24 's' (hasta fecha_registro), 1 'i' (orientador_id), 7 's' (incluye estado_proceso), 1 'i' (acceso_panel)
$types = str_repeat('s', 26) . 'i' . str_repeat('s', 7) . 'i';
 

$stmt->bind_param(
    $types,
    $hora_inicio, $hora_fin, $nombres, $apellidos, $tipo_id, $numero_id, $correo,
    $celular, $pais, $nacionalidad, $departamento,
    $municipio, $fecha_nacimiento, $ts_inicio,
    $sexo, $clasificacion, $discapacidad, $tipo_emprendedor, $nivel_formacion,
    $ficha, $carrera, $programa, $situacion_negocio, $centro_orientacion,
    $fecha_expedicion, $fecha_registro, $orientador_id, $orientador_nombre, $pais_origen, $rol,
    $ejercer_actividad, $empresa_formalizada, $contrasena_hash, $estado_proceso_def, $acceso_panel_def
);

$exito = $stmt->execute();
$stmt->close();

if ($exito) {
    $conn->close();
    // ÉXITO
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
            .card h1{margin:0 0 15px;font-size:1.8rem}
            .card p{margin:0 0 25px}
            .btn{display:inline-block;padding:12px 26px;background:#39a900;color:#fff;border-radius:6px;text-decoration:none;transition:.3s}
            .btn:hover{background:#2e7d32}
            @media (max-width: 480px){.card{padding:30px 40px}.card h1{font-size:1.5rem}.btn{padding:10px 20px;font-size:0.9rem}}
            @media (max-width: 760px){.card{max-width:90%;padding:30px 20px}}
        </style>
    </head>
    <body>
        <div class="card">
            <h1>¡Datos enviados con éxito!</h1>
            <p>Gracias por registrar tu información.</p>
            <a class="btn" href="../../login.php">Ir al inicio de sesión</a>
        </div>
    </body>
    </html>
    <?php
    exit;
} else {
    $err = mysqli_error($conn);
    $conn->close();
    echo "❌ Error al guardar en la base de datos. Detalle: " . htmlspecialchars($err);
}
