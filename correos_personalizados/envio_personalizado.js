const inputArchivo = document.getElementById('csv');
const nombreArchivo = document.getElementById('file-name');
const inputImagen = document.getElementById('imagen');
const imagenNombre = document.getElementById('imagen-nombre');
const preview = document.getElementById('preview');

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
