<?php
declare(strict_types=1);

/**
 * Escapa un valor antes de imprimirlo en HTML. Convierte < > " ' & en entidades,
 * así un dato con código HTML o JavaScript se muestra como texto y no se ejecuta (XSS).
 */
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,  // la cookie de sesión no es accesible desde JavaScript
            'cookie_samesite' => 'Lax',
        ]);
    }
}

// Token CSRF: confirma que el formulario enviado salió de este mismo sitio
function tokenCsrf(): string
{
    iniciarSesion();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verificarCsrf(mixed $token): bool
{
    iniciarSesion();
    return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

// Mensajes que sobreviven a una sola redirección (patrón Post/Redirect/Get)
function guardarFlash(string $clave, mixed $valor): void
{
    iniciarSesion();
    $_SESSION['flash'][$clave] = $valor;
}

function tomarFlash(string $clave, mixed $porDefecto = null): mixed
{
    iniciarSesion();
    $valor = $_SESSION['flash'][$clave] ?? $porDefecto;
    unset($_SESSION['flash'][$clave]);
    return $valor;
}

function redirigir(string $ruta): never
{
    header('Location: ' . $ruta, true, 303);
    exit;
}
