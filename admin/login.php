<?php
session_start();
require '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['usuario'] ?? '');
    $p = $_POST['password'] ?? '';

    if ($u === '' || $p === '') {
        $error = 'Completa todos los campos';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$u]);
        $user = $stmt->fetch();

        if ($user && password_verify($p, $user['password_hash'])) {
            $_SESSION['admin'] = $user;
            // Actualizar última conexión
            $pdo->prepare("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = ?")->execute([$user['id']]);
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Credenciales incorrectas';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - DDP Command Center</title>
  <link rel="stylesheet" href="../assets/css/style-starter.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <img src="../assets/images/logo.png" alt="DDP">
    </div>
    <div class="login-title">DDP Command Center</div>
    <div class="login-subtitle">Acceso restringido a operadores</div>

    <?php if ($error): ?>
      <div class="alert alert-error"><i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label><i class="fa fa-user"></i> Usuario</label>
        <input type="text" name="usuario" class="form-control" placeholder="Ingresa tu usuario" autofocus required>
      </div>

      <div class="form-group">
        <label><i class="fa fa-lock"></i> Contraseña</label>
        <input type="password" name="password" class="form-control" placeholder="Ingresa tu contraseña" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
        <i class="fa fa-sign-in"></i> Entrar al Hyperion
      </button>
    </form>

    <p style="text-align:center;margin-top:24px;font-size:12px;color:#6b7280;">
      <i class="fa fa-shield"></i> Sistema protegido
    </p>
  </div>
</div>
</body>
</html>