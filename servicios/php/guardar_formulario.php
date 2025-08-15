<?php
// Conexión
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'fondo_emprender';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) { http_response_code(500); exit('Error de conexión'); }
mysqli_set_charset($conn, 'utf8mb4');

// ------- Nivel de formación y carrera -------
$nivel_formacion = isset($_POST['nivel_formacion']) ? mb_strtoupper(trim($_POST['nivel_formacion']), 'UTF-8') : '';
$carrera = '';
switch ($nivel_formacion) {
    case 'TÉCNICO':   $carrera = trim($_POST['carrera_tecnico']   ?? ''); break;
    case 'TECNÓLOGO': $carrera = trim($_POST['carrera_tecnologo'] ?? ''); break;
    case 'OPERARIO':  $carrera = trim($_POST['carrera_operario']  ?? ''); break;
    case 'AUXILIAR':  $carrera = trim($_POST['carrera_auxiliar']  ?? ''); break;
}

// ------- Variables del formulario (todas como string) -------
$nombres            = mb_convert_case(trim($_POST['nombres']      ?? ''), MB_CASE_TITLE, "UTF-8");
$apellidos          = mb_convert_case(trim($_POST['apellidos']    ?? ''), MB_CASE_TITLE, "UTF-8");

// Tipo y número de identificación con validación
$tipo_id            = mb_strtoupper(trim($_POST['tipo_id']        ?? ''), 'UTF-8');
$numero_id          = mb_strtoupper(trim($_POST['numero_id']      ?? ''), 'UTF-8');

// Reglas espejo del front (incluye PEP, PPT, PAS)
$reglas = [
  'TI'  => ['min' => 6, 'max' => 10, 'soloNumeros' => true ],
  'CC'  => ['min' => 6, 'max' => 12, 'soloNumeros' => true ],
  'CE'  => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
  'PEP' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
  'PPT' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
  'PAS' => ['min' => 6, 'max' => 15, 'soloNumeros' => false],
];
if (!isset($reglas[$tipo_id])) { http_response_code(422); exit('Tipo de identificación inválido.'); }
$rg  = $reglas[$tipo_id];
$len = mb_strlen($numero_id, 'UTF-8');
if ($len < $rg['min'] || $len > $rg['max'])           { http_response_code(422); exit("Número de identificación inválido: debe tener entre {$rg['min']} y {$rg['max']} caracteres."); }
if ($rg['soloNumeros'] && !preg_match('/^\d+$/', $numero_id)) { http_response_code(422); exit("Número de identificación inválido: solo se permiten dígitos."); }
if (!$rg['soloNumeros'] && !preg_match('/^[A-Za-z0-9]+$/', $numero_id)) { http_response_code(422); exit("Número de identificación inválido: solo letras y/o números, sin espacios ni símbolos."); }

$correo             = filter_var(strtolower(trim($_POST['correo'] ?? '')), FILTER_SANITIZE_EMAIL);
$orientador_nombre  = preg_replace('/\s+/', ' ', trim($_POST['orientador'] ?? '')); // nombre completo

$departamento = (($_POST['departamento'] ?? '') === 'Otro' && !empty($_POST['departamento_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['departamento_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['departamento']       ?? ''), 'UTF-8'));

$municipio            = ucfirst(mb_strtolower(trim($_POST['municipio']          ?? ''), 'UTF-8'));
$fecha_nacimiento     = (string)($_POST['fecha_nacimiento']   ?? '');
$fecha_expedicioncc   = (string)($_POST['fecha_expedicion']   ?? '');

// Fecha + hora (del inicio del formulario). Si no llega, usa la del servidor.
date_default_timezone_set('America/Bogota');
$ts_inicio            = $_POST['ts_inicio'] ?? date('Y-m-d H:i:s');

