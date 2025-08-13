<?php
// Conexión
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

// Nivel formación y carrera
$nivel_formacion = isset($_POST['nivel_formacion']) ? mb_strtoupper(trim($_POST['nivel_formacion']), 'UTF-8') : '';
$carrera = null;
switch ($nivel_formacion) {
    case 'TÉCNICO':
        $carrera = trim($_POST['carrera_tecnico'] ?? '');
        break;
    case 'TECNÓLOGO':
        $carrera = trim($_POST['carrera_tecnologo'] ?? '');
        break;
    case 'OPERARIO':
        $carrera = trim($_POST['carrera_operario'] ?? '');
        break;
    case 'AUXILIAR':
        $carrera = trim($_POST['carrera_auxiliar'] ?? '');
        break;
}

// Variables del formulario
$nombres = mb_convert_case(trim($_POST['nombres']), MB_CASE_TITLE, "UTF-8");
$apellidos = mb_convert_case(trim($_POST['apellidos']), MB_CASE_TITLE, "UTF-8");
$numero_id = mb_strtoupper(trim($_POST['numero_id']), 'UTF-8');
$correo = filter_var(strtolower(trim($_POST['correo'])), FILTER_SANITIZE_EMAIL);
$orientador_nombre = ucfirst(mb_strtolower(trim($_POST['orientador']), 'UTF-8'));

// Demás campos
$departamento = ($_POST['departamento'] === 'Otro' && !empty($_POST['departamento_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['departamento_otro']), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['departamento']), 'UTF-8'));
$municipio = ucfirst(mb_strtolower(trim($_POST['municipio']), 'UTF-8'));
$tipo_id = mb_strtoupper(trim($_POST['tipo_id']), 'UTF-8');
$pais = ucfirst(mb_strtolower(trim($_POST['pais'] ?? ''), 'UTF-8'));
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$fecha_expedicioncc = $_POST['fecha_expedicion'] ?? '';
$fecha_orientacion = $_POST['fecha_orientacion'] ?? '';
$pais_origen = $_POST['pais_origen'] ?? null;
$celular = trim($_POST['celular']);
$genero = ucfirst(mb_strtolower(trim($_POST['genero']), 'UTF-8'));
$nacionalidad = ucfirst(mb_strtolower(trim($_POST['nacionalidad']), 'UTF-8'));
$clasificacion = ucfirst(mb_strtolower(trim($_POST['clasificacion'] ?? ''), 'UTF-8'));
$discapacidad = ucfirst(mb_strtolower(trim($_POST['discapacidad'] ?? ''), 'UTF-8'));
$tipo_emprendedor = ucfirst(mb_strtolower(trim($_POST['tipo_emprendedor']), 'UTF-8'));
$programa = ($_POST['programa'] === 'Otro' && !empty($_POST['programa_especial_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['programa_especial_otro']), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['programa']), 'UTF-8'));
$situacion_negocio = ($_POST['situacion_negocio'] === 'Otro' && !empty($_POST['situacion_negocio_otro']))
    ? ucfirst(mb_strtolower(trim($_POST['situacion_negocio_otro']), 'UTF-8'))
    : ucfirst(mb_strtolower(trim($_POST['situacion_negocio']), 'UTF-8'));
$ejercer_actividad = ucfirst(mb_strtolower(trim($_POST['ejercer_actividad_proyecto']), 'UTF-8'));
$empresa_formalizada = ucfirst(mb_strtolower(trim($_POST['empresa_formalizada']), 'UTF-8'));
$ficha = ucfirst(mb_strtolower(trim($_POST['ficha']), 'UTF-8'));
$centro_orientacion = mb_strtoupper(trim($_POST['centro_orientacion']), 'UTF-8');

// Buscar ID del orientador (si existe)
$orientador_id = null;
$buscar_orientador = $conn->prepare("SELECT id_usuarios FROM usuarios WHERE CONCAT(nombres, ' ', apellidos) = ? AND rol = 'orientador'");
$buscar_orientador->bind_param("s", $orientador_nombre);
$buscar_orientador->execute();
$buscar_orientador->bind_result($orientador_id);
$buscar_orientador->fetch();
$buscar_orientador->close();

// Insertar en ruta_emprendedora
$sql = "INSERT INTO ruta_emprendedora
        (nombres, apellidos, departamento, municipio, pais, tipo_id, numero_id,
        fecha_nacimiento, fecha_expedicion, fecha_orientacion, genero, nacionalidad, pais_origen,
        correo, clasificacion, discapacidad, tipo_emprendedor, nivel_formacion,
        carrera, celular, programa, situacion_negocio, ejercer_actividad_proyecto, empresa_formalizada, ficha,
        centro_orientacion, orientador)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'sssssssssssssssssssssssssss',
    $nombres, $apellidos, $departamento, $municipio, $pais, $tipo_id, $numero_id,
    $fecha_nacimiento, $fecha_expedicioncc, $fecha_orientacion, $genero, $nacionalidad, $pais_origen,
    $correo, $clasificacion, $discapacidad, $tipo_emprendedor, $nivel_formacion,
    $carrera, $celular, $programa, $situacion_negocio, $ejercer_actividad, $empresa_formalizada, $ficha,
    $centro_orientacion, $orientador_id
);

$exito = $stmt->execute();
$stmt->close();

if ($exito) {
    // Insertar o actualizar usuario
    $rol_usuario = 'emprendedor';
    $contrasena_hash = password_hash($numero_id, PASSWORD_DEFAULT);

    $verificar = $conn->prepare("SELECT id_usuarios FROM usuarios WHERE numero_id = ?");
    $verificar->bind_param("s", $numero_id);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows === 0) {
        $insertUser = $conn->prepare("INSERT INTO usuarios (nombres, apellidos, correo, numero_id, celular, contrasena, rol, orientador_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertUser->bind_param("sssssssi", $nombres, $apellidos, $correo, $numero_id, $celular, $contrasena_hash, $rol_usuario, $orientador_id);
        $insertUser->execute();
        $insertUser->close();
    } else if ($orientador_id) {
        // Usuario existe: actualizar orientador
        $updateUser = $conn->prepare("UPDATE usuarios SET orientador_id = ? WHERE numero_id = ?");
        $updateUser->bind_param("is", $orientador_id, $numero_id);
        $updateUser->execute();
        $updateUser->close();
    }

    $verificar->close();
    $conn->close();

    // Mostrar mensaje éxito
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
            <a class="btn" href="../../login.php">Volver</a>
        </div>
    </body>
    </html>
    <?php
    exit;
} else {
    $conn->close();
    echo "❌ Error al guardar en la base de datos.";
}
?>
