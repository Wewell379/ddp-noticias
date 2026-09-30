<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

// === Filtro por tipo ===
$tipo = trim($_GET['tipo'] ?? '');
$where = $tipo ? "WHERE tipo = ?" : "";
$params = $tipo ? [$tipo] : [];

$stmt = $pdo->prepare("SELECT * FROM media $where ORDER BY creado_en DESC");
$stmt->execute($params);
$archivos = $stmt->fetchAll();

// === Estadísticas ===
$stats = [
  'total' => $pdo->query("SELECT COUNT(*) FROM media")->fetchColumn(),
  'imagenes' => $pdo->query("SELECT COUNT(*) FROM media WHERE tipo='imagen'")->fetchColumn(),
  'audios' => $pdo->query("SELECT COUNT(*) FROM media WHERE tipo='audio'")->fetchColumn(),
  'videos' => $pdo->query("SELECT COUNT(*) FROM media WHERE tipo='video'")->fetchColumn(),
  'documentos' => $pdo->query("SELECT COUNT(*) FROM media WHERE tipo='documento'")->fetchColumn(),
  'tamano' => (int)$pdo->query("SELECT COALESCE(SUM(tamano),0) FROM media")->fetchColumn(),
];

function formatoTamano($bytes) {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
    if ($bytes < 1024 * 1024 * 1024) return round($bytes / (1024 * 1024), 2) . ' MB';
    return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
}

$page_title = 'Biblioteca de Medios';
$page_subtitle = "$stats[total] archivos · " . formatoTamano($stats['tamano']) . ' usados';
$seccion_actual = 'media';

ob_start();
?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i>
    <?php
      echo match($_GET['msg']) {
        'subido' => 'Archivo subido correctamente',
        'eliminado' => 'Archivo eliminado',
        default => 'Operación realizada'
      };
    ?>
  </div>
<?php endif; ?>

<!-- KPIs por tipo -->
<div class="kpi-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
  <div class="kpi-card"><i class="fa fa-file kpi-icon"></i><div class="kpi-label">Total</div><div class="kpi-value"><?= $stats['total'] ?></div></div>
  <div class="kpi-card"><i class="fa fa-image kpi-icon"></i><div class="kpi-label">Imágenes</div><div class="kpi-value"><?= $stats['imagenes'] ?></div></div>
  <div class="kpi-card"><i class="fa fa-music kpi-icon"></i><div class="kpi-label">Audios</div><div class="kpi-value"><?= $stats['audios'] ?></div></div>
  <div class="kpi-card"><i class="fa fa-video-camera kpi-icon"></i><div class="kpi-label">Videos</div><div class="kpi-value"><?= $stats['videos'] ?></div></div>
  <div class="kpi-card"><i class="fa fa-file-pdf-o kpi-icon"></i><div class="kpi-label">Documentos</div><div class="kpi-value"><?= $stats['documentos'] ?></div></div>
</div>

<!-- UPLOAD -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-title"><i class="fa fa-cloud-upload"></i> Subir nuevo archivo</div>
  <form method="post" action="media-subir.php" enctype="multipart/form-data" id="form-upload">
    <div id="drop-area" style="border:2px dashed #1f2937;border-radius:12px;padding:40px;text-align:center;cursor:pointer;transition:all 0.2s;">
      <i class="fa fa-cloud-upload" style="font-size:48px;color:#4b5563;margin-bottom:12px;"></i>
      <p style="font-size:15px;color:#e5e7eb;margin-bottom:6px;">Arrastra un archivo aquí o haz clic</p>
      <p style="font-size:12px;color:#6b7280;">Imágenes (5 MB) · Audios (50 MB) · Videos (100 MB) · PDFs (20 MB)</p>
      <input type="file" name="archivo" id="archivo" style="display:none;" required>
    </div>
    <div id="file-info" style="display:none;margin-top:12px;padding:12px;background:#0d1520;border-radius:8px;"></div>
    <button type="submit" class="btn btn-primary" style="margin-top:14px;">
      <i class="fa fa-upload"></i> Subir archivo
    </button>
  </form>
</div>

