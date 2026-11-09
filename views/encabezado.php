<?php
/** @var string $titulo */
/** @var string $paginaActual */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Sistema web</title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="encabezado">
        <div class="contenedor encabezado-contenido">
            <a href="index.php" class="marca">Registro de <span class="resaltado">clientes</span></a>
            <nav class="nav" aria-label="Navegación principal">
                <a href="index.php" class="nav-enlace<?= $paginaActual === 'formulario' ? ' activo' : '' ?>">Registrar</a>
                <a href="listado.php" class="nav-enlace<?= $paginaActual === 'listado' ? ' activo' : '' ?>">Clientes registrados</a>
            </nav>
        </div>
    </header>
    <main class="contenedor contenido">
