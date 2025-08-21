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
    // Si no existe, sesión inconsistente
    session_destroy();
    header("Location: login.php?error=" . urlencode("Usuario no encontrado"));
    exit;
}

// Si viene de continuar_proceso.php, mostramos modal de confirmación + luego revisión
if (isset($_SESSION['mostrar_modal_confirmacion'])) {
    echo '
    <div id="modal-confirmacion" class="modal">
        <div class="modal-contenido">
            <h2>✅ Solicitud enviada</h2>
            <p>Tu solicitud fue enviada al orientador. Pronto se comunicará contigo.</p>
            <button onclick="mostrarModalRevision()">Aceptar</button>
        </div>
    </div>

    <div id="modal-revision" class="modal" style="display:none;">
        <div class="modal-contenido">
            <p>Tu proceso está en revisión. Aún no puedes acceder a las herramientas. Te enviaremos una notificación cuando tu orientador confirme la siguiente cita.</p>
            <a href="login.php"><button>Cerrar sesión</button></a>
        </div>
    </div>

    <script>
        function mostrarModalRevision() {
            document.getElementById("modal-confirmacion").style.display = "none";
            document.getElementById("modal-revision").style.display = "flex";
        }
    </script>

    <style>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


        body{
            font-family: "Work Sans", sans-serif;
            height: 100%;
        }
            .modal {
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Work Sans", sans-serif;
        }
        .modal-contenido {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            font: inherit;
            font-family: "Work Sans", sans-serif;
        }
        .modal-contenido h2 {
            color: green;
            margin-bottom: 10px;
            font-family: inherit;
        }

        .modal-contenido p {
            font-family: inherit;
            color: #333;
        }
        .modal-contenido button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #39A900;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-family: inherit;
        }
        .modal-contenido button:hover {
            background-color: #39A900;
        }
    </style>';
    unset($_SESSION['mostrar_modal_confirmacion']);
    exit;
}