<!-- FILTROS -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <a href="media.php" class="btn <?= $tipo===''?'btn-primary':'btn-outline' ?>">
    <i class="fa fa-th"></i> Todos
  </a>
  <a href="?tipo=imagen" class="btn <?= $tipo==='imagen'?'btn-primary':'btn-outline' ?>">
    <i class="fa fa-image"></i> Imágenes
  </a>
  <a href="?tipo=audio" class="btn <?= $tipo==='audio'?'btn-primary':'btn-outline' ?>">
    <i class="fa fa-music"></i> Audios
  </a>
  <a href="?tipo=video" class="btn <?= $tipo==='video'?'btn-primary':'btn-outline' ?>">
    <i class="fa fa-video-camera"></i> Videos
  </a>
  <a href="?tipo=documento" class="btn <?= $tipo==='documento'?'btn-primary':'btn-outline' ?>">
    <i class="fa fa-file-pdf-o"></i> Documentos
  </a>
</div>

<!-- GALERÍA -->
<?php if (empty($archivos)): ?>
  <div class="card" style="text-align:center;padding:60px 20px;">
    <i class="fa fa-folder-open-o" style="font-size:60px;color:#374151;"></i>
    <h3 style="margin-top:16px;">Biblioteca vacía</h3>
    <p style="color:#9ca3af;margin-top:8px;">Sube tu primer archivo usando el formulario de arriba</p>
  </div>
<?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
    <?php foreach ($archivos as $m): ?>
      <div class="card" style="padding:0;overflow:hidden;">
        <!-- Preview -->
        <div style="height:150px;background:#0d1520;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;">
          <?php if ($m['tipo'] === 'imagen'): ?>
            <img src="../<?= htmlspecialchars($m['ruta']) ?>" style="width:100%;height:100%;object-fit:cover;">
          <?php elseif ($m['tipo'] === 'video'): ?>
            <i class="fa fa-video-camera" style="font-size:48px;color:#60a5fa;"></i>
          <?php elseif ($m['tipo'] === 'audio'): ?>
            <i class="fa fa-music" style="font-size:48px;color:#34d399;"></i>
          <?php else: ?>
            <i class="fa fa-file-pdf-o" style="font-size:48px;color:#f87171;"></i>
          <?php endif; ?>
          <span style="position:absolute;top:8px;left:8px;background:#ff2e2e;color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;">
            <?= htmlspecialchars($m['tipo']) ?>
          </span>
        </div>

        <!-- Info -->
        <div style="padding:14px;">
          <div style="font-size:13px;font-weight:600;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= htmlspecialchars($m['nombre_original']) ?>">
            <?= htmlspecialchars($m['nombre_original']) ?>
          </div>
          <div style="font-size:11px;color:#6b7280;margin-bottom:10px;">
            <?= formatoTamano($m['tamano']) ?> · <?= date('d M Y', strtotime($m['creado_en'])) ?>
          </div>

          <!-- Acciones -->
          <div style="display:flex;gap:6px;">
            <button type="button" class="btn btn-sm btn-outline" style="flex:1;" onclick="copiar('<?= htmlspecialchars($m['ruta']) ?>', this)">
              <i class="fa fa-copy"></i> URL
            </button>
            <form method="post" action="media-eliminar.php" style="display:inline;" onsubmit="return confirm('¿Eliminar este archivo? No se puede deshacer.');">
              <input type="hidden" name="id" value="<?= $m['id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<script>
// Drag & Drop
const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('archivo');
const fileInfo = document.getElementById('file-info');

dropArea.addEventListener('click', () => fileInput.click());
dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.style.borderColor = '#ff2e2e'; dropArea.style.background = '#111a28'; });
dropArea.addEventListener('dragleave', () => { dropArea.style.borderColor = '#1f2937'; dropArea.style.background = 'transparent'; });
dropArea.addEventListener('drop', e => {
  e.preventDefault();
  dropArea.style.borderColor = '#1f2937';
  dropArea.style.background = 'transparent';
  if (e.dataTransfer.files.length) {
    fileInput.files = e.dataTransfer.files;
    mostrarArchivo();
  }
});
fileInput.addEventListener('change', mostrarArchivo);

function mostrarArchivo() {
  if (fileInput.files.length) {
    const f = fileInput.files[0];
    const tamaño = f.size < 1048576 ? (f.size/1024).toFixed(1)+' KB' : (f.size/1048576).toFixed(2)+' MB';
    fileInfo.style.display = 'block';
    fileInfo.innerHTML = '<strong>' + f.name + '</strong> · ' + tamaño;
  }
}

function copiar(ruta, btn) {
  const url = window.location.origin + '/ddp/' + ruta;
  navigator.clipboard.writeText(url);
  const original = btn.innerHTML;
  btn.innerHTML = '<i class="fa fa-check"></i> ¡Copiado!';
  setTimeout(() => btn.innerHTML = original, 2000);
}
</script>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>