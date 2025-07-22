<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../conexion.php';

$conn = ConectarDB();   //  ←  OBTIENES LA CONEXIÓN

// Recibir arrays (ajusta si agregaste importancias)
$actors = $_POST['actor'] ?? [];
$job1   = $_POST['job_1'] ?? [];
$job2   = $_POST['job_2'] ?? [];
$job3   = $_POST['job_3'] ?? [];

$stmt = $conn->prepare(
    "INSERT INTO jobs_to_be_done (actor, job_1, job_2, job_3)
     VALUES (?, ?, ?, ?)"
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => $conn->error]);
    exit;
}

for ($i = 0; $i < count($actors); $i++) {
    $stmt->bind_param(
        'ssss',
        $actors[$i],
        $job1[$i],
        $job2[$i],
        $job3[$i]
    );
    $stmt->execute();
}

echo json_encode(['message' => 'Guardado con éxito']);
$stmt->close();
$conn->close();