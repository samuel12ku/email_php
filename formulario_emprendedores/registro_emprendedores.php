<?php
include '../servicios/conexion.php';
require_once '../servicios/php/config_qr.php';

$cn = ConectarDB();

function verify_params(array $params, string $sig): bool
{
  return hash_equals(sign_params($params), $sig);
}

// Leer GET
$center = isset($_GET['center']) ? trim($_GET['center']) : '';
$region = isset($_GET['region']) ? trim($_GET['region']) : '';
$name   = isset($_GET['name'])   ? trim($_GET['name'])   : '';
$sig    = $_GET['sig'] ?? '';

// Normalización ligera
$center = preg_replace('/[^A-Za-z0-9_\-]/', '', $center);
$region = preg_replace('/[^A-Za-z0-9_\-]/', '', $region);

// Firma sobre los campos visibles (sin id)
$params_for_sig = ['center' => $center, 'region' => $region, 'name' => $name];
$prefill_ok = ($center !== '' && $region !== '' && $name !== '' && $sig !== '' && verify_params($params_for_sig, $sig));

// Si la firma es válida, verificar que ese nombre pertenezca a ese centro/regional y obtener su ID
$oid_resuelto = 0;
if ($prefill_ok) {
  $st = $cn->prepare("
    SELECT id_orientador
    FROM orientadores
    WHERE centro=? AND regional=? AND TRIM(CONCAT(nombres,' ',apellidos))=?
    LIMIT 1
  ");
  $st->bind_param("sss", $center, $region, $name);
  $st->execute();
  $r = $st->get_result()->fetch_assoc();
  $st->close();
  if ($r) {
    $oid_resuelto = (int)$r['id_orientador'];
  } else {
    // no coincide; no prellenar
    $prefill_ok = false;
  }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Registro - Ruta Emprendedora</title>

  <link rel="stylesheet" href="formulario_emprendedores.css" />
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;600;700&display=swap" rel="stylesheet" />

  <!-- Prefill data disponible para JS -->
  <script>
    window.PREFILL = <?= json_encode([
                        'ok'     => $prefill_ok,
                        'center' => $center,
                        'name'   => $name,
                        // solo para uso interno del front si lo necesitas (no va en URL)
                        'oid'    => $oid_resuelto,
                      ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>
</head>

<body>
  <!-- Encabezado institucional -->
  <header class="encabezado-sena">
    <div class="encabezado-logo-titulo">
      <img src="../componentes/img/logosena.png" alt="Logo SENA" class="encabezado-logo" />
      <span class="encabezado-titulo">Formulario de Registro Fondo Emprender - SENA</span>
    </div>
    <nav class="encabezado-nav"></nav>
  </header>

  <div class="dashboard-contenedor">
    <div class="dashboard-header">
      <img src="../componentes/img/logoFondoEmprender.svg" alt="Logo SENA" class="dashboard-logo" />
      <p><b>SBDC - Centro de Desarrollo Empresarial</b></p>
      <h2>Registro Ruta Emprendedora - 2025</h2>
    </div>

    <div class="dashboard-manual">
      <strong><b>ORIENTACIÓN A EMPRENDEDORES 2025</b></strong><br /><br />
      <strong>Centros de Desarrollo Empresarial - Regional Valle</strong>
      <p>
        ¡Bienvenido/a Emprendedor(a)! Por favor registre su asistencia a la orientación sobre los servicios
        de los Centros de Desarrollo Empresarial del SENA Regional Valle. Este espacio permite acceder a la
        <b>Ruta Emprendedora</b> y a las herramientas necesarias para fortalecer sus habilidades blandas, desarrollar
        competencias emprendedoras y acceder a oportunidades como participar en convocatorias Fondo Emprender.
      </p>
      <br />
      <p>
        <b>CONSENTIMIENTO INFORMADO Y PROTECCIÓN DE DATOS:</b> Entiendo que mi
        participación consiste en el diligenciamiento del presente formulario. La información es confidencial
        (Ley 1581 de 2012).
      </p>
    </div>

    <!-- Progreso -->
    <div class="progress-container">
      <div class="progress-bar" id="progress-bar"></div>
      <div class="progress-steps">
        <span class="step active" data-step="1">1</span>
        <span class="step" data-step="2">2</span>
        <span class="step" data-step="3">3</span>
        <span class="step" data-step="4">4</span>
        <span class="step" data-step="5">5</span>
        <span class="step" data-step="6">6</span>
        <span class="step" data-step="7">7</span>
      </div>
    </div>

    <form action="servicios/php/guardar_formulario.php" method="post" id="MIformulario" accept-charset="UTF-8">

      <!-- ===== FASE 1 ===== -->
      <div class="fase">
        <div class="titulo-seccion">📝 Información Personal</div>

        <div class="form-grupodos">
          <div class="form-grupo">
            <label for="nombres">1. Nombres <span style="color:red">*</span></label><br />
            <input type="text" id="nombres" name="nombres" class="form-control"
              pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]{2,25}" minlength="2" maxlength="25" required />
          </div>

          <div class="form-grupo">
            <label for="apellidos">2. Apellidos <span style="color:red">*</span></label><br />
            <input type="text" id="apellidos" name="apellidos" class="form-control"
              pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]{2,40}" minlength="2" maxlength="40" required />
          </div>
        </div>

        <div class="form-grupodos">
          <div class="form-grupo">
            <label for="tipo_id">3. Tipo de Identificación <span style="color:red">*</span></label><br />
            <select id="tipo_id" name="tipo_id" required class="form-control">
              <option value="" disabled selected>-- Selecciona una opción --</option>
              <option value="TI">Tarjeta de Identidad (TI)</option>
              <option value="CC">Cédula de Ciudadanía (CC)</option>
              <option value="CE">Cédula de Extranjería (CE)</option>
              <option value="PEP">Permiso Especial de Permanencia (PEP)</option>
              <option value="PAS">Pasaporte (P)</option>
              <option value="PPT">Permiso Temporal de Protección (PPT)</option>
            </select>
          </div>

          <div class="form-grupo">
            <label for="numero_id">4. Número de Identificación <span style="color:red">*</span></label><br />
            <input type="text" id="numero_id" name="numero_id" inputmode="numeric" class="form-control"
              pattern="[A-Za-z0-9]{6,20}" minlength="6" maxlength="20" required />
            <small id="numero_id_hint" style="display:block;color:#666;margin-top:4px;"></small>
          </div>
        </div>

        <div class="form-grupo">
          <label for="correo">5. Correo Electrónico <span style="color:red">*</span></label><br />
          <input type="email" id="correo" name="correo" required class="form-control" />
        </div>

        <div class="form-grupo">
          <label for="celular">6. Número de Celular <span style="color:red">*</span></label><br />
          <input type="tel" id="celular" name="celular" class="form-control"
            title="Solo números" pattern="[0-9]{10}" maxlength="10" minlength="10" required />
        </div>

        <div class="form-grupo">
          <label for="fecha_nacimiento">7. Fecha de nacimiento</label><br />
          <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required />
        </div>

        <!-- eliminnar  -->
        <div class="form-grupo">
          <label for="fecha_expedicion">8. Fecha de expedición del documento (opcional)</label><br />
          <input type="date" id="fecha_expedicion" name="fecha_expedicion" class="form-control" />
        </div>
      </div>

      <!-- ===== FASE 2 ===== -->
      <div class="fase">
        <div class="titulo-seccion">🏳 Nacionalidad</div>

        <div class="form-grupo">
          <label for="pais">9. País <span style="color:red">*</span></label><br />
          <select id="pais" name="pais_origen" class="form-control" required>
            <option value="" disabled>-- Selecciona un país --</option>
            <option value="Colombia" selected>Colombia</option>
          </select>
        </div>

        <div class="form-grupo">
          <label>10. Nacionalidad <span style="color:red">*</span></label><br />
          <span id="nacionalidad" class="form-control" name="nacionalidad"
            style="display:inline-block;min-height:38px;padding:8px 20px;margin-top:10px;"></span>
        </div>

        <div class="form-grupo">
          <label for="departamento">11. Departamento (si es de otro país, elija "Otro")
            <span style="color:red">*</span></label><br />
          <select id="departamento" name="departamento" class="form-control" required>
            <option value="" disabled selected>-- Selecciona un departamento --</option>
            <option value="Amazonas">Amazonas</option>
            <option value="Antioquia">Antioquia</option>
            <option value="Arauca">Arauca</option>
            <option value="Atlántico">Atlántico</option>
            <option value="Bogotá D.C.">Bogotá D.C.</option>
            <option value="Bolívar">Bolívar</option>
            <option value="Boyacá">Boyacá</option>
            <option value="Caldas">Caldas</option>
            <option value="Caquetá">Caquetá</option>
            <option value="Casanare">Casanare</option>
            <option value="Cauca">Cauca</option>
            <option value="Cesar">Cesar</option>
            <option value="Chocó">Chocó</option>
            <option value="Córdoba">Córdoba</option>
            <option value="Cundinamarca">Cundinamarca</option>
            <option value="Guainía">Guainía</option>
            <option value="Guaviare">Guaviare</option>
            <option value="Huila">Huila</option>
            <option value="La Guajira">La Guajira</option>
            <option value="Magdalena">Magdalena</option>
            <option value="Meta">Meta</option>
            <option value="Nariño">Nariño</option>
            <option value="Norte de Santander">Norte de Santander</option>
            <option value="Putumayo">Putumayo</option>
            <option value="Quindío">Quindío</option>
            <option value="Risaralda">Risaralda</option>
            <option value="San Andrés y Providencia">San Andrés y Providencia</option>
            <option value="Santander">Santander</option>
            <option value="Sucre">Sucre</option>
            <option value="Tolima">Tolima</option>
            <option value="Valle del Cauca">Valle del Cauca</option>
            <option value="Vaupés">Vaupés</option>
            <option value="Vichada">Vichada</option>
            <option value="Otro">Otro</option>
          </select>
          <input type="text" id="dpto_otro" name="departamento_otro" placeholder="Especifique cuál"
            class="form-control" style="display:none;margin-top:8px" />
        </div>

        <div class="form-grupo">
          <label for="municipio">12. Municipio <span style="color:red">*</span></label><br />
          <input type="text" id="municipio" name="municipio" class="form-control"
            pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]{2,45}" title="Solo letras" required />
        </div>
      </div>

      <!-- ===== FASE 3 ===== -->
      <div class="fase">
        <div class="titulo-seccion">📆 Información Adicional</div>

        <div class="form-grupo">
          <label for="fecha_orientacion">13. Fecha de orientación</label><br />
          <input type="text" id="fecha_orientacion_display" value="" readonly class="form-control" />
          <input type="hidden" name="fecha_orientacion" id="fecha_orientacion" />
          <input type="hidden" name="ts_inicio" id="ts_inicio" />
        </div>

        <div class="form-grupo">
          <label for="genero">13. Sexo <span style="color:red">*</span></label><br />
          <select id="genero" name="genero" class="form-control" required>
            <option value="">-- Selecciona --</option>
            <option value="Mujer">Mujer</option>
            <option value="Hombre">Hombre</option>
            <option value="No definido">No definido</option>
          </select>
        </div>
      </div>

      <!-- ===== FASE 4 ===== -->
      <div class="fase">
        <div class="titulo-seccion">📧 Caracterización</div>

        <div class="form-grupo">
          <label for="clasificacion">14. Clasificación de población (si aplica)</label><br />
          <select id="clasificacion" name="clasificacion" class="form-control" required>
            <option value="" disabled selected>
              -- Selecciona una opción --
            </option>
            <option value="Ninguno">Ninguno</option>
            <option value="Adolescente trabajador">
              Adolescente trabajador
            </option>
            <option value="Adolescente en conflicto con la ley penal">
              Adolescente en conflicto con la ley penal
            </option>
            <option value="Adolescentes y jóvenes vulnerables">
              Adolescentes y jóvenes vulnerables
            </option>
            <option value="Afrocolombianos">Afrocolombianos</option>
            <option value="Campesinos">Campesinos</option>
            <option value="Desplazado por fenómenos naturales">
              Desplazado por fenómenos naturales
            </option>
            <option value="Migrantes que retornan al país">
              Migrantes que retornan al país
            </option>
            <option value="Mujer cabeza de hogar">
              Mujer cabeza de hogar
            </option>
            <option value="Negritudes">Negritudes</option>
            <option value="Palenqueros">Palenqueros</option>
            <option value="Reintegrados (ARN)">
              Participantes del programa de reintegración - Reintegrados (ARN)
            </option>
            <option value="Personas en reincorporación">
              Personas en Proceso de Reincorporación
            </option>
            <option value="Población con discapacidad">
              Población con discapacidad
            </option>
            <option value="Población indígena">Población indígena</option>
            <option value="Población LGBTI">Población LGBTI</option>
            <option value="Víctima de minas antipersona">
              Población víctima de minas antipersona
            </option>
            <option value="Pueblo ROM">Pueblo ROM</option>
            <option value="Raizales">Raizales</option>
            <option value="Remitidos por PAL">
              Remitidos por programa de adaptación laboral - PAL
            </option>
            <option value="Soldados campesinos">Soldados campesinos</option>
            <option value="Tercera edad">Tercera edad</option>
            <option value="Víctima de la violencia">
              Víctima de la violencia
            </option>
            <option value="Víctima de otros hechos">
              Víctima de otros hechos victimizantes
            </option>
            <option value="Sobrevivientes de agentes químicos">
              Víctimas sobrevivientes con agentes químicos
            </option>
          </select>
        </div>

        <div class="form-grupo">
          <label for="discapacidad">15. Si es persona en condición de discapacidad, seleccionar el tipo</label><br />
          <select id="discapacidad" name="discapacidad" class="form-control" required>
            <option value="" disabled selected>-- Selecciona una opción --</option>
            <option value="Ninguna">Ninguna</option>
            <option value="Auditiva">Auditiva</option>
            <option value="Cognitiva">Cognitiva</option>
            <option value="Física">Física</option>
            <option value="Múltiple">Múltiple</option>
            <option value="Psicosocial">Psicosocial</option>
            <option value="Sordoceguera">Sordoceguera</option>
            <option value="Visual">Visual</option>
          </select>
        </div>
      </div>

      <!-- ===== FASE 5 ===== -->
      <div class="fase">
        <div class="titulo-seccion">🎓 Caracterización Educativa</div>

        <div class="form-grupo">
          <label for="tipo_emprendedor">16. Tipo de Emprendedor <span style="color:red">*</span></label><br />
          <select id="tipo_emprendedor" name="tipo_emprendedor" required class="form-control">
            <option value="" disabled selected>-- Selecciona una opción --</option>
            <option value="Aprendiz">Aprendiz</option>
            <option value="Instructor">Instructor</option>
            <option value="Egresado de Otras Instituciones">Egresado de Otras Instituciones</option>
            <option value="Egresado SENA Complementaria">Egresado SENA Complementaria</option>
            <option value="Egresado SENA Titulada">Egresado SENA Titulada</option>
            <option value="No cuenta con formación">No cuenta con formación</option>
            <option value="Otro">Otro</option>
          </select>
          <input type="text" id="tipo_emprendedor_otro" name="tipo_emprendedor_otro"
            class="form-control" placeholder="Escribe tu tipo de emprendedor"
            style="display:none;margin-top:8px"
            pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]{3,60}"
            title="Solo letras, de 3 a 60 caracteres" />
        </div>

        <div class="form-grupo">
          <label for="nivel_formacion">17. Nivel de Formación en el momento actual <span style="color:red">*</span></label><br />
          <select id="nivel_formacion" name="nivel_formacion" class="form-control" required>
            <option value="" disabled selected>-- Selecciona --</option>
            <option value="Técnico">Técnico</option>
            <option value="Tecnólogo">Tecnólogo</option>
            <option value="Operario">Operario</option>
            <option value="Auxiliar">Auxiliar</option>
            <option value="Auxiliar">Profesional</option>
            <option value="Sin título">Sin título</option>
          </select>

          <select id="carrera_tecnologo" name="carrera_tecnologo" style="display:none;margin-top:8px" class="form-control">
            <option value="" disabled selected>-- Elige tu Tecnólogo --</option>
            <option>Análisis y desarrollo de software</option>
            <option>Gestión de talento humano</option>
            <option>Gestión empresarial</option>
          </select>

          <select id="carrera_tecnico" name="carrera_tecnico" style="display:none;margin-top:8px" class="form-control">
            <option value="" disabled selected>-- Elige tu Técnico --</option>
            <option>Asistencia administrativa</option>
            <option>Programación de software</option>
            <option>Enfermería</option>
          </select>

          <select id="carrera_operario" name="carrera_operario" style="display:none;margin-top:8px" class="form-control">
            <option value="" disabled selected>-- Elige tu Operario --</option>
            <option>Procesos de panadería</option>
          </select>

          <select id="carrera_auxiliar" name="carrera_auxiliar" style="display:none;margin-top:8px" class="form-control">
            <option value="" disabled selected>-- Elige tu Auxiliar --</option>
            <option>Servicios de apoyo al cliente</option>
          </select>
        </div>

        <div class="form-grupo">
          <label for="ficha">18. Si eres aprendiz o egresado SENA, escribe tu <b>número de ficha</b>.
            De lo contrario, escribe "no aplica". <span style="color:red">*</span></label><br />
          <input type="text" id="ficha" name="ficha" class="form-control" placeholder="2825817" required />
        </div>
      </div>

      <!-- ===== FASE 6 ===== -->
      <div class="fase">
        <div class="titulo-seccion">📱 Información Complementaria</div>

        <div class="form-grupo">
          <label>19. Eres un emprendedor que tiene… <span style="color:red">*</span></label><br />
          <select id="situacion_negocio" name="situacion_negocio" required class="form-control">
            <option value="" disabled selected>-- Selecciona --</option>
            <option value="Ninguno">Ninguno</option>
            <option value="Idea de negocio">Una idea de negocio</option>
            <option value="Unidad productiva">Una unidad productiva (informal)</option>
            <option value="Empresa persona natural">Una empresa como persona natural</option>
            <option value="Empresa persona jurídica">Una empresa como persona jurídica</option>
            <option value="Asociación">Una asociación</option>
          </select>
          <input type="text" id="negocio_otro" name="situacion_negocio_otro"
            placeholder="Especifique cuál" class="form-control"
            style="display:none;margin-top:8px"
            pattern="[a-zA-Z\s]+" title="Solo ingrese letras" />
        </div>

        <div class="form-grupo">
          <label>20. ¿Pertenece a alguno de los siguientes programas especiales?
            <span style="color:red">*</span></label><br />
          <select id="programa" name="programa" required class="form-control">
            <option value="" disabled selected>-- Selecciona --</option>
            <option value="Ninguno">Ninguno</option>
            <option value="Jóvenes en paz">Jóvenes en paz</option>
            <option value="Indígenas amazónicos">Indígenas amazónicos</option>
            <option value="Parques nacionales">Parques nacionales</option>
            <option value="ICBF">ICBF</option>
            <option value="Economía popular">Economía popular</option>
            <option value="Ninguno">Cuidadores</option>
          </select>

          <!-- Campo que solo se muestra si la opción es "Otro" -->
          <!-- <input type="text" id="programa_otro" name="programa_otro" placeholder="Especifique cuál" class="form-control" style="display:none; margin-top:8px;" required> -->
        </div>


        <div class="form-grupo">
          <label>21. ¿Usted ejerce la actividad relacionada con el proyecto que desea presentar?
            <span style="color:red">*</span></label><br />
          <select id="ejercer_actividad_proyecto" name="ejercer_actividad_proyecto" required class="form-control">
            <option value="" disabled selected hidden>-- Selecciona --</option>
            <option value="SI">Sí</option>
            <option value="NO">No</option>
          </select>
        </div>

        <div class="form-grupo">
          <label>22. ¿Usted tiene empresa formalizada ante Cámara de Comercio?
            <span style="color:red">*</span></label><br />
          <select id="empresa_formalizada" name="empresa_formalizada" required class="form-control">
            <option value="" disabled selected hidden>-- Selecciona --</option>
            <option value="SI">Sí</option>
            <option value="NO">No</option>
          </select>
        </div>
      </div>

      <!-- ===== FASE 7 ===== -->
      <div class="fase">
        <div class="titulo-seccion">🏢 Centro y Orientador</div>

        <div class="form-grupo">
          <label for="centro_orientacion">23. ¿Cuál es el Centro de Desarrollo Empresarial que brinda la orientación?
            <span style="color:red">*</span></label><br />
          <select id="centro_orientacion" name="centro_orientacion" class="form-control" required
            onchange="actualizarOrientadores()"
            <?= $prefill_ok ? 'disabled title="Preseleccionado desde QR"' : '' ?>>
            <option value="" disabled <?= $prefill_ok ? '' : 'selected' ?>>-- Selecciona un centro --</option>
            <option value="CAB">Centro Agropecuario de Buga (CAB)</option>
            <option value="CBI">Centro de Biotecnología Industrial (CBI Palmira)</option>
            <option value="CDTI">Centro de Diseño Tecnológico Industrial (CDTI Cali)</option>
            <option value="CEAI">Centro de Electricidad y Automatización Industrial (CEAI Cali)</option>
            <option value="CGTS">Centro de Gestión Tecnológica de Servicios (CGTS Cali)</option>
            <option value="ASTIN">Centro Nacional de Asistencia Técnica a la Industria (ASTIN - Cali)</option>
            <option value="CTA">Centro de Tecnologías Agroindustriales (CTA - Cartago)</option>
            <option value="CLEM">Centro Latinoamericano de Especies Menores (CLEM - Tuluá)</option>
            <option value="CNP">Centro Náutico y Pesquero (CNP - Buenaventura)</option>
            <option value="CC">Centro de la Construcción (CC - Cali)</option>
          </select>
          <?php if ($prefill_ok): ?>
            <input type="hidden" name="centro_orientacion" value="<?= htmlspecialchars($center, ENT_QUOTES, 'UTF-8') ?>">
          <?php endif; ?>
        </div>

        <div class="form-grupo">
          <label for="orientador">24. ¿Cuál fue el orientador que brindó la orientación?
            <span style="color:red">*</span></label><br />
          <select id="orientador" name="orientador" class="form-control" required
            <?= $prefill_ok ? 'disabled title="Preseleccionado desde QR"' : '' ?>>
            <option value="" disabled <?= $prefill_ok ? '' : 'selected' ?>>-- Selecciona primero un centro --</option>
          </select>

          <?php if ($prefill_ok): ?>
            <input type="hidden" name="orientador" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="orientador_id_prefill" value="<?= (int)$oid_resuelto ?>">
            <input type="hidden" name="qr_sig" value="<?= htmlspecialchars($sig, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="regional_prefill" value="<?= htmlspecialchars($region, ENT_QUOTES, 'UTF-8') ?>">
          <?php endif; ?>
        </div>

        <button type="submit" class="btn-verde">Enviar Formulario</button>
      </div>
    </form>
  </div>

  <script src="formulario.js"></script>

  <script>
    (function() {
      const pre = window.PREFILL || {};
      if (!pre.ok) return;

      const selCentro = document.getElementById('centro_orientacion');
      const selOri = document.getElementById('orientador');
      if (!selCentro || !selOri) return;

      if (pre.center) selCentro.value = pre.center;

      const tryPick = () => {
        // 1) Si tus <option> de orientador usan value=id, puedes usar pre.oid
        let match = pre.oid ? Array.from(selOri.options).find(o => String(o.value) === String(pre.oid)) : null;

        // 2) Si no, elegimos por texto visible (nombre)
        if (!match && pre.name) {
          const target = pre.name.trim().toLowerCase().replace(/\s+/g, ' ');
          match = Array.from(selOri.options).find(o => o.text.trim().toLowerCase().replace(/\s+/g, ' ') === target);
        }
        if (match) {
          selOri.value = match.value;
          return true;
        }
        return false;
      };

      const maybe = window.actualizarOrientadores?.(); // si rellenas vía JS
      const wait = () => {
        if (tryPick()) return;
        setTimeout(wait, 120);
      };
      if (maybe && typeof maybe.then === 'function') {
        maybe.then(wait).catch(() => setTimeout(wait, 120));
      } else {
        wait();
      }
    })();
  </script>
</body>

</html>