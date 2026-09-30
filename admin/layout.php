<?php
// layout.php — Layout común para todas las páginas del admin
//
// Uso en cualquier página del admin:
//   $page_title = 'Título';
//   $page_subtitle = 'Subtítulo opcional';
//   $seccion_actual = 'dashboard';
//   $page_actions = '<a href="..." class="btn btn-primary">Nuevo</a>';
//   ob_start();
//   // ... HTML del contenido ...
//   $page_content = ob_get_clean();
//   include 'layout.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title ?? 'Panel') ?> - DDP Command Center</title>
  <link rel="stylesheet" href="../assets/css/style-starter.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="admin-layout">

  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <div style="display:flex;align-items:center;gap:16px;">
        <button class="admin-menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
          <i class="fa fa-bars"></i>
        </button>
        <div>
          <h1><?= htmlspecialchars($page_title ?? 'Panel') ?></h1>
          <?php if (!empty($page_subtitle)): ?>
            <div style="font-size:13px;color:#9ca3af;margin-top:2px;"><?= htmlspecialchars($page_subtitle) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <?php if (!empty($page_actions)): ?>
        <div class="admin-topbar-actions"><?= $page_actions ?></div>
      <?php endif; ?>
    </div>

    <div class="admin-content">
      <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($mensaje_exito) ?></div>
      <?php endif; ?>
      <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-error"><i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($mensaje_error) ?></div>
      <?php endif; ?>

      <?= $page_content ?? '' ?>
    </div>

    <footer class="admin-footer">
      © <?= date('Y') ?> DDP Command Center · Hyperion v2.0
    </footer>
  </main>
</div>
</body>
</html>