<?php
require 'config/db.php';

$page_title = 'Reportajes - DDP Noticias';
$current = 'reportajes';

$por_pagina = 9;
$pagina = max(1, (int)($_GET['p'] ?? 1));
$offset = ($pagina - 1) * $por_pagina;

// === Filtros ===
$busqueda = trim($_GET['q'] ?? '');
$filtro_cat = trim($_GET['cat'] ?? '');

$where = ["estado = 'publicado'"];
$params = [];

if ($busqueda !== '') {
    $where[] = "(titulo LIKE ? OR resumen LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}
if ($filtro_cat !== '') {
    $where[] = "categoria = ?";
    $params[] = $filtro_cat;
}
$sql_where = 'WHERE ' . implode(' AND ', $where);

// === Total ===
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reportajes $sql_where");
$stmt->execute($params);
$total = $stmt->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

// === Reportajes paginados ===
$stmt = $pdo->prepare("SELECT * FROM reportajes $sql_where ORDER BY fecha DESC LIMIT $por_pagina OFFSET $offset");
$stmt->execute($params);
$reportajes = $stmt->fetchAll();

// === Categorías para filtro ===
$categorias = $pdo->query("SELECT DISTINCT categoria FROM reportajes WHERE categoria IS NOT NULL ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);

require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="breadcrumb-contents">
      <h2 class="title-big">Reportajes</h2>
      <p>Total: <?= $total ?> reportaje<?= $total != 1 ? 's' : '' ?>
        <?= $busqueda ? "para \"$busqueda\"" : '' ?></p>
    </div>
  </div>
</section>

<div class="container py-4">
  <!-- BUSCADOR Y FILTROS -->
  <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;background:#111a28;padding:16px;border-radius:12px;margin-bottom:30px;">
    <input type="text" name="q" placeholder="Buscar reportajes..." value="<?= htmlspecialchars($busqueda) ?>"
           style="flex:1;min-width:200px;padding:11px 16px;border-radius:8px;border:1px solid #1f2937;background:#0d1520;color:#fff;font-size:14px;">

    <select name="cat" style="padding:11px 16px;border-radius:8px;border:1px solid #1f2937;background:#0d1520;color:#fff;font-size:14px;">
      <option value="">Todas las categorías</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>" <?= $filtro_cat===$c?'selected':'' ?>>
          <?= htmlspecialchars($c) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit" style="padding:11px 24px;background:#ff2e2e;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
      <i class="fa fa-search"></i> Buscar
    </button>

    <?php if ($busqueda || $filtro_cat): ?>
      <a href="reportajes.php" style="padding:11px 16px;background:#1a2332;color:#fff;border-radius:8px;text-decoration:none;">
        <i class="fa fa-times"></i> Limpiar
      </a>
    <?php endif; ?>
  </form>
</div>

<div class="grids-block-5 py-1">
  <section class="py-lg-4 py-md-3">
    <div class="container">
      <div class="row">
        <?php if (empty($reportajes)): ?>
          <div class="col-12 text-center py-5">
            <i class="fa fa-newspaper-o" style="font-size:60px;color:#374151;margin-bottom:20px;"></i>
            <h3>No se encontraron reportajes</h3>
            <?php if ($busqueda || $filtro_cat): ?>
              <p style="color:#9ca3af;margin-top:10px;">Intenta con otra búsqueda o <a href="reportajes.php" style="color:#ff2e2e;">ver todos</a>.</p>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <?php foreach ($reportajes as $r): ?>
            <div class="col-lg-4 col-md-6 grids5-info mt-4">
              <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="d-block">
                <img src="assets/images/<?= htmlspecialchars($r['imagen']) ?>" alt="" class="img-fluid"
                     style="width:100%;height:250px;object-fit:cover;border-radius:10px;">
              </a>
              <div class="blog-info">
                <span style="display:inline-block;padding:3px 10px;background:#ff2e2e;color:#fff;border-radius:12px;font-size:12px;font-weight:600;margin-bottom:8px;">
                  <?= htmlspecialchars($r['categoria']) ?>
                </span>
                <h5 style="color:#9ca3af;font-size:13px;">
                  <?= date('M d, Y', strtotime($r['fecha'])) ?> · <?= (int)$r['vistas'] ?> vistas
                </h5>
                <h4>
                  <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="d-block">
                    <?= htmlspecialchars($r['titulo']) ?>
                  </a>
                </h4>
                <a href="reportaje.php?slug=<?= urlencode($r['slug']) ?>" class="btn mt-2 p-0">
                  Leer <span class="fa fa-arrow-right"></span>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- PAGINACIÓN -->
      <?php if ($total_paginas > 1): ?>
        <div class="pagination mt-5">
          <ul class="d-flex justify-content-center" style="list-style:none;gap:8px;">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
              <li>
                <a href="?p=<?= $i ?>&q=<?= urlencode($busqueda) ?>&cat=<?= urlencode($filtro_cat) ?>"
                   class="btn <?= ($i==$pagina)?'btn-primary':'btn-outline-primary' ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endfor; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require 'includes/footer.php'; ?>