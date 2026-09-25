<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/db.php';
$page_title = 'Boletín NTEP - DDP Noticias';
$current = 'boletines';
$boletines = $pdo->query("SELECT * FROM boletines ORDER BY fecha DESC")->fetchAll();
require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-contents">
          <h2 class="title-big">Boletín NTEP</h2>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="w3l-homeblock5 py-5">
  <div class="container">
    <?php if (empty($boletines)): ?>
      <div class="text-center py-5"><p>No hay boletines publicados aún.</p></div>
    <?php else: ?>
      <?php foreach ($boletines as $b): ?>
        <div class="row mb-5 pb-5 border-bottom">
          <div class="col-lg-8 align-self">
            <h3 class="title-big mb-3">Boletín NTEP Nº <?= (int)$b['numero'] ?></h3>
            <p class="text-muted"><?= date('d \d\e F \d\e Y', strtotime($b['fecha'])) ?></p>
            <?php
              $titulares = explode('|', $b['titulares']);
              foreach ($titulares as $t) {
                if (trim($t)) echo '<p>- ' . htmlspecialchars(trim($t)) . '</p>';
              }
            ?>
            <a href="<?= htmlspecialchars($b['archivo_pdf']) ?>" target="_blank" class="btn btn-style btn-primary mt-3">
              <span class="fa fa-download"></span> Descargar PDF
            </a>
          </div>
          <div class="col-lg-4 mt-lg-0 mt-4">
            <img src="assets/images/<?= htmlspecialchars($b['imagen_portada']) ?>" class="img-fluid radius-image" alt="">
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php require 'includes/footer.php'; ?>