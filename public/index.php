<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

// Mensajes y datos que deja guardar.php antes de redirigir
$errores      = tomarFlash('errores', []);
$anteriores   = tomarFlash('anteriores', []);
$exito        = tomarFlash('exito');
$errorGeneral = tomarFlash('error_general');

try {
    $servicios = (new ClienteRepositorio(obtenerConexion()))->servicios();
} catch (RuntimeException $error) {
    error_log('No se pudo cargar el catálogo de servicios: ' . $error->getMessage());
    $servicios = [];
    $errorGeneral = 'El sistema no está disponible en este momento. Intenta más tarde.';
}

$valor = fn(string $campo): string => e($anteriores[$campo] ?? '');
$titulo = 'Registrar cliente';
$paginaActual = 'formulario';

require __DIR__ . '/../views/encabezado.php';
?>
        <section class="panel">
            <h1 class="panel-titulo">Registrar cliente</h1>
            <p class="panel-texto">Los campos marcados con * son obligatorios.</p>

            <?php if ($exito): ?>
                <p class="alerta alerta-exito" role="status"><?= e($exito) ?></p>
            <?php endif; ?>
            <?php if ($errorGeneral): ?>
                <p class="alerta alerta-error" role="alert"><?= e($errorGeneral) ?></p>
            <?php endif; ?>
            <?php if ($errores): ?>
                <p class="alerta alerta-error" role="alert">Revisa los campos marcados.</p>
            <?php endif; ?>

            <form action="guardar.php" method="post" class="formulario">
                <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">

                <div class="campo<?= isset($errores['nombres']) ? ' campo-error' : '' ?>">
                    <label for="nombres">Nombres completos *</label>
                    <input type="text" id="nombres" name="nombres" value="<?= $valor('nombres') ?>"
                           required minlength="3" maxlength="100" pattern="\p{L}[\p{L} .'\-]*"
                           title="Solo letras, espacios, puntos, guiones o apóstrofos" autocomplete="name">
                    <?php if (isset($errores['nombres'])): ?><span class="mensaje-error"><?= e($errores['nombres']) ?></span><?php endif; ?>
                </div>

                <div class="campo<?= isset($errores['correo']) ? ' campo-error' : '' ?>">
                    <label for="correo">Correo electrónico *</label>
                    <input type="email" id="correo" name="correo" value="<?= $valor('correo') ?>"
                           required maxlength="150" autocomplete="email">
                    <?php if (isset($errores['correo'])): ?><span class="mensaje-error"><?= e($errores['correo']) ?></span><?php endif; ?>
                </div>

                <div class="campo<?= isset($errores['telefono']) ? ' campo-error' : '' ?>">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?= $valor('telefono') ?>"
                           pattern="\+?[0-9 ]{7,15}" maxlength="15" title="Entre 7 y 15 dígitos; puede empezar con +" autocomplete="tel">
                    <?php if (isset($errores['telefono'])): ?><span class="mensaje-error"><?= e($errores['telefono']) ?></span><?php endif; ?>
                </div>

                <div class="campo<?= isset($errores['empresa']) ? ' campo-error' : '' ?>">
                    <label for="empresa">Empresa</label>
                    <input type="text" id="empresa" name="empresa" value="<?= $valor('empresa') ?>" maxlength="120" autocomplete="organization">
                    <?php if (isset($errores['empresa'])): ?><span class="mensaje-error"><?= e($errores['empresa']) ?></span><?php endif; ?>
                </div>

                <div class="campo<?= isset($errores['servicio_id']) ? ' campo-error' : '' ?>">
                    <label for="servicio_id">Servicio de interés *</label>
                    <select id="servicio_id" name="servicio_id" required>
                        <option value="">Selecciona un servicio</option>
                        <?php foreach ($servicios as $servicio): ?>
                            <option value="<?= (int) $servicio['id'] ?>"<?= (string) $servicio['id'] === ($anteriores['servicio_id'] ?? '') ? ' selected' : '' ?>>
                                <?= e($servicio['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['servicio_id'])): ?><span class="mensaje-error"><?= e($errores['servicio_id']) ?></span><?php endif; ?>
                </div>

                <div class="campo campo-ancho<?= isset($errores['mensaje']) ? ' campo-error' : '' ?>">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="4" maxlength="1000"><?= $valor('mensaje') ?></textarea>
                    <?php if (isset($errores['mensaje'])): ?><span class="mensaje-error"><?= e($errores['mensaje']) ?></span><?php endif; ?>
                </div>

                <div class="campo-ancho">
                    <button type="submit" class="boton">Registrar cliente</button>
                </div>
            </form>
        </section>
<?php require __DIR__ . '/../views/pie.php'; ?>
