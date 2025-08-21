<?php
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/config_qr.php';

$cn = ConectarDB();

// Debes tener tabla orientadores: id_orientador, nombres, apellidos, centro (código: CAB, CBI, etc.)
$sql = "SELECT id_orientador, TRIM(CONCAT(nombres,' ',apellidos)) AS nombre, centro
        FROM orientadores
        ORDER BY centro, apellidos, nombres";
$rs  = $cn->query($sql);
if(!$rs){ die('Error SQL: '.$cn->error); }

function prefill_link(int $oid, string $centro, string $nombre): string {
    $params = ['oid' => $oid, 'center' => $centro, 'name' => $nombre];
    $sig = sign_params($params);
    $params['sig'] = $sig;
    return APP_BASE_URL.'?'.http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>QR por orientador</title>
<style>
body{font-family:system-ui,Arial,sans-serif;background:#f7faf7;margin:24px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px}
.card{background:#fff;border:1px solid #e8e8e8;border-radius:12px;padding:14px;box-shadow:0 6px 16px rgba(0,0,0,.06)}
.card h3{margin:.2rem 0;font-size:1.05rem}
.card small{color:#445}
.card img{width:220px;height:220px;display:block;margin:8px auto 0;border-radius:8px}
.link{display:block;margin-top:10px;font-size:.8rem;color:#0a7c2f;word-break:break-all}
.badge{display:inline-block;background:#0a7c2f;color:#fff;padding:2px 8px;border-radius:999px;font-size:.75rem}
</style>
</head>
<body>
<h1>QR pre-llenados para el formulario</h1>
<p>Escanea el QR para abrir el formulario con Centro y Orientador ya seleccionados.</p>

<div class="grid">
<?php while($row=$rs->fetch_assoc()):
  $url = prefill_link((int)$row['id_orientador'], $row['centro'], $row['nombre']);
  // Servicio público para QR (sin instalar librerías)
  $qr  = 'https://quickchart.io/qr?size=220&text='.urlencode($url);
?>
  <div class="card">
    <span class="badge"><?=htmlspecialchars($row['centro'])?></span>
    <h3><?=htmlspecialchars($row['nombre'])?></h3>
    <small>ID: <?= (int)$row['id_orientador']?></small>
    <img src="<?=$qr?>" alt="QR <?=$row['nombre']?>">
    <a class="link" href="<?=$url?>" target="_blank"><?=$url?></a>
  </div>
<?php endwhile; ?>
</div>
</body>
</html>
