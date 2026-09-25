<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

$msg = '';

// CREAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $_POST['titulo']));
    $stmt = $pdo->prepare("INSERT INTO reportajes (titulo, slug, resumen, imagen, fecha) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['titulo'], $slug, $_POST['resumen'], $_POST['imagen'], $_POST['fecha']]);
    $msg = 'Reportaje creado';
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $pdo->prepare("DELETE FROM reportajes WHERE id = ?")->execute([$_GET['eliminar']]);
    header('Location: reportajes.php'); exit;
}

$reportajes = $pdo->query("SELECT * FROM reportajes ORDER BY fecha DESC")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reportajes - Panel DDP</title>
  <link rel="stylesheet" href="../assets/css/style-starter.css">
</head>
<body style="background:#0b1220;color:#fff;padding:40px;">
  <div class="container">
    <h1 style="color:#ff2e2e;">Gestión de Reportajes</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Volver</a>
    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <div style="background:#1a2740;padding:20px;border-radius:8px;margin-bottom:30px;">
      <h3>Nuevo reportaje</h3>
      <form method="post">
        <input class="form-control mb-2" name="titulo" placeholder="Título" required>
        <textarea class="form-control mb-2" name="resumen" placeholder="Resumen" rows="3"></textarea>
        <input class="form-control mb-2" name="imagen" placeholder="Imagen (ej: video.jpg)" required>
        <input class="form-control mb-2" type="date" name="fecha" required>
        <button class="btn btn-primary" name="crear">Guardar</button>
      </form>
    </div>

    <table class="table" style="color:#fff;">
      <thead><tr><th>ID</th><th>Título</th><th>Fecha</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($reportajes as $r): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['titulo']) ?></td>
            <td><?= $r['fecha'] ?></td>
            <td>
              <a href="?eliminar=<?= $r['id'] ?>" onclick="return confirm('¿Eliminar?')" class="btn btn-sm btn-danger">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>