<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

// === KPIs ===
$stats = [
  'reportajes'   => $pdo->query("SELECT COUNT(*) FROM reportajes")->fetchColumn(),
  'noticias'     => $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn(),
  'podcasts'     => $pdo->query("SELECT COUNT(*) FROM podcasts")->fetchColumn(),
  'boletines'    => $pdo->query("SELECT COUNT(*) FROM boletines")->fetchColumn(),
  'mensajes'     => $pdo->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0")->fetchColumn(),
  'media'        => $pdo->query("SELECT COUNT(*) FROM media")->fetchColumn(),
  'vistas'       => (int)$pdo->query("SELECT COALESCE(SUM(vistas),0) FROM reportajes")->fetchColumn(),
  'suscriptores' => $pdo->query("SELECT COUNT(*) FROM suscriptores WHERE activo = 1")->fetchColumn(),
];

// === Top reportajes por vistas ===
$top_reportajes = $pdo->query("SELECT id, titulo, vistas FROM reportajes ORDER BY vistas DESC LIMIT 5")->fetchAll();

// === Últimos mensajes ===
$ultimos_mensajes = $pdo->query("SELECT * FROM mensajes_contacto ORDER BY creado_en DESC LIMIT 5")->fetchAll();

// === Últimos reportajes ===
$ultimos_reportajes = $pdo->query("SELECT id, titulo, fecha FROM reportajes ORDER BY creado_en DESC LIMIT 5")->fetchAll();

$page_title = 'Dashboard';
$page_subtitle = 'Bienvenido de vuelta, Comandante';
$seccion_actual = 'dashboard';
$page_actions = '
  <a href="reportajes.php" class="btn btn-secondary"><i class="fa fa-newspaper-o"></i> Reportajes</a>
  <a href="media.php" class="btn btn-primary"><i class="fa fa-plus"></i> Subir archivo</a>
';

ob_start();
?>

<!-- KPI GRID -->
<div class="kpi-grid">
  <div class="kpi-card">
    <i class="fa fa-newspaper-o kpi-icon"></i>
    <div class="kpi-label">Reportajes</div>
    <div class="kpi-value"><?= $stats['reportajes'] ?></div>
    <div class="kpi-trend up"><i class="fa fa-arrow-up"></i> Publicados</div>
  </div>

  <div class="kpi-card">
    <i class="fa fa-bullhorn kpi-icon"></i>
    <div class="kpi-label">Noticias</div>
    <div class="kpi-value"><?= $stats['noticias'] ?></div>
    <div class="kpi-trend neutral"><i class="fa fa-minus"></i> En el sitio</div>
  </div>

  <div class="kpi-card">
    <i class="fa fa-microphone kpi-icon"></i>
    <div class="kpi-label">Podcasts</div>
    <div class="kpi-value"><?= $stats['podcasts'] ?></div>
    <div class="kpi-trend up"><i class="fa fa-arrow-up"></i> Episodios</div>
  </div>

  <div class="kpi-card">
    <i class="fa fa-eye kpi-icon"></i>
    <div class="kpi-label">Vistas totales</div>
    <div class="kpi-value"><?= number_format($stats['vistas']) ?></div>
    <div class="kpi-trend up"><i class="fa fa-arrow-up"></i> Acumulado</div>
  </div>

  <div class="kpi-card">
    <i class="fa fa-envelope kpi-icon"></i>
    <div class="kpi-label">Mensajes nuevos</div>
    <div class="kpi-value"><?= $stats['mensajes'] ?></div>
    <div class="kpi-trend <?= $stats['mensajes']>0?'down':'neutral' ?>">
      <i class="fa fa-<?= $stats['mensajes']>0?'exclamation-circle':'check' ?>"></i>
      <?= $stats['mensajes']>0?'Requieren atención':'Todo al día' ?>
    </div>
  </div>

  <div class="kpi-card">
    <i class="fa fa-users kpi-icon"></i>
    <div class="kpi-label">Suscriptores</div>
    <div class="kpi-value"><?= $stats['suscriptores'] ?></div>
    <div class="kpi-trend neutral"><i class="fa fa-envelope-o"></i> Newsletter</div>
  </div>

  <div class="kpi-card">
    <i class="fa fa-file-pdf-o kpi-icon"></i>
    <div class="kpi-label">Boletines</div>
    <div class="kpi-value"><?= $stats['boletines'] ?></div>
    <div class="kpi-trend neutral"><i class="fa fa-download"></i> Disponibles</div>
  </div>

  <div class="kpi-card">
    <i class="fa fa-picture-o kpi-icon"></i>
    <div class="kpi-label">Archivos</div>
    <div class="kpi-value"><?= $stats['media'] ?></div>
    <div class="kpi-trend neutral"><i class="fa fa-hdd-o"></i> En biblioteca</div>
  </div>
</div>

<!-- GRID: Top reportajes + Últimos mensajes -->
<div class="grid-2">

  <!-- Top reportajes -->
  <div class="card">
    <div class="card-title"><i class="fa fa-fire" style="color:#ff2e2e;"></i> Top 5 reportajes más vistos</div>
    <?php if (empty($top_reportajes)): ?>
      <p style="color:#6b7280;font-size:14px;">Aún no hay datos de vistas.</p>
    <?php else: ?>
      <?php foreach ($top_reportajes as $i => $r): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #1a2332;">
          <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#ff2e2e,#b91c1c);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;">
            <?= $i + 1 ?>
          </div>
          <div style="flex:1;font-size:13px;"><?= htmlspecialchars(mb_substr($r['titulo'], 0, 45)) ?>...</div>
          <div style="font-weight:700;color:#ff2e2e;font-size:14px;"><?= (int)$r['vistas'] ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Últimos mensajes -->
  <div class="card">
    <div class="card-title"><i class="fa fa-envelope" style="color:#3b82f6;"></i> Últimos mensajes</div>
    <?php if (empty($ultimos_mensajes)): ?>
      <p style="color:#6b7280;font-size:14px;">No hay mensajes aún.</p>
    <?php else: ?>
      <div class="activity-feed">
        <?php foreach ($ultimos_mensajes as $m): ?>
          <div class="activity-item">
            <div class="activity-icon <?= $m['leido']?'blue':'red' ?>">
              <i class="fa fa-<?= $m['leido']?'envelope-open-o':'envelope' ?>"></i>
            </div>
            <div class="activity-text">
              <strong><?= htmlspecialchars($m['nombre']) ?></strong>
              <div style="color:#9ca3af;font-size:12px;margin-top:2px;">
                <?= htmlspecialchars(mb_substr($m['asunto'] ?: $m['mensaje'], 0, 50)) ?>...
              </div>
              <div class="activity-time"><?= $m['creado_en'] ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Últimos reportajes -->
<div class="card">
  <div class="card-title"><i class="fa fa-clock-o" style="color:#10b981;"></i> Reportajes recientes</div>
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Fecha</th>
        <th style="text-align:right;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ultimos_reportajes as $r): ?>
        <tr>
          <td>#<?= $r['id'] ?></td>
          <td><?= htmlspecialchars(mb_substr($r['titulo'], 0, 70)) ?></td>
          <td><?= $r['fecha'] ?></td>
          <td style="text-align:right;">
            <a href="editar.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-secondary"><i class="fa fa-pencil"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>