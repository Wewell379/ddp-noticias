<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/db.php';
$page_title = 'Reportajes - DDP Noticias';
$current = 'reportajes';

$por_pagina = 9;
$pagina = max(1, (int)($_GET['p'] ?? 1));
$offset = ($pagina - 1) * $por_pagina;

$total = $pdo->query("SELECT COUNT(*) FROM reportajes")->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

$stmt = $pdo->query("SELECT * FROM reportajes ORDER BY fecha DESC LIMIT $por_pagina OFFSET $offset");
$reportajes = $stmt->fetchAll();

require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-contents">
          <h2 class="title-big">Reportajes</h2>
          <p>Total: <?= $total ?> reportajes publicados</p>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="grids-block-5 py-5">
  <section class="py-lg-4 py-md-3">
    <div class="container">
      <div class="row">
        <?php if (empty($reportajes)): ?>
          <div class="col-12 text-center py-5">
            <p>No hay reportajes publicados aún.</p>
          </div>
        <?php else: ?>
          <?php foreach ($reportajes as $r): ?>
            <div class="col-lg-4 col-md-6 grids5-info mt-4">
              <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="d-block">
                <img src="assets/images/<?= htmlspecialchars($r['imagen']) ?>" alt="" class="img-fluid">
              </a>
              <div class="blog-info">
                <h5><?= date('M d, Y', strtotime($r['fecha'])) ?></h5>
                <h4><a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="d-block"><?= htmlspecialchars($r['titulo']) ?></a></h4>
                <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="btn mt-2 p-0">Leer <span class="fa fa-arrow-right"></span></a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if ($total_paginas > 1): ?>
        <div class="pagination mt-5">
          <ul class="d-flex justify-content-center" style="list-style:none;gap:8px;">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
              <li><a href="?p=<?= $i ?>" class="btn <?= ($i == $pagina) ? 'btn-primary' : 'btn-outline-primary' ?>"><?= $i ?></a></li>
            <?php endfor; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require 'includes/footer.php'; ?>