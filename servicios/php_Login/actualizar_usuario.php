<?php
include "../conexion.php";
$conexion = ConectarDB();

$id_usuario = $_POST['id_usuarios'];
$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
$numero_id = $_POST['numero_id'];
$correo = $_POST['correo'];
$celular = $_POST['celular'];

// Obtener el numero_id anterior
$stmt0 = $conexion->prepare("SELECT numero_id FROM usuarios WHERE id_usuarios = ?");
$stmt0->bind_param("i", $id_usuario);
$stmt0->execute();
$stmt0->bind_result($numero_id_anterior);
$stmt0->fetch();
$stmt0->close();

// Cifrar la nueva contraseña igual al nuevo número de documento
$contrasena_hash = password_hash($numero_id, PASSWORD_DEFAULT);

// Actualizar tabla usuarios
$stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ?, contrasena = ? WHERE id_usuarios = ?");
$stmt->bind_param("ssssssi", $nombres, $apellidos, $numero_id, $correo, $celular, $contrasena_hash, $id_usuario);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

if ($stmt->execute()) {
    // Actualizar tabla ruta_emprendedora usando el numero_id anterior
    $stmt2 = $conexion->prepare("UPDATE ruta_emprendedora SET nombres = ?, apellidos = ?, numero_id = ?, correo = ?, celular = ? WHERE numero_id = ?");
    $stmt2->bind_param("ssssss", $nombres, $apellidos, $numero_id, $correo, $celular, $numero_id_anterior);
    $stmt2->execute();

    header("Location: ../../dashboard.html");
    exit;
} else {
    echo "Error al actualizar: " . $stmt->error;
}
?>
