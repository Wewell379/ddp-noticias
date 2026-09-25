<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $page_title ?? 'DDP Noticias - Diálogo y Desarrollo Perú' ?></title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-starter.css">
</head>
<body>
<header id="site-header" class="fixed-top">
  <div class="container">
    <nav class="navbar navbar-expand-lg stroke">
      <a class="navbar-brand" href="index.php">
        <img src="assets/images/logo.png" alt="DDP Noticias" style="height:75px;">
      </a>
      <button class="navbar-toggler collapsed bg-gradient" type="button" data-toggle="collapse"
              data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
        <span class="navbar-toggler-icon fa icon-close fa-times"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item <?= ($current == 'inicio') ? 'active' : '' ?>">
            <a class="nav-link" href="index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php#actualidad">Actualidad</a>
          </li>
          <li class="nav-item <?= ($current == 'reportajes') ? 'active' : '' ?>">
            <a class="nav-link" href="reportajes.php">Reportajes</a>
          </li>
          <li class="nav-item <?= ($current == 'podcast') ? 'active' : '' ?>">
            <a class="nav-link" href="podcast.php">Podcast</a>
          </li>
          <li class="nav-item <?= ($current == 'boletines') ? 'active' : '' ?>">
            <a class="nav-link" href="boletines.php">Boletín NTEP</a>
          </li>
          <li class="nav-item <?= ($current == 'alianzas') ? 'active' : '' ?>">
            <a class="nav-link" href="alianzas.php">Alianzas</a>
          </li>
          <li class="nav-item <?= ($current == 'sobre') ? 'active' : '' ?>">
            <a class="nav-link" href="sobre.php">Sobre D&D</a>
          </li>
          <li class="ml-2">
            <a href="contacto.php" class="btn btn-style btn-outline-secondary">Contacto</a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</header>