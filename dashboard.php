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
$id_usuario = (int) $_SESSION['usuario_id'];

// ===== OJO: ahora leemos al EMPRENDEDOR desde orientacion_rcde2025_valle =====
$stmt = $conexion->prepare("SELECT * FROM orientacion_rcde2025_valle WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

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
        .modal-contenido h2 {
            color: green;
            margin-bottom: 10px;
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
    </style>';
    unset($_SESSION['mostrar_modal_confirmacion']);
    exit;
}

// Mostrar modal de revisión si acceso_panel sigue siendo 0
if (isset($usuario['acceso_panel']) && (int)$usuario['acceso_panel'] === 0) {
    echo '
    <div class="modal">
        <div class="modal-contenido">
            <p>Tu proceso está en revisión. Aún no puedes acceder a las herramientas. Te enviaremos una notificación cuando tu orientador confirme la siguiente cita.</p>
            <a href="login.php"><button>Cerrar sesión</button></a>
        </div>
    </div>
    <style>
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

// Fases completadas del usuario (misma tabla de progreso)
$fases_completadas = [];
$stmt = $conexion->prepare("SELECT fase FROM progreso_herramientas WHERE usuario_id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $fases_completadas[] = (int)$row['fase'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control Fondo Emprender SENA</title>
    <link rel="stylesheet" href="componentes/estilo_dashboard.css" />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"/>
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

      <!-- Grupo: Herramientas de Ideación -->
      <fieldset class="grupo-seccion">
        <legend class="titulo-seccion">🧠 Herramientas de Ideación</legend>
        <div class="dashboard-tarjetas">
          <?php
            foreach ($fases as $num => $fase) {
                $icono = $fase['icono'];
                $descripcion = $fase['descripcion'];
                $completada = in_array($num, $fases_completadas, true);
                $bloqueada = ($num > 1 && !in_array($num - 1, $fases_completadas, true));

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
      </fieldset>

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
    </script>
  </body>
</html>
