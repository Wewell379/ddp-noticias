<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

$id = (int)($_GET['id'] ?? 0);
$es_nuevo = $id === 0;
$error = '';

// === Reportaje existente ===
if (!$es_nuevo) {
    $stmt = $pdo->prepare("SELECT * FROM reportajes WHERE id = ?");
    $stmt->execute([$id]);
    $r = $stmt->fetch();
    if (!$r) { header('Location: reportajes.php'); exit; }
} else {
    $r = [
        'titulo' => '', 'slug' => '', 'resumen' => '', 'contenido' => '',
        'imagen' => '', 'fecha' => date('Y-m-d'), 'categoria' => 'Actualidad',
        'autor' => $_SESSION['admin']['nombre'] ?? 'DDP Noticias',
        'estado' => 'publicado', 'destacado' => 0, 'meta_descripcion' => ''
    ];
}

// === Guardar ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(preg_replace('/[^a-z0-9]+/i', '-', $titulo));
    $slug = trim($slug, '-');

    if ($titulo === '') {
        $error = 'El título es obligatorio';
    } else {
        // Manejar subida de imagen
        $imagen = $_POST['imagen_actual'] ?? '';
        if (!empty($_FILES['imagen_file']['name'])) {
            $ext = strtolower(pathinfo($_FILES['imagen_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                $nuevo_nombre = 'subida-' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], '../assets/images/' . $nuevo_nombre)) {
                    $imagen = $nuevo_nombre;
                }
            }
        } elseif (!empty($_POST['imagen_url'])) {
            $imagen = trim($_POST['imagen_url']);
        }

        $datos = [
            $titulo, $slug,
            $_POST['resumen'] ?? '',
            $_POST['contenido'] ?? '',
            $imagen,
            $_POST['fecha'] ?? date('Y-m-d'),
            $_POST['categoria'] ?? 'Actualidad',
            $_POST['autor'] ?? 'DDP Noticias',
            $_POST['estado'] ?? 'publicado',
            isset($_POST['destacado']) ? 1 : 0,
            $_POST['meta_descripcion'] ?? ''
        ];

        try {
            if ($es_nuevo) {
                $stmt = $pdo->prepare("INSERT INTO reportajes (titulo, slug, resumen, contenido, imagen, fecha, categoria, autor, estado, destacado, meta_descripcion) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute($datos);
                header('Location: reportajes.php?msg=creado');
            } else {
                $datos[] = $id;
                $stmt = $pdo->prepare("UPDATE reportajes SET titulo=?, slug=?, resumen=?, contenido=?, imagen=?, fecha=?, categoria=?, autor=?, estado=?, destacado=?, meta_descripcion=? WHERE id=?");
                $stmt->execute($datos);
                header('Location: reportajes.php?msg=editado');
            }
            exit;
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

$page_title = $es_nuevo ? 'Nuevo reportaje' : 'Editar reportaje #' . $id;
$page_subtitle = $es_nuevo ? 'Crea un nuevo reportaje para el sitio' : 'Modifica el contenido del reportaje';
$seccion_actual = 'reportajes';
$page_actions = '<a href="reportajes.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Volver</a>';

ob_start();
?>

<?php if ($error): ?>
  <div class="alert alert-error"><i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <div class="grid-2">

    <!-- COLUMNA IZQUIERDA -->
    <div>
      <div class="card" style="margin-bottom:20px;">
        <div class="card-title"><i class="fa fa-file-text"></i> Contenido principal</div>

        <div class="form-group">
          <label>Título *</label>
          <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($r['titulo']) ?>" placeholder="Escribe un título atractivo" required>
        </div>

        <div class="form-group">
          <label>Slug (URL)</label>
          <input type="text" name="slug" id="slug" class="form-control" value="<?= htmlspecialchars($r['slug']) ?>" placeholder="se-genera-automatico">
          <div style="font-size:11px;color:#6b7280;margin-top:4px;">URL amigable. Se genera del título si lo dejas vacío.</div>
        </div>

        <div class="form-group">
          <label>Resumen corto</label>
          <textarea name="resumen" class="form-control" rows="3" placeholder="Un resumen breve que aparecerá en las cards"><?= htmlspecialchars($r['resumen']) ?></textarea>
        </div>

        <div class="form-group">
          <label>Contenido completo</label>
          <textarea name="contenido" id="contenido" class="form-control" rows="12"><?= htmlspecialchars($r['contenido']) ?></textarea>
        </div>
      </div>

      <div class="card">
        <div class="card-title"><i class="fa fa-search"></i> SEO</div>
        <div class="form-group">
          <label>Meta descripción (máx 160 caracteres)</label>
          <textarea name="meta_descripcion" class="form-control" rows="2" maxlength="300"><?= htmlspecialchars($r['meta_descripcion'] ?? '') ?></textarea>
        </div>
      </div>
    </div>

    <!-- COLUMNA DERECHA -->
    <div>
      <div class="card" style="margin-bottom:20px;">
        <div class="card-title"><i class="fa fa-cog"></i> Configuración</div>

        <div class="form-group">
          <label>Categoría</label>
          <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($r['categoria']) ?>" placeholder="Actualidad">
        </div>

        <div class="form-group">
          <label>Autor</label>
          <input type="text" name="autor" class="form-control" value="<?= htmlspecialchars($r['autor']) ?>">
        </div>

        <div class="form-group">
          <label>Fecha de publicación</label>
          <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($r['fecha']) ?>" required>
        </div>

        <div class="form-group">
          <label>Estado</label>
          <select name="estado" class="form-control">
            <option value="publicado" <?= $r['estado']==='publicado'?'selected':'' ?>>✅ Publicado</option>
            <option value="borrador" <?= $r['estado']==='borrador'?'selected':'' ?>>📝 Borrador</option>
          </select>
        </div>

        <div class="form-group">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
            <input type="checkbox" name="destacado" value="1" <?= $r['destacado']?'checked':'' ?> style="width:18px;height:18px;">
            <span>⭐ Marcar como destacado</span>
          </label>
        </div>
      </div>

      <div class="card">
        <div class="card-title"><i class="fa fa-image"></i> Imagen destacada</div>

        <?php if (!empty($r['imagen']) && file_exists("../assets/images/{$r['imagen']}")): ?>
          <div style="margin-bottom:14px;">
            <img src="../assets/images/<?= htmlspecialchars($r['imagen']) ?>" style="width:100%;border-radius:8px;max-height:180px;object-fit:cover;">
            <div style="font-size:12px;color:#6b7280;margin-top:6px;">Actual: <?= htmlspecialchars($r['imagen']) ?></div>
          </div>
        <?php endif; ?>

        <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($r['imagen']) ?>">

        <div class="form-group">
          <label>Nombre de imagen existente</label>
          <input type="text" name="imagen_url" class="form-control" value="<?= htmlspecialchars($r['imagen']) ?>" placeholder="Ej: video.jpg">
        </div>

        <div class="form-group">
          <label>O sube una nueva</label>
          <input type="file" name="imagen_file" class="form-control" accept="image/*">
        </div>
      </div>
    </div>

  </div>

  <div style="display:flex;gap:12px;margin-top:20px;">
    <button type="submit" class="btn btn-primary" style="font-size:15px;padding:14px 28px;">
      <i class="fa fa-save"></i> <?= $es_nuevo ? 'Crear reportaje' : 'Guardar cambios' ?>
    </button>
    <a href="reportajes.php" class="btn btn-outline" style="padding:14px 28px;">Cancelar</a>
  </div>
</form>

<!-- TinyMCE Editor -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
tinymce.init({
  selector: '#contenido',
  height: 500,
  menubar: false,
  plugins: 'lists link image code table media preview',
  toolbar: 'undo redo | bold italic underline | h2 h3 | bullist numlist | link image media | alignleft aligncenter alignright | code preview',
  skin: 'oxide-dark',
  content_css: 'dark',
  branding: false,
  promotion: false
});

// Auto-slug desde título
document.getElementById('titulo')?.addEventListener('input', function() {
  const slugField = document.getElementById('slug');
  if (slugField && !slugField.dataset.manual) {
    slugField.value = this.value.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  }
});
document.getElementById('slug')?.addEventListener('input', function() {
  this.dataset.manual = '1';
});
</script>

<?php
$page_content = ob_get_clean();
include 'layout.php';
?>