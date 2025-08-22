<?php
function ConectarDB() {
    $host = "localhost";
    $user = " arcanoposada_adso_emprender";
    $password = 'F8nd83mpr3nd3r2025'; 
    $dbname = "arcano_fondo";
/*usr: arcanoposada_adso_emprender

pw: F8nd83mpr3nd3r2025

bdd: arcanoposada_fondo */
    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    return $conn;
}

