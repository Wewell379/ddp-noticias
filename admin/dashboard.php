<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

$stats = [
  'reportajes' => $pdo->query("SELECT COUNT(*) FROM reportajes")->fetchColumn(),
  'noticias'   => $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn(),
  'mensajes'   => $pdo->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0")->fetchColumn(),
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel DDP</title>
  <link rel="stylesheet" href="../assets/css/style-starter.css">
</head>
<body style="background:#0b1220;color:#fff;padding:40px;">
  <div class="container">
    <h1 style="color:#ff2e2e;">Panel de Mando DDP</h1>
    <p>Operador: <strong><?= htmlspecialchars($_SESSION['admin']['nombre']) ?></strong> |
       <a href="logout.php" style="color:#ff8080;">Cerrar sesión</a></p>

    <div class="row mt-4">
      <div class="col-md-4">
        <div style="background:#1a2740;padding:20px;border-radius:8px;">
          <h2><?= $stats['reportajes'] ?></h2><p>Reportajes</p>
          <a href="reportajes.php" class="btn btn-primary">Gestionar</a>
        </div>
      </div>
      <div class="col-md-4">
        <div style="background:#1a2740;padding:20px;border-radius:8px;">
          <h2><?= $stats['noticias'] ?></h2><p>Noticias</p>
        </div>
      </div>
      <div class="col-md-4">
        <div style="background:#1a2740;padding:20px;border-radius:8px;">
          <h2><?= $stats['mensajes'] ?></h2><p>Mensajes sin leer</p>
          <a href="mensajes.php" class="btn btn-primary">Ver</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>