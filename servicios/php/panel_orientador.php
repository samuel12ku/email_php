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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel del Orientador</title>
  <link rel="stylesheet" href="../../componentes/estilo_panel_orientador.css">
<link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
  <header class="encabezado-sena">
    <div class="encabezado-logo-titulo">
        <img src="../../componentes/img/logosena.png" alt="Logo SENA" class="encabezado-logo" />
      </a>
      <span class="encabezado-titulo">Panel Orientador - Fondo Emprender</span>
    </div>
    <nav class="encabezado-nav">
      <span class="usuario-nombre">Bienvenido, <strong><?= htmlspecialchars($_SESSION['nombre']) ?> <?= htmlspecialchars($_SESSION['apellido']) ?></strong></span>
        <div class="dropdown">
          <button class="dropdown-btn">Perfil</button>
            <div class="dropdown-content">
              <a href="../php_Login/editar_usuario.php">Editar usuario</a>
              <a href="cerrar_sesion.php">Cerrar sesión</a>
          </div>
    </nav>
  </header>

  <main class="panel-orientador">
    <section class="panel-bienvenida">
      <h2>📊 Seguimiento de Emprendedores</h2>
      <p>Desde este panel podrás visualizar y hacer seguimiento a todos los emprendedores registrados.</p>
    </section>

    <section class="acciones-orientador">
      <ul class="lista-opciones">
        <li><a class="btn-opcion" href="lista_emprendedores.php">Ver lista de emprendedores</a></li>
        <!-- Puedes agregar más opciones aquí -->
      </ul>
    </section>

    <!-- Grupo: Correos Institucionales -->
<section class="grupo-seccion">
  <legend class="titulo-seccion">📬 Envío de Correos Institucionales</legend>
  <div class="dashboard-tarjetas">
    <a class="tarjeta-interactiva" href="../../correos_masivos/mail.html">
      <div class="tarjeta-icono">📨</div>
      <div class="tarjeta-titulo">Correos Masivos</div>
      <div class="tarjeta-desc">
        Envía un mensaje igual a varios destinatarios.<br /><b>Sin personalización</b>
      </div>
    </a>
    <a class="tarjeta-interactiva" href="../../correos_personalizados/email.html">
      <div class="tarjeta-icono">✉️</div>
      <div class="tarjeta-titulo">Correos Personalizados</div>
      <div class="tarjeta-desc">
        Envía mensajes personalizados a partir de CSV o separado por comas.<br /><b>Para comunicaciones individualizadas</b>
      </div>
    </a>
  </div>
</section>
  </main>
</body>
</html>
