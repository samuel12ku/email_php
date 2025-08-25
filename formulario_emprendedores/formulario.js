/* =========================
   MULTIPASO / PROGRESO
   ========================= */
let faseActual = 0;
const fases = document.querySelectorAll(".fase");

function mostrarFase(index) {
  fases.forEach((fase, i) => {
    fase.style.display = i === index ? "block" : "none";
  });
  actualizarBarra();
}

function actualizarBarra() {
  const pasos = document.querySelectorAll(".progress-steps .step");
  const barra = document.getElementById("progress-bar");

  pasos.forEach((paso, idx) => {
    if (idx <= faseActual) paso.classList.add("active");
    else paso.classList.remove("active");
  });

  const progreso = faseActual / (pasos.length - 1) * 100;
  if (barra) barra.style.width = `${progreso}%`;
}

function validarFaseActual() {
  const fase = fases[faseActual];
  if (!fase) return true;

  let valid = true;
  let primerNoValido = null;

  // Solo valida los campos visibles de la fase actual
  const campos = fase.querySelectorAll("input[required], select[required], textarea[required]");
  campos.forEach((campo) => {
    // #ficha: números o "no aplica"
    if (campo.id === "ficha") {
      const valor = campo.value.trim();
      if (
        valor !== "" &&
        !/^[0-9]+$/.test(valor) &&
        valor.toLowerCase() !== "no aplica"
      ) {
        valid = false;
        campo.classList.add("campo-error");
        campo.setCustomValidity("Solo se permite ingresar números o 'no aplica'.");
        if (!primerNoValido) primerNoValido = campo;
      } else {
        campo.setCustomValidity("");
        campo.classList.remove("campo-error");
      }
      return; // siguiente campo
    }

    // Radios por grupo (si hubiera)
    if (campo.type === "radio") {
      const radios = fase.querySelectorAll(`input[name="${campo.name}"]`);
      const alguno = Array.from(radios).some((r) => r.checked);
      if (!alguno) {
        valid = false;
        radios.forEach((r) => r.classList.add("campo-error"));
        if (!primerNoValido) primerNoValido = radios[0];
      } else {
        radios.forEach((r) => r.classList.remove("campo-error"));
      }
      return;
    }

    // Celular: exactamente 10 dígitos
    if (campo.type === "tel" && campo.id === "celular") {
      const soloNum = campo.value.replace(/\D/g, "");
      if (soloNum.length !== 10) {
        valid = false;
        campo.classList.add("campo-error");
        campo.setCustomValidity("El celular debe tener exactamente 10 dígitos numéricos.");
        if (!primerNoValido) primerNoValido = campo;
      } else {
        campo.setCustomValidity("");
        campo.classList.remove("campo-error");
      }
      return;
    }

    // Validación nativa del navegador
    if (!campo.checkValidity()) {
      valid = false;
      campo.classList.add("campo-error");
      if (!primerNoValido) primerNoValido = campo;
    } else {
      campo.classList.remove("campo-error");
    }
  });

  if (!valid && primerNoValido) {
    primerNoValido.scrollIntoView({ behavior: "smooth", block: "center" });
    primerNoValido.reportValidity();
  }
  return valid;
}

