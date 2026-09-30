<?php
$seccion_actual = $seccion_actual ?? 'dashboard';
$mensajes_nuevos = 0;
if (isset($pdo)) {
  try {
    $mensajes_nuevos = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0")->fetchColumn();
  } catch (Exception $e) {}
}
?>
<aside class="admin-sidebar" id="sidebar">
  <div class="admin-sidebar-logo">
    <img src="../assets/images/logo.png" alt="DDP">
    <span>DDP</span>
  </div>

  <div class="admin-sidebar-user">
    <div class="admin-user-avatar">
      <?= strtoupper(substr($_SESSION['admin']['nombre'] ?? 'A', 0, 1)) ?>
    </div>
    <div class="admin-user-info">
      <div class="admin-user-name"><?= htmlspecialchars($_SESSION['admin']['nombre'] ?? 'Admin') ?></div>
      <div class="admin-user-role"><?= htmlspecialchars($_SESSION['admin']['rol'] ?? 'admin') ?></div>
    </div>
  </div>

  <nav class="admin-menu">
    <div class="admin-menu-section">
      <div class="admin-menu-title">General</div>
      <a href="dashboard.php" class="admin-menu-item <?= $seccion_actual=='dashboard'?'active':'' ?>">
        <i class="fa fa-chart-line"></i> Dashboard
      </a>
    </div>

    <div class="admin-menu-section">
      <div class="admin-menu-title">Contenido</div>
      <a href="reportajes.php" class="admin-menu-item <?= $seccion_actual=='reportajes'?'active':'' ?>">
        <i class="fa fa-newspaper-o"></i> Reportajes
      </a>
      <a href="noticias.php" class="admin-menu-item <?= $seccion_actual=='noticias'?'active':'' ?>">
        <i class="fa fa-bullhorn"></i> Noticias
      </a>
      <a href="podcasts.php" class="admin-menu-item <?= $seccion_actual=='podcasts'?'active':'' ?>">
        <i class="fa fa-microphone"></i> Podcasts
      </a>
      <a href="boletines.php" class="admin-menu-item <?= $seccion_actual=='boletines'?'active':'' ?>">
        <i class="fa fa-file-pdf-o"></i> Boletines
      </a>
      <a href="alianzas.php" class="admin-menu-item <?= $seccion_actual=='alianzas'?'active':'' ?>">
        <i class="fa fa-handshake-o"></i> Alianzas
      </a>
      <a href="categorias.php" class="admin-menu-item <?= $seccion_actual=='categorias'?'active':'' ?>">
        <i class="fa fa-tags"></i> Categorías
      </a>
    </div>

    <div class="admin-menu-section">
      <div class="admin-menu-title">Medios</div>
      <a href="media.php" class="admin-menu-item <?= $seccion_actual=='media'?'active':'' ?>">
        <i class="fa fa-picture-o"></i> Biblioteca
      </a>
    </div>

    <div class="admin-menu-section">
      <div class="admin-menu-title">Comunicaciones</div>
      <a href="mensajes.php" class="admin-menu-item <?= $seccion_actual=='mensajes'?'active':'' ?>">
        <i class="fa fa-envelope"></i> Mensajes
        <?php if ($mensajes_nuevos > 0): ?>
          <span class="admin-menu-badge"><?= $mensajes_nuevos ?></span>
        <?php endif; ?>
      </a>
      <a href="suscriptores.php" class="admin-menu-item <?= $seccion_actual=='suscriptores'?'active':'' ?>">
        <i class="fa fa-users"></i> Suscriptores
      </a>
    </div>

    <div class="admin-menu-section">
      <div class="admin-menu-title">Sistema</div>
      <a href="usuarios.php" class="admin-menu-item <?= $seccion_actual=='usuarios'?'active':'' ?>">
        <i class="fa fa-user-secret"></i> Usuarios
      </a>
      <a href="configuracion.php" class="admin-menu-item <?= $seccion_actual=='configuracion'?'active':'' ?>">
        <i class="fa fa-cog"></i> Configuración
      </a>
      <a href="logs.php" class="admin-menu-item <?= $seccion_actual=='logs'?'active':'' ?>">
        <i class="fa fa-history"></i> Auditoría
      </a>
    </div>

    <div class="admin-menu-section">
      <a href="../index.php" target="_blank" class="admin-menu-item">
        <i class="fa fa-external-link"></i> Ver sitio
      </a>
      <a href="logout.php" class="admin-menu-item">
        <i class="fa fa-sign-out"></i> Cerrar sesión
      </a>
    </div>
  </nav>
</aside>