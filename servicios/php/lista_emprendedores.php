<?php
session_start();
include "../conexion.php";

// Solo orientador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'orientador') {
    header("Location: ../../index.php");
    exit;
}

$conexion = ConectarDB();
$id_orientador = (int)$_SESSION['usuario_id'];

// -------- Paginación --------
$perPage = 20;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Total de emprendedores asignados
$sqlTotal = $conexion->prepare("
  SELECT COUNT(DISTINCT e.id) AS total
  FROM orientacion_rcde2025_valle e
  WHERE e.rol = 'emprendedor' AND e.orientador_id = ?
");
$sqlTotal->bind_param("i", $id_orientador);
$sqlTotal->execute();
$total = (int)($sqlTotal->get_result()->fetch_assoc()['total'] ?? 0);
$sqlTotal->close();

$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

// Mapa fases
$fases_totales = [
    1 => 'Identificar Problema',
    2 => 'Tarjeta Persona',
    3 => 'Jobs To Be Done',
    4 => 'Lean Canvas'
];

// Datos página actual
$resultado = $conexion->prepare("
  SELECT 
    e.id,
    e.nombres, 
    e.apellidos, 
    e.numero_id, 
    e.correo, 
    e.celular,
    e.acceso_panel,
    COALESCE(MAX(ph.fase),0) AS ultima_fase
  FROM orientacion_rcde2025_valle e
  LEFT JOIN progreso_herramientas ph ON e.id = ph.usuario_id
  WHERE e.rol = 'emprendedor' AND e.orientador_id = ?
  GROUP BY e.id
  ORDER BY e.nombres ASC, e.apellidos ASC
  LIMIT ? OFFSET ?
");
$resultado->bind_param("iii", $id_orientador, $perPage, $offset);
$resultado->execute();
$res = $resultado->get_result();

// helper paginador
function page_link(int $p): string {
    $p = max(1, $p);
    return htmlspecialchars('?page='.$p, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lista de Emprendedores</title>
  <link rel="stylesheet" href="../../componentes/tabla_emprendedores.css">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <style>
    /* asegurar modal por encima */
    .modal{ z-index:99999 !important; }
    .btn, .btn *{ pointer-events:auto !important; }
  </style>
</head>
<body>


  <div class="contenedor">
    <h2>📋 Lista de Emprendedores</h2>

    <div class="filtros-emprendedores-barra">
      <span class="filtros-barra-titulo">🔎 Filtrar y ordenar emprendedores</span>
      <div class="filtro-barra-campo">
        <label for="ordenFiltro">Ordenar por:</label>
        <select id="ordenFiltro">
          <option value="alfabetico" selected>A-Z</option>
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

    <div class="tabla-scroll">
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
            <?php
              $faseTxt   = $fila['ultima_fase'] ? ($fases_totales[$fila['ultima_fase']] ?? 'Sin avance') : 'Sin avance';
              $nombreC   = $fila['nombres'].' '.$fila['apellidos'];
              $nombreAttr = htmlspecialchars($nombreC, ENT_QUOTES, 'UTF-8');
              $numeroAttr = htmlspecialchars($fila['numero_id'], ENT_QUOTES, 'UTF-8');
            ?>
            <tr>
              <td data-label="Nombres"><?= htmlspecialchars($fila['nombres']) ?></td>
              <td data-label="Apellidos"><?= htmlspecialchars($fila['apellidos']) ?></td>
              <td data-label="Número de documento"><?= htmlspecialchars($fila['numero_id']) ?></td>
              <td data-label="Correo">
                <a class="cell-link" href="mailto:<?= htmlspecialchars($fila['correo']) ?>">
                  <?= htmlspecialchars($fila['correo']) ?>
                </a>
              </td>
              <td data-label="Celular">
                <a class="cell-link" href="tel:<?= preg_replace('/\D+/', '', $fila['celular']) ?>">
                  <?= htmlspecialchars($fila['celular']) ?>
                </a>
              </td>
              <td data-label="Estado de avance">
                <span class="badge" data-fase="<?= htmlspecialchars($faseTxt) ?>">
                  <?= htmlspecialchars($faseTxt) ?>
                </span>
              </td>
              <td data-label="Desarrollo">
                <a class="btn btn--link" href="ver_progreso.php?numero_id=<?= htmlspecialchars($fila['numero_id']) ?>">Ver progreso</a>
              </td>
              <td data-label="Acceso">
                <?php if ((int)$fila['acceso_panel'] === 1): ?>
                  <span class="badge badge--ok">Habilitado</span>
                <?php else: ?>
                  <button type="button"
                          class="btn btn--success btn-habilitar"
                          data-numero="<?= $numeroAttr ?>"
                          data-nombre="<?= $nombreAttr ?>">
                    Habilitar acceso
                  </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginador -->
    <div class="paginacion" aria-label="Paginación">
      <?php if ($page > 1): ?>
        <a class="page-btn" href="<?= page_link($page-1) ?>" aria-label="Página anterior">‹</a>
      <?php else: ?>
        <span class="page-btn is-disabled">‹</span>
      <?php endif; ?>

      <?php
        $window = 2;
        $shownFirstDots = false; $shownLastDots = false;
        for ($p = 1; $p <= $totalPages; $p++) {
          if ($p == 1 || $p == $totalPages || ($p >= $page-$window && $p <= $page+$window)) {
            if ($p == $page) echo '<span class="page-btn is-active">'.$p.'</span>';
            else echo '<a class="page-btn" href="'.page_link($p).'">'.$p.'</a>';
          } else {
            if ($p < $page-$window && !$shownFirstDots) { echo '<span class="page-ellipsis">…</span>'; $shownFirstDots = true; }
            if ($p > $page+$window && !$shownLastDots) { echo '<span class="page-ellipsis">…</span>'; $shownLastDots = true; }
          }
        }
      ?>

      <?php if ($page < $totalPages): ?>
        <a class="page-btn" href="<?= page_link($page+1) ?>" aria-label="Página siguiente">›</a>
      <?php else: ?>
        <span class="page-btn is-disabled">›</span>
      <?php endif; ?>
      <span class="page-info">Página <?= $page ?> de <?= $totalPages ?> · <?= $total ?> registros</span>
    </div>

    <div class="volver">
      <a href="panel_orientador.php">⬅️ Volver al panel</a>
    </div>
  </div>

  <!-- Form oculto para enviar POST -->
  <form id="formHabilitar" method="POST" action="habilitar_dashboard.php" style="display:none">
    <input type="hidden" name="numero_id" id="numeroHidden">
  </form>

<!-- Modal confirmar (bonita) -->
<div id="modalConfirmar" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="modal-card" role="document">
    <div class="modal-head">
      <div class="modal-title" id="confirmTitulo">Confirmar acción</div>
      <button class="modal-close" type="button" aria-label="Cerrar" id="modalCloseBtn">×</button>
    </div>
    <div class="modal-body">
      <p id="textoConfirmacion">¿Estás seguro?</p>
    </div>
    <div class="modal-actions">
      <button type="button" class="btn-pill btn-secondary" id="cancelarBtn">Cancelar</button>
      <button type="button" class="btn-pill btn-primary" id="confirmarBtn">Confirmar</button>
    </div>
  </div>
</div>


  <!-- (tu JS de filtros) -->
  <script src="../../componentes/js/tabla_emprendedores.js" defer></script>

<script>
  (function(){
    const modal      = document.getElementById('modalConfirmar');
    const card       = modal.querySelector('.modal-card');
    const msgEl      = document.getElementById('textoConfirmacion');
    const btnOpenSel = '.btn-habilitar';       // botones de la tabla
    const btnConfirm = document.getElementById('confirmarBtn');
    const btnCancel  = document.getElementById('cancelarBtn');
    const btnClose   = document.getElementById('modalCloseBtn');
    const formHidden = document.getElementById('formHabilitar');
    const inputNum   = document.getElementById('numeroHidden');
    let currentNum   = null;

    function openModal(nombre, numero){
      currentNum = numero || '';
      msgEl.textContent = `¿Estás seguro de habilitar el acceso al panel para ${nombre}?`;
      modal.classList.add('modal--show');
      document.body.style.overflow = 'hidden';
      // foco al botón Confirmar
      setTimeout(()=> btnConfirm.focus(), 10);
    }
    function closeModal(){
      modal.classList.remove('modal--show');
      document.body.style.overflow = '';
      currentNum = null;
    }

    // Apertura (delegación)
    document.addEventListener('click', function(e){
      const btn = e.target.closest(btnOpenSel);
      if (!btn) return;
      e.preventDefault();
      openModal(btn.dataset.nombre || 'este emprendedor', btn.dataset.numero || '');
    });

    // Confirmar => POST
    btnConfirm.addEventListener('click', function(){
      if (!currentNum) return closeModal();
      inputNum.value = currentNum;
      if (typeof formHidden.requestSubmit === 'function') formHidden.requestSubmit();
      else formHidden.submit();
    });

    // Cerrar
    btnCancel.addEventListener('click', closeModal);
    btnClose.addEventListener('click', closeModal);
    modal.addEventListener('click', (e)=> { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', (e)=> {
      if (e.key === 'Escape' && modal.classList.contains('modal--show')) closeModal();
    });
  })();
</script>

</body>
</html>
