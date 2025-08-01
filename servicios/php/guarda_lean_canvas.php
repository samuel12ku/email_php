<?php
// Configuración de conexión
$host = 'localhost';
$user = 'root';         // Cambia si es necesario
$pass = '';             // Cambia si es necesario
$db   = 'fondo_emprender'; // Cambia si es necesario

// Conexión MySQLi
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    http_response_code(500);
    die("Error de conexión: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// Recibir datos del formulario
$nombre_emprendador  = $_POST['nombre_emprendedor'] ?? '';
$documento_emprendedor = $_POST['documento_emprendedor'] ?? '';
$nombre_proyecto     = $_POST['nombre_proyecto'] ?? '';
$problema            = $_POST['problema'] ?? '';
$solucion            = $_POST['solucion'] ?? '';
$alternativas        = $_POST['alternativas'] ?? '';
$valor_unico         = $_POST['valor_unico'] ?? '';
$ventaja             = $_POST['ventaja'] ?? '';
$usuarios            = $_POST['usuarios'] ?? '';
$clientes            = $_POST['clientes'] ?? '';
$canales             = $_POST['canales'] ?? '';
$ingresos            = $_POST['ingresos'] ?? '';
$costos              = $_POST['costos'] ?? '';
$metricas            = $_POST['metricas'] ?? '';
$early_adopters      = $_POST['early_adopters'] ?? '';

// Consulta preparada
$sql = "INSERT INTO     formulario_lean_canvas (
    nombre_emprendador, documento_emprendedor, nombre_proyecto, problema, solucion,
    alternativas, valor_unico, ventaja, usuarios, clientes, canales,
    ingresos, costos, metricas, early_adopters
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    http_response_code(500);
    die("Error preparando consulta: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    'sssssssssssssss',
    $nombre_emprendador,
    $documento_emprendedor,
    $nombre_proyecto,
    $problema,
    $solucion,
    $alternativas,
    $valor_unico,
    $ventaja,
    $usuarios,
    $clientes,
    $canales,
    $ingresos,
    $costos,
    $metricas,
    $early_adopters
);

$exito = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Respuesta para tu JS
// Después de guardar...
if ($exito) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>¡Gracias por tu participación!</title>
        <style>
            body {
                font-family: 'Sora', sans-serif;
                background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
                color: #1b5e20;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                text-align: center;
            }
            .mensaje {
                background: #ffffff;
                padding: 40px 60px;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(0,0,0,.1);
                max-width: 500px;
            }
            .mensaje h1 {
                margin-top: 0;
                color: #2e7d32;
            }
            .mensaje a {
                display: inline-block;
                margin-top: 25px;
                padding: 12px 25px;
                background: #39a900;
                color: #fff;
                border-radius: 6px;
                text-decoration: none;
                transition: .3s;
            }
            .mensaje a:hover {
                background: #2e7d32;
            }
        </style>
    </head>
    <body>
        <div class="mensaje">
            <h1>¡Tu Lean Canvas fue enviado con éxito!</h1>
            <p>Gracias por compartir tu modelo de negocio con nosotros.  
            Pronto nos pondremos en contacto contigo.</p>
<a onclick="guardarAvance(4)" class="btn">Siguiente</a>

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
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error al enviar</title>
        <style>
            body {
                font-family: 'Sora', sans-serif;
                background: #ffebee;
                color: #c62828;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                text-align: center;
            }
            .error {
                background: #ffffff;
                padding: 40px 60px;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(0,0,0,.1);
                max-width: 500px;
            }
            .error a {
                display: inline-block;
                margin-top: 25px;
                padding: 12px 25px;
                background: #d32f2f;
                color: #fff;
                border-radius: 6px;
                text-decoration: none;
                transition: .3s;
            }
            .error a:hover {
                background: #b71c1c;
            }
        </style>
    </head>
    <body>
        <div class="error">
            <h1>Ups… algo salió mal</h1>
            <p>No pudimos guardar tu información. Por favor, inténtalo de nuevo.</p>
            <a href="javascript:history.back()">Regresar al formulario</a>
        </div>
    </body>
    </html>
    <?php
}
exit;