let faseActual = 0;
const fases = document.querySelectorAll('.fase');

function mostrarFase(index) {
  fases.forEach((fase, i) => {
    fase.style.display = i === index ? 'block' : 'none';
  });
}

// --- VALIDACIÓN ROBUSTA CON TELÉFONO PERSONALIZADO ---
function validarFaseActual() {
  const fase = fases[faseActual];
  let valid = true;
  let primerNoValido = null;

  // Selecciona todos los campos requeridos en la fase
  const campos = fase.querySelectorAll('input[required], select[required], textarea[required]');
  campos.forEach(campo => {
    // Radios: al menos uno seleccionado del grupo
    if (campo.type === 'radio') {
      const radios = fase.querySelectorAll(`input[name="${campo.name}"][required]`);
      const algunoMarcado = Array.from(radios).some(r => r.checked);
      if (!algunoMarcado) {
        valid = false;
        radios.forEach(r => r.classList.add('campo-error'));
        if (!primerNoValido) primerNoValido = radios[0];
      } else {
        radios.forEach(r => r.classList.remove('campo-error'));
      }
    } else if (campo.type === 'tel' && campo.id === 'celular') {
      // Validación personalizada para teléfono: solo 10 dígitos
      const soloNumeros = campo.value.replace(/\D/g, "");
      if (soloNumeros.length !== 10) {
        valid = false;
        campo.classList.add('campo-error');
        if (!primerNoValido) primerNoValido = campo;
      } else {
        campo.classList.remove('campo-error');
      }
    } else {
      // checkValidity para otros campos (date, email, number, etc)
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
  }
  return valid;
}

// --- BOTONES DINÁMICOS MULTIPASO ---
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

// --- ORIENTADORES POR CENTRO ---
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

    // Limpiar opciones previas
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
