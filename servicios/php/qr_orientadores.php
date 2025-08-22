<?php
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/config_qr.php';

$cn = ConectarDB();

// Ajusta aquí el tamaño del QR (px)
$QR_SIZE = 420;

$sql = "SELECT id_orientador, TRIM(CONCAT(nombres,' ',apellidos)) AS nombre, centro
        FROM orientadores
        ORDER BY centro, id_orientador ASC";
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
:root{
  --qr-size: <?= (int)$QR_SIZE ?>px;
}

/* Layout */
body{font-family:system-ui,Arial,sans-serif;background:#f7faf7;margin:24px}
.grid{
  display:grid;
  /* ancho mínimo de cada tarjeta en función del QR */
  grid-template-columns:repeat(auto-fill,minmax(calc(var(--qr-size) + 40px),1fr));
  gap:18px
}
.card{
  background:#fff;border:1px solid #e8e8e8;border-radius:12px;padding:14px;
  box-shadow:0 6px 16px rgba(0,0,0,.06)
}
.card h3{margin:.2rem 0;font-size:1.05rem}
.card small{color:#445}
.card img{
  width:var(--qr-size);
  height:var(--qr-size);
  display:block;margin:8px auto 0;border-radius:8px
}
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
  // Genera el QR con quickchart al tamaño elegido
  $qr  = 'https://quickchart.io/qr?size='.$QR_SIZE.'&margin=2&text='.urlencode($url);
?>
  <div class="card">
    <span class="badge"><?=htmlspecialchars($row['centro'])?></span>
    <h3><?=htmlspecialchars($row['nombre'])?></h3>
    <small>ID: <?= (int)$row['id_orientador']?></small>
    <img src="<?=$qr?>" alt="QR <?=htmlspecialchars($row['nombre'])?>">
    <a class="link" href="<?=$url?>" target="_blank"><?=$url?></a>
  </div>
<?php endwhile; ?>
</div>
</body>
</html>
