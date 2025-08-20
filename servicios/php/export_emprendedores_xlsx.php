<?php
// export_emprendedores_xlsx.php
// SOLO rol = 'orientador'. Exporta XLSX (o CSV si no hay PhpSpreadsheet).
declare(strict_types=1);
date_default_timezone_set('America/Bogota');

function deny_and_exit(int $code, string $msg): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ======= Conexión a BD ======= */
if (is_file(__DIR__ . '../../conexion.php')) {
    require_once __DIR__ . '../../conexion.php';
} else {
    deny_and_exit(500, 'No se encontró conexion.php / conexiondb.php en ../../');
}
if (function_exists('ConectarDB')) {
    $conexion = ConectarDB();
} elseif (isset($conexion) && $conexion instanceof mysqli) {
    // ok
} elseif (isset($conn) && $conn instanceof mysqli) {
    $conexion = $conn;
} elseif (isset($con) && $con instanceof mysqli) {
    $conexion = $con;
} else {
    deny_and_exit(500, 'No hay conexión activa: define ConectarDB() o una variable $conexion (mysqli).');
}
@$conexion->set_charset('utf8mb4');
@$conexion->query("SET time_zone = '-05:00'");

/* ======= Auth: solo rol orientador ======= */
$ALLOWED_ROLES = ['orientador'];
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$AUTH_OK = false;
if (isset($_SESSION['usuario_id'])) {
    $sessRol = (string)($_SESSION['rol'] ?? '');
    if (in_array($sessRol, $ALLOWED_ROLES, true)) $AUTH_OK = true;
    else deny_and_exit(403, 'No autorizado: se requiere rol orientador.');
} else {
    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/Bearer\s+(.+)/i', $hdr, $m)) deny_and_exit(401, 'No autenticado: inicia sesión o envía Bearer token.');
    $apiToken = trim($m[1]);
    if ($apiToken === '') deny_and_exit(401, 'No autenticado: token vacío.');

    $stmt = $conexion->prepare("SELECT id, rol FROM usuarios WHERE api_token = ? AND activo = 1");
    if (!$stmt) deny_and_exit(500, 'Error de servidor (validando token).');
    $stmt->bind_param("s", $apiToken);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) deny_and_exit(401, 'No autenticado: token inválido o usuario inactivo.');
    if (!in_array((string)$user['rol'], $ALLOWED_ROLES, true)) deny_and_exit(403, 'No autorizado: se requiere rol orientador.');
    $AUTH_OK = true;
}
if (!$AUTH_OK) deny_and_exit(401, 'No autenticado.');

/* ======= Config de exportación ======= */
$TABLE = 'orientacion_rcde2025_valle';

$HEADER_MAP = [
  'hora_inicio'              => 'Hora inicio',
  'hora_fin'                 => 'Hora fin',
  'fecha_registro'           => 'Fecha de registro',
  'nombres'                  => 'Nombres',
  'apellidos'                => 'Apellidos',
  'tipo_id'                  => 'Tipo de documento',
  'numero_id'                => 'Número de documento',
  'correo'                   => 'Correo electrónico',
  'celular'                  => 'Teléfono',
  'pais'                     => 'País',
  'nacionalidad'             => 'Nacionalidad',
  'departamento'             => 'Departamento',
  'municipio'                => 'Municipio',
  'fecha_nacimiento'         => 'Fecha de nacimiento',
  'fecha_expedicion'         => 'Fecha de expedición',
  'fecha_orientacion'        => 'Fecha de orientación',
  'sexo'                     => 'Sexo',
  'clasificacion'            => 'Clasificación',
  'discapacidad'             => 'Discapacidad',
  'tipo_emprendedor'         => 'Tipo de emprendedor',
  'nivel_formacion'          => 'Nivel de formación',
  'ficha'                    => 'Ficha de la carrera',
  'carrera'                  => 'Carrera Formativa',
  'programa'                 => 'Programa',
  'situacion_negocio'        => 'Actualmente usted tiene...',
  'ejercer_actividad_proyecto' => 'Usted ejerce actividad relacionada a su proyecto?',
  'empresa_formalizada'      => 'Usted tiene una empresa formalizada en la Cámara de Comercio?',
  'centro_orientacion'       => 'Centro de Orientación',
  'orientador'               => 'Orientador',
];

