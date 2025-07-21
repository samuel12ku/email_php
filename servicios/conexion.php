<?php
function ConectarDB() {
    $host = "localhost";
    $user = "root";
    $password = ''; 
    $dbname = "fondo_emprender";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    return $conn;
}

