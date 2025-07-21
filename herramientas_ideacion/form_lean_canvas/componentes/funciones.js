document.addEventListener('DOMContentLoaded', function() {
  const btnSiguiente = document.getElementById('siguiente');
  const btnAnterior = document.getElementById('anterior');
  const momento1 = document.getElementById('momento1');
  const momento2 = document.getElementById('momento2');
  const paso1 = document.getElementById('paso1');
  const paso2 = document.getElementById('paso2');

  btnSiguiente.addEventListener('click', function() {
    const primerVacio = primerCampoVacio(momento1);
    if (primerVacio) {
      primerVacio.classList.add('campo-error');
      primerVacio.focus();
      alert('Por favor, completa todos los campos antes de continuar.');
      return;
    }
    // Mostrar nombre de proyecto en paso 2
    const nombreProyecto = document.querySelector('input[name="nombre_proyecto"]');
    let contenedor = document.getElementById('nombre-proyecto-momento2');
    if (!contenedor) {
      const fieldset2 = document.getElementById('momento2');
      contenedor = document.createElement('div');
      contenedor.id = 'nombre-proyecto-momento2';
      contenedor.style = 'font-size:1.4rem;font-weight:600;color:#007832;margin-bottom:32px;text-align:center;';
      fieldset2.insertBefore(contenedor, fieldset2.children[1]);
    }
    contenedor.textContent = 'Proyecto: ' + (nombreProyecto ? nombreProyecto.value : '');

    momento1.style.display = 'none';
    momento2.style.display = 'block';
    paso1.querySelector('div').style.background = '#e1e5e9';
    paso1.querySelector('div').style.color = '#39A900';
    paso1.querySelector('span').style.color = '#888';
    paso2.querySelector('div').style.background = '#39A900';
    paso2.querySelector('div').style.color = '#fff';
    paso2.querySelector('span').style.color = '#39A900';
  });

  btnAnterior.addEventListener('click', function() {
    momento2.style.display = 'none';
    momento1.style.display = 'block';
    paso1.querySelector('div').style.background = '#39A900';
    paso1.querySelector('div').style.color = '#fff';
    paso1.querySelector('span').style.color = '#39A900';
    paso2.querySelector('div').style.background = '#e1e5e9';
    paso2.querySelector('div').style.color = '#39A900';
    paso2.querySelector('span').style.color = '#888';
  });

  // Al enviar, valida que todos los campos de Momento 2 estén llenos
  const form = document.getElementById('form-lean-canvas');
  form.addEventListener('submit', function(e) {
    const primerVacio = primerCampoVacio(momento2);
    if (primerVacio) {
      primerVacio.classList.add('campo-error');
      primerVacio.focus();
      alert('Por favor, completa todos los campos antes de enviar.');
      e.preventDefault();
      return false;
    }
  });
});

// Devuelve el primer campo vacío requerido dentro de un contenedor, o null si todos llenos
function primerCampoVacio(contenedor) {
  const campos = contenedor.querySelectorAll('input[required], textarea[required], select[required]');
  for (let campo of campos) {
    campo.classList.remove('campo-error');
    if (!campo.value.trim()) {
      return campo;
    }
  }
  return null;
}
