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
    e.nombres, 
    e.apellidos, 
    e.numero_id, 
    e.correo, 
    e.celular,
    e.acceso_panel,
    MAX(ph.fase) AS ultima_fase
  FROM orientacion_rcde2025_valle e
  LEFT JOIN progreso_herramientas ph ON e.id = ph.usuario_id
  WHERE e.rol = 'emprendedor' AND e.orientador_id = ?
  GROUP BY  e.id");

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
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div id="modalExito" class="modal">
        <div class="modal-contenido">
            <p><?= $_SESSION['mensaje_exito'] ?></p>
            <button onclick="cerrarModal('modalExito')">Cerrar</button>
        </div>
    </div>
    <?php unset($_SESSION['mensaje_exito']); endif; ?>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
    <div id="modalError" class="modal">
        <div class="modal-contenido">
            <p><?= $_SESSION['mensaje_error'] ?></p>
            <button onclick="cerrarModal('modalError')">Cerrar</button>
        </div>
    </div>
    <?php unset($_SESSION['mensaje_error']); endif; ?>

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
                    <th>Acceso</th>
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
                        <td data-label="Desarrollo">
                            <a href="ver_progreso.php?numero_id=<?= $fila['numero_id'] ?>">Ver progreso</a></td>
                        <td data-label="Acceso">
                            <?php if ($fila['acceso_panel'] == 1): ?>
                                <button type="button" onclick="mostrarModalYaHabilitado('<?= $fila['nombres'] . ' ' . $fila['apellidos'] ?>')">
                                    Habilitado
                                </button>
                            <?php else: ?>
                                <form method="POST" action="habilitar_dashboard.php" 
                                    onsubmit="return confirmarHabilitar('<?= $fila['nombres'] . ' ' . $fila['apellidos'] ?>', this)">
                                    <input type="hidden" name="numero_id" value="<?= $fila['numero_id'] ?>">
                                    <button type="submit">Habilitar acceso</button>
                                </form>
                            <?php endif; ?>
                        </td>  

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div id="modalConfirmar" class="modal" style="display:none;">
            <div class="modal-contenido">
                <p id="textoConfirmacion"></p>
                <div style="text-align: right;">
                    <button onclick="cerrarModal('modalConfirmar')">Cancelar</button>
                    <button id="confirmarBtn">Confirmar</button>
                </div>
            </div>
        </div>

        <div id="modalYaHabilitado" class="modal" style="display:none;">
            <div class="modal-contenido">
                <p id="textoYaHabilitado"></p>
                <div style="text-align: right;">
                    <button onclick="cerrarModal('modalYaHabilitado')">Cerrar</button>
                </div>
            </div>
        </div>

    </div>
        <div class="volver">
            <a href="panel_orientador.php">⬅️ Volver al panel</a>
        </div>
    </div>
</body>
<script src="../../componentes/js/tabla_emprendedores.js"></script>
<script>

let formParaEnviar = null; // variable global para el form
let modal = document.getElementById('modalConfirmar'); // variable global para la modal

function confirmarHabilitar(nombreCompleto, form) {
    formParaEnviar = form;
    document.getElementById('textoConfirmacion').innerText =
        `¿Estás seguro de habilitar el acceso al panel para ${nombreCompleto}?`;
    modal.style.display = 'flex';
    return false; // evita que se envíe el form de inmediato
}

document.getElementById('confirmarBtn').onclick = function() {
    modal.style.display = 'none'; // cerrar modal
    if (formParaEnviar) {
        formParaEnviar.submit(); // enviar el form
    }
};

function mostrarModalYaHabilitado(nombreCompleto) {
    document.getElementById('textoYaHabilitado').innerText =
        `${nombreCompleto} ya tiene el acceso habilitado al panel.`;
    document.getElementById('modalYaHabilitado').style.display = 'flex';
}

function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}

</script>


</html>
