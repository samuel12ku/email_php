let faseActual = 0;
const fases = document.querySelectorAll('.fase');


function toggleMenu() {
  const menu = document.getElementById("navMenu");
  menu.classList.toggle("show");
}



function mostrarFase(index) {
  fases.forEach((fase, i) => {
    fase.style.display = i === index ? 'block' : 'none';
  });
  actualizarBarra(); // Actualiza la barra de progreso al mostrar una fase
}

// Validación de la fase actual, incluyendo restricciones personalizadas
function validarFaseActual() {
  const fase = fases[faseActual];
  let valid = true;
  let primerNoValido = null;

  const campos = fase.querySelectorAll('input[required], select[required], textarea[required]');
  campos.forEach(campo => {
    // Campo 20: ficha
    if (campo.id === 'ficha') {
      let valor = campo.value.trim();
      if (
        valor !== '' &&
        !/^[0-9]+$/.test(valor) &&
        valor.toLowerCase() !== 'no aplica'
      ) {
        valid = false;
        campo.classList.add('campo-error');
        campo.setCustomValidity("Solo se permite ingresar números o 'no aplica'.");
        if (!primerNoValido) primerNoValido = campo;
      } else {
        campo.setCustomValidity('');
        campo.classList.remove('campo-error');
      }
    }
    // Campo 21: programa_formacion
    else if (campo.id === 'programa_formacion') {
      let valor = campo.value.trim();
      if (
        valor !== '' &&
        !/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(valor) &&
        valor.toLowerCase() !== 'no aplica'
      ) {
        valid = false;
        campo.classList.add('campo-error');
        campo.setCustomValidity("Solo se permite texto o 'no aplica'.");
        if (!primerNoValido) primerNoValido = campo;
      } else {
        campo.setCustomValidity('');
        campo.classList.remove('campo-error');
      }
    }
    // Radios: al menos uno seleccionado del grupo
    else if (campo.type === 'radio') {
      const radios = fase.querySelectorAll(`input[name="${campo.name}"]`);
      const algunoMarcado = Array.from(radios).some(r => r.checked);
      if (!algunoMarcado) {
        valid = false;
        radios.forEach(r => r.classList.add('campo-error'));
        if (!primerNoValido) primerNoValido = radios[0];
      } else {
        radios.forEach(r => r.classList.remove('campo-error'));
      }
    }
    // Teléfono celular
    else if (campo.type === 'tel' && campo.id === 'celular') {
      const soloNumeros = campo.value.replace(/\D/g, "");
      if (soloNumeros.length !== 10) {
        valid = false;
        campo.classList.add('campo-error');
        campo.setCustomValidity("El celular debe tener exactamente 10 dígitos numéricos.");
        if (!primerNoValido) primerNoValido = campo;
      } else {
        campo.setCustomValidity('');
        campo.classList.remove('campo-error');
      }
    }
    // Otros campos
    else {
      if (!campo.checkValidity()) {
        valid = false;
        campo.classList.add('campo-error');
        if (!primerNoValido) primerNoValido = campo;
      } else {
        campo.classList.remove('campo-error');
      }
    }
  });

  if (!valid && primerNoValido) {
    primerNoValido.scrollIntoView({ behavior: 'smooth', block: 'center' });
    alert(primerNoValido.validationMessage || 'Por favor, completa correctamente todos los campos.');
  }

  // Validamos el campo #17: ficha
  const ficha = fase.querySelector('#ficha');
  if (ficha && !ficha.value.trim()) {
    ficha.classList.add('campo-error');          // lo pinta de rojo
    ficha.scrollIntoView({behavior:'smooth'});   // lo lleva arriba
    ficha.reportValidity();                      // muestra el tooltip del navegador
    return false;                                // bloquea el avance
}
  return valid;
}

