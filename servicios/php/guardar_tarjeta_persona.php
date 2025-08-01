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
$fotoNombreFinal = null;
$descriptor        = $_POST['descriptor']        ?? '';
$citas             = $_POST['citas']             ?? '';
$quien             = $_POST['quien']             ?? '';
$metas             = $_POST['metas']             ?? '';
$actitud           = $_POST['actitud']           ?? '';
$comportamiento    = $_POST['comportamiento']    ?? '';
$modas             = $_POST['modas']             ?? '';
$beneficios        = $_POST['beneficios']        ?? '';
$decisiones_base   = $_POST['decisiones_base']   ?? '';
$job_funcional     = $_POST['job_funcional']     ?? '';
$job_emocional     = $_POST['job_emocional']     ?? '';
$job_social        = $_POST['job_social']        ?? '';

// 4. Sentencia preparada
$sql = "INSERT INTO tarjeta_persona
        (nombre, foto, descriptor, citas, quien, metas, actitud, comportamiento,
        modas, beneficios, decisiones_base,
        job_funcional, job_emocional, job_social)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssssssssssssss',
    $nombre, $fotoNombreFinal, $descriptor, $citas, $quien, $metas, $actitud, $comportamiento,
    $modas, $beneficios, $decisiones_base,
    $job_funcional, $job_emocional, $job_social
);

// Procesar la imagen si fue enviada
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fotoTmp   = $_FILES['foto']['tmp_name'];
    $fotoNombre = basename($_FILES['foto']['name']);
    $ext = strtolower(pathinfo($fotoNombre, PATHINFO_EXTENSION));

    // Validar extensión permitida
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $permitidas)) {
        // Generar nombre único
        $fotoNombreFinal = 'foto_' . uniqid() . '.' . $ext;

        // Ruta de guardado (ajusta según tu estructura)
        $destino = __DIR__ . '/../../componentes/uploads/' . $fotoNombreFinal;

        // Mover archivo
        move_uploaded_file($fotoTmp, $destino);
    } else {
        echo "Formato de imagen no permitido.";
        exit;
    }
}


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
<button onclick="guardarAvance(2)" class="btn">Siguiente</button>

<script>
function guardarAvance(fase) {
  fetch('guardar_avance.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'fase=' + fase
  })
  .then(response => response.text())
  .then(data => {
    console.log('Respuesta:', data);
    if (data.includes('OK')) {
      window.location.href = '../../dashboard.php';
    } else {
      alert('Error al guardar avance: ' + data);
    }
  });
}
</script>
    </div>
</body>
</html>
    <?php
} else {
    echo "No se pudo guardar la tarjeta.";
}
?>