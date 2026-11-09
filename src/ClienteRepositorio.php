<?php
declare(strict_types=1);

/**
 * Modelo de acceso a datos. Es la única clase que escribe SQL;
 * todas las consultas usan sentencias preparadas.
 */
final class ClienteRepositorio
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @return array<int, array{id: int, nombre: string}> */
    public function servicios(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre FROM servicios ORDER BY nombre');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Inserta un cliente ya validado y devuelve su id.
     * Los valores viajan separados del SQL, así que una comilla o un ";" en un campo
     * se guardan como texto y nunca se interpretan como parte de la consulta.
     *
     * @param array<string, string> $datos
     */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO clientes (nombres, correo, telefono, empresa, servicio_id, mensaje)
                VALUES (:nombres, :correo, :telefono, :empresa, :servicio_id, :mensaje)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nombres', $datos['nombres'], PDO::PARAM_STR);
        $stmt->bindValue(':correo', $datos['correo'], PDO::PARAM_STR);
        $this->vincularOpcional($stmt, ':telefono', $datos['telefono']);
        $this->vincularOpcional($stmt, ':empresa', $datos['empresa']);
        $stmt->bindValue(':servicio_id', (int) $datos['servicio_id'], PDO::PARAM_INT);
        $this->vincularOpcional($stmt, ':mensaje', $datos['mensaje']);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Devuelve los clientes con el nombre de su servicio, del más reciente al más antiguo.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listar(): array
    {
        $sql = 'SELECT c.id, c.nombres, c.correo, c.telefono, c.empresa, s.nombre AS servicio,
                       c.mensaje, c.fecha_creacion
                FROM clientes AS c
                INNER JOIN servicios AS s ON s.id = c.servicio_id
                ORDER BY c.fecha_creacion DESC, c.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Los campos opcionales vacíos se guardan como NULL y no como cadena vacía
    private function vincularOpcional(PDOStatement $stmt, string $parametro, string $valor): void
    {
        if ($valor === '') {
            $stmt->bindValue($parametro, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue($parametro, $valor, PDO::PARAM_STR);
        }
    }
}
