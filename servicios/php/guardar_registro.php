<?php
// Cambia la ruta si quieres guardar el archivo en otra carpeta
$archivo = __DIR__ . '/registros.csv';

// Si no existe, crea encabezado
if(!file_exists($archivo)) {
    $header = [
        'nombres', 'apellidos', 'departamento', 'municipio', 'pais', 'tipo_id', 'numero_id', 
        'fecha_nacimiento', 'fecha_orientacion', 'genero', 'nacionalidad', 'pais_origen', 'correo', 
        'clasificacion', 'discapacidad', 'tipo_emprendedor', 'nivel_formacion', 'celular', 
        'programa', 'situacion_negocio', 'ficha', 'programa_formacion', 'centro_orientacion', 'orientador'
    ];
    $fh = fopen($archivo, 'a');
    fputcsv($fh, $header);
    fclose($fh);
}

// Agrega el registro
$registro = [];
foreach([
    'nombres', 'apellidos', 'departamento', 'municipio', 'pais', 'tipo_id', 'numero_id',
    'fecha_nacimiento', 'fecha_orientacion', 'genero', 'nacionalidad', 'pais_origen', 'correo',
    'clasificacion', 'discapacidad', 'tipo_emprendedor', 'nivel_formacion', 'celular',
    'programa', 'situacion_negocio', 'ficha', 'programa_formacion', 'centro_orientacion', 'orientador'
] as $campo) {
    $registro[] = isset($_POST[$campo]) ? $_POST[$campo] : '';
}

$fh = fopen($archivo, 'a');
fputcsv($fh, $registro);
fclose($fh);

echo "OK";
?>
