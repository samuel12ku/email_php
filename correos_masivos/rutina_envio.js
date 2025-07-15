const form = document.getElementById('correo-form');
const log = document.getElementById('log');
const destinatarioInput = document.getElementById('destinatario');
const contadorCorreos = document.getElementById('contador-correos');
const textarea = document.getElementById('contenido');
const contador = document.getElementById('contador');
const LIMITE = 700;

// Vista previa y nombre de imagen cargada
const inputImagen = document.getElementById('imagen');
const imagenNombre = document.getElementById('imagen-nombre');
const preview = document.getElementById('preview');

//  CONTADOR DE CORREOS
destinatarioInput.addEventListener('input', () => {
  const texto = destinatarioInput.value;

  const correos = texto.split(',')
    .map(c => c.trim())
    .filter(c => c.length > 0 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c));

  contadorCorreos.textContent = `${correos.length} correo${correos.length === 1 ? '' : 's'}`;
});

// CONTADOR DE CARACTERES
textarea.addEventListener('input', () => {
  const longitud = textarea.value.length;
  contador.textContent = `${longitud} / ${LIMITE}`;
  contador.style.color = longitud >= LIMITE ? 'red' : '#555';
});

// 🖼️ MOSTRAR NOMBRE Y VISTA PREVIA DE IMAGEN
inputImagen?.addEventListener('change', function () {
  const file = this.files[0];
  if (file) {
    imagenNombre.textContent = file.name;

    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    imagenNombre.textContent = "Ningún archivo seleccionado";
    preview.style.display = 'none';
  }
});

// 📤 ENVÍO DE CORREOS UNO A UNO
form.addEventListener('submit', function (e) {
  e.preventDefault();

  const dataOriginal = new FormData(form);
  const destinatariosRaw = dataOriginal.get('destinatario');

  if (!destinatariosRaw) {
    log.innerHTML = "❌ No ingresaste ningún destinatario.<br>";
    return;
  }

  const destinatarios = destinatariosRaw
    .split(',')
    .map(correo => correo.trim())
    .filter((correo, index, self) =>
      correo !== "" &&
      self.indexOf(correo) === index &&
      /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)
    );

  if (destinatarios.length === 0) {
    log.innerHTML = "❌ No hay correos válidos para enviar.<br>";
    return;
  }

  log.innerHTML = `⏳ Iniciando envío a ${destinatarios.length} destinatario(s)...<br><br>`;
  let index = 0;

  const enviarSiguiente = () => {
    if (index >= destinatarios.length) {
      log.innerHTML += `<br><strong>✅ Todos los correos han sido enviados.</strong>`;
      return;
    }

    const correoActual = destinatarios[index];
    const formData = new FormData(form);
    formData.set('destinatario', correoActual);

    fetch('mail.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.text())
      .then(response => {
        log.innerHTML += `📤 Envío a ${correoActual}: ${response}<br>`;
        index++;
        setTimeout(enviarSiguiente, 1000);
      })
      .catch(error => {
        log.innerHTML += `❌ Error con ${correoActual}: ${error}<br>`;
        index++;
        setTimeout(enviarSiguiente, 1000);
      });
  };

  enviarSiguiente();
});
