<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

// Helper para rutas de imagen
function rutaImagen($imagen) {
    if (empty($imagen)) return '';
    if (str_starts_with($imagen, 'uploads/')) return '../' . $imagen;
    if (file_exists("../assets/images/{$imagen}")) return '../assets/images/' . $imagen;
    return '';
}

// === Filtros ===
$busqueda = trim($_GET['q'] ?? '');
$filtro_cat = trim($_GET['cat'] ?? '');
$filtro_estado = trim($_GET['estado'] ?? '');

$where = [];
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
if ($filtro_estado !== '') {
    $where[] = "estado = ?";
    $params[] = $filtro_estado;
}
$sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM reportajes $sql_where ORDER BY fecha DESC");
$stmt->execute($params);
$reportajes = $stmt->fetchAll();

// Categorías para el filtro
$categorias = $pdo->query("SELECT nombre FROM categorias WHERE activa = 1 ORDER BY orden")->fetchAll(PDO::FETCH_COLUMN);
if (empty($categorias)) {
    $categorias = $pdo->query("SELECT DISTINCT categoria FROM reportajes WHERE categoria IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
}

$total = count($reportajes);

$page_title = 'Gestión de Reportajes';
$page_subtitle = "$total reportajes encontrados";
$seccion_actual = 'reportajes';
$page_actions = '<a href="editar.php" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo reportaje</a>';

ob_start();
?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i>
    <?php
      echo match($_GET['msg']) {
        'creado' => 'Reportaje creado correctamente',
        'editado' => 'Reportaje actualizado correctamente',
        'eliminado' => 'Reportaje eliminado',
        default => 'Operación realizada'
      };
    ?>
  </div>
<?php endif; ?>

<!-- BARRA DE FILTROS -->
<div class="card" style="margin-bottom:20px;">
  <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
    <div style="flex:2;min-width:200px;">
      <label style="display:block;font-size:12px;color:#9ca3af;margin-bottom:6px;">Buscar</label>
      <input type="text" name="q" class="form-control" placeholder="Título o resumen..." value="<?= htmlspecialchars($busqueda) ?>">
    </div>
    <div style="flex:1;min-width:160px;">
      <label style="display:block;font-size:12px;color:#9ca3af;margin-bottom:6px;">Categoría</label>
      <select name="cat" class="form-control">
        <option value="">Todas</option>
        <?php foreach ($categorias as $c): ?>
          <option value="<?= htmlspecialchars($c) ?>" <?= $filtro_cat===$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="flex:1;min-width:140px;">
      <label style="display:block;font-size:12px;color:#9ca3af;margin-bottom:6px;">Estado</label>
      <select name="estado" class="form-control">
        <option value="">Todos</option>
        <option value="publicado" <?= $filtro_estado==='publicado'?'selected':'' ?>>Publicados</option>
        <option value="borrador" <?= $filtro_estado==='borrador'?'selected':'' ?>>Borradores</option>
      </select>
    </div>
    <div>
      <button class="btn btn-primary"><i class="fa fa-search"></i> Filtrar</button>
      <?php if ($busqueda || $filtro_cat || $filtro_estado): ?>
        <a href="reportajes.php" class="btn btn-outline"><i class="fa fa-times"></i></a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- TABLA -->
<div class="card" style="padding:0;overflow:hidden;">
  <?php if (empty($reportajes)): ?>
    <div style="padding:60px 20px;text-align:center;">
      <i class="fa fa-newspaper-o" style="font-size:48px;color:#374151;margin-bottom:16px;"></i>
      <h3 style="font-size:18px;margin-bottom:8px;">No hay reportajes</h3>
      <p style="color:#9ca3af;font-size:14px;margin-bottom:20px;">Empieza creando el primer reportaje</p>
      <a href="editar.php" class="btn btn-primary"><i class="fa fa-plus"></i> Crear reportaje</a>
    </div>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:60px;">ID</th>
          <th style="width:80px;">Imagen</th>
          <th>Título</th>
          <th style="width:140px;">Categoría</th>
          <th style="width:110px;">Fecha</th>
          <th style="width:80px;">Vistas</th>
          <th style="width:110px;">Estado</th>
          <th style="width:180px;text-align:right;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reportajes as $r): ?>
          <tr>
            <td><strong>#<?= $r['id'] ?></strong></td>
            <td>
              <?php $ruta = rutaImagen($r['imagen']); ?>
              <?php if ($ruta): ?>
                <img src="<?= htmlspecialchars($ruta) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
              <?php else: ?>
                <div style="width:60px;height:40px;background:#1a2332;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#4b5563;">
                  <i class="fa fa-image"></i>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600;margin-bottom:2px;"><?= htmlspecialchars(mb_substr($r['titulo'], 0, 60)) ?><?= mb_strlen($r['titulo']) > 60 ? '...' : '' ?></div>
              <?php if ($r['destacado']): ?>
                <span style="font-size:11px;color:#f59e0b;"><i class="fa fa-star"></i> Destacado</span>
              <?php endif; ?>
            </td>
            <td>
              <span style="background:rgba(255,46,46,0.15);color:#ff6b6b;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600;">
                <?= htmlspecialchars($r['categoria'] ?: 'Sin categoría') ?>
              </span>
            </td>
            <td style="font-size:13px;"><?= htmlspecialchars($r['fecha']) ?></td>
            <td><strong style="color:#ff2e2e;"><?= (int)$r['vistas'] ?></strong></td>
            <td>
              <?php if ($r['estado'] === 'publicado'): ?>
                <span style="background:rgba(16,185,129,0.15);color:#34d399;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600;">
                  <i class="fa fa-check"></i> Publicado
                </span>
              <?php else: ?>
                <span style="background:rgba(245,158,11,0.15);color:#fbbf24;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600;">
                  <i class="fa fa-pencil"></i> Borrador
                </span>
              <?php endif; ?>
            </td>
            <td style="text-align:right;white-space:nowrap;">
              <a href="../reportaje.php?slug=<?= urlencode($r['slug']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver en sitio">
                <i class="fa fa-eye"></i>
              </a>
              <a href="editar.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-secondary" title="Editar">
                <i class="fa fa-pencil"></i>
              </a>
              <form method="post" action="eliminar.php" style="display:inline;" onsubmit="return confirm('¿Eliminar este reportaje?');">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <input type="hidden" name="tipo" value="reportaje">
                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                  <i class="fa fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>