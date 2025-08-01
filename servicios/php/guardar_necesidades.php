<?php
require '../conexion.php'; // ajusta la ruta si es necesario
$conn = ConectarDB();

$situacion = $_POST['situacion'] ?? '';
$nino = $_POST['nino'] ?? '';
$mayor = $_POST['mayor'] ?? '';
$entendieron = $_POST['entendieron'] ?? '';

$sql = "INSERT INTO necesidades (situacion_problematica, descripcion_nino, descripcion_persona_mayor, validadores_entendieron)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $situacion, $nino, $mayor, $entendieron);

if ($stmt->execute()) {
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
<button onclick="guardarAvance(1)" class="btn">Siguiente</button>

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
    echo "Error al enviar los datos.";
}
?>