<?php
session_start();
include "../conexion.php";

// Verifica que sea un orientador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'orientador') {
    header("Location: ../../login.html");
    exit;
}

$conexion = ConectarDB();
$resultado = $conexion->query("SELECT nombres, apellidos, numero_id, correo, celular, estado_avance FROM usuarios WHERE rol = 'emprendedor'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Lista de Emprendedores</title>
    <link rel="stylesheet" href="../../componentes/tabla_emprendedores.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenedor">
        <h2>📋 Lista de Emprendedores</h2>

        <table class="tabla-emprendedores">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Número de Documento</th>
                    <th>Correo</th>
                    <th>Celular</th>
                    <th>Estado de Avance</th>
                    <th>Desarrollo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['nombres']) ?></td>
                        <td><?= htmlspecialchars($fila['apellidos']) ?></td>
                        <td><?= htmlspecialchars($fila['numero_id']) ?></td>
                        <td><?= htmlspecialchars($fila['correo']) ?></td>
                        <td><?= htmlspecialchars($fila['celular']) ?></td>
                        <td><?= htmlspecialchars($fila['estado_avance']) ?></td>
                        <td><a href="ver_progreso.php?numero_id=<?= $fila['numero_id'] ?>">Ver progreso</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="volver">
            <a href="panel_orientador.php">⬅️ Volver al panel</a>
        </div>
    </div>
</body>
</html>
