<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/db.php';
$page_title = 'Contacto - DDP Noticias';
$current = 'contacto';

$ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nombre']  ?? '',
        $_POST['email']   ?? '',
        $_POST['asunto']  ?? '',
        $_POST['mensaje'] ?? ''
    ]);
    $ok = true;
}

require 'includes/header.php';
?>

<section class="breadcrumb-area py-sm-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-contents">
          <h2 class="title-big">Contacto</h2>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container py-lg-5 py-4">
  <div class="row">
    <div class="col-lg-8 mx-auto">
      <?php if ($ok): ?>
        <div class="alert alert-success">¡Mensaje enviado correctamente! Te responderemos pronto.</div>
      <?php endif; ?>

      <form method="post">
        <div class="form-group mb-3">
          <label>Nombre completo</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label>Asunto</label>
          <input type="text" name="asunto" class="form-control">
        </div>
        <div class="form-group mb-3">
          <label>Mensaje</label>
          <textarea name="mensaje" class="form-control" rows="6" required></textarea>
        </div>
        <button type="submit" class="btn btn-style btn-primary">Enviar mensaje</button>
      </form>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>