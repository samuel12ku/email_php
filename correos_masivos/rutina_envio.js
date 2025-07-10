const form = document.getElementById('correo-form');
const log = document.getElementById('log');

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
