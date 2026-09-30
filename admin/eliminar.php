<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$tipo = $_POST['tipo'] ?? '';

if ($id > 0) {
    $tablas = ['reportaje' => 'reportajes', 'noticia' => 'noticias', 'podcast' => 'podcasts', 'boletin' => 'boletines'];
    if (isset($tablas[$tipo])) {
        $pdo->prepare("DELETE FROM {$tablas[$tipo]} WHERE id = ?")->execute([$id]);
    }
}

$ref = $_SERVER['HTTP_REFERER'] ?? 'reportajes.php';
$sep = str_contains($ref, '?') ? '&' : '?';
header("Location: $ref{$sep}msg=eliminado");
exit;