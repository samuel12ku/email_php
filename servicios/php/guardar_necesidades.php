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
    echo "Datos enviados correctamente.";
} else {
    echo "Error al enviar los datos.";
}
?>
