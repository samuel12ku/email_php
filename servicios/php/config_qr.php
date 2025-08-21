<?php
// servicios/php/config_qr.php

// 1) URL base del formulario (usa define por consistencia)
define('APP_BASE_URL', 'https://commander-hudson-opens-egyptian.trycloudflare.com/email_php/formulario_emprendedores/registro_emprendedores.php');

// 2) Clave secreta: intenta leer del entorno; si no existe, usa un fallback (solo para desarrollo)
$envSecret = getenv('QR_SECRET');            // <- Configúrala en el sistema/servidor para producción
if (!$envSecret || $envSecret === false) {
    // Fallback local (no lo uses en producción)
    $envSecret = 'C8FE7E04CC61D8E852F47B7E5EE8409FFFD78B73BB5E95D217EA3FE108E0C0E9';
}
define('QR_SECRET', $envSecret);

// 3) Firma HMAC-SHA256 ordenando parámetros por clave y con encoding estable (RFC3986)
function sign_params(array $params): string {
    ksort($params, SORT_STRING);
    return hash_hmac(
        'sha256',
        http_build_query($params, '', '&', PHP_QUERY_RFC3986),
        QR_SECRET
    );
}

// (Opcional) helper para construir URL firmada
function build_signed_url(array $params): string {
    $sig = sign_params($params);
    $params['sig'] = $sig;
    return APP_BASE_URL . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}
