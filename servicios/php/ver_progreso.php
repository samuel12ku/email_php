<?php
session_start();
include "../conexion.php";

if (!isset($_GET['numero_id'])) {
    echo "No se especificó el usuario.";
    exit;
}

$conexion = ConectarDB();

// Obtener ID del usuario con su número de documento
$numero_id = intval($_GET['numero_id']);
$resultado = $conexion->query("SELECT id_usuarios, nombres, apellidos FROM usuarios WHERE numero_id = $numero_id LIMIT 1");

if (!$resultado || $resultado->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit;
}

$usuario = $resultado->fetch_assoc();
$usuario_id = $usuario['id_usuarios'];

// Obtener fases completadas
$fases_result = $conexion->query("SELECT fase FROM progreso_herramientas WHERE usuario_id = $usuario_id");

$fases_completadas = [];
while ($row = $fases_result->fetch_assoc()) {
    $fases_completadas[] = $row['fase'];
}

// Lista completa de fases
$fases_totales = [
    1 => 'Identificar Problema',
    2 => 'Tarjeta Persona',
    3 => 'Jobs To Be Done',
    4 => 'Lean Canvas'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../componentes/ver_progreso.css">
    <title>Progreso de <?= htmlspecialchars($usuario['nombres']) ?></title>
</head>
<body>
    <form method="post" action="ver_progreso.php">
    <div class="contenedor">
    <h2>🧾 Progreso de <b><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></b></h2>
    <ul>
        <?php foreach ($fases_totales as $num => $nombre): ?>
            <li>
                <?= $num . '. ' . $nombre ?>
                <span class="<?= in_array($num, $fases_completadas) ? 'completada' : 'pendiente' ?>">
                    <?= in_array($num, $fases_completadas) ? '✔ Completado' : '✘ Pendiente por hacer' ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="javascript:history.back()">⬅ Volver</a>
    </div>
    </form>
</body>
</html>
