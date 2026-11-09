<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$exito = tomarFlash('exito');
$errorGeneral = null;

try {
    $clientes = (new ClienteRepositorio(obtenerConexion()))->listar();
} catch (RuntimeException $error) {
    error_log('No se pudo consultar los clientes: ' . $error->getMessage());
    $clientes = [];
    $errorGeneral = 'No se pudieron cargar los registros. Intenta más tarde.';
}

$titulo = 'Clientes registrados';
$paginaActual = 'listado';

require __DIR__ . '/../views/encabezado.php';
?>
        <section class="panel">
            <div class="panel-cabecera">
                <div>
                    <h1 class="panel-titulo">Clientes registrados</h1>
                    <p class="panel-texto"><?= count($clientes) ?> registro(s) en la base de datos.</p>
                </div>
                <a href="index.php" class="boton">Registrar cliente</a>
            </div>

            <?php if ($exito): ?>
                <p class="alerta alerta-exito" role="status"><?= e($exito) ?></p>
            <?php endif; ?>
            <?php if ($errorGeneral): ?>
                <p class="alerta alerta-error" role="alert"><?= e($errorGeneral) ?></p>
            <?php endif; ?>

            <?php if (!$clientes && !$errorGeneral): ?>
                <p class="vacio">Todavía no hay clientes registrados.</p>
            <?php elseif ($clientes): ?>
                <div class="tabla-contenedor">
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombres</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Empresa</th>
                                <th scope="col">Servicio</th>
                                <th scope="col">Mensaje</th>
                                <th scope="col">Registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <!-- Cada dato que viene de la base pasa por e() (htmlspecialchars) antes de imprimirse -->
                                <tr>
                                    <td data-titulo="#"><?= (int) $cliente['id'] ?></td>
                                    <td data-titulo="Nombres"><?= e($cliente['nombres']) ?></td>
                                    <td data-titulo="Correo"><?= e($cliente['correo']) ?></td>
                                    <td data-titulo="Teléfono"><?= e($cliente['telefono'] ?? '—') ?></td>
                                    <td data-titulo="Empresa"><?= e($cliente['empresa'] ?? '—') ?></td>
                                    <td data-titulo="Servicio"><?= e($cliente['servicio']) ?></td>
                                    <td data-titulo="Mensaje" class="celda-mensaje"><?= e($cliente['mensaje'] ?? '—') ?></td>
                                    <td data-titulo="Registrado"><?= e(date('d/m/Y H:i', strtotime($cliente['fecha_creacion']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
<?php require __DIR__ . '/../views/pie.php'; ?>
