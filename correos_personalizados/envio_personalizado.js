const inputArchivo = document.getElementById('csv');
const nombreArchivo = document.getElementById('file-name');
const inputImagen = document.getElementById('imagen');
const imagenNombre = document.getElementById('imagen-nombre');
const preview = document.getElementById('preview');

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

inputImagen.addEventListener('change', function () {
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



inputArchivo.addEventListener('change', function () {
nombreArchivo.textContent = this.files[0] ? this.files[0].name : "Ningún archivo seleccionado";
});
