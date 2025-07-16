// Permite subir imagen y mostrar preview instantánea en el área de la foto
const inputFoto = document.getElementById('foto-input');
const fotoPreview = document.getElementById('foto-preview');
const fotoLabel = document.getElementById('foto-label');
const fotoHint = document.getElementById('foto-hint');


// Drag & Drop directo al área de foto
fotoLabel.addEventListener('dragover', function(e) {
  e.preventDefault();
  fotoLabel.style.borderColor = '#22bb88';
  fotoLabel.style.background = '#e2fbe4';
});
fotoLabel.addEventListener('dragleave', function(e) {
  fotoLabel.style.borderColor = '';
  fotoLabel.style.background = '';
});
fotoLabel.addEventListener('drop', function(e) {
  e.preventDefault();
  fotoLabel.style.borderColor = '';
  fotoLabel.style.background = '';
  if(e.dataTransfer.files && e.dataTransfer.files[0]) {
    inputFoto.files = e.dataTransfer.files;
    mostrarPreviewFoto(inputFoto.files[0]);
  }
});

inputFoto.addEventListener('change', function() {
  if (this.files && this.files[0]) {
    mostrarPreviewFoto(this.files[0]);
  }
});

function mostrarPreviewFoto(file) {
  if (!file.type.startsWith('image/')) {
    fotoHint.textContent = "¡Por favor sube una imagen válida!";
    fotoHint.style.color = "#c62626";
    return;
  }
  const reader = new FileReader();
  reader.onload = function (e) {
    fotoPreview.src = e.target.result;
    fotoHint.textContent = "¡Imagen cargada!";
    fotoHint.style.color = "#45ada1";
    setTimeout(() => {
      fotoHint.textContent = "Click aquí o arrastra una imagen";
      fotoHint.style.color = "#45ada1";
    }, 1700);
  }
  reader.readAsDataURL(file);
}
