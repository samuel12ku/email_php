        // Ocultar el mensaje de error al empezar a escribir la contraseña
    const inputContrasena = document.getElementById('contrasena');
    const mensajeError = document.getElementById('errorMensaje');

    if (inputContrasena && mensajeError) {
        inputContrasena.addEventListener('input', () => {
            mensajeError.style.display = 'none';
        });
    }

    // Eliminar parámetros de error de la URL para evitar que se mantenga al refrescar
    if (window.location.search.includes('error') || window.location.search.includes('documento')) {
        const url = new URL(window.location);
        url.searchParams.delete('error');
        url.searchParams.delete('documento');
        window.history.replaceState({}, document.title, url.pathname);
    }

    // Mostrar/Ocultar contraseña con íconos
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('contrasena');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    toggleBtn.addEventListener('click', function () {
        const isPassword = passInput.type === 'password';
        passInput.type = isPassword ? 'text' : 'password';
        eyeOpen.style.display = isPassword ? 'none' : '';
        eyeClosed.style.display = isPassword ? '' : 'none';
        toggleBtn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });