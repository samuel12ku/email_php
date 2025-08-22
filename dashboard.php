<?php
session_start();
include "servicios/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit;
}

// Opcional: si un orientador intenta entrar aquí, redirígelo a su panel
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'orientador') {
  header("Location: servicios/php/panel_orientador.php");
  exit;
}

$conexion = ConectarDB();
$id_usuario = (int) $_SESSION['usuario_id'] ?? 1;

// ===== OJO: ahora leemos al EMPRENDEDOR desde orientacion_rcde2025_valle =====
$stmt = $conexion->prepare("SELECT * FROM orientacion_rcde2025_valle WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

$total_fases = 4;
$fases_completadas = array_fill(1, $total_fases, 0);

if (!$usuario) {
  session_destroy();
  header("Location: login.php?error=" . urlencode("Usuario no encontrado"));
  exit;
}

// Mostrar modal de revisión si acceso_panel sigue siendo 0
if (isset($usuario['acceso_panel']) && (int)$usuario['acceso_panel'] === 0) {
  echo '
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <div class="modal"><div class="modal-contenido">
        <p>Tu proceso está en revisión. Aún no puedes acceder a las herramientas. Te enviaremos una notificación cuando tu orientador confirme la siguiente cita.</p>
        <a href="login.php"><button>Cerrar sesión</button></a>
    </div></div>
    <style>
      .modal{position:fixed;z-index:9999;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.6)}
      .modal-contenido{background:#fff;padding:30px;border-radius:10px;max-width:420px;width:92%;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,.3)}
      .modal-contenido button{margin-top:15px;padding:10px 20px;background:#39A900;border:0;color:#fff;border-radius:6px;cursor:pointer}
    </style>';
  exit;
}

// Definir fases
$fases = [
  1 => [
    'nombre' => 'Identificar Problema',
    'url' => 'herramientas_ideacion/identificar_problema/necesidades.html',
    'icono' => '🔍',
    'descripcion' => 'Detecta el problema raíz a resolver.'
  ],
  2 => [
    'nombre' => 'Tarjeta Persona',
    'url' => 'herramientas_ideacion/tarjeta_persona/tarjeta_persona.html',
    'icono' => '🔲',
    'descripcion' => 'Crea el retrato perfecto de tu usuario clave.'
  ],
  3 => [
    'nombre' => 'Jobs To Be Done',
    'url' => 'herramientas_ideacion/jobs_to_be_done/main.html',
    'icono' => '👨‍💼',
    'descripcion' => 'Comprende las necesidades reales de tus usuarios.'
  ],
  4 => [
    'nombre' => 'Lean Canvas',
    'url' => 'herramientas_ideacion/form_lean_canvas/formulario_lean_canvas.html',
    'icono' => '🧩',
    'descripcion' => 'Modelo visual para estructurar tu idea de negocio.'
  ],
];

$total_fases = 4;

// Mapa booleano de estado por fase
$fases_completadas = array_fill(1, $total_fases, false);

$stmt = $conexion->prepare("
  SELECT fase, completado_en
  FROM progreso_herramientas
  WHERE usuario_id = ?
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
  $f = (int)$row['fase'];
  if ($f >= 1 && $f <= $total_fases && !empty($row['completado_en'])) {
    $fases_completadas[$f] = true;
  }
}
$stmt->close();

// Primera fase pendiente
$primera_pendiente = null;
for ($i = 1; $i <= $total_fases; $i++) {
  if (!$fases_completadas[$i]) {
    $primera_pendiente = $i;
    break;
  }
}
if ($primera_pendiente === null) { // todo completo
  $primera_pendiente = $total_fases + 1;
}

// Arreglo para el front
$etapas = [];
for ($i = 1; $i <= $total_fases; $i++) {
  $estado = $fases_completadas[$i] ? 'done'
    : (($i === $primera_pendiente) ? 'active' : 'locked');

  $etapas[] = [
    'id'      => $i,
    'nombre'  => $fases[$i]['nombre'],
    'url'     => $fases[$i]['url'],
    'estado'  => $estado
  ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Control Fondo Emprender SENA</title>
  <link rel="stylesheet" href="componentes/estilo_dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />

</head>

<body>
  <!-- Encabezado institucional -->
  <header class="encabezado-sena">
    <div class="encabezado-logo-titulo">
      <a href="dashboard.php" class="encabezado-logo-link" title="Ir al inicio">
        <img src="componentes/img/logosena.png" alt="Logo SENA" class="encabezado-logo" />
      </a>
      <span class="encabezado-titulo">Herramientas Fondo Emprender - SENA</span>
    </div>
    <nav class="encabezado-nav">
      <div class="nav-izquierda"></div>
      <div class="nav-centro">
        <a href="dashboard.php" class="encabezado-nav-link">Inicio</a>
      </div>
      <div class="nav-derecha">
        <div class="dropdown">
          <button class="dropdown-btn">Perfil</button>
          <div class="dropdown-content">
            <a href="servicios/php_Login/editar_usuario.php">Editar usuario</a>
            <a href="servicios/php/cerrar_sesion.php">Cerrar sesión</a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <div class="dashboard-contenedor">
    <div class="dashboard-header">
      <img src="componentes/img/logoFondoEmprender.svg" alt="Logo SENA" class="dashboard-logo" />
      <h2><b>PANEL DE CONTROL - FONDO EMPRENDER SENA</b></h2>
      <span class="dashboard-titulo">
        Bienvenido/a,
        <strong>
          <?php
          echo htmlspecialchars(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
          ?>
        </strong>
      </span>
    </div>

    <div class="dashboard-manual">
      <strong>Uso del aplicativo</strong>
      <ul>
        <li>Este panel te guía paso a paso a través de las <b>herramientas de ideación</b> del Fondo Emprender.</li>
        <li>Cada fase debe ser <b>completada en orden</b>. Al finalizar una, se habilitará la siguiente automáticamente.</li>
        <li>Las fases completadas se pueden realizar nuevamente. Si deseas añadir otra respuesta, el sistema te consultará antes de continuar.</li>
        <li>La plataforma guarda tu progreso y lo asocia con tu usuario registrado.</li>
        <li>Mantén un <b>lenguaje claro y profesional</b> en cada herramienta.</li>
        <li>Para dudas de redacción/visual, puedes consultar la guía de identidad SENA.</li>
      </ul>
    </div>

    <div class="ruta-header">
      <h3>Ruta de Herramientas de Ideación</h3>
      <p>Fase actual: <span id="faseActivaTxt">—</span></p>
    </div>

    <!-- RUTA CARRETERA -->
    <div class="ruta-standalone-card">
      <div class="ruta-standalone-wrap">
        <svg id="rutaCarreteraSVG" viewBox="0 0 1000 260" preserveAspectRatio="none" filter="url(#roadShadow)">
          <!-- sombra exterior (más gruesa) -->
          <path id="road-shadow"
            d="M 40 200 C 180 180 220 120 340 110 S 560 140 700 100 S 880 120 960 80"
            fill="none" stroke="#4b5563" stroke-width="76" stroke-linecap="round" stroke-linejoin="round" />
          <!-- carretera -->
          <path id="road"
            d="M 40 200 C 180 180 220 120 340 110 S 560 140 700 100 S 880 120 960 80"
            fill="none" stroke="#6b7280" stroke-width="56" stroke-linecap="round" stroke-linejoin="round"
            filter="url(#roadShadow)" />
          <!-- línea discontinua central -->
          <path id="road-center"
            d="M 40 200 C 180 180 220 120 340 110 S 560 140 700 100 S 880 120 960 80"
            fill="none" stroke="#ffffff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"
            stroke-dasharray="18 18" />

          <!-- Círculos de las fases (debajo) -->
          <g id="nodes-circles"></g>

          <!-- Coche (en medio, por encima de los círculos) -->
          <g id="car" transform="" style="pointer-events:none">
            <g transform="scale(1.25)">
              <ellipse cx="0" cy="20" rx="18" ry="5" fill="rgba(0,0,0,.18)"></ellipse>
              <g id="car-body" transform="translate(0,-4)">
                <rect x="-24" y="-16" width="48" height="20" rx="7" fill="#ef4444"></rect>
                <rect x="-16" y="-28" width="32" height="12" rx="3" fill="#dc2626"></rect>
                <path d="M -12 -16 L -12 -24 L 12 -24 L 15 -16 Z" fill="#a1e0ff"></path>
              </g>
            </g>
          </g>

          <!-- Etiquetas/íconos de las fases (encima de todo) -->
          <g id="nodes-labels" style="pointer-events:none"></g>

          <!-- Meta -->
          <g id="meta" transform="">
            <circle r="32" fill="#fbbf24"></circle>
            <text x="0" y="8" text-anchor="middle" font-size="22" style="font-family:'Work Sans';">🏁</text>
          </g>

          <defs>
            <filter id="roadShadow" x="-20%" y="-20%" width="140%" height="160%">
              <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000" flood-opacity=".16" />
            </filter>
          </defs>
        </svg>

        <div id="finishMsg" class="finish-toast" role="status" aria-live="polite">
          🏆 ¡Listo! Completaste todas las herramientas de ideación.
        </div>
        <!-- leyenda -->
        <div class="ruta-legend">
          <span><i class="dot dot-green"></i>Completada</span>
          <span><i class="dot dot-yellow"></i>Activa</span>
          <span><i class="dot dot-gray"></i>Bloqueada</span>
        </div>
      </div>
    </div>
    <!-- FIN RUTA CARRETERA -->

    <fieldset class="grupo-seccion" id="grupo-ideacion">
      <legend class="titulo-seccion">🧠 Herramientas de Ideación</legend>
      <div class="dashboard-tarjetas">
        <?php
        foreach ($fases as $num => $fase) {
          $icono = $fase['icono'];
          $descripcion = $fase['descripcion'];
          $completada = !empty($fases_completadas[$num]);
          $bloqueada  = ($num > 1 && empty($fases_completadas[$num - 1]));

          if ($completada) {
            echo "<a class='tarjeta-interactiva fase-completada' href='{$fase['url']}' id='fase-$num' data-fase='{$num}' data-url='{$fase['url']}'>
                        <div class='tarjeta-icono'>{$icono}</div>
                        <div class='tarjeta-titulo'>{$fase['nombre']}</div>
                        <div class='desc'>{$descripcion}</div>
                        <div class='tarjeta-desc'>Completada ✔️</div>
                      </a>";
          } elseif ($bloqueada) {
            echo "<div class='tarjeta-bloqueada' id='fase-$num'>
                        <div class='tarjeta-icono'>{$icono}</div>
                        <div class='tarjeta-titulo'>{$fase['nombre']}</div>
                        <div class='desc'>{$descripcion}</div>
                        <div class='tarjeta-desc'>Fase bloqueada. Completa la anterior. 🔒</div>
                      </div>";
          } else {
            echo "<a class='tarjeta-interactiva fase-activa' href='{$fase['url']}' id='fase-$num'>
                        <div class='tarjeta-icono'>{$icono}</div>
                        <div class='tarjeta-titulo'>{$fase['nombre']}</div>
                        <div class='desc'>{$descripcion}</div>
                        <div class='tarjeta-desc'>Haz clic en la tarjeta para comenzar</div>
                      </a>";
          }
        }
        ?>
        <a class="tarjeta-interactiva" href="#">
          <div class="tarjeta-icono">🚧</div>
          <div class="tarjeta-titulo">Próximamente...</div>
          <div class="tarjeta-desc">En construcción…</div>
        </a>
      </div>
    </fieldset>

    <fieldset class="grupo-seccion" id="grupo-pitch">
      <legend class="titulo-seccion">🎤 Pitch (después de ideación)</legend>
      <div class="dashboard-tarjetas" id="fases-pitch">
        <a class="tarjeta-interactiva fase fase-5" href="herramientas_pitch/pitch.html" name="pitch" id="pitch">
          <div class="tarjeta-icono">🎤</div>
          <div class="tarjeta-titulo">Pitch (en proceso)</div>
          <div class="tarjeta-desc">
            Presenta tu idea de negocio de forma clara y concisa.<br />
            <b>¡Prepárate para impresionar!</b>
          </div>
        </a>
      </div>
    </fieldset>
  </div>

  <script>
    // === Data que viene del servidor ===
    const ETAPAS = <?= json_encode($etapas, JSON_UNESCAPED_UNICODE) ?>;

    // Posiciones de cada fase sobre el camino (fase 4 separada de la meta)
    const POS_T = [0.11, 0.37, 0.66, 0.88];
    const META_T = 0.985;

    const path = document.getElementById('road-center');
    const nodesCircles = document.getElementById('nodes-circles');
    const nodesLabels = document.getElementById('nodes-labels');
    const car = document.getElementById('car');
    const carBody = document.getElementById('car-body');
    const meta = document.getElementById('meta');

    function pointAtT(t) {
      const L = path.getTotalLength();
      const u = Math.max(0.005, Math.min(0.995, t)); // evita extremos
      return path.getPointAtLength(L * u);
    }

    let idleRAF = null;

    function cancelIdleBounce() {
      if (idleRAF) {
        cancelAnimationFrame(idleRAF);
        idleRAF = null;
      }
    }

    /** Balanceo muy suave al estar quieto en tBase
     *  ampPx   = amplitud en píxeles (3 por defecto)
     *  speedHz = frecuencia en Hz (0.45 Hz ≈ 2.2 s por ciclo)
     */
    function startIdleBounce(tBase, ampPx = 3, speedHz = 0.45) {
      cancelIdleBounce();

      function loop(ts) {
        // Punto y tangente de la carretera
        const p = pointAtT(tBase);
        const p2 = pointAtT(Math.min(0.995, tBase + 0.002));
        const angle = Math.atan2(p2.y - p.y, p2.x - p.x) * 180 / Math.PI;

        // Fase correcta: 2π f t (t en segundos)
        const phase = 2 * Math.PI * speedHz * (ts / 1000);
        const offsetY = Math.sin(phase) * ampPx; // balanceo vertical suave
        const wobble = Math.sin(phase * 0.5) * 0.4; // oscilación leve del cuerpo

        car.setAttribute('transform', `translate(${p.x}, ${p.y + offsetY}) rotate(${angle})`);
        if (carBody) carBody.setAttribute('transform', `translate(0, ${-2.6 + wobble})`);

        idleRAF = requestAnimationFrame(loop);
      }

      idleRAF = requestAnimationFrame(loop);
    }


    function setCarAtT(t) {
      const p = pointAtT(t);
      const p2 = pointAtT(Math.min(0.995, t + 0.002)); // para la tangente
      const angle = Math.atan2(p2.y - p.y, p2.x - p.x) * 180 / Math.PI;
      car.setAttribute('transform', `translate(${p.x}, ${p.y}) rotate(${angle})`);
    }

    // Vaivén vertical (arriba/abajo) alrededor de tBase
    function verticalBounceAt(tBase, ms = 900, ampPx = 8, cycles = 2.25) {
      let start = null;
      const easeOut = x => 1 - Math.pow(1 - x, 2);

      function frame(ts) {
        if (!start) start = ts;
        const u = Math.min(1, (ts - start) / ms); // 0..1

        // Punto sobre la carretera + orientación
        const p = pointAtT(tBase);
        const p2 = pointAtT(Math.min(0.995, tBase + 0.002));
        const angle = Math.atan2(p2.y - p.y, p2.x - p.x) * 180 / Math.PI;

        // Offset vertical con amortiguación
        const phase = 2 * Math.PI * cycles * u;
        const offsetY = Math.sin(phase) * ampPx * easeOut(1 - u);

        // Pequeño “wobble” del cuerpo del carro
        const wobble = Math.sin(ts * 0.006) * 0.8;

        car.setAttribute('transform', `translate(${p.x}, ${p.y + offsetY}) rotate(${angle})`);
        if (carBody) carBody.setAttribute('transform', `translate(0, ${-3 + wobble})`);

        if (u < 1) requestAnimationFrame(frame);
        else setCarAtT(tBase); // asegurar posición final exacta
      }
      requestAnimationFrame(frame);
    }


    // Colocar meta
    (function() {
      const p = pointAtT(META_T);
      meta.setAttribute('transform', `translate(${p.x}, ${p.y})`);
    })();

    // Fase activa para el subtítulo
    let idxActiva = ETAPAS.findIndex(e => e.estado === 'active');
    if (idxActiva < 0) idxActiva = ETAPAS.length - 1;
    const subEl = document.getElementById('faseActivaTxt');
    if (subEl) {
      subEl.textContent = ETAPAS.every(e => e.estado === 'done') ?
        'Meta' :
        `#${ETAPAS[idxActiva].id} · ${ETAPAS[idxActiva].nombre}`;
    }

    // ===== Dibujar nodos (círculos abajo, número arriba) =====
    nodesCircles.innerHTML = '';
    nodesLabels.innerHTML = '';

    ETAPAS.forEach((et, i) => {
      const t = POS_T[i];
      const p = pointAtT(t);

      // Capa de CÍRCULOS (interactiva)
      const gC = document.createElementNS('http://www.w3.org/2000/svg', 'g');
      gC.setAttribute('transform', `translate(${p.x}, ${p.y})`);
      gC.style.cursor = (et.estado === 'locked') ? 'not-allowed' : 'pointer';
      gC.setAttribute('data-url', et.url);
      gC.setAttribute('data-estado', et.estado);

      // Halo + círculo proporcionados
      const haloR = 30,
        nodeR = 25;
      const ring = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
      ring.setAttribute('r', haloR);
      ring.setAttribute('fill', 'rgba(255,255,255,.95)');
      gC.appendChild(ring);

      const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
      circle.setAttribute('r', nodeR);
      circle.setAttribute('stroke-width', 4);

      if (et.estado === 'done') {
        circle.setAttribute('fill', 'rgba(16,185,129,.95)');
        circle.setAttribute('stroke', '#065f46');
      } else if (et.estado === 'active') {
        circle.setAttribute('fill', 'rgba(251,191,36,.95)');
        circle.setAttribute('stroke', '#b45309');
      } else {
        circle.setAttribute('fill', 'rgba(156,163,175,.95)');
        circle.setAttribute('stroke', '#6b7280');
      }
      gC.appendChild(circle);

      // Click
      gC.addEventListener('click', () => {
        if (gC.getAttribute('data-estado') === 'locked') {
          alert('Fase bloqueada. Completa la anterior para continuar.');
          return;
        }
        const url = gC.getAttribute('data-url');
        if (url) window.location.href = url;
      });
      nodesCircles.appendChild(gC);

      // Capa de ETIQUETA (encima de todo)
      const gL = document.createElementNS('http://www.w3.org/2000/svg', 'g');
      gL.setAttribute('transform', `translate(${p.x}, ${p.y})`);

      const tx = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      tx.setAttribute('x', 0);
      tx.setAttribute('y', 6);
      tx.setAttribute('text-anchor', 'middle');
      tx.setAttribute('font-size', '14');
      tx.setAttribute('style',
        "font-family:'Work Sans';font-weight:700;fill:#0f172a;paint-order:stroke;stroke:#f5f5f590;stroke-width:3px;"
      );
      tx.textContent = (et.estado === 'locked') ? '🔒' : (et.estado === 'done' ? '✓' : (i + 1));

      gL.appendChild(tx);
      nodesLabels.appendChild(gL);
    });

    // ===== Animación de transición ÚNICA (del último "done" a la fase activa) =====
    const doneIdxs = ETAPAS.map((e, i) => e.estado === 'done' ? i : -1).filter(i => i >= 0);
    const allDone = ETAPAS.every(e => e.estado === 'done');

    let fromT, toT;
    if (allDone) {
      fromT = POS_T[POS_T.length - 1]; // última fase
      toT = META_T - 0.015; // antes de la meta
    } else if (doneIdxs.length) {
      fromT = POS_T[doneIdxs[doneIdxs.length - 1]]; // último done
      toT = POS_T[idxActiva]; // activa
    } else {
      fromT = 0.06; // inicio del camino
      toT = POS_T[idxActiva]; // activa
    }

    if (Math.abs(toT - fromT) < 0.0001) {
      // ya estamos en ese punto: iniciar balanceo
      startIdleBounce(toT);
    } else {
      const DURATION = 1400; // igual que lo tenías
      let start = null;

      function easeInOut(u) {
        return 0.5 - 0.5 * Math.cos(Math.PI * u);
      }

      // detener balanceo mientras nos movemos
      cancelIdleBounce();

      function step(ts) {
        if (!start) start = ts;
        const t = Math.min(1, (ts - start) / DURATION);
        const u = easeInOut(t);
        const curT = fromT + (toT - fromT) * u;

        const p = pointAtT(curT);
        const p2 = pointAtT(Math.min(0.995, curT + 0.002));
        const angle = Math.atan2(p2.y - p.y, p2.x - p.x) * 180 / Math.PI;
        const wobble = Math.sin(ts * 0.006) * 1.0;

        car.setAttribute('transform', `translate(${p.x}, ${p.y}) rotate(${angle})`);
        if (carBody) carBody.setAttribute('transform', `translate(0, ${-3 + wobble})`);

        if (t < 1) requestAnimationFrame(step);
        else startIdleBounce(toT); // ← al llegar, balanceo continuo
      }
      requestAnimationFrame(step);
    }


    // Referencia al toast
const finishMsg = document.getElementById('finishMsg');

// Ubica el mensaje sobre la meta y lo muestra/oculta según estado:
function updateFinishMsg() {
  const allDone = ETAPAS.every(e => e.estado === 'done');
  if (!allDone) {
    finishMsg.classList.remove('show','bob');
    finishMsg.style.display = 'none';
    return;
  }

  // Posición de la meta en el path
  const p   = pointAtT(META_T);

  // Medidas y límites del contenedor
  finishMsg.style.display = 'block';             // medir
  const tw = finishMsg.offsetWidth;
  const th = finishMsg.offsetHeight;

  const svgW = svg.clientWidth;
  const svgH = svg.clientHeight;
  const pad  = 12;                               // margen interno
  const gap  = 36;                               // separación debajo de la meta

  // Colocar DEBAJO de la bandera, centrado respecto a su x
  let left = p.x - (tw / 2);
  let top  = p.y + gap;

  // Limitar para que nunca se salga del SVG
  left = Math.max(pad, Math.min(left, svgW - pad - tw));
  top  = Math.max(pad, Math.min(top,  svgH - pad - th));

  finishMsg.style.left = `${left}px`;
  finishMsg.style.top  = `${top}px`;
  finishMsg.classList.add('show','bob');
}

// Llama al colocar meta y cada vez que cambie el tamaño
updateFinishMsg();
window.addEventListener('resize', updateFinishMsg);

  </script>
</body>

</html>