// Botones multipaso
function crearBotones() {
  fases.forEach((fase, i) => {
    const contenedor = document.createElement('div');
    contenedor.className = 'navegacion-botones';

    if (i > 0) {
      const btnAtras = document.createElement('button');
      btnAtras.type = 'button';
      btnAtras.className = 'btn-verde';
      btnAtras.textContent = 'Atrás';
      btnAtras.onclick = () => {
        faseActual--;
        mostrarFase(faseActual);
      };
      contenedor.appendChild(btnAtras);
    }

    if (i < fases.length - 1) {
      const btnSiguiente = document.createElement('button');
      btnSiguiente.type = 'button';
      btnSiguiente.className = 'btn-verde';
      btnSiguiente.textContent = 'Siguiente';
      btnSiguiente.onclick = () => {
        if (validarFaseActual()) {
          faseActual++;
          mostrarFase(faseActual);
          actualizarBarra(); // Actualiza la barra de progreso al cambiar de fase
        }
      };
      contenedor.appendChild(btnSiguiente);
    }

    fase.appendChild(contenedor);
  });
}



crearBotones();
mostrarFase(faseActual);




document.querySelectorAll('input, select, textarea').forEach(campo => {
  campo.addEventListener('blur', () => {
    campo.classList.add('tocado');
  });
  campo.addEventListener('change', () => {
    campo.classList.add('tocado');
  });
});


 //Función genérica para mostrar el campo "otro" asociado
function setupCampoOtro(selectId, inputId) {
  const select = document.getElementById(selectId);
  const input = document.getElementById(inputId);

  if (select && input) {
    select.addEventListener('change', function () {
      if (select.value === 'Otro') {
        input.style.display = 'block';
        input.required = false;
      } else {
        input.style.display = 'none';
        input.required = false;
        input.value = ''; // Limpiar si se oculta
      }
    });
  }
}

// Reglas por tipo de documento (ajústalas si tus rangos reales difieren)
const REGLAS_ID = {
  TI:  { min: 6,  max: 10,  soloNumeros: true,  etiqueta: 'Tarjeta de Identidad' },
  CC:  { min: 6,  max: 10,  soloNumeros: true,  etiqueta: 'Cédula de Ciudadanía' }, // amplié max a 12 por casos largos
  CE:  { min: 6,  max: 15,  soloNumeros: false, etiqueta: 'Cédula de Extranjería' },
  PEP: { min: 6,  max: 15,  soloNumeros: false, etiqueta: 'Permiso Especial de Permanencia' },
  PPT: { min: 6,  max: 15,  soloNumeros: false, etiqueta: 'Permiso por Protección Temporal' },
  PAS: { min: 6,  max: 15,  soloNumeros: false, etiqueta: 'Pasaporte' }
};

function actualizarReglasNumeroId() {
  const tipo = document.getElementById('tipo_id');
  const input = document.getElementById('numero_id');
  const hint  = document.getElementById('numero_id_hint');
  if (!tipo || !input) return;

  const regla = REGLAS_ID[tipo.value];

  if (!regla) {
    input.removeAttribute('maxlength');
    input.removeAttribute('minlength');
    input.removeAttribute('pattern');
    input.placeholder = '';
    if (hint) hint.textContent = '';
    return;
  }

  input.maxLength = regla.max;
  input.minLength = regla.min;

  if (regla.soloNumeros) {
    input.setAttribute('pattern', `\\d{${regla.min},${regla.max}}`);
    input.setAttribute('inputmode', 'numeric');
    input.placeholder = `Solo números (${regla.min}-${regla.max} dígitos)`;
  } else {
    // Alfanumérico sin espacios (si necesitas guiones, avísame y lo habilitamos)
    input.setAttribute('pattern', `[A-Za-z0-9]{${regla.min},${regla.max}}`);
    input.setAttribute('inputmode', 'text');
    input.placeholder = `Letras y/o números (${regla.min}-${regla.max} caracteres)`;
  }

  if (hint) {
    const tipoTxt = regla.etiqueta || tipo.value;
    hint.textContent = `${tipoTxt}: ${regla.min}-${regla.max} ${regla.soloNumeros ? 'dígitos (solo números)' : 'caracteres alfanuméricos'}.`;
  }

  input.oninvalid = () => {
    input.setCustomValidity(
      regla.soloNumeros
        ? `Ingresa de ${regla.min} a ${regla.max} dígitos numéricos.`
        : `Ingresa de ${regla.min} a ${regla.max} caracteres alfanuméricos (sin espacios).`
    );
  };
  input.oninput = () => input.setCustomValidity('');
}

