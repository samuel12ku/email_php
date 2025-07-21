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
$descriptor        = $_POST['descriptor']        ?? '';
$citas             = $_POST['citas']             ?? '';
$quien             = $_POST['quien']             ?? '';
$metas             = $_POST['metas']             ?? '';
$actitud           = $_POST['actitud']           ?? '';
$comportamiento    = $_POST['comportamiento']    ?? '';
$modas             = $_POST['modas']             ?? '';
$beneficios        = $_POST['beneficios']        ?? '';
$decisiones_tiempo = $_POST['decisiones_tiempo'] ?? '';
$decisiones_base   = $_POST['decisiones_base']   ?? '';
$job_funcional     = $_POST['job_funcional']     ?? '';
$job_emocional     = $_POST['job_emocional']     ?? '';
$job_social        = $_POST['job_social']        ?? '';

// 4. Sentencia preparada
$sql = "INSERT INTO tarjeta_persona
        (nombre, descriptor, citas, quien, metas, actitud, comportamiento,
        modas, beneficios, decisiones_tiempo, decisiones_base,
        job_funcional, job_emocional, job_social)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssssssssssssss',
    $nombre, $descriptor, $citas, $quien, $metas, $actitud, $comportamiento,
    $modas, $beneficios, $decisiones_tiempo, $decisiones_base,
    $job_funcional, $job_emocional, $job_social
);

// 5. Ejecutar y responder
$exito = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if ($exito) {
    echo "Tarjeta Persona guardada correctamente.";
} else {
    echo "No se pudo guardar la tarjeta.";
}
?>