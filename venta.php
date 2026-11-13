<?php

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/src/RepositorioMySQL.php';

$error = null;
$productos = [];

try {
    $repositorio = new RepositorioMySQL(conectar());
    $productos = $repositorio->listarProductos();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'No se pudo cargar el catálogo. Verifique la conexión con MySQL.';
}

function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva venta | Bodega Rojas Castillo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="mb-4">
        <a href="index.php" class="link-secondary text-decoration-none">&larr; Volver al inventario</a>
        <h1 class="h2 mt-3 mb-1">Registrar venta</h1>
        <p class="text-secondary">Agregue uno o más productos y revise el total antes de confirmar.</p>
    </div>

    <?php if ($error !== null): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
    <?php elseif ($productos === []): ?>
        <div class="alert alert-info" role="status">No hay productos disponibles para registrar una venta.</div>
    <?php else: ?>
        <form action="guardar_venta.php" method="post" id="form-venta">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label for="cliente" class="form-label">Nombre del cliente</label>
                    <input type="text" class="form-control" id="cliente" name="cliente" maxlength="255" required>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Productos</h2>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="agregar-linea">
                        Agregar producto
                    </button>
                </div>
                <div class="card-body">
                    <div id="lineas"></div>
                    <div class="border-top pt-3 mt-3 ms-auto" style="max-width: 360px">
                        <div class="d-flex justify-content-between"><span>Subtotal</span><strong id="subtotal">S/ 0.00</strong></div>
                        <div class="d-flex justify-content-between"><span>Descuento</span><strong id="descuento">S/ 0.00</strong></div>
                        <div class="d-flex justify-content-between fs-5 mt-2"><span>Total</span><strong id="total">S/ 0.00</strong></div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Confirmar venta</button>
                </div>
            </div>
        </form>

        <template id="plantilla-linea">
            <div class="row g-2 align-items-end mb-3 linea">
                <div class="col-md-7">
                    <label class="form-label">Producto</label>
                    <select class="form-select producto" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $producto): ?>
                            <option
                                value="<?= (int) $producto['id'] ?>"
                                data-precio="<?= e((string) $producto['precio']) ?>"
                                data-stock="<?= (int) $producto['stock'] ?>"
                                <?= (int) $producto['stock'] === 0 ? 'disabled' : '' ?>
                            ><?= e($producto['nombre']) ?> — S/ <?= number_format((float) $producto['precio'], 2) ?> (stock: <?= (int) $producto['stock'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control cantidad" min="1" step="1" required>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="button" class="btn btn-outline-danger quitar-linea">Quitar</button>
                </div>
            </div>
        </template>
    <?php endif; ?>
</main>

<?php if ($error === null && $productos !== []): ?>
<script>
(() => {
    const lineas = document.getElementById('lineas');
    const plantilla = document.getElementById('plantilla-linea');
    let siguienteIndice = 0;

    function actualizarTotales() {
        let subtotal = 0;
        lineas.querySelectorAll('.linea').forEach((fila) => {
            const opcion = fila.querySelector('.producto').selectedOptions[0];
            const cantidad = Number(fila.querySelector('.cantidad').value) || 0;
            if (opcion && opcion.dataset.precio) {
                subtotal += Number(opcion.dataset.precio) * cantidad;
            }
        });
        subtotal = Math.round((subtotal + Number.EPSILON) * 100) / 100;
        const descuento = subtotal > 100 ? Math.round((subtotal * 0.05 + Number.EPSILON) * 100) / 100 : 0;
        document.getElementById('subtotal').textContent = `S/ ${subtotal.toFixed(2)}`;
        document.getElementById('descuento').textContent = `S/ ${descuento.toFixed(2)}`;
        document.getElementById('total').textContent = `S/ ${(subtotal - descuento).toFixed(2)}`;
    }

    function agregarLinea() {
        const fragmento = plantilla.content.cloneNode(true);
        const fila = fragmento.querySelector('.linea');
        fila.querySelector('.producto').name = `items[${siguienteIndice}][producto_id]`;
        fila.querySelector('.cantidad').name = `items[${siguienteIndice}][cantidad]`;
        siguienteIndice += 1;
        lineas.appendChild(fragmento);
        actualizarTotales();
    }

    document.getElementById('agregar-linea').addEventListener('click', agregarLinea);
    lineas.addEventListener('input', actualizarTotales);
    lineas.addEventListener('change', actualizarTotales);
    lineas.addEventListener('click', (evento) => {
        if (evento.target.classList.contains('quitar-linea')) {
            evento.target.closest('.linea').remove();
            actualizarTotales();
        }
    });

    agregarLinea();
})();
</script>
<?php endif; ?>
</body>
</html>
