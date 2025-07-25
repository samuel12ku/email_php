let faseActual = 0;
const fases = document.querySelectorAll('.fase');

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

