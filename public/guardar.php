<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

// Controlador del formulario: valida, inserta y redirige (patrón Post/Redirect/Get)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('index.php');
}

if (!verificarCsrf($_POST['csrf'] ?? null)) {
    guardarFlash('error_general', 'La sesión expiró. Vuelve a enviar el formulario.');
    redirigir('index.php');
}

// Lo escrito vuelve al formulario si algo falla, para no obligar a repetirlo
$anteriores = [];
foreach (['nombres', 'correo', 'telefono', 'empresa', 'servicio_id', 'mensaje'] as $campo) {
    $anteriores[$campo] = is_string($_POST[$campo] ?? null) ? trim($_POST[$campo]) : '';
}

try {
    $repositorio = new ClienteRepositorio(obtenerConexion());
    $idsServicio = array_map('intval', array_column($repositorio->servicios(), 'id'));

    [$datos, $errores] = ValidadorCliente::validar($_POST, $idsServicio);

    if ($errores) {
        guardarFlash('errores', $errores);
        guardarFlash('anteriores', $datos);
        redirigir('index.php');
    }

    $repositorio->crear($datos);
    guardarFlash('exito', 'Cliente registrado correctamente.');
    redirigir('index.php');
} catch (PDOException $error) {
    guardarFlash('anteriores', $anteriores);

    // 1062: clave única duplicada (el correo ya existe)
    if ((int) ($error->errorInfo[1] ?? 0) === 1062) {
        guardarFlash('errores', ['correo' => 'Ya existe un cliente registrado con este correo.']);
    } else {
        error_log('Error al registrar cliente: ' . $error->getMessage());
        guardarFlash('error_general', 'No se pudo registrar el cliente. Intenta más tarde.');
    }
    redirigir('index.php');
} catch (RuntimeException $error) {
    error_log('Error al registrar cliente: ' . $error->getMessage());
    guardarFlash('anteriores', $anteriores);
    guardarFlash('error_general', 'El sistema no está disponible en este momento. Intenta más tarde.');
    redirigir('index.php');
}
