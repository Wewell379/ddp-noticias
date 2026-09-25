<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/db.php';
$page_title = 'Podcast - DDP Noticias';
$current = 'podcast';
$podcasts = $pdo->query("SELECT * FROM podcasts ORDER BY fecha DESC")->fetchAll();
require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-contents">
          <h2 class="title-big">Podcast</h2>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="w3l-homeblock3 py-5">
  <div class="container py-lg-5 py-md-4">
    <div class="row">
      <?php if (empty($podcasts)): ?>
        <div class="col-12 text-center py-5"><p>No hay podcasts publicados aún.</p></div>
      <?php else: ?>
        <?php foreach ($podcasts as $p): ?>
          <div class="col-lg-3 col-sm-6 mb-4">
            <div class="area-box">
              <img src="assets/images/<?= htmlspecialchars($p['imagen']) ?>" class="img-fluid">
              <h4 class="mt-3"><?= htmlspecialchars($p['titulo']) ?></h4>
              <p><?= htmlspecialchars($p['descripcion']) ?></p>
              <small class="text-muted"><?= date('d M Y', strtotime($p['fecha'])) ?></small>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>