let faseActual = 0;
const fases = document.querySelectorAll('.fase');

function mostrarFase(index) {
  fases.forEach((fase, i) => {
    fase.style.display = i === index ? 'block' : 'none';
  });
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
        }
      };
      contenedor.appendChild(btnSiguiente);
    }

    fase.appendChild(contenedor);
  });
}

crearBotones();
mostrarFase(faseActual);

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

// === Lista de países actualizada (2024) ===
const listaPaises = [
  "Afganistán","Albania","Alemania","Andorra","Angola","Antigua y Barbuda","Arabia Saudita","Argelia","Argentina","Armenia","Australia","Austria",
  "Azerbaiyán","Bahamas","Bangladés","Barbados","Baréin","Bélgica","Belice","Benín","Bielorrusia","Birmania","Bolivia","Bosnia y Herzegovina",
  "Botsuana","Brasil","Brunéi","Bulgaria","Burkina Faso","Burundi","Bután","Cabo Verde","Camboya","Camerún","Canadá","Catar","Chad","Chile","China",
  "Chipre","Ciudad del Vaticano","Colombia","Comoras","Corea del Norte","Corea del Sur","Costa de Marfil","Costa Rica","Croacia","Cuba","Dinamarca",
  "Dominica","Ecuador","Egipto","El Salvador","Emiratos Árabes Unidos","Eritrea","Eslovaquia","Eslovenia","España","Estados Unidos","Estonia","Esuatini",
  "Etiopía","Filipinas","Finlandia","Fiyi","Francia","Gabón","Gambia","Georgia","Ghana","Granada","Grecia","Guatemala","Guyana","Guinea","Guinea ecuatorial",
  "Guinea-Bisáu","Haití","Honduras","Hungría","India","Indonesia","Irak","Irán","Irlanda","Islandia","Islas Marshall","Islas Salomón","Israel","Italia",
  "Jamaica","Japón","Jordania","Kazajistán","Kenia","Kirguistán","Kiribati","Kuwait","Laos","Lesoto","Letonia","Líbano","Liberia","Libia","Liechtenstein",
  "Lituania","Luxemburgo","Macedonia del Norte","Madagascar","Malasia","Malaui","Maldivas","Malí","Malta","Marruecos","Mauricio","Mauritania","México",
  "Micronesia","Moldavia","Mónaco","Mongolia","Montenegro","Mozambique","Namibia","Nauru","Nepal","Nicaragua","Níger","Nigeria","Noruega","Nueva Zelanda",
  "Omán","Países Bajos","Pakistán","Palaos","Palestina","Panamá","Papúa Nueva Guinea","Paraguay","Perú","Polonia","Portugal","Reino Unido","República Centroafricana",
  "República Checa","República del Congo","República Democrática del Congo","República Dominicana","República Sudafricana","Ruanda","Rumania","Rusia","Samoa","San Cristóbal y Nieves",
  "San Marino","San Vicente y las Granadinas","Santa Lucía","Santo Tomé y Príncipe","Senegal","Serbia","Seychelles","Sierra Leona","Singapur","Siria","Somalia","Sri Lanka",
  "Sudán","Sudán del Sur","Suecia","Suiza","Surinam","Tailandia","Tanzania","Tayikistán","Timor Oriental","Togo","Tonga","Trinidad y Tobago","Túnez","Turkmenistán","Turquía",
  "Tuvalu","Ucrania","Uganda","Uruguay","Uzbekistán","Vanuatu","Venezuela","Vietnam","Yemen","Yibuti","Zambia","Zimbabue"
];

function poblarSelectPaises(selectElement) {
  selectElement.innerHTML = '<option value="" disabled selected>-- Selecciona un país --</option>';
  listaPaises.forEach(pais => {
    const option = document.createElement("option");
    option.value = pais;
    option.textContent = pais;
    selectElement.appendChild(option);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const selectPais = document.getElementById('pais');
  if (selectPais) poblarSelectPaises(selectPais);
  const selectPaisOrigen = document.getElementById('pais_origen');
  if (selectPaisOrigen) poblarSelectPaises(selectPaisOrigen);
});

function mostrarPaisNacionalidad() {
  const radioOtro = document.querySelector('input[name="nacionalidad"][value="otro"]');
  const divPaisOrigen = document.getElementById('select-nacionalidad-origen');
  if (radioOtro && radioOtro.checked) {
    divPaisOrigen.style.display = "block";
    document.getElementById('pais_origen').setAttribute("required", "required");
  } else {
    divPaisOrigen.style.display = "none";
    document.getElementById('pais_origen').removeAttribute("required");
  }
}

// Envío del formulario con alert de éxito
document.querySelector('form').addEventListener('submit', function(event) {
  event.preventDefault();
  if (!validarFaseActual()) return; // Previene envío si hay error en la última fase
  enviarFormulario();
});

function enviarFormulario() {
  const form = document.querySelector("form");
  form.submit();              
}