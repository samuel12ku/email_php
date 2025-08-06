<?php
session_start();
include "../conexion.php";

// Verifica que sea un orientador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'orientador') {
    header("Location: ../../login.php");
    exit;
}

$conexion = ConectarDB();

// ID del orientador logueado
$id_orientador = $_SESSION['usuario_id'];

// Obtener emprendedores asignados a este orientador
$resultado = $conexion->prepare("
  SELECT 
    u.nombres, 
    u.apellidos, 
    u.numero_id, 
    u.correo, 
    u.celular, 
    MAX(ph.fase) AS ultima_fase
  FROM usuarios u
  LEFT JOIN progreso_herramientas ph ON u.id_usuarios = ph.usuario_id
  WHERE u.rol = 'emprendedor' AND u.orientador_id = ?
  GROUP BY u.id_usuarios
");

$resultado->bind_param("i", $id_orientador);
$resultado->execute();
$res = $resultado->get_result();

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
    <title>Lista de Emprendedores</title>
    <link rel="stylesheet" href="../../componentes/tabla_emprendedores.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenedor">
        <h2>📋 Lista de Emprendedores</h2>
        <div class="cards-emprendedores"></div>

        <div class="filtros-emprendedores-barra">
        <span class="filtros-barra-titulo">🔎 Filtrar y ordenar emprendedores</span>
        <div class="filtro-barra-campo">
            <label for="ordenFiltro">Ordenar por:</label>
            <select id="ordenFiltro">
            <option value="alfabetico">A-Z</option>
            <option value="alfabetico_desc">Z-A</option>
            <option value="recientes">Más recientes primero</option>
            <option value="antiguos">Más antiguos primero</option>
            </select>
        </div>
        <div class="filtro-barra-campo">
            <label for="estadoFiltro">Estado:</label>
            <select id="estadoFiltro">
            <option value="todos">Todos</option>
            <option value="completados">Completados</option>
            <option value="no_completados">No completados</option>
            </select>
        </div>
        </div>

    <div class ="tabla-scroll">
        <table class="tabla-emprendedores" id="tablaEmprendedores">
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
            <tbody id="tbodyEmprendedores">
                <?php while ($fila = $res->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Nombres"><?= htmlspecialchars($fila['nombres']) ?></td>
                        <td data-label="Apellidos"><?= htmlspecialchars($fila['apellidos']) ?></td>
                        <td data-label="Número de documento"><?= htmlspecialchars($fila['numero_id']) ?></td>
                        <td data-label="Correo"><?= htmlspecialchars($fila['correo']) ?></td>
                        <td data-label="Celular"><?= htmlspecialchars($fila['celular']) ?></td>
                        <td data-label="Estado de avance"><?= isset($fila['ultima_fase']) && $fila['ultima_fase'] ? $fases_totales[$fila['ultima_fase']] : 'Sin avance' ?></td>
                        <td data-label="Desarrollo"><a href="ver_progreso.php?numero_id=<?= $fila['numero_id'] ?>">Ver progreso</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
        <div class="volver">
            <a href="panel_orientador.php">⬅️ Volver al panel</a>
        </div>
    </div>
</body>
<script src="../../componentes/js/tabla_emprendedores.js"></script>

</html>