// Mostrar modal de revisión si acceso_panel sigue siendo 0
if (isset($usuario['acceso_panel']) && (int)$usuario['acceso_panel'] === 0) {
    echo '
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <div class="modal">
        
    
    
    <div class="modal-contenido">
            <p>Tu proceso está en revisión. Aún no puedes acceder a las herramientas. Te enviaremos una notificación cuando tu orientador confirme la siguiente cita.</p>
            <a href="login.php"><button>Cerrar sesión</button></a>
        </div>
    </div>
    <style>


        body{
        height: 100%;
        }
        
        .modal {
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            fotn-family: "Work Sans", sans-serif;
        }
        .modal-contenido {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        .modal-contenido button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #39A900;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-contenido button:hover {
            background-color: #39A900;
        }
    </>';
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

// ✅ Mapa: [1=>false, 2=>false, 3=>false, 4=>false]
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

// # Fases totales que dibuja la ruta (deben coincidir con $fases)
$total_fases = 4;

// Asegura booleanos
for ($i = 1; $i <= $total_fases; $i++) {
    $fases_completadas[$i] = !empty($fases_completadas[$i]);
}

// Primera fase pendiente
$primera_pendiente = null;
for ($i = 1; $i <= $total_fases; $i++) {
    if (!$fases_completadas[$i]) { $primera_pendiente = $i; break; }
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
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"/>
    <style>

    .ruta {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 30px 0;
}
.nodo {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  position: relative;
  z-index: 1;
}
.linea {
  height: 6px;
  flex: 1;
  background-color: #d1d5db;
  margin: 0 -2px;
  z-index: 0;
}
.completado {
  background-color: #10b981; 
  color: white;
}
.pendiente {
  background-color: #9ca3af; 
  color: white;
}
.meta {
  background-color: #fbbf24; 
  color: black;
}
    </style>
  </head>

  <body>
    <!-- Encabezado institucional -->
    <header class="encabezado-sena">
      <div class="encabezado-logo-titulo">
        <a href="dashboard.php" class="encabezado-logo-link" title="Ir al inicio">
          <img src="componentes/img/logosena.png" alt="Logo SENA" class="encabezado-logo"/>
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
        <img src="componentes/img/logoFondoEmprender.svg" alt="Logo SENA" class="dashboard-logo"/>
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
  <p id="ruta-sub">Fase activa: <span id="faseActivaTxt">—</span></p>
</div>


      <!-- RUTA CARRETERA (standalone, dentro del contenedor) -->
<div class="ruta-standalone-card">
  <div class="ruta-standalone-wrap">
    <svg id="rutaCarreteraSVG" viewBox="0 0 1000 260" preserveAspectRatio="none">
      <!-- sombra exterior (más gruesa) -->
      <path id="road-shadow"
            d="M 40 200 C 180 180 220 120 340 110 S 560 140 700 100 S 880 120 960 80"
            fill="none" stroke="#4b5563" stroke-width="76" stroke-linecap="round" stroke-linejoin="round"/>
      <!-- carretera -->
      <path id="road"
            d="M 40 200 C 180 180 220 120 340 110 S 560 140 700 100 S 880 120 960 80"
            fill="none" stroke="#6b7280" stroke-width="56" stroke-linecap="round" stroke-linejoin="round"/>
      <!-- línea discontinua central -->
      <path id="road-center"
            d="M 40 200 C 180 180 220 120 340 110 S 560 140 700 100 S 880 120 960 80"
            fill="none" stroke="#ffffff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"
            stroke-dasharray="18 18"/>

      <!-- meta -->
      <g id="meta" transform="">
        <circle r="28" fill="#fbbf24"></circle>
        <text x="0" y="8" text-anchor="middle" font-size="22" style="font-family:'Work Sans';">🏁</text>
      </g>

      <!-- coche (escala 1.3 para verse más grande) -->
      <g id="car" transform="">
        <g transform="scale(1.3)">
          <ellipse cx="0" cy="18" rx="16" ry="4" fill="rgba(0,0,0,.18)"></ellipse>
          <g transform="translate(0,-4)">
            <rect x="-22" y="-14" width="44" height="18" rx="6" fill="#ef4444"></rect>
            <rect x="-14" y="-24" width="28" height="12" rx="3" fill="#dc2626"></rect>
            <path d="M -12 -14 L -12 -22 L 12 -22 L 14 -14 Z" fill="#a1e0ff"></path>
          </g>
        </g>
      </g>

      <!-- nodos (se agregan por JS) -->
      <g id="nodes"></g>
    </svg>

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

  <!-- (tus tarjetas) -->
  <div class="dashboard-tarjetas">
    <?php
      foreach ($fases as $num => $fase) {
        $icono = $fase['icono'];
        $descripcion = $fase['descripcion'];
        $completada = !empty($fases_completadas[$num]);
        $bloqueada  = ($num > 1 && empty($fases_completadas[$num - 1]));
        if ($completada) {
          echo "<a class='tarjeta-interactiva fase-completada' href='{$fase['url']}' id='fase-$num' data-fase='{$num}' data-url='{$fase['url']}'><div class='tarjeta-icono'>{$icono}</div><div class='tarjeta-titulo'>{$fase['nombre']}</div><div class='desc'>{$descripcion}</div><div class='tarjeta-desc'>Completada ✔️</div></a>";
        } elseif ($bloqueada) {
          echo "<div class='tarjeta-bloqueada' id='fase-$num'><div class='tarjeta-icono'>{$icono}</div><div class='tarjeta-titulo'>{$fase['nombre']}</div><div class='desc'>{$descripcion}</div><div class='tarjeta-desc'>Fase bloqueada. Completa la anterior. 🔒</div></div>";
        } else {
          echo "<a class='tarjeta-interactiva fase-activa' href='{$fase['url']}' id='fase-$num'><div class='tarjeta-icono'>{$icono}</div><div class='tarjeta-titulo'>{$fase['nombre']}</div><div class='desc'>{$descripcion}</div><div class='tarjeta-desc'>Haz clic en la tarjeta para comenzar</div></a>";
        }
      }
    ?>
    <a class="tarjeta-interactiva" href="#"><div class="tarjeta-icono">🚧</div><div class="tarjeta-titulo">Próximamente...</div><div class="tarjeta-desc">En construcción…</div></a>
  </div>
</fieldset>


      <!-- Grupo: Herramientas de Ideación -->
      <!-- <fieldset class="grupo-seccion">
        <legend class="titulo-seccion">🧠 Herramientas de Ideación</legend>
        <div class="dashboard-tarjetas">
          <?php
            foreach ($fases as $num => $fase) {
                $icono = $fase['icono'];
                $descripcion = $fase['descripcion'];
                // ❌ NO usar in_array(...)
                $completada = !empty($fases_completadas[$num]);
                $bloqueada  = ($num > 1 && empty($fases_completadas[$num - 1]));

                if ($completada) {
                    echo "
                    <a class='tarjeta-interactiva fase-completada' href='{$fase['url']}' name='fase-$num' id='fase-$num' data-fase='{$num}' data-url='{$fase['url']}'>
                      <div class='tarjeta-icono'>{$icono}</div>
                      <div class='tarjeta-titulo'>{$fase['nombre']}</div>
                      <div class='desc'>{$descripcion}</div>
                      <div class='tarjeta-desc'>Completada ✔️</div>
                    </a>";
                } elseif ($bloqueada) {
                    echo "
                    <div class='tarjeta-bloqueada' name='fase-$num' id='fase-$num'>
                      <div class='tarjeta-icono'>{$icono}</div>
                      <div class='tarjeta-titulo'>{$fase['nombre']}</div>
                      <div class='desc'>{$descripcion}</div>
                      <div class='tarjeta-desc'>Fase bloqueada. Completa la anterior. 🔒</div>
                    </div>";
                } else {
                    echo "
                    <a class='tarjeta-interactiva fase-activa' href='{$fase['url']}' name='fase-$num' id='fase-$num'>
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
      </fieldset> -->

      <!-- Grupo: Pitch -->
      <fieldset class="grupo-seccion" id="grupo-pitch">
        <legend class="titulo-seccion">🎤 Pitch (después de ideación)</legend>
        <div class="dashboard-tarjetas" id="fases-pitch">
          <a class="tarjeta-interactiva fase fase-5" href="herramientas_pitch/pitch.html" name="pitch" id="pitch">
            <div class="tarjeta-icono">🎤</div>
            <div class="tarjeta-titulo">Pitch (en proceso)</div>
            <div class="tarjeta-desc">
              Presenta tu idea de negocio de forma clara y concisa.<br/>
              <b>¡Prepárate para impresionar!</b>
            </div>
          </a>
        </div>
      </fieldset>
    </div>

    <div id="modalReabrir" class="modal" style="display:none;">
      <div class="modal-contenido">
        <p>Esta fase ya está completada.<br>¿Deseas crear una nueva?</p>
        <div class="modal-botones">
          <button id="confirmarReabrir">Sí, continuar</button>
          <button id="cancelarReabrir">Cancelar</button>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const fases = <?= json_encode($fases_completadas) ?>;
        fases.forEach(fase => {
          const el = document.querySelector('.fase-' + fase);
          if (el) el.classList.add('completada');
        });
      });

      document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modalReabrir');
        const btnConfirmar = document.getElementById('confirmarReabrir');
        const btnCancelar = document.getElementById('cancelarReabrir');

        let urlParaAbrir = null;

        document.querySelectorAll('.fase-completada').forEach(link => {
          link.addEventListener('click', (e) => {
            e.preventDefault();
            urlParaAbrir = link.getAttribute('data-url');
            modal.style.display = 'flex';
          });
        });

        btnConfirmar.addEventListener('click', () => {
          if (urlParaAbrir) window.location.href = urlParaAbrir;
        });

        btnCancelar.addEventListener('click', () => {
          modal.style.display = 'none';
          urlParaAbrir = null;
        });
      });
      
   // === Data que viene del servidor ===
