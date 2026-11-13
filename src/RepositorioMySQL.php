<?php

require_once __DIR__ . '/ServicioVenta.php';

class RepositorioMySQL
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listarProductos(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nombre, precio, stock FROM producto ORDER BY nombre'
        );

        return $stmt->fetchAll();
    }

    public function obtenerProducto(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, precio, stock FROM producto WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $producto = $stmt->fetch();

        return $producto === false ? null : $producto;
    }

    public function registrarVenta(string $cliente, array $items): int
    {
        $subtotal = ServicioVenta::calcularSubtotal($items);
        $descuento = ServicioVenta::calcularDescuento($subtotal);
        $total = ServicioVenta::calcularTotal($subtotal);

        $this->pdo->beginTransaction();

        try {
            $stmtVenta = $this->pdo->prepare(
                'INSERT INTO venta (cliente, subtotal, descuento, total)
                 VALUES (:cliente, :subtotal, :descuento, :total)'
            );
            $stmtVenta->execute([
                'cliente' => $cliente,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
            ]);
            $ventaId = (int) $this->pdo->lastInsertId();

            $stmtDetalle = $this->pdo->prepare(
                'INSERT INTO detalle_venta
                    (venta_id, producto_id, cantidad, precio_unitario)
                 VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario)'
            );
            $stmtStock = $this->pdo->prepare(
                'UPDATE producto
                 SET stock = stock - :cantidad
                 WHERE id = :producto_id AND stock >= :stock_minimo'
            );

            foreach ($items as $item) {
                $stmtStock->execute([
                    'cantidad' => $item['cantidad'],
                    'producto_id' => $item['producto_id'],
                    'stock_minimo' => $item['cantidad'],
                ]);
                if ($stmtStock->rowCount() !== 1) {
                    throw new RuntimeException(
                        'El stock cambió durante la venta. Verifique las cantidades e inténtelo nuevamente.'
                    );
                }

                $stmtDetalle->execute([
                    'venta_id' => $ventaId,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            $this->pdo->commit();

            return $ventaId;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($e instanceof RuntimeException) {
                throw $e;
            }

            throw new RuntimeException(
                'No se pudo guardar la venta. No se modificó el inventario.',
                0,
                $e
            );
        }
    }
}
