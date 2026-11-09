<?php
declare(strict_types=1);

// Punto de arranque común: configuración de errores, sesión y carga de módulos

ini_set('display_errors', '0');
ini_set('log_errors', '1');

require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/ValidadorCliente.php';
require __DIR__ . '/ClienteRepositorio.php';

// Último recurso: cualquier error no previsto se registra y el usuario ve un mensaje genérico
set_exception_handler(function (Throwable $error): void {
    error_log('Error no controlado: ' . $error->getMessage() . ' en ' . $error->getFile() . ':' . $error->getLine());
    http_response_code(500);
    echo 'Ocurrió un error inesperado. Intenta nuevamente más tarde.';
});

iniciarSesion();
