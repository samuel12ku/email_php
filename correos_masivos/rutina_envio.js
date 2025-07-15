const form = document.getElementById('correo-form');
const log = document.getElementById('log');
// Contador de correos ingresados
const destinatarioInput = document.getElementById('destinatario');
const contadorCorreos = document.getElementById('contador-correos');

destinatarioInput.addEventListener('input', () => {
  const texto = destinatarioInput.value;
    
    // Separar por coma y limpiar espacios
  const correos = texto.split(',')
    .map(c => c.trim())
    .filter(c => c.length > 0 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c));

  contadorCorreos.textContent = `${correos.length} correo${correos.length === 1 ? '' : 's'}`;
});
// Contador de caracteres para el contenido del mensaje
const textarea = document.getElementById('contenido');
const contador = document.getElementById('contador');
const LIMITE = 700;

textarea.addEventListener('input', () => {
  const longitud = textarea.value.length;
  contador.textContent = `${longitud} / ${LIMITE}`;

  if (longitud >= LIMITE) {
    contador.style.color = 'red';
  } else {
    contador.style.color = '#555';
    }
});

form.addEventListener('submit', function (e) {
  e.preventDefault();

  const dataOriginal = new FormData(form);
  const destinatariosRaw = dataOriginal.get('destinatario');

  if (!destinatariosRaw) {
    log.innerHTML = "❌ No ingresaste ningún destinatario.<br>";
    return;
  }

  // Separar, limpiar y eliminar duplicados
  const destinatarios = destinatariosRaw
    .split(',')
    .map(correo => correo.trim())
    .filter((correo, index, self) =>
      correo !== "" &&
      self.indexOf(correo) === index &&
      /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo) // validación básica
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
    formData.set('destinatario', correoActual); // reemplazar el campo con solo 1

    fetch('mail.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.text())
      .then(response => {
        log.innerHTML += `📤 Envío a ${correoActual}: ${response}<br>`;
        index++;
        setTimeout(enviarSiguiente, 1000); // 1 segundo entre envíos
      })
      .catch(error => {
        log.innerHTML += `❌ Error con ${correoActual}: ${error}<br>`;
        index++;
        setTimeout(enviarSiguiente, 1000);
      });
  };

  enviarSiguiente();
});
