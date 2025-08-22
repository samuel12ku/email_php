<?php
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/config_qr.php';

$cn = ConectarDB();

$sql = "
  SELECT 
    id_orientador,
    TRIM(CONCAT(nombres,' ',apellidos)) AS nombre,
    centro,
    regional
  FROM orientadores
  ORDER BY id_orientador ASC
";
$rs = $cn->query($sql);
if(!$rs){ die('Error SQL: '.$cn->error); }

/** Link sin ID — firmamos center/region/name */
function prefill_link(string $nombre, string $centro, string $regional): string {
  $params = ['center'=>$centro, 'region'=>$regional, 'name'=>$nombre];
  $params['sig'] = sign_params($params);
  return rtrim(APP_BASE_URL, '/').'?'.http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>QR pre-llenados para el formulario</title>
<style>
  :root{
    --bg:#f5f8f5; --card:#fff; --muted:#556; --brand:#0a7c2f;
    --ring:rgba(10,124,47,.15);
  }
  *{box-sizing:border-box}
  body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);margin:0;padding:24px}
  h1{margin:0 0 8px;font-size:clamp(20px,2.6vw,32px)}
  p.lead{color:var(--muted);margin:0 0 18px}

  .grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:18px;
  }
  .card{
    background:var(--card);
    border:1px solid #e9ece8;
    border-radius:14px;
    padding:16px;
    box-shadow:0 8px 24px rgba(0,0,0,.05);
    display:flex;flex-direction:column;gap:10px;
  }
  .topline{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .badge{
    display:inline-block;background:var(--brand);color:#fff;
    padding:2px 8px;border-radius:999px;font-size:.75rem
  }
  .name{font-weight:700;margin:0}
  .meta{color:var(--muted);font-size:.85rem}

  .qr-wrap{
    display:grid;place-items:center;
    padding:8px;border-radius:12px;background:linear-gradient(0deg,#fafafa,#fff);
    border:1px solid #f0f0f0;
  }
  .qr{
    width:min(100%, 300px);
    aspect-ratio:1/1;
    border-radius:10px;
    box-shadow:0 0 0 6px #fff, 0 1px 18px rgba(0,0,0,.06);
  }

  .link-row{display:flex;gap:8px;align-items:center}
  .link{
    flex:1;min-width:0;
    font-size:.82rem;color:var(--brand);
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
    background:#f7faf8;border:1px solid #e6eee8;border-radius:8px;
    padding:6px 10px;
  }
  .btn{
    appearance:none;border:0;border-radius:8px;
    padding:8px 10px;cursor:pointer;background:var(--brand);color:#fff;
    transition:transform .06s ease, box-shadow .2s ease;
    box-shadow:0 2px 0 var(--ring);
    font-size:.82rem
  }
  .btn:active{transform:translateY(1px)}
  .copied{background:#116b2f !important}

  @media (max-width:480px){
    body{padding:16px}
    .qr{width:min(100%, 230px)}
  }
</style>
</head>
<body>
  <h1>QR pre-llenados para el formulario</h1>
  <p class="lead">Escanea el QR para abrir el formulario con Centro y Orientador ya seleccionados.</p>

  <div class="grid">
    <?php while($row=$rs->fetch_assoc()):
      $oid      = (int)$row['id_orientador'];
      $nombre   = $row['nombre'];
      $centro   = $row['centro'];
      $regional = $row['regional'];

      $url = prefill_link($nombre, $centro, $regional);
      $qr  = 'https://quickchart.io/qr?size=500&margin=2&text='.urlencode($url);
    ?>
      <div class="card">
        <div class="topline">
          <span class="badge">ID <?= $oid ?></span>
          <h3 class="name"><?= htmlspecialchars($nombre) ?></h3>
        </div>
        <div class="meta">Centro: <b><?= htmlspecialchars($centro) ?></b> · Regional: <b><?= htmlspecialchars($regional) ?></b></div>

        <div class="qr-wrap">
          <img class="qr" src="<?= $qr ?>" alt="QR de <?= htmlspecialchars($nombre) ?>">
        </div>

        <div class="link-row">
          <span class="link" title="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($url) ?></span>
          <button class="btn" data-copy="<?= htmlspecialchars($url) ?>">Copiar</button>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

<script>
  // Copiar URL al portapapeles
  document.addEventListener('click', async (e)=>{
    const btn = e.target.closest('button[data-copy]');
    if(!btn) return;
    try{
      await navigator.clipboard.writeText(btn.dataset.copy);
      btn.classList.add('copied');
      const old = btn.textContent;
      btn.textContent = '¡Copiado!';
      setTimeout(()=>{ btn.textContent = old; btn.classList.remove('copied'); }, 1200);
    }catch(err){ alert('No se pudo copiar: ' + err); }
  });
</script>
</body>
</html>