const ETAPAS = <?= json_encode($etapas, JSON_UNESCAPED_UNICODE) ?>;

  // Distribución sobre la ruta (4 fases)
  const POS_T = [0.08, 0.33, 0.63, 0.92];

  const path      = document.getElementById('road-center');
  const nodesWrap = document.getElementById('nodes');
  const car       = document.getElementById('car');
  const meta      = document.getElementById('meta');

  function pointAtT(t){ const L=path.getTotalLength(); return path.getPointAtLength(Math.max(0,Math.min(L,t*L))); }

  // Meta
  (function(){ const p=pointAtT(0.985); meta.setAttribute('transform', `translate(${p.x},${p.y})`); })();

  // Detectar activa y pintar texto arriba
  let idxActiva = ETAPAS.findIndex(e=>e.estado==='active');
  if (idxActiva < 0) idxActiva = ETAPAS.length - 1;
  const subEl = document.getElementById('faseActivaTxt');
  if (subEl) {
    subEl.textContent = ETAPAS.every(e=>e.estado==='done')
      ? 'Meta superada'
      : `#${ETAPAS[idxActiva].id} · ${ETAPAS[idxActiva].nombre}`;
  }

  // Nodos
  nodesWrap.innerHTML='';
  ETAPAS.forEach((et,i)=>{
    const t = POS_T[i] ?? (i/(ETAPAS.length+1));
    const p = pointAtT(t);
    const g = document.createElementNS('http://www.w3.org/2000/svg','g');
    g.setAttribute('transform',`translate(${p.x},${p.y})`);
    g.style.cursor = (et.estado==='locked')?'not-allowed':'pointer';
    g.setAttribute('data-url', et.url); g.setAttribute('data-estado', et.estado);

    const c = document.createElementNS('http://www.w3.org/2000/svg','circle');
    c.setAttribute('r','28'); c.setAttribute('stroke-width','5');
    if(et.estado==='done'){ c.setAttribute('fill','rgba(16,185,129,.95)'); c.setAttribute('stroke','#065f46'); }
    else if(et.estado==='active'){ c.setAttribute('fill','rgba(251,191,36,.95)'); c.setAttribute('stroke','#b45309'); }
    else { c.setAttribute('fill','rgba(156,163,175,.95)'); c.setAttribute('stroke','#6b7280'); }
    g.appendChild(c);

    const tx = document.createElementNS('http://www.w3.org/2000/svg','text');
    tx.setAttribute('x','0'); tx.setAttribute('y','8'); tx.setAttribute('text-anchor','middle'); tx.setAttribute('font-size','18');
    tx.setAttribute('style',"font-family:'Work Sans'; fill:#111827;");
    tx.textContent = (et.estado==='locked') ? '🔒' : (et.estado==='done' ? '✓' : (i+1));
    g.appendChild(tx);

    const title = document.createElementNS('http://www.w3.org/2000/svg','title');
    title.textContent = `${et.id}. ${et.nombre} (${et.estado})`; g.appendChild(title);

    g.addEventListener('click', ()=>{
      if(g.getAttribute('data-estado')==='locked'){ alert('Fase bloqueada. Completa la anterior para continuar.'); return; }
      const url=g.getAttribute('data-url'); if(url) window.location.href=url;
    });

    nodesWrap.appendChild(g);
  });

  // Coche (activa o meta)
  (function(){
    let t = ETAPAS.every(e=>e.estado==='done') ? 0.985 : POS_T[idxActiva];
    const p  = pointAtT(t);
    const L  = path.getTotalLength();
    const p2 = path.getPointAtLength(Math.min(t*L+1, L));
    const a  = Math.atan2(p2.y-p.y, p2.x-p.x)*180/Math.PI;
    car.setAttribute('transform', `translate(${p.x},${p.y}) rotate(${a})`);
  })();
    </script>
  </body>
</html>