$pais_origen          = (string)($_POST['pais_origen']        ?? '');
$celular              = (string)trim($_POST['celular']        ?? '');
$genero               = ucfirst(mb_strtolower(trim($_POST['genero']             ?? ''), 'UTF-8'));
$nacionalidad         = ucfirst(mb_strtolower(trim($_POST['nacionalidad']       ?? ''), 'UTF-8'));
$clasificacion        = ucfirst(mb_strtolower(trim($_POST['clasificacion']      ?? ''), 'UTF-8'));
$discapacidad         = ucfirst(mb_strtolower(trim($_POST['discapacidad']       ?? ''), 'UTF-8'));
$tipo_emprendedor     = ucfirst(mb_strtolower(trim($_POST['tipo_emprendedor']   ?? ''), 'UTF-8'));
$programa = (($_POST['programa'] ?? '') === 'Otro' && !empty($_POST['programa_especial_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['programa_especial_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['programa']                ?? ''), 'UTF-8'));
$situacion_negocio = (($_POST['situacion_negocio'] ?? '') === 'Otro' && !empty($_POST['situacion_negocio_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['situacion_negocio_otro'] ?? ''), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['situacion_negocio']      ?? ''), 'UTF-8'));
$ejercer_actividad    = mb_strtoupper(trim($_POST['ejercer_actividad_proyecto'] ?? ''), 'UTF-8');
$empresa_formalizada  = mb_strtoupper(trim($_POST['empresa_formalizada']        ?? ''), 'UTF-8');
$ficha                = ucfirst(mb_strtolower(trim($_POST['ficha']              ?? ''), 'UTF-8'));
$centro_orientacion   = mb_strtoupper(trim($_POST['centro_orientacion']         ?? ''), 'UTF-8');

// ------- Validación mínima de orientador (obligatorio como nombre) -------
if ($orientador_nombre === '') {
    http_response_code(422);
    ?>
    <!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Falta el orientador</title>
    <style>body{font-family:sans-serif;background:#fff7f7;color:#b71c1c;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
    .card{background:#fff;padding:28px 32px;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,.08);max-width:640px;text-align:center}
    .btn{display:inline-block;margin-top:14px;padding:10px 18px;background:#b71c1c;color:#fff;border-radius:6px;text-decoration:none}</style></head>
    <body><div class="card"><h2>Debes seleccionar un orientador</h2><a class="btn" href="javascript:history.back()">Volver</a></div></body></html>
    <?php
    exit;
}

// ------- Chequeo de DUPLICADOS (ruta_emprendedora) -------
$duplicados = [];

// ¿Número de identificación ya existe?
$chk1 = $conn->prepare("SELECT 1 FROM ruta_emprendedora WHERE numero_id = ? LIMIT 1");
$chk1->bind_param("s", $numero_id);
$chk1->execute();
$chk1->store_result();
if ($chk1->num_rows > 0) { $duplicados[] = "El número de identificación ya está registrado."; }
$chk1->close();

// ¿Correo ya existe?
$chk2 = $conn->prepare("SELECT 1 FROM ruta_emprendedora WHERE correo = ? LIMIT 1");
$chk2->bind_param("s", $correo);
$chk2->execute();
$chk2->store_result();
if ($chk2->num_rows > 0) { $duplicados[] = "El correo ya está registrado."; }
$chk2->close();

if (!empty($duplicados)) {
    http_response_code(409); // Conflicto
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <title>Datos duplicados</title>
      <style>
        body{font-family:sans-serif;background:#fff7f7;color:#b71c1c;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
        .card{background:#fff;padding:28px 32px;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,.08);max-width:680px}
        ul{margin:8px 0 0 18px}
        .btn{display:inline-block;margin-top:14px;padding:10px 18px;background:#b71c1c;color:#fff;border-radius:6px;text-decoration:none}
      </style>
    </head>
    <body>
      <div class="card">
        <h2>No pudimos guardar: se encontraron datos duplicados</h2>
        <ul>
          <?php foreach ($duplicados as $msg) { echo "<li>".htmlspecialchars($msg)."</li>"; } ?>
        </ul>
        <a class="btn" href="javascript:history.back()">Volver y corregir</a>
      </div>
    </body>
    </html>
    <?php
    exit;
}

// ------- INSERT en ruta_emprendedora (27 columnas) -------
// Guardamos fecha y hora en fecha_orientacion con $ts_inicio
$sql = "INSERT INTO ruta_emprendedora
        (nombres, apellidos, departamento, municipio, tipo_id, numero_id,
         fecha_nacimiento, fecha_expedicion, fecha_orientacion, genero, nacionalidad, pais_origen,
         correo, clasificacion, discapacidad, tipo_emprendedor, nivel_formacion,
         carrera, celular, programa, situacion_negocio, ejercer_actividad_proyecto, empresa_formalizada, ficha,
         centro_orientacion, orientador)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$types = str_repeat('s', 26);
$stmt->bind_param(
    $types,
    $nombres, $apellidos, $departamento, $municipio, $tipo_id, $numero_id,
    $fecha_nacimiento, $fecha_expedicioncc, $ts_inicio, // <-- DATETIME
    $genero, $nacionalidad, $pais_origen,
    $correo, $clasificacion, $discapacidad, $tipo_emprendedor, $nivel_formacion,
    $carrera, $celular, $programa, $situacion_negocio, $ejercer_actividad, $empresa_formalizada, $ficha,
    $centro_orientacion, $orientador_nombre
);

$exito = $stmt->execute();
$stmt->close();

if ($exito) {
    // Usuarios: sin orientador_id por ahora
    $rol_usuario     = 'emprendedor';
    $contrasena_hash = password_hash($numero_id, PASSWORD_DEFAULT);

    $verificar = $conn->prepare("SELECT id_usuarios FROM usuarios WHERE numero_id = ?");
    $verificar->bind_param("s", $numero_id);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows === 0) {
        $insertUser = $conn->prepare("INSERT INTO usuarios (nombres, apellidos, correo, numero_id, celular, contrasena, rol)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertUser->bind_param("sssssss", $nombres, $apellidos, $correo, $numero_id, $celular, $contrasena_hash, $rol_usuario);
        $insertUser->execute();
        $insertUser->close();
    } else {
        $updateUser = $conn->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, correo = ?, celular = ? WHERE numero_id = ?");
        $updateUser->bind_param("sssss", $nombres, $apellidos, $correo, $celular, $numero_id);
        $updateUser->execute();
        $updateUser->close();
    }

    $verificar->close();
    $conn->close();

    // Éxito
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

            @media (max-width: 480px) {
                .card{padding:30px 40px}
                .card h1{font-size:1.5rem}
                .btn{padding:10px 20px;font-size:0.9rem}
            }

            @media (max-width: 760px) {
                .card{max-width:90%;padding:30px 20px}
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>¡Datos enviados con éxito!</h1>
            <p>Gracias por registrar tu información.</p>
            <a class="btn" href="../../login.php">Ir al inicio de sesión    </a>
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
?>