function filtroNumeroIdEnVivo() {
  const tipo = document.getElementById('tipo_id');
  const input = document.getElementById('numero_id');
  if (!tipo || !input) return;

  const regla = REGLAS_ID[tipo.value];
  if (!regla) return;

  if (regla.soloNumeros) {
    const limpio = input.value.replace(/\D+/g, '');
    if (limpio !== input.value) input.value = limpio;
  } else {
    // Quita todo lo que no sea letra o número
    const limpio = input.value.replace(/[^A-Za-z0-9]+/g, '');
    if (limpio !== input.value) input.value = limpio;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const tipo = document.getElementById('tipo_id');
  const input = document.getElementById('numero_id');
  if (tipo) {
    tipo.addEventListener('change', actualizarReglasNumeroId);
    actualizarReglasNumeroId();
  }
  if (input) input.addEventListener('input', filtroNumeroIdEnVivo);
});


// Configurar todos los campos que usan "Otro"
setupCampoOtro('departamento', 'dpto_otro');
setupCampoOtro('programa', 'programa_otro');
setupCampoOtro('situacion_negocio', 'negocio_otro');

// Restricción de fecha para los campos de nacimiento, expedición y orientación
document.addEventListener('DOMContentLoaded', function () {
  const hoy = new Date();
  const minFecha = '1900-01-01';
  const fecha18 = new Date(hoy.getFullYear() - 16, hoy.getMonth(), hoy.getDate()).toISOString().split('T')[0];


  const campoNacimiento = document.getElementById('fecha_nacimiento');
  const campoExpedicion = document.getElementById('fecha_expedicion');
  const campoOrientacion = document.getElementById('fecha_orientacion');

  if (campoNacimiento) {
    campoNacimiento.setAttribute('max', fecha18);
    campoNacimiento.setAttribute('min', minFecha);
    campoNacimiento.addEventListener('input', () => {
      const seleccionada = campoNacimiento.value;
      if (seleccionada > fecha18) {
        campoNacimiento.setCustomValidity('Debes tener al menos 16 años.');
      } else {
        campoNacimiento.setCustomValidity('');
      }
    });
  }

  if (campoExpedicion) {  
    const fechaExpedicion = hoy.toISOString().split('T')[0];
    campoExpedicion.setAttribute('max', fechaExpedicion);
    campoExpedicion.setAttribute('min', minFecha);
    campoExpedicion.addEventListener('input', () => {
      const seleccionada = campoExpedicion.value;
      if (seleccionada > fechaExpedicion) {
        campoExpedicion.setCustomValidity('La fecha de expedición no puede pasarse de el día en curso.');
      } else {
        campoExpedicion.setCustomValidity('');
      }
    });
  }

  if (campoOrientacion) {
    const maxOrientacion = hoy.toISOString().split('T')[0];
    campoOrientacion.setAttribute('max', maxOrientacion);
    campoOrientacion.setAttribute('min', '2010-01-01');
  }
});


