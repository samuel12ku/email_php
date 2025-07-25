<?php
session_start();
include "../conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuarios'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $numero_id = $_POST['numero_id'];
    $correo = $_POST['correo'];
    $celular = $_POST['celular'];

    $conexion = ConectarDB();

    // Actualiza los datos en tabla usuarios
    $stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ? WHERE id_usuarios = ?");
    $stmt->bind_param("sssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $id_usuario);
    $stmt->execute();

    // También actualiza los mismos campos en ruta_emprendedora si existe
    $stmt2 = $conexion->prepare("UPDATE ruta_emprendedora SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ? WHERE numero_id = ?");
    $stmt2->bind_param("ssssss", $nombres, $apellidos, $numero_id, $correo, $celular, $numero_id);
    $stmt2->execute();

    // Redirige automáticamente al dashboard
    header("Location: ../../dashboard.html");
    exit;
}
?>
