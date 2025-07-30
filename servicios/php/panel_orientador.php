<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'orientador') {
    header("Location: ../../login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Orientador</title>
</head>
<body>
    <h2>Bienvenido Orientador: <?= htmlspecialchars($_SESSION['nombre']) ?> <?= htmlspecialchars($_SESSION['apellido']) ?></h2>
    
    <p>Aquí verás el seguimiento de todos los emprendedores.</p>

    <ul>
        <li><a href="lista_emprendedores.php">📋 Ver lista de emprendedores</a></li>
        <li><a href="cerrar_sesion.php">🔒 Cerrar sesión</a></li>
    </ul>


    <!-- Puedes mostrar una lista de emprendedores, su estado de avance, etc. -->
</body>
</html>
