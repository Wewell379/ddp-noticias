<?php
require 'config/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: reportajes.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM reportajes WHERE slug = ? AND estado = 'publicado'");
$stmt->execute([$slug]);
$r = $stmt->fetch();

if (!$r) {
    http_response_code(404);
    $page_title = 'No encontrado';
    $current = 'reportajes';
    require 'includes/header.php';
    echo '<div class="container py-5 mt-5" style="text-align:center;">
            <h1 style="font-size:60px;color:#ff2e2e;">404</h1>
            <h2>Reportaje no encontrado</h2>
            <p style="color:#9ca3af;margin-top:12px;">El reportaje que buscas no existe o fue despublicado.</p>
            <a href="reportajes.php" class="btn btn-style btn-primary mt-4">Volver a Reportajes</a>
          </div>';
    require 'includes/footer.php';
    exit;
}

$pdo->prepare("UPDATE reportajes SET vistas = vistas + 1 WHERE id = ?")->execute([$r['id']]);
$r['vistas'] = $r['vistas'] + 1;

$stmt = $pdo->prepare("SELECT * FROM reportajes WHERE categoria = ? AND id != ? AND estado = 'publicado' ORDER BY fecha DESC LIMIT 3");
$stmt->execute([$r['categoria'], $r['id']]);
$relacionados = $stmt->fetchAll();

if (empty($relacionados)) {
    $stmt = $pdo->prepare("SELECT * FROM reportajes WHERE id != ? AND estado = 'publicado' ORDER BY fecha DESC LIMIT 3");
    $stmt->execute([$r['id']]);
    $relacionados = $stmt->fetchAll();
}

$palabras = str_word_count(strip_tags($r['contenido'] ?: $r['resumen']));
$minutos = max(1, ceil($palabras / 200));

// Fecha en español
$meses_es = [
    'January'=>'Enero', 'February'=>'Febrero', 'March'=>'Marzo',
    'April'=>'Abril', 'May'=>'Mayo', 'June'=>'Junio',
    'July'=>'Julio', 'August'=>'Agosto', 'September'=>'Septiembre',
    'October'=>'Octubre', 'November'=>'Noviembre', 'December'=>'Diciembre'
];
$mes_en = date('F', strtotime($r['fecha']));
$mes_es = $meses_es[$mes_en] ?? $mes_en;
$fecha_es = date('d', strtotime($r['fecha'])) . ' de ' . $mes_es . ' de ' . date('Y', strtotime($r['fecha']));

// Ruta de imagen (upload o assets)
$ruta_imagen = '';
if ($r['imagen']) {
    $ruta_imagen = str_starts_with($r['imagen'], 'uploads/') ? $r['imagen'] : 'assets/images/' . $r['imagen'];
}

$url_actual = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url_compartir = urlencode($url_actual);
$titulo_compartir = urlencode($r['titulo']);

$page_title = $r['titulo'] . ' - DDP Noticias';
$current = 'reportajes';
require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="breadcrumb-contents">
      <div style="margin-bottom:12px;">
        <span style="display:inline-block;padding:4px 12px;background:#ff2e2e;color:#fff;border-radius:12px;font-size:12px;font-weight:600;">
          <?= htmlspecialchars($r['categoria']) ?>
        </span>
      </div>
      <h2 class="title-big"><?= htmlspecialchars($r['titulo']) ?></h2>
      <p style="color:#9ca3af;margin-top:16px;font-size:14px;">
        <?= $fecha_es ?>
        &nbsp;·&nbsp;
        <?= htmlspecialchars($r['autor']) ?>
        &nbsp;·&nbsp;
        <i class="fa fa-clock-o"></i> <?= $minutos ?> min de lectura
        &nbsp;·&nbsp;
        <i class="fa fa-eye"></i> <?= (int)$r['vistas'] ?> vistas
      </p>
    </div>
  </div>
</section>

<div class="container py-lg-5 py-4">
  <div class="row">
    <div class="col-lg-10 mx-auto">

      <?php if ($ruta_imagen): ?>
        <img src="<?= htmlspecialchars($ruta_imagen) ?>"
             class="img-fluid radius-image mb-4"
             style="width:100%;max-height:500px;object-fit:cover;border-radius:14px;" alt="">
      <?php endif; ?>

      <div class="contenido-reportaje" style="font-size:16px;line-height:1.8;color:#d1d5db;">
        <?= $r['contenido'] ?: '<p class="lead">' . htmlspecialchars($r['resumen']) . '</p>' ?>
      </div>

      <hr class="my-5" style="border-color:#1a2332;">
      <h4 style="margin-bottom:20px;">Comparte este reportaje</h4>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $url_compartir ?>"
           style="padding:10px 20px;background:#1877f2;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">
          <i class="fa fa-facebook"></i> Facebook
        </a>
        <a target="_blank" href="https://wa.me/?text=<?= $titulo_compartir ?>%20<?= $url_compartir ?>"
           style="padding:10px 20px;background:#25d366;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">
          <i class="fa fa-whatsapp"></i> WhatsApp
        </a>
        <a target="_blank" href="https://twitter.com/intent/tweet?text=<?= $titulo_compartir ?>&url=<?= $url_compartir ?>"
           style="padding:10px 20px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">
          <i class="fa fa-twitter"></i> X
        </a>
        <a onclick="navigator.clipboard.writeText('<?= $url_actual ?>');this.innerText='¡Copiado!';setTimeout(()=>this.innerText='Copiar enlace',2000)"
           style="padding:10px 20px;background:#666;color:#fff;border-radius:8px;cursor:pointer;font-weight:600;">
          <i class="fa fa-link"></i> Copiar enlace
        </a>
      </div>

      <hr class="my-5" style="border-color:#1a2332;">
      <a href="reportajes.php" class="btn btn-style btn-primary">← Volver a Reportajes</a>
    </div>
  </div>
</div>

<?php if (!empty($relacionados)): ?>
<section style="background:#111a28;padding:60px 0;margin-top:40px;">
  <div class="container">
    <h3 class="title-big text-center mb-5" style="color:#fff;">También te puede interesar</h3>
    <div class="row">
      <?php foreach ($relacionados as $rel):
        $ruta_rel = str_starts_with($rel['imagen'], 'uploads/') ? $rel['imagen'] : 'assets/images/' . $rel['imagen'];
      ?>
        <div class="col-lg-4 col-md-6 mt-4">
          <a href="reportaje.php?slug=<?= urlencode($rel['slug']) ?>" style="text-decoration:none;color:inherit;">
            <div style="background:#0d1520;border-radius:12px;overflow:hidden;height:100%;">
              <img src="<?= htmlspecialchars($ruta_rel) ?>" style="width:100%;height:200px;object-fit:cover;" alt="">
              <div style="padding:20px;">
                <span style="font-size:11px;color:#ff2e2e;font-weight:600;text-transform:uppercase;"><?= htmlspecialchars($rel['categoria']) ?></span>
                <h4 style="margin-top:10px;font-size:18px;color:#fff;line-height:1.4;"><?= htmlspecialchars($rel['titulo']) ?></h4>
                <p style="font-size:12px;color:#6b7280;margin-top:10px;"><?= date('d M Y', strtotime($rel['fecha'])) ?></p>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>