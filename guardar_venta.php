<?php

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/src/ServicioVenta.php';
require_once __DIR__ . '/src/RepositorioMySQL.php';

function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$mensaje = null;
$esExito = false;
$detalle = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $mensaje = 'Esta página solo acepta ventas enviadas desde el formulario.';
} else {
    try {
        $cliente = trim((string) ($_POST['cliente'] ?? ''));
        if ($cliente === '' || mb_strlen($cliente) > 255) {
            throw new InvalidArgumentException('Ingrese un nombre de cliente de hasta 255 caracteres.');
        }

        $itemsPost = $_POST['items'] ?? null;
        if (!is_array($itemsPost) || $itemsPost === []) {
            throw new InvalidArgumentException('Agregue al menos un producto a la venta.');
        }

        $repositorio = new RepositorioMySQL(conectar());
        $items = [];

        foreach ($itemsPost as $linea) {
            if (!is_array($linea)) {
                throw new InvalidArgumentException('Una de las líneas de la venta no es válida.');
            }

            $productoRaw = $linea['producto_id'] ?? '';
            $cantidadRaw = $linea['cantidad'] ?? '';
            $productoId = is_scalar($productoRaw)
                ? filter_var((string) $productoRaw, FILTER_VALIDATE_INT)
                : false;
            $cantidad = ServicioVenta::parsearCantidad($cantidadRaw);

            if ($productoId === false || $productoId < 1) {
                throw new InvalidArgumentException('Seleccione un producto válido en cada línea.');
            }
            if ($cantidad === null) {
                throw new InvalidArgumentException('La cantidad debe ser un número entero mayor a cero.');
            }

            $producto = $repositorio->obtenerProducto($productoId);
            if ($producto === null) {
                throw new InvalidArgumentException('Uno de los productos seleccionados ya no existe.');
            }

            $items[] = [
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => (float) $producto['precio'],
            ];
        }

        foreach (ServicioVenta::agruparCantidadesPorProducto($items) as $productoId => $cantidadTotal) {
            $producto = $repositorio->obtenerProducto((int) $productoId);
            if ($producto === null) {
                throw new InvalidArgumentException('Uno de los productos seleccionados ya no existe.');
            }
            if (!ServicioVenta::hayStock($cantidadTotal, (int) $producto['stock'])) {
                throw new InvalidArgumentException(
                    'Stock insuficiente para ' . $producto['nombre']
                    . ' (disponible: ' . $producto['stock'] . ').'
                );
            }
        }

        $subtotal = ServicioVenta::calcularSubtotal($items);
        $descuento = ServicioVenta::calcularDescuento($subtotal);
        $total = ServicioVenta::calcularTotal($subtotal);
        $ventaId = $repositorio->registrarVenta($cliente, $items);
        $esExito = true;
        $mensaje = 'Venta registrada correctamente.';
        $detalle = sprintf(
            'Venta #%d para %s. Subtotal: S/ %s · Descuento: S/ %s · Total: S/ %s',
            $ventaId,
            $cliente,
            number_format($subtotal, 2),
            number_format($descuento, 2),
            number_format($total, 2)
        );
    } catch (InvalidArgumentException $e) {
        $mensaje = $e->getMessage();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $mensaje = 'No se pudo acceder a la base de datos. Verifique la conexión con MySQL.';
    } catch (RuntimeException $e) {
        error_log($e->getMessage());
        $mensaje = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado de venta | Bodega Rojas Castillo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 640px">
        <div class="card-body p-4">
            <div class="alert <?= $esExito ? 'alert-success' : 'alert-danger' ?>" role="alert">
                <h1 class="h4"><?= e($mensaje ?? 'No se pudo procesar la venta.') ?></h1>
                <?php if ($detalle !== ''): ?>
                    <p class="mb-0"><?= e($detalle) ?></p>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-primary" href="venta.php">Nueva venta</a>
                <a class="btn btn-outline-secondary" href="index.php">Ver inventario</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