document.addEventListener('DOMContentLoaded', () => {
  const d = new Date();

  const yyyy = d.getFullYear();
  const mm   = String(d.getMonth() + 1).padStart(2, '0');
  const dd   = String(d.getDate()).padStart(2, '0');
  const hh   = String(d.getHours()).padStart(2, '0');
  const mi   = String(d.getMinutes()).padStart(2, '0');
  const ss   = String(d.getSeconds()).padStart(2, '0');

  const soloFecha = `${yyyy}-${mm}-${dd}`;
  const fechaHoraInicio = `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;

  // visible y no editable
  const display = document.getElementById('fecha_orientacion_display');
  if (display) display.value = soloFecha;

  // ocultos que se envían
  const hiddenFecha = document.getElementById('fecha_orientacion');
  if (hiddenFecha) hiddenFecha.value = soloFecha;

  const hiddenTs = document.getElementById('ts_inicio');
  if (hiddenTs) hiddenTs.value = fechaHoraInicio;
});

 const form  = document.querySelector('#formEmprendedores'); // <form id="formEmprendedores">
  const nivel_formacion = document.querySelector('#nivel_formacion');

  // Mapa: nivel -> id del select de carrera
  const mapaCarreras = {
    'Tecnólogo': '#carrera_tecnologo',
    'Técnico':   '#carrera_tecnico',
    'Operario':  '#carrera_operario',
    'Auxiliar':  '#carrera_auxiliar'
  };

  const todosCarrera = Object.values(mapaCarreras).map(sel => document.querySelector(sel));

  function resetCarreras() {
    todosCarrera.forEach(s => {
      s.style.display = 'none';   // oculto
      s.required = false;         // que no sea obligatorio si está oculto
      s.disabled = true;          // no se envía al backend
      s.value = '';               // reset
    });
  }

  function syncCarreraConNivel() {
    resetCarreras();
    const val = nivel_formacion.value;
    if (mapaCarreras[val]) {
      const s = document.querySelector(mapaCarreras[val]);
      s.style.display = '';       // mostrar (block/inline según tu CSS)
      s.disabled = false;         // habilitar envío
      s.required = true;          // obligatorio
    }
    // Si es "Sin título" o vacío: no se muestra ninguna carrera (no requerida)
  }

  // Cambios en el nivel
  nivel_formacion.addEventListener('change', syncCarreraConNivel);

  // Estado inicial al cargar la página
  document.addEventListener('DOMContentLoaded', syncCarreraConNivel);

  // Extra: validación por si acaso (usa los mensajes nativos del navegador)
  form?.addEventListener('submit', (e) => {
    // El atributo "required" en #nivel_formacion ya fuerza la elección
    const sel = mapaCarreras[nivel.value] ? document.querySelector(mapaCarreras[nivel.value]) : null;
    if (sel && !sel.value) {
      sel.reportValidity(); // muestra el aviso nativo en el select de carrera
      e.preventDefault();
    }

    // Enfoca el primer campo inválido para que se vea el foco rojo al intentar enviar
document.querySelector('#formEmprendedores')?.addEventListener('submit', (e) => {
  const form = e.currentTarget;
  if (!form.checkValidity()) {
    e.preventDefault();
    const firstInvalid = form.querySelector(':invalid');
    firstInvalid?.focus(); // al enfocarlo, tomará el estilo rojo del CSS
  }
});
  });


// Restricción dinámica para campo ficha (solo números o 'no aplica')
const inputFicha = document.getElementById('ficha');
if (inputFicha) {
  inputFicha.addEventListener('input', function() {
    let valor = inputFicha.value.trim();
    if (
      valor === '' ||
      /^[0-9]+$/.test(valor) ||
      valor.toLowerCase() === 'no aplica'
    ) {
      inputFicha.setCustomValidity('');
      inputFicha.classList.remove('campo-error');
    } else {
      inputFicha.setCustomValidity("Solo se permite ingresar números o 'no aplica'.");
      inputFicha.classList.add('campo-error');
    }
  });
}

// Restricción dinámica para campo programa_formacion (solo texto o 'no aplica')
const inputPrograma = document.getElementById('programa_formacion');
if (inputPrograma) {
  inputPrograma.addEventListener('input', function() {
    let valor = inputPrograma.value.trim();
    if (
      valor === '' ||
      /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(valor) ||
      valor.toLowerCase() === 'no aplica'
    ) {
      inputPrograma.setCustomValidity('');
      inputPrograma.classList.remove('campo-error');
    } else {
      inputPrograma.setCustomValidity("Solo se permite texto o 'no aplica'.");
      inputPrograma.classList.add('campo-error');
    }
  });
}

// ORIENTADORES POR CENTRO
const orientadoresPorCentro = {
  CAB: [
    "Celiced Castaño Barco",
    "Jose Julian Angulo Hernandez",
    "Lina Maria Varela",
    "Harby Arce",
    "Carlos Andrés Matallana"
  ],
  CBI: [
    "Hector James Serrano Ramírez",
    "Javier Duvan Cano León",
    "Sandra Patricia Reinel Piedrahita",
    "Julian Adolfo Manzano Gutierrez"
  ],
  CDTI: [
    "Diana Lorena Bedoya Vásquez",
    "Jacqueline Mafla Vargas",
    "Juan Manuel Oyola",
    "Gloria Betancourth"
  ],
  CEAI: [
    "Carolina Gálvez Noreña",
    "Cerbulo Andres Cifuentes Garcia",
    "Clara Ines Campo chaparro"
  ],
  CGTS: [
    "Francia Velasquez",
    "Julio Andres Pabon Arboleda",
    "Andres Felipe Betancourt Hernandez"
  ],
  ASTIN: [
    "Pablo Andres Cardona Echeverri",
    "Juan Carlos Bernal Bernal",
    "Pablo Diaz",
    "Marlen Erazo"
  ],
  CTA: [
    "Angela Rendon Marin",
    "Juan Manuel Marmolejo Escobar",
    "Liliana Fernandez Angulo",
    "Luz Adriana Loaiza"
  ],
  CLEM: [
    "Adalgisa Palacio Santa",
    "Eiider Cardona",
    "Manuela Jimenez",
    "William Bedoya Gomez"
  ],
  CNP: [
    "LEIDDY DIANA MOLANO CAICEDO",
    "PEDRO ANDRÉS ARCE MONTAÑO",
    "DIANA MORENO FERRÍN"
  ],
  CC: [
    "Franklin Ivan Marin Gomez",
    "Jorge Iván Valencia Vanegas",
    "Deider Arboleda Riascos"
  ]
};

function actualizarOrientadores() {
  const centroSeleccionado = document.getElementById("centro_orientacion").value;
  const selectOrientador = document.getElementById("orientador");

  selectOrientador.innerHTML = '<option value="">-- Selecciona un orientador --</option>';

  if (orientadoresPorCentro[centroSeleccionado]) {
    orientadoresPorCentro[centroSeleccionado].forEach(nombre => {
      const option = document.createElement("option");
      option.value = nombre;
      option.textContent = nombre;
      selectOrientador.appendChild(option);
    });
  }
}

// Mapa simple de nacionalidades
const paisNacionalidad = {
  "Afganistán": "Afgano/a",
  "Albania": "Albanés/a",
  "Alemania": "Alemán/a",
  "Andorra": "Andorrano/a",
  "Angola": "Angoleño/a",
  "Antigua y Barbuda": "Antiguano/a",
  "Arabia Saudita": "Saudí/a",
  "Argelia": "Argelino/a",
  "Argentina": "Argentino/a",
  "Armenia": "Armenio/a",
  "Australia": "Australiano/a",
  "Austria": "Austriaco/a",
  "Azerbaiyán": "Azerí/a",
  "Bahamas": "Bahamés/a",
  "Bangladés": "Bangladesí/a",
  "Barbados": "Barbadense/a",
  "Baréin": "Bareiní/a",
  "Bélgica": "Belga/a",
  "Belice": "Beliceño/a",
  "Benín": "Beninés/a",
  "Bielorrusia": "Bielorruso/a",
  "Birmania": "Birmano/a",
  "Bolivia": "Boliviano/a",
  "Bosnia y Herzegovina": "Bosnio/a",
  "Botsuana": "Botsuano/a",
  "Brasil": "Brasileño/a",
  "Brunéi": "Bruneano/a",
  "Bulgaria": "Búlgaro/a",
  "Burkina Faso": "Burkinés/a",
  "Burundi": "Burundés/a",
  "Bután": "Butanés/a",
  "Cabo Verde": "Caboverdiano/a",
  "Camboya": "Camboyano/a",
  "Camerún": "Camerunés/a",
  "Canadá": "Canadiense/a",
  "Catar": "Catarí/a",
  "Chile": "Chileno/a",
  "China": "Chino/a",
  "Chipre": "Chipriota/a",
  "Colombia": "Colombiano/a",
  "Corea del Norte": "Norcoreano/a",
  "Corea del Sur": "Surcoreano/a",
  "Costa Rica": "Costarricense/a",
  "Croacia": "Croata/a",
  "Cuba": "Cubano/a",
  "Dinamarca": "Danés/a",
  "Ecuador": "Ecuatoriano/a",
  "Egipto": "Egipcio/a",
  "El Salvador": "Salvadoreño/a",
  "Emiratos Árabes Unidos": "Emiratí/a",
  "Eslovaquia": "Eslovaco/a",
  "Eslovenia": "Esloveno/a",
  "España": "Español/a",
  "Estados Unidos": "Estadounidense/a",
  "Estonia": "Estonio/a",
  "Etiopía": "Etíope/a",
  "Filipinas": "Filipino/a",
  "Finlandia": "Finlandés/a",
  "Francia": "Francés/a",
  "Gabón": "Gabonés/a",
  "Gambia": "Gambiano/a",
  "Georgia": "Georgiano/a",
  "Ghana": "Ghanés/a",
  "Grecia": "Griego/a",
  "Guatemala": "Guatemalteco/a",
  "Guinea": "Guineano/a",
  "Guyana": "Guyanés/a",
  "Haití": "Haitiano/a",
  "Honduras": "Hondureño/a",
  "Hungría": "Húngaro/a",
  "India": "Indio/a",
  "Indonesia": "Indonesio/a",
  "Irak": "Iraquí/a",
  "Irán": "Iraní/a",
  "Irlanda": "Irlandés/a",
  "Islandia": "Islandés/a",
  "Israel": "Israelí/a",
  "Italia": "Italiano/a",
  "Jamaica": "Jamaicano/a",
  "Japón": "Japonés/a",
  "Jordania": "Jordano/a",
  "Kazajistán": "Kazajo/a",
  "Kenia": "Keniano/a",
  "Kirguistán": "Kirguís/a",
  "Kuwait": "Kuwaití/a",
  "Laos": "Laosiano/a",
  "Letonia": "Letón/a",
  "Líbano": "Libanés/a",
  "Liberia": "Liberiano/a",
  "Libia": "Libio/a",
  "Liechtenstein": "Liechtensteiniano/a",
  "Lituania": "Lituano/a",
  "Luxemburgo": "Luxemburgués/a",
  "Madagascar": "Malgache/a",
  "Malasia": "Malasio/a",
  "Malawi": "Malauí/a",
  "Maldivas": "Maldivo/a",
  "Malta": "Maltés/a",
  "Marruecos": "Marroquí/a",
  "México": "Mexicano/a",
  "Moldavia": "Moldavo/a",
  "Mónaco": "Monegasco/a",
  "Mongolia": "Mongol/a",
  "Montenegro": "Montenegrino/a",
  "Mozambique": "Mozambiqueño/a",
  "Namibia": "Namibio/a",
  "Nepal": "Nepalí/a",
  "Nicaragua": "Nicaragüense/a",
  "Níger": "Nigerino/a",
  "Nigeria": "Nigeriano/a",
  "Noruega": "Noruego/a",
  "Nueva Zelanda": "Neozelandés/a",
  "Omán": "Omaní/a",
  "Países Bajos": "Neerlandés/a",
  "Pakistán": "Pakistaní/a",
  "Panamá": "Panameño/a",
  "Paraguay": "Paraguayo/a",
  "Perú": "Peruano/a",
  "Polonia": "Polaco/a",
  "Portugal": "Portugués/a",
  "Reino Unido": "Británico/a",
  "República Checa": "Checo/a",
  "República Dominicana": "Dominicano/a",
  "Rumania": "Rumano/a",
  "Rusia": "Ruso/a",
  "San Marino": "Sanmarinense/a",
  "Senegal": "Senegalés/a",
  "Serbia": "Serbio/a",
  "Singapur": "Singapurense/a",
  "Siria": "Sirio/a",
  "Somalia": "Somalí/a",
  "Sri Lanka": "Ceilanés/a",
  "Sudáfrica": "Sudafricano/a",
  "Sudán": "Sudanés/a",
  "Suecia": "Sueco/a",
  "Suiza": "Suizo/a",
  "Tailandia": "Tailandés/a",
  "Tanzania": "Tanzano/a",
  "Túnez": "Tunecino/a",
  "Turquía": "Turco/a",
  "Ucrania": "Ucraniano/a",
  "Uganda": "Ugandés/a",
  "Uruguay": "Uruguayo/a",
  "Uzbekistán": "Uzbeko/a",
  "Venezuela": "Venezolano/a",
  "Vietnam": "Vietnamita/a",
  "Yemen": "Yemení/a",
  "Zambia": "Zambiano/a",
  "Zimbabue": "Zimbabuense/a"
};

// Lista de países desde el mapa
const listaPaises = Object.keys(paisNacionalidad);



document.addEventListener('DOMContentLoaded', function () {
  const selectPais = document.getElementById('pais');
  const nacionalidadSpan = document.getElementById('nacionalidad');

  // Llenar el select de países
  selectPais.innerHTML = '<option value="" disabled selected>-- Selecciona un país --</option>';
  listaPaises.forEach(pais => {
    const option = document.createElement('option');
    option.value = pais;
    option.textContent = pais;
    selectPais.appendChild(option);
  });

  // Al cambiar de país, mostrar la nacionalidad correspondiente
  selectPais.addEventListener('change', function () {
    const paisSeleccionado = this.value;
    const nacionalidad = paisNacionalidad[paisSeleccionado] || '';
    nacionalidadSpan.textContent = nacionalidad;

        // Crear o actualizar el campo oculto para enviar al backend
    let inputHidden = document.getElementById('nacionalidad_hidden');
    if (!inputHidden) {
      inputHidden = document.createElement('input');
      inputHidden.type = 'hidden';
      inputHidden.name = 'nacionalidad';
      inputHidden.id = 'nacionalidad_hidden';
      selectPais.closest('form').appendChild(inputHidden);
    }
    inputHidden.value = nacionalidad;
  });
});

// Envío del formulario con alert de éxito
document.querySelector('form').addEventListener('submit', function(event) {
  event.preventDefault();
  if (!validarFaseActual()) return; // Previene envío si hay error en la última fase
  enviarFormulario();
});

const nivel = document.getElementById('nivel_formacion');
const tecno = document.getElementById('carrera_tecnologo');
const tecni = document.getElementById('carrera_tecnico');
const op    = document.getElementById('carrera_operario');
const aux   = document.getElementById('carrera_auxiliar');

function mostrarSelect(valor) {
  [tecno, tecni, op, aux].forEach(s => s.style.display = 'none');
  if (valor === 'Tecnólogo') tecno.style.display = 'block';
  if (valor === 'Técnico')   tecni.style.display = 'block';
  if (valor === 'Operario')  op.style.display  = 'block';
  if (valor === 'Auxiliar')  aux.style.display = 'block';
}

nivel.addEventListener('change', e => mostrarSelect(e.target.value));

const selectPrograma = document.getElementById('programa');
  const inputOtro      = document.getElementById('programa_otro');
  

  selectPrograma.addEventListener('change', function () {
    if (this.value === 'Otro') {
      inputOtro.style.display = 'block';
      inputOtro.setAttribute('required', 'required');
    } else {
      inputOtro.style.display = 'none';
      inputOtro.removeAttribute('required');
      inputOtro.value = ''; // limpia si se cambia
    }
  });
  


function enviarFormulario() {
  const form = document.querySelector("form");
  form.submit();              
}

function actualizarBarra() {
  const pasos = document.querySelectorAll('.progress-steps .step');
  const barra = document.getElementById('progress-bar');

  pasos.forEach((paso, index) => {
    if (index <= faseActual) {
      paso.classList.add('active');
    } else {
      paso.classList.remove('active');
    }
  });

  const progreso = (faseActual) / (pasos.length - 1) * 100;
  barra.style.width = `${progreso}%`;
}

