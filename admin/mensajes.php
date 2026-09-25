    <?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

$mensajes = $pdo->query("SELECT * FROM mensajes_contacto ORDER BY creado_en DESC")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mensajes - Panel DDP</title>
  <link rel="stylesheet" href="../assets/css/style-starter.css">
</head>
<body style="background:#0b1220;color:#fff;padding:40px;">
  <div class="container">
    <h1 style="color:#ff2e2e;">Mensajes Recibidos</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Volver</a>

    <?php if (empty($mensajes)): ?>
      <p>No hay mensajes aún.</p>
    <?php else: ?>
      <?php foreach ($mensajes as $m): ?>
        <div style="background:#1a2740;padding:20px;border-radius:8px;margin-bottom:15px;">
          <h4><?= htmlspecialchars($m['asunto'] ?: '(Sin asunto)') ?></h4>
          <p><strong><?= htmlspecialchars($m['nombre']) ?></strong> — <?= htmlspecialchars($m['email']) ?></p>
          <p><?= nl2br(htmlspecialchars($m['mensaje'])) ?></p>
          <small style="color:#888;"><?= $m['creado_en'] ?></small>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>