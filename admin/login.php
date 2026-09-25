<?php
session_start();
require '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['usuario'] ?? '';
    $p = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();
    if ($user && password_verify($p, $user['password_hash'])) {
        $_SESSION['admin'] = $user;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Credenciales incorrectas';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Panel DDP</title>
  <link rel="stylesheet" href="../assets/css/style-starter.css">
</head>
<body style="background:#0b1220;color:#fff;">
  <div class="container py-5" style="max-width:400px;">
    <h1 style="color:#ff2e2e;">Panel DDP</h1>
    <p style="color:#aaa;">Acceso restringido a operadores autorizados</p>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="post" style="margin-top:20px;">
      <input class="form-control mb-2" name="usuario" placeholder="Usuario" required>
      <input class="form-control mb-2" name="password" type="password" placeholder="Contraseña" required>
      <button class="btn btn-primary w-100">Entrar al puente</button>
    </form>
  </div>
</body>
</html>