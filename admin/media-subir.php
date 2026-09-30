<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['archivo']['name'])) {
    header('Location: media.php');
    exit;
}

$archivo = $_FILES['archivo'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

// Tipos permitidos por categoría
$tipos = [
    'imagen'    => ['jpg','jpeg','png','webp','gif'],
    'audio'     => ['mp3','wav','m4a','ogg'],
    'video'     => ['mp4','webm'],
    'documento' => ['pdf'],
];

// Detectar tipo
$tipo = null;
foreach ($tipos as $t => $exts) {
    if (in_array($ext, $exts)) { $tipo = $t; break; }
}

if (!$tipo) {
    header('Location: media.php?msg=error');
    exit;
}

// Límites por tipo (en MB)
$limites = ['imagen' => 5, 'audio' => 50, 'video' => 100, 'documento' => 20];
$max = $limites[$tipo] * 1024 * 1024;
if ($archivo['size'] > $max) {
    header('Location: media.php?msg=error');
    exit;
}

// Estructura por año/mes
$año = date('Y');
$mes = date('m');
$carpeta = "../uploads/{$tipo}s/{$año}/{$mes}/";
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

// Nombre único
$nombre_unico = time() . '-' . preg_replace('/[^a-z0-9]+/i', '-', pathinfo($archivo['name'], PATHINFO_FILENAME)) . '.' . $ext;
$destino = $carpeta . $nombre_unico;
$ruta_relativa = "uploads/{$tipo}s/{$año}/{$mes}/{$nombre_unico}";

if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
    header('Location: media.php?msg=error');
    exit;
}

// Detectar MIME
$mime = mime_content_type($destino) ?: 'application/octet-stream';

// Dimensiones (solo imágenes)
$ancho = null; $alto = null;
if ($tipo === 'imagen' && function_exists('getimagesize')) {
    $dim = @getimagesize($destino);
    if ($dim) { $ancho = $dim[0]; $alto = $dim[1]; }
}

// Insertar en BD
$stmt = $pdo->prepare("INSERT INTO media (nombre_original, nombre_archivo, tipo, mime, ruta, tamano, ancho, alto, subido_por) VALUES (?,?,?,?,?,?,?,?,?)");
$stmt->execute([
    $archivo['name'],
    $nombre_unico,
    $tipo,
    $mime,
    $ruta_relativa,
    $archivo['size'],
    $ancho,
    $alto,
    $_SESSION['admin']['id']
]);

header('Location: media.php?msg=subido');
exit;