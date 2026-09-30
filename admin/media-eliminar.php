<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: media.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT ruta FROM media WHERE id = ?");
    $stmt->execute([$id]);
    $m = $stmt->fetch();
    if ($m) {
        // Borrar archivo físico
        $path = '../' . $m['ruta'];
        if (file_exists($path)) @unlink($path);
        // Borrar de BD
        $pdo->prepare("DELETE FROM media WHERE id = ?")->execute([$id]);
    }
}

header('Location: media.php?msg=eliminado');
exit;