$TEXT_COLUMNS = ['numero_id', 'celular', 'ficha']; // Forzar como texto
$ORDER_BY = 'id ASC';

/* ======= Consulta ======= */
$cols = implode(',', array_keys($HEADER_MAP));
$sql  = "SELECT $cols FROM $TABLE ORDER BY $ORDER_BY";
$result = $conexion->query($sql);
if (!$result) deny_and_exit(500, 'Error SQL: '.$conexion->error);

/* ======= XLSX o CSV ======= */
$autoload = __DIR__ . '../../../vendor/autoload.php';
$USE_XLSX = is_file($autoload);
if ($USE_XLSX) require $autoload;

$filenameBase = 'Emprendedores_' . date('Ymd_His');

if ($USE_XLSX) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Emprendedores');
    $sheet->getDefaultRowDimension()->setRowHeight(17); // más bajo por defecto

    // Encabezados
    $colIndex = 1; // A
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++);
    $sheet->setCellValue($colLetter.'1', 'ID');
    foreach ($HEADER_MAP as $nice) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++);
        $sheet->setCellValue($colLetter.'1', $nice);
    }

    // Estilos encabezado
    $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($HEADER_MAP)+1);
    $sheet->getStyle('A1:'.$lastColLetter.'1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
    $sheet->getStyle('A1:'.$lastColLetter.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:'.$lastColLetter.'1')->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FF0A7C2F'); // verde
    $sheet->getRowDimension(1)->setRowHeight(22);
    $sheet->freezePane('A2');

    // --- Preparar medición de longitudes por columna (para ancho dinámico) ---
    $maxLen = []; // por campo del HEADER_MAP
    foreach ($HEADER_MAP as $dbCol => $title) {
        // partimos del tamaño del encabezado
        $maxLen[$dbCol] = mb_strlen($title, 'UTF-8');
    }

    // Datos
    $rowNum = 2;
    $consec = 1;
    $TEXT_FLIP = array_fill_keys($TEXT_COLUMNS, true);

    while ($row = $result->fetch_assoc()) {
        // No.
        $colIndex = 1;
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++);
        $sheet->setCellValue($colLetter.$rowNum, $consec++);

        // Campos
        foreach ($HEADER_MAP as $dbCol => $_title) {
            $value = $row[$dbCol];
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++);

            // medir longitud del texto para la columna
            $display = (string)$value;
            $len = mb_strlen($display, 'UTF-8');
            if ($len > $maxLen[$dbCol]) $maxLen[$dbCol] = $len;

            if ($dbCol === 'correo' && $display !== '') {
                $sheet->setCellValueExplicit($colLetter.$rowNum, $display, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->getCell($colLetter.$rowNum)->getHyperlink()->setUrl('mailto:'.$display);
                $sheet->getStyle($colLetter.$rowNum)->getFont()->getColor()->setARGB('FF1155CC');
            } elseif (isset($TEXT_FLIP[$dbCol])) {
                $sheet->setCellValueExplicit($colLetter.$rowNum, $display, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            } else {
                $sheet->setCellValue($colLetter.$rowNum, $value);
            }
        }
        // fijar altura baja a cada fila de datos
        $sheet->getRowDimension($rowNum)->setRowHeight(17);
        $rowNum++;
    }

    // Rango final de datos
    $lastRow = max(1, $rowNum - 1);

    // Bordes finos
    $sheet->getStyle('A1:'.$lastColLetter.$lastRow)
          ->getBorders()->getAllBorders()
          ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
          ->getColor()->setARGB('FFDDDDDD');

    // Zebra striping
    for ($r = 2; $r <= $lastRow; $r++) {
        if ($r % 2 === 0) {
            $sheet->getStyle('A'.$r.':'.$lastColLetter.$r)
                  ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setARGB('FFF6F8F6');
        }
    }

    // === Ancho dinámico por columna ===
    // reglas: convertir "caracteres" -> ancho Excel, con mínimos y máximos por tipo
    $MIN_DEFAULT = 12;
    $MAX_NORMAL  = 30;
    $MAX_WIDE    = 60;

$WIDE_COLS  = ['correo','carrera','programa','situacion_negocio','centro_orientacion','orientador']; // correo ahora es “ancha”
$WRAP_COLS  = ['situacion_negocio','ejercer_actividad_proyecto','empresa_formalizada']; // sin correo
$SHRINK_COLS = ['nombres','apellidos']; // quita correo


    // Columna "No."
    $sheet->getColumnDimensionByColumn(1)->setWidth(6);

    // Aplicar ancho por cada campo respetando límites
    $colIdx = 2; // primera columna de datos
    foreach ($HEADER_MAP as $dbCol => $title) {
        $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
        $cap = in_array($dbCol, $WIDE_COLS, true) ? $MAX_WIDE : $MAX_NORMAL;

        // +2 de padding; clamp entre MIN_DEFAULT y $cap
        $w = max($MIN_DEFAULT, min($cap, $maxLen[$dbCol] + 2));
        $sheet->getColumnDimensionByColumn($colIdx)->setWidth($w);

        // Envolver SOLO en las columnas muy largas
        if (in_array($dbCol, $WRAP_COLS, true)) {
            $sheet->getStyle($letter.'2:'.$letter.$lastRow)
                  ->getAlignment()
                  ->setWrapText(true)
                  ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        } else {
            // mantener una línea y ajustar ancho de fuente si hace falta
            $sheet->getStyle($letter.'2:'.$letter.$lastRow)
                  ->getAlignment()
                  ->setWrapText(false)
                  ->setShrinkToFit(true)
                  ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        $colIdx++;
    }

    // Alineaciones útiles
    $CENTER_COLS = ['id','hora_inicio','hora_fin','fecha_registro','fecha_nacimiento','fecha_expedicion','fecha_orientacion','sexo','tipo_id'];
    $LEFT_TEXT_COLS = ['numero_id','celular','ficha'];
    $colIdx = 2;
    foreach ($HEADER_MAP as $dbCol => $_) {
        $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
        if (in_array($dbCol, $CENTER_COLS, true)) {
            $sheet->getStyle($letter.'2:'.$letter.$lastRow)
                  ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
        if (in_array($dbCol, $LEFT_TEXT_COLS, true)) {
            $sheet->getStyle($letter.'2:'.$letter.$lastRow)
                  ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }
        $colIdx++;
    }

    // Formatos de fecha/hora (strings -> formato)
    $DATE_COLS = ['fecha_registro','fecha_nacimiento','fecha_expedicion','fecha_orientacion'];
    $DATETIME_COLS = ['hora_inicio','hora_fin'];
    $colIdx = 2;
    foreach ($HEADER_MAP as $dbCol => $_) {
        $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
        if (in_array($dbCol, $DATE_COLS, true)) {
            $sheet->getStyle($letter.'2:'.$letter.$lastRow)
                  ->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        }
        if (in_array($dbCol, $DATETIME_COLS, true)) {
            $sheet->getStyle($letter.'2:'.$letter.$lastRow)
                  ->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
        }
        $colIdx++;
    }

    // Autofiltro
    $sheet->setAutoFilter('A1:'.$lastColLetter.$lastRow);

    // Descargar
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'.$filenameBase.'.xlsx"');
    header('Cache-Control: max-age=0');
    (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
    exit;
} else {
    // CSV fallback
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$filenameBase.'.csv"');
    $out = fopen('php://output','w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, array_merge(['No.'], array_values($HEADER_MAP)));
    $no = 1;
    mysqli_data_seek($result, 0);
    while ($r = $result->fetch_assoc()) {
        $line = [$no++];
        foreach ($HEADER_MAP as $dbCol => $_) $line[] = $r[$dbCol];
        fputcsv($out, $line);
    }
    fclose($out);
    exit;
}