function crearBotones() {
  fases.forEach((fase, i) => {
    const contenedor = document.createElement("div");
    contenedor.className = "navegacion-botones";

    if (i > 0) {
      const btnAtras = document.createElement("button");
      btnAtras.type = "button";
      btnAtras.className = "btn-verde";
      btnAtras.textContent = "Atrás";
      btnAtras.onclick = () => {
        faseActual--;
        mostrarFase(faseActual);
      };
      contenedor.appendChild(btnAtras);
    }

    if (i < fases.length - 1) {
      const btnSiguiente = document.createElement("button");
      btnSiguiente.type = "button";
      btnSiguiente.className = "btn-verde";
      btnSiguiente.textContent = "Siguiente";
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

/* =========================
   UX: MARCAR CAMPOS TOCADOS
   ========================= */
document.querySelectorAll("input, select, textarea").forEach((campo) => {
  campo.addEventListener("blur", () => campo.classList.add("tocado"));
  campo.addEventListener("change", () => campo.classList.add("tocado"));
});

/* =========================
   CAMPOS "OTRO"
   ========================= */
function setupCampoOtro(selectId, inputId) {
  const select = document.getElementById(selectId);
  const input = document.getElementById(inputId);
  if (!select || !input) return;

  select.addEventListener("change", () => {
    if (select.value === "Otro") {
      input.style.display = "block";
      input.required = true;
    } else {
      input.required = false;
      input.style.display = "none";
      input.value = "";
    }
  });
}

/* =========================
   REGLAS TIPO / NÚMERO ID
   ========================= */
const REGLAS_ID = {
  TI:  { min: 6,  max: 10, soloNumeros: true,  etiqueta: "Tarjeta de Identidad" },
  CC:  { min: 6,  max: 12, soloNumeros: true,  etiqueta: "Cédula de Ciudadanía" },
  CE:  { min: 6,  max: 15, soloNumeros: false, etiqueta: "Cédula de Extranjería" },
  PEP: { min: 6,  max: 15, soloNumeros: false, etiqueta: "Permiso Especial de Permanencia" },
  PPT: { min: 6,  max: 15, soloNumeros: false, etiqueta: "Permiso por Protección Temporal" },
  PAS: { min: 6,  max: 15, soloNumeros: false, etiqueta: "Pasaporte" }
};

function actualizarReglasNumeroId() {
  const tipo = document.getElementById("tipo_id");
  const input = document.getElementById("numero_id");
  const hint  = document.getElementById("numero_id_hint");
  if (!tipo || !input) return;

  const regla = REGLAS_ID[tipo.value];

  if (!regla) {
    input.removeAttribute("maxlength");
    input.removeAttribute("minlength");
    input.removeAttribute("pattern");
    input.placeholder = "";
    if (hint) hint.textContent = "";
    return;
  }

  input.maxLength = regla.max;
  input.minLength = regla.min;

  if (regla.soloNumeros) {
    input.setAttribute("pattern", `\\d{${regla.min},${regla.max}}`);
    input.setAttribute("inputmode", "numeric");
    input.placeholder = `Solo números (${regla.min}-${regla.max} dígitos)`;
  } else {
    input.setAttribute("pattern", `[A-Za-z0-9]{${regla.min},${regla.max}}`);
    input.setAttribute("inputmode", "text");
    input.placeholder = `Letras y/o números (${regla.min}-${regla.max} caracteres)`;
  }

  if (hint) {
    const tipoTxt = regla.etiqueta || tipo.value;
    hint.textContent = `${tipoTxt}: ${regla.min}-${regla.max} ${regla.soloNumeros ? "dígitos (solo números)" : "caracteres alfanuméricos"}.`;
  }

  input.oninvalid = () => {
    input.setCustomValidity(
      regla.soloNumeros
        ? `Ingresa de ${regla.min} a ${regla.max} dígitos numéricos.`
        : `Ingresa de ${regla.min} a ${regla.max} caracteres alfanuméricos (sin espacios).`
    );
  };
  input.oninput = () => input.setCustomValidity("");
}

function filtroNumeroIdEnVivo() {
  const tipo = document.getElementById("tipo_id");
  const input = document.getElementById("numero_id");
  if (!tipo || !input) return;

  const regla = REGLAS_ID[tipo.value];
  if (!regla) return;

  if (regla.soloNumeros) {
    const limpio = input.value.replace(/\D+/g, "");
    if (limpio !== input.value) input.value = limpio;
  } else {
    const limpio = input.value.replace(/[^A-Za-z0-9]+/g, "");
    if (limpio !== input.value) input.value = limpio;
  }
}

/* =========================
   FECHAS (NAC/EXP/ORIENT)
   ========================= */
function ahoraTimestamp() {
  const d = new Date();
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const dd = String(d.getDate()).padStart(2, "0");
  const hh = String(d.getHours()).padStart(2, "0");
  const mi = String(d.getMinutes()).padStart(2, "0");
  const ss = String(d.getSeconds()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
}

function setFechas() {
  const hoy = new Date();
  const minFecha = "1900-01-01";

  // 16 años atrás
  const fecha16 = new Date(hoy.getFullYear() - 16, hoy.getMonth(), hoy.getDate())
    .toISOString()
    .split("T")[0];

  const campoNacimiento = document.getElementById("fecha_nacimiento");
  const campoExpedicion = document.getElementById("fecha_expedicion");
  const campoOrientacion = document.getElementById("fecha_orientacion");
  const display = document.getElementById("fecha_orientacion_display");
  const hiddenTs = document.getElementById("ts_inicio");

  if (campoNacimiento) {
    campoNacimiento.setAttribute("max", fecha16);
    campoNacimiento.setAttribute("min", minFecha);
    campoNacimiento.addEventListener("input", () => {
      const sel = campoNacimiento.value;
      if (sel > fecha16) {
        campoNacimiento.setCustomValidity("Debes tener al menos 16 años.");
      } else {
        campoNacimiento.setCustomValidity("");
      }
    });
  }

  if (campoExpedicion) {
    const hoyStr = hoy.toISOString().split("T")[0];
    campoExpedicion.setAttribute("max", hoyStr);
    campoExpedicion.setAttribute("min", campoNacimiento ? campoNacimiento.value || minFecha : minFecha);
    campoExpedicion.addEventListener("input", () => {
      const sel = campoExpedicion.value;
      if (sel > hoyStr) {
        campoExpedicion.setCustomValidity("La fecha de expedición no puede ser futura.");
      } else {
        campoExpedicion.setCustomValidity("");
      }
    });
  }

  if (campoOrientacion) {
    const hoyStr = hoy.toISOString().split("T")[0];
    campoOrientacion.setAttribute("max", hoyStr);
    campoOrientacion.setAttribute("min", "2010-01-01");
  }

  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, "0");
  const dd = String(hoy.getDate()).padStart(2, "0");
  const hh = String(hoy.getHours()).padStart(2, "0");
  const mi = String(hoy.getMinutes()).padStart(2, "0");
  const ss = String(hoy.getSeconds()).padStart(2, "0");

  const soloFecha = `${yyyy}-${mm}-${dd}`;
  const tsInicio = `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;

  if (display) display.value = soloFecha;
  if (campoOrientacion) campoOrientacion.value = soloFecha;
  if (hiddenTs) hiddenTs.value = tsInicio;
}

/* =========================
   NIVEL / CARRERAS
   ========================= */
function setupNivelYCarreras() {
  const nivel = document.getElementById("nivel_formacion");
  const mapaCarreras = {
    "Tecnólogo":  document.getElementById("carrera_tecnologo"),
    "Técnico":    document.getElementById("carrera_tecnico"),
    "Operario":   document.getElementById("carrera_operario"),
    "Auxiliar":   document.getElementById("carrera_auxiliar"),
  };

  function resetCarreras() {
    Object.values(mapaCarreras).forEach((el) => {
      if (!el) return;
      el.style.display = "none";
      el.required = false;
      el.disabled = true; // deshabilitado no viaja
      el.value = "";
    });
  }

  function sync() {
    resetCarreras();
    if (!nivel) return;
    const target = mapaCarreras[nivel.value];
    if (target) {
      target.style.display = "";
      target.disabled = false;
      target.required = true;
    }
  }

  if (nivel) {
    nivel.addEventListener("change", sync);
    sync();
  }
}

/* =========================
   ORIENTADORES (FRONT)
   ========================= */
const orientadoresPorCentro = {
  CAB: [
    "Celiced Castaño Barco",
    "Jose Julian Angulo Hernandez",
    "Lina Maria Varela",
    "Harby Arce",
    "Carlos Andrés Matallana",
    "Albeth Martinez Valencia",
  ],
  CBI: [
    "Hector James Serrano Ramírez",
    "Javier Duvan Cano León",
    "Sandra Patricia Reinel Piedrahita",
    "Julian Adolfo Manzano Gutierrez",
  ],
  CDTI: [
    "Diana Lorena Bedoya Vásquez",
    "Jacqueline Mafla Vargas",
    "Juan Manuel Oyola",
    "Gloria Betancourth",
  ],
  CEAI: [
    "Carolina Gálvez Noreña",
    "Cerbulo Andres Cifuentes Garcia",
    "Clara Ines Campo chaparro",
  ],
  CGTS: [
    "Francia Velasquez",
    "Julio Andres Pabon Arboleda",
    "Andres Felipe Betancourt Hernandez",
  ],
  ASTIN: [
    "Pablo Andres Cardona Echeverri",
    "Juan Carlos Bernal Bernal",
    "Pablo Diaz",
    "Marlen Erazo",
  ],
  CTA: [
    "Angela Rendon Marin",
    "Juan Manuel Marmolejo Escobar",
    "Liliana Fernandez Angulo",
    "Luz Adriana Loaiza",
  ],
  CLEM: [
    "Adalgisa Palacio Santa",
    "Eiider Cardona",
    "Manuela Jimenez",
    "William Bedoya Gomez",
  ],
  CNP: [
    "LEIDDY DIANA MOLANO CAICEDO",
    "PEDRO ANDRÉS ARCE MONTAÑO",
    "DIANA MORENO FERRÍN",
  ],
  CC: [
    "Franklin Ivan Marin Gomez",
    "Jorge Iván Valencia Vanegas",
    "Deider Arboleda Riascos",
  ],
};

function actualizarOrientadores() {
  const centroSeleccionado = document.getElementById("centro_orientacion")?.value || "";
  const selectOrientador = document.getElementById("orientador");
  if (!selectOrientador) return;

  selectOrientador.innerHTML = '<option value="">-- Selecciona un orientador --</option>';

  if (orientadoresPorCentro[centroSeleccionado]) {
    orientadoresPorCentro[centroSeleccionado].forEach((nombre) => {
      const option = document.createElement("option");
      option.value = nombre;
      option.textContent = nombre;
      selectOrientador.appendChild(option);
    });
  }
}
// Asegura que sea accesible desde inline HTML
window.actualizarOrientadores = actualizarOrientadores;

/* =========================
   TIPO EMPRENDEDOR "OTRO"
   ========================= */
function setupTipoEmprendedorOtro() {
  const sel = document.getElementById("tipo_emprendedor");
  const inp = document.getElementById("tipo_emprendedor_otro");
  if (!sel || !inp) return;

  const toggle = () => {
    if (sel.value === "Otro") {
      inp.style.display = "block";
      inp.required = true;
      // No cambiamos el value del select; backend ya lee 'tipo_emprendedor_otro'
    } else {
      inp.required = false;
      inp.value = "";
      inp.style.display = "none";
    }
  };

  sel.addEventListener("change", toggle);
  toggle();
}

/* =========================
   NACIONALIDAD (OPCIONAL)
   ========================= */
// Si en tu HTML de países solo está Colombia, no hace falta poblar.
// Dejamos el soporte mínimo: si hay <span id="nacionalidad">, y el select cambia
// a un país distinto, podrías mapear. Aquí solo reflejamos "Colombiano/a".
function setupNacionalidad() {
  const selectPais = document.getElementById("pais");
  const nacionalidadSpan = document.getElementById("nacionalidad");
  if (!selectPais || !nacionalidadSpan) return;

  const setNac = () => {
    // Si deseas un mapa completo, puedes ampliarlo.
    const val = selectPais.value || "Colombia";
    nacionalidadSpan.textContent = (val === "Colombia") ? "Colombiano/a" : "";
    // Mantener un hidden para el backend:
    let hidden = document.getElementById("nacionalidad_hidden");
    if (!hidden) {
      hidden = document.createElement("input");
      hidden.type = "hidden";
      hidden.name = "nacionalidad";
      hidden.id = "nacionalidad_hidden";
      selectPais.closest("form")?.appendChild(hidden);
    }
    hidden.value = nacionalidadSpan.textContent;
  };

  selectPais.addEventListener("change", setNac);
  setNac();
}

/* =========================
   SUBMIT
   ========================= */
function setupSubmit() {
  const formMain = document.getElementById("MIformulario");
  if (!formMain) return;

  formMain.addEventListener("submit", (event) => {
    // Valida TODO el formulario, incluidas fases ocultas
    if (!formMain.checkValidity()) {
      event.preventDefault();

      // Ubica la primera fase que contenga inválidos y muéstrala
      for (let i = 0; i < fases.length; i++) {
        const invalido = fases[i].querySelector(":invalid");
        if (invalido) {
          faseActual = i;
          mostrarFase(faseActual);
          invalido.reportValidity();
          break;
        }
      }
      return;
    }
  });
}


/* =========================
   INIT
   ========================= */
document.addEventListener("DOMContentLoaded", () => {
  // Multipaso
  crearBotones();
  mostrarFase(faseActual);

  // Campos "Otro"
  setupCampoOtro("departamento", "dpto_otro");
  setupCampoOtro("programa", "programa_otro");
  setupCampoOtro("situacion_negocio", "negocio_otro");

  // Reglas tipo/número de ID
  const tipo = document.getElementById("tipo_id");
  const numero = document.getElementById("numero_id");
  if (tipo) {
    tipo.addEventListener("change", actualizarReglasNumeroId);
    actualizarReglasNumeroId();
  }
  if (numero) numero.addEventListener("input", filtroNumeroIdEnVivo);

  // Fechas
  setFechas();

  // Nivel / carreras
  setupNivelYCarreras();

  // Nacionalidad
  setupNacionalidad();

  // Tipo emprendedor "Otro"
  setupTipoEmprendedorOtro();

  // Submit
  setupSubmit();
});
