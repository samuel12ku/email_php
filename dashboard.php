<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control Fondo Emprender SENA</title>
    <link rel="stylesheet" href="componentes/estilo_dashboard.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <!-- Encabezado institucional -->
    <header class="encabezado-sena">
      <div class="encabezado-logo-titulo">
        <a
          href="dashboard.php"
          class="encabezado-logo-link"
          title="Ir al inicio"
        >
          <img
            src="componentes/img/logosena.png"
            alt="Logo SENA"
            class="encabezado-logo"
          />
        </a>
        <span class="encabezado-titulo"
          >Herramientas Fondo Emprender - SENA</span
        >
      </div>
      <nav class="encabezado-nav">
        <div class="nav-izquierda">
          <!-- (opcional) contenido a la izquierda, o dejar vacío -->
        </div>
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
        <img
          src="componentes/img/logoFondoEmprender.svg"
          alt="Logo SENA"
          class="dashboard-logo"
        />
        <h2><b>PANEL DE CONTROL - FONDO EMPRENDER SENA</b></h2>
        <span class="dashboard-titulo">
            Bienvenido/a,
            <strong>
              <?php include "servicios/php_Login/obtener_nombre.php"; ?>
            </strong>
        </span>
      </div>
      <div class="dashboard-manual">
        <strong>Uso del aplicativo</strong>
        <ul>
          <li>
            Usa <b>correos masivos</b> para mensajes generales (no
            personalizados) a muchos destinatarios simultáneamente.
          </li>
          <li>
            Usa <b>correos personalizados</b> si necesitas incluir datos
            diferentes para cada persona (nombre, documento, etc) a partir de un
            <b><u>archivo CSV o en block de notas separadas por coma.</u></b>
          </li>
          <li>
            Adjunta imágenes solo si es necesario y revisa el orden de
            presentación (texto antes o después de la imagen).
          </li>
          <li>
            Respeta la <b>imagen institucional</b>: lenguaje formal, incluye
            logo SENA y firma corporativa cuando corresponda.
          </li>
          <li>
            Consulta la
            <a
              href="https://www.sena.edu.co/es-co/Documents/MANUAL_IDENTIDAD_VISUAL_SENA_2024.pdf"
              target="_blank"
              >guía de identidad SENA</a
            >
            para dudas sobre colores, logos y redacción.
          </li>
          
        </ul>
      </div>
      
      
      <!-- Grupo: Herramientas de Ideación -->
      <fieldset class="grupo-seccion">
        <legend class="titulo-seccion">🧠 Herramientas de Ideación</legend>
        <div class="dashboard-tarjetas">
          <!-- <a class="tarjeta-interactiva" href="formulario_emprendedores/registro_emprendedores.html">
            <div class="tarjeta-icono">📃</div>
            <div class="tarjeta-titulo">Formulario del emprendedor</div>
            <div class="tarjeta-desc">Brinda información de ti mismo emprendedor</div>
          </a> -->
          <a class="tarjeta-interactiva fase fase-1" href="herramientas_ideacion/identificar_problema/necesidades.html" name="identificar_problema" id="identificar_problema">
            <div class="tarjeta-icono">🔍</div>
            <div class="tarjeta-titulo">Identificar Problema</div>
              <div class="tarjeta-desc">Detecta el problema raíz a resolver.</div>
            </a>
            <a class="tarjeta-interactiva fase fase-2" href="herramientas_ideacion/tarjeta_persona/tarjeta_persona.html" name="tarjeta_persona" id="tarjeta_persona">
              <div class="tarjeta-icono">🔲</div>
              <div class="tarjeta-titulo">Tarjeta persona</div>
              <div class="tarjeta-desc">Crea el retrato perfecto de tu usuario clave.</div>
            </a>
            <a class="tarjeta-interactiva fase fase-3" href="herramientas_ideacion/jobs_to_be_done/main.html" name="jobs_to_be_done" id="jobs_to_be_done">
              <div class="tarjeta-icono">👨‍💼</div>
              <div class="tarjeta-titulo">Jobs To Be Done</div>
              <div class="tarjeta-desc">Comprende las necesidades reales de tus usuarios.</div>
            </a>
            
          <a class="tarjeta-interactiva fase fase-4" href="herramientas_ideacion/form_lean_canvas/formulario_lean_canvas.html" name="lean_canvas" id="lean_canvas">
            <div class="tarjeta-icono">🧩</div>
            <div class="tarjeta-titulo">Lean Canvas</div>
            <div class="tarjeta-desc">Modelo visual para estructurar tu idea de negocio.</div>
          </a>

    <a class="tarjeta-interactiva" href="#">
      <div class="tarjeta-icono">🚧</div>
      <div class="tarjeta-titulo">Proximamente...</div>
      <div class="tarjeta-desc">En construcción........</div>
    </a>
        </a>
  </div>
</fieldset>

<!-- Grupo: Correos Institucionales -->
<fieldset class="grupo-seccion">
  <legend class="titulo-seccion">📬 Envío de Correos Institucionales</legend>
  <div class="dashboard-tarjetas">
    <a class="tarjeta-interactiva" href="correos_masivos/mail.html">
      <div class="tarjeta-icono">📨</div>
      <div class="tarjeta-titulo">Correos Masivos</div>
      <div class="tarjeta-desc">
        Envía un mensaje igual a varios destinatarios.<br /><b>Sin personalización</b>
      </div>
    </a>
    <a class="tarjeta-interactiva" href="correos_personalizados/email.html">
      <div class="tarjeta-icono">✉️</div>
      <div class="tarjeta-titulo">Correos Personalizados</div>
      <div class="tarjeta-desc">
        Envía mensajes personalizados a partir de CSV o separado por comas.<br /><b>Para comunicaciones individualizadas</b>
      </div>
    </a>
  </div>
</fieldset>

    </div>
<?php
include_once "servicios/conexion.php";
$usuario_id = $_SESSION['usuario_id'];
$fases_completadas = [];

$result = $conexion->query("SELECT fase FROM progreso_herramientas WHERE usuario_id = $usuario_id");
while ($row = $result->fetch_assoc()) {
    $fases_completadas[] = $row['fase'];
}
?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const fases = <?= json_encode($fases_completadas) ?>;
    fases.forEach(fase => {
      const el = document.querySelector('.fase-' + fase);
      if (el) {
        el.classList.add('completada');
      }
    });
  });
</script>

  </body>
</html>
