<?php
function ConectarDB() {
    $host = "localhost";
    $user = "arcanoposada_adso_emprender";
    $password = "F8nd83mpr3nd3r2025"; 
    $dbname = "arcanoposada_fondo";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    return $conn;
}
/*$host = "localhost";
    $user = "arcanoposada_adso_emprender";
    $password = "F8nd83mpr3nd3r2025"; 
    $dbname = "arcanoposada_fondo";*/