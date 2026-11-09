<?php
declare(strict_types=1);

/**
 * Validación del lado del servidor. Se repite aunque el navegador ya valide,
 * porque cualquiera puede enviar una petición sin pasar por el formulario.
 */
final class ValidadorCliente
{
    /**
     * @param array<string, mixed> $entrada         Datos recibidos por POST
     * @param int[]                $serviciosValidos Ids existentes en la tabla servicios
     * @return array{0: array<string, string>, 1: array<string, string>} Datos limpios y errores por campo
     */
    public static function validar(array $entrada, array $serviciosValidos): array
    {
        $campos = ['nombres', 'correo', 'telefono', 'empresa', 'servicio_id', 'mensaje'];
        $datos = [];
        foreach ($campos as $campo) {
            $valor = $entrada[$campo] ?? '';
            $datos[$campo] = is_string($valor) ? trim($valor) : '';
        }

        // Texto que no es UTF-8 válido se descarta; su error tiene prioridad sobre las demás reglas
        $erroresCodificacion = [];
        foreach ($datos as $campo => $valor) {
            if (!mb_check_encoding($valor, 'UTF-8')) {
                $erroresCodificacion[$campo] = 'El texto contiene caracteres no válidos.';
                $datos[$campo] = '';
            }
        }

        $errores = [];

        if ($datos['nombres'] === '') {
            $errores['nombres'] = 'Los nombres son obligatorios.';
        } elseif (mb_strlen($datos['nombres']) < 3 || mb_strlen($datos['nombres']) > 100) {
            $errores['nombres'] = 'Los nombres deben tener entre 3 y 100 caracteres.';
        } elseif (!preg_match("/^\p{L}[\p{L} .'-]*$/u", $datos['nombres'])) {
            $errores['nombres'] = 'Los nombres solo pueden contener letras, espacios, puntos, guiones o apóstrofos.';
        }

        if ($datos['correo'] === '') {
            $errores['correo'] = 'El correo es obligatorio.';
        } elseif (mb_strlen($datos['correo']) > 150 || filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) === false) {
            $errores['correo'] = 'Ingresa un correo electrónico válido.';
        }

        if ($datos['telefono'] !== '' && !preg_match('/^\+?[0-9 ]{7,15}$/', $datos['telefono'])) {
            $errores['telefono'] = 'El teléfono debe tener entre 7 y 15 dígitos; puede empezar con +.';
        }

        if (mb_strlen($datos['empresa']) > 120) {
            $errores['empresa'] = 'La empresa admite como máximo 120 caracteres.';
        }

        if (!ctype_digit($datos['servicio_id']) || !in_array((int) $datos['servicio_id'], $serviciosValidos, true)) {
            $errores['servicio_id'] = 'Selecciona un servicio de la lista.';
        }

        if (mb_strlen($datos['mensaje']) > 1000) {
            $errores['mensaje'] = 'El mensaje admite como máximo 1000 caracteres.';
        }

        return [$datos, $erroresCodificacion + $errores];
    }
}
