<?php
declare(strict_types=1);

/**
 * Devuelve una única conexión PDO para toda la petición.
 *
 * Las credenciales se leen de variables de entorno (Docker) o de config/config.php
 * (XAMPP, Laragon). Ninguna de las dos fuentes se sube al repositorio.
 */
function obtenerConexion(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $archivo = __DIR__ . '/config.php';
    $config = is_file($archivo) ? require $archivo : [];

    $host    = getenv('DB_HOST') ?: ($config['host'] ?? '127.0.0.1');
    $puerto  = getenv('DB_PORT') ?: ($config['puerto'] ?? '3306');
    $base    = getenv('DB_NAME') ?: ($config['base'] ?? 'db_sistema_web');
    $usuario = getenv('DB_USER') ?: ($config['usuario'] ?? 'root');
    $clave   = getenv('DB_PASSWORD') ?: ($config['clave'] ?? '');

    $dsn = "mysql:host={$host};port={$puerto};dbname={$base};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $usuario, $clave, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // los errores lanzan excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,                  // sentencias preparadas reales en el servidor
        ]);
    } catch (PDOException $e) {
        // El detalle queda en el log del servidor. Al usuario solo le llega un mensaje genérico,
        // porque la traza de una PDOException sin capturar puede incluir el usuario y la clave.
        error_log('Error de conexión a la base de datos: ' . $e->getMessage());
        throw new RuntimeException('No se pudo conectar con la base de datos.');
    }

    return $pdo;
}
