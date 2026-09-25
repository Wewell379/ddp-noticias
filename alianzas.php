<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/db.php';
$page_title = 'Alianzas - DDP Noticias';
$current = 'alianzas';
$alianzas = $pdo->query("SELECT * FROM alianzas WHERE activo = 1")->fetchAll();
require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-contents">
          <h2 class="title-big">Alianzas</h2>
          <p>Instituciones y organizaciones que trabajan con nosotros</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="w3l-logos py-5">
  <div class="container py-lg-3">
    <div class="row">
      <?php if (empty($alianzas)): ?>
        <div class="col-12 text-center py-5"><p>No hay alianzas registradas aún.</p></div>
      <?php else: ?>
        <?php foreach ($alianzas as $a): ?>
          <div class="col-lg-4 col-md-6 mb-4 text-center">
            <a href="<?= htmlspecialchars($a['url']) ?>" target="_blank">
              <img src="assets/images/<?= htmlspecialchars($a['logo']) ?>" class="img-fluid" alt="<?= htmlspecialchars($a['nombre']) ?>" style="max-height:120px;">
            </a>
            <p class="mt-3"><?= htmlspecialchars($a['nombre']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>