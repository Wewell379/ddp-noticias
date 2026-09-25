<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/db.php';
$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: reportajes.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM reportajes WHERE slug = ?");
$stmt->execute([$slug]);
$r = $stmt->fetch();

if (!$r) {
    http_response_code(404);
    $page_title = 'No encontrado';
    $current = 'reportajes';
    require 'includes/header.php';
    echo '<div class="container py-5 mt-5"><h1>Reportaje no encontrado</h1><a href="reportajes.php" class="btn btn-primary mt-3">Volver</a></div>';
    require 'includes/footer.php';
    exit;
}

$page_title = htmlspecialchars($r['titulo']) . ' - DDP Noticias';
$current = 'reportajes';
require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-contents">
          <h2 class="title-big"><?= htmlspecialchars($r['titulo']) ?></h2>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container py-lg-5 py-4">
  <div class="row">
    <div class="col-lg-10 mx-auto">
      <p class="text-muted mb-3">
        <?= date('d \d\e F \d\e Y', strtotime($r['fecha'])) ?> |
        <?= htmlspecialchars($r['autor']) ?>
      </p>
      <?php if ($r['imagen']): ?>
        <img src="assets/images/<?= htmlspecialchars($r['imagen']) ?>" class="img-fluid radius-image mb-4" alt="">
      <?php endif; ?>
      <div class="contenido-reportaje">
        <?= $r['contenido'] ?: '<p class="lead">' . htmlspecialchars($r['resumen']) . '</p>' ?>
      </div>
      <hr class="my-5">
      <a href="reportajes.php" class="btn btn-style btn-primary">← Volver a Reportajes</a>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>