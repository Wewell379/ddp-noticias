<?php
require 'config/db.php';

$page_title = 'DDP Noticias - Diálogo y Desarrollo Perú';
$current = 'inicio';

$reportajes = $pdo->query("SELECT * FROM reportajes ORDER BY fecha DESC LIMIT 4")->fetchAll();
$destacado  = $reportajes[0] ?? null;
$otros      = array_slice($reportajes, 1, 3);

$noticias = $pdo->query("SELECT * FROM noticias ORDER BY fecha DESC LIMIT 3")->fetchAll();
$boletin  = $pdo->query("SELECT * FROM boletines ORDER BY fecha DESC LIMIT 1")->fetch();
$podcasts = $pdo->query("SELECT * FROM podcasts ORDER BY fecha DESC LIMIT 4")->fetchAll();
$especiales = $pdo->query("SELECT * FROM especiales ORDER BY fecha DESC")->fetchAll();

require 'includes/header.php';
?>

<?php if ($destacado): ?>
<section class="w3l-video w3l-homeblock3" id="video">
  <div class="container-fluid">
    <div class="video-grids-info row">
      <div class="video-gd-right col-lg-6 p-0">
        <div class="position-relative">
          <a href="reportaje.php?slug=<?= urlencode($destacado['slug']) ?>">
            <img src="assets/images/<?= htmlspecialchars($destacado['imagen']) ?>" alt="" class="img-fluid">
          </a>
        </div>
      </div>
      <div class="video-gd-left col-lg-6 p-lg-5 p-4 align-self">
        <div class="p-xl-4 p-0 video-wrap">
          <h5><?= date('M d, Y', strtotime($destacado['fecha'])) ?></h5>
          <h3 class="title-big text-left mb-4">
            <a href="reportaje.php?slug=<?= urlencode($destacado['slug']) ?>">
              <?= htmlspecialchars($destacado['titulo']) ?>
            </a>
          </h3>
          <p><?= htmlspecialchars($destacado['resumen']) ?></p>
          <a href="reportaje.php?slug=<?= urlencode($destacado['slug']) ?>" class="btn mt-4 p-0">
            Leer <span class="fa fa-arrow-right"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<div class="grids-block-5 py-1">
  <section class="py-lg-4 py-md-3">
    <div class="container">
      <div class="row">
        <?php foreach ($otros as $r): ?>
          <div class="col-lg-4 col-md-6 grids5-info mt-5">
            <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="d-block">
              <img src="assets/images/<?= htmlspecialchars($r['imagen']) ?>" alt="" class="img-fluid">
            </a>
            <div class="blog-info">
              <h5><?= date('M d, Y', strtotime($r['fecha'])) ?></h5>
              <h4>
                <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="d-block">
                  <?= htmlspecialchars($r['titulo']) ?>
                </a>
              </h4>
              <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="btn mt-4 p-0">
                Leer <span class="fa fa-arrow-right"></span>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="pagination">
        <ul><li><a href="reportajes.php">Ver todos</a></li></ul>
      </div>
    </div>
  </section>
</div>

<section class="breadcrumb-area py-sm-5 py-1">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-contents">
          <h2 class="title-big">Noticias Recientes</h2>
          <a class="anchor" id="actualidad"></a>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="grids-block-5 py-5">
  <section class="py-lg-4 py-md-3">
    <div class="container">
      <div class="row">
        <?php foreach ($noticias as $n): ?>
          <div class="col-lg-4 col-md-6 grids5-info">
            <a target="_blank" href="<?= htmlspecialchars($n['url_externa']) ?>" class="d-block">
              <img src="assets/images/<?= htmlspecialchars($n['imagen']) ?>" alt="" class="img-fluid">
            </a>
            <div class="blog-info">
              <h5><?= date('F d, Y', strtotime($n['fecha'])) ?></h5>
              <h4>
                <a target="_blank" href="<?= htmlspecialchars($n['url_externa']) ?>" class="d-block">
                  <?= htmlspecialchars($n['titulo']) ?>
                </a>
              </h4>
              <a target="_blank" href="<?= htmlspecialchars($n['url_externa']) ?>" class="btn mt-4 p-0">
                Leer <span class="fa fa-arrow-right"></span>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</div>

<?php if ($boletin): ?>
<section class="w3l-homeblock5 py-0">
  <div class="container py-lg-5 py-4">
    <div class="row">
      <div class="col-lg-8 align-self">
        <h3 class="title-big mb-4"> Boletín NTEP </h3>
        <?php
          $titulares = explode('|', $boletin['titulares']);
          foreach ($titulares as $t) {
            if (trim($t)) echo '<p>- ' . htmlspecialchars(trim($t)) . '</p>';
          }
        ?>
        <div class="row mt-sm-4 mt-2 px-3">
          <div class="col-6 p-0">
            <span>Nº <?= (int)$boletin['numero'] ?></span>
            <h4><?= date('d F', strtotime($boletin['fecha'])) ?></h4>
          </div>
          <div class="col-6 p-0">
            <span><a target="_blank" href="<?= htmlspecialchars($boletin['archivo_pdf']) ?>" class="facebook"><span class="fa fa-download"></span></a></span>
            <h4>Ver Boletín</h4>
          </div>
          <center><a href="boletines.php" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a></center>
        </div>
      </div>
      <div class="col-lg-4 mt-lg-0 mt-4">
        <img src="assets/images/<?= htmlspecialchars($boletin['imagen_portada']) ?>" class="img-fluid radius-image" alt="">
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="w3l-homeblock3 py-5">
  <div class="container py-lg-5 py-md-4">
    <h3 class="title-big mb-5 text-center">Podcast</h3>
    <div class="row">
      <?php foreach ($podcasts as $p): ?>
        <div class="col-lg-3 col-sm-6">
          <div class="area-box">
            <img src="assets/images/<?= htmlspecialchars($p['imagen']) ?>">
            <p><?= htmlspecialchars($p['descripcion']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <center><a href="podcast.php" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a></center>
  </div>
</section>

<section class="w3l-team" id="team">
  <div class="teams1 py-5 mb-3">
    <div class="container py-lg-3 pb-lg-5 pb-4">
      <div class="teams1-content">
        <h3 class="title-big text-center mb-5">Especiales</h3>
        <div class="owl-carousel owl-theme text-center">
          <?php foreach ($especiales as $e): ?>
            <div class="item">
              <div class="d-grid team-info">
                <div class="column position-relative">
                  <a href="<?= htmlspecialchars($e['url'] ?: '#url') ?>">
                    <img src="assets/images/<?= htmlspecialchars($e['imagen']) ?>" alt="" class="img-fluid rounded team-image">
                  </a>
                </div>
                <div class="column">
                  <p><?= htmlspecialchars($e['descripcion'] ?: $e['titulo']) ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>