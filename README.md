# DDP Noticias - Diálogo y Desarrollo Perú

Sitio web de noticias sobre diálogo y desarrollo territorial en el Perú.

## 🛠 Tecnologías
- PHP 8
- MySQL / MariaDB
- HTML5 + CSS3
- Bootstrap 4
- JavaScript (jQuery, Owl Carousel)

## 📁 Estructura del proyecto

    ddp/
    ├── config/
    │   └── db.php              # Conexión a MySQL
    ├── includes/
    │   ├── header.php          # Cabecera compartida
    │   └── footer.php          # Pie compartido
    ├── admin/                  # Panel de administración
    │   ├── login.php
    │   ├── dashboard.php
    │   ├── reportajes.php
    │   ├── mensajes.php
    │   └── logout.php
    ├── assets/                 # CSS, JS, imágenes
    ├── boletines/              # PDFs de boletines
    ├── index.php               # Portada
    ├── reportajes.php          # Listado
    ├── reportaje.php           # Detalle individual
    ├── podcast.php
    ├── boletines.php
    ├── alianzas.php
    ├── sobre.php
    └── contacto.php

## 🗄 Base de datos

Nombre: `ddp_noticias`

Tablas principales:
- `reportajes` - Contenido de reportajes
- `noticias` - Enlaces a noticias externas
- `boletines` - PDFs del boletín NTEP
- `podcasts` - Episodios de podcast
- `especiales` - Contenido especial
- `alianzas` - Instituciones aliadas
- `mensajes_contacto` - Formulario de contacto
- `usuarios` - Administradores del panel

## 🚀 Instalación local

1. Clonar el repositorio en `htdocs/`
2. Crear la base de datos `ddp_noticias` e importar el SQL
3. Editar `config/db.php` con tus credenciales
4. Acceder a `http://localhost/ddp/`

## 👤 Acceso al panel
- URL: `/admin/login.php`
- Usuario por defecto: `admin`

## 📝 Autor
Proyecto desarrollado para el curso de **Plataforma de Aplicaciones**.