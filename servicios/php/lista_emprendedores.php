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
    <meta charset="UTF-8">
    <title>Lista de Emprendedores</title>
    <link rel="stylesheet" href="../../componentes/tabla_emprendedores.css">
</head>
<body>
    <h2>Lista de Emprendedores</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
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
                    <td><a href="ver_progreso.php?numero_id=<?= $row['numero_id'] ?>">Ver progreso</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <a href="panel_orientador.php">⬅️ Volver al panel</a>
</body>
</html>
