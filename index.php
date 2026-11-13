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
    $error = 'No se pudo conectar con la base de datos. Verifique la configuración de MySQL.';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario | Bodega Rojas Castillo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Bodega Rojas Castillo</h1>
            <p class="text-secondary mb-0">Productos y stock disponible</p>
        </div>
        <a class="btn btn-primary" href="venta.php">Registrar venta</a>
    </div>

    <?php if ($error !== null): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php elseif ($productos === []): ?>
        <div class="alert alert-info" role="status">No hay productos registrados.</div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Stock</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>S/ <?= number_format((float) $producto['precio'], 2) ?></td>
                            <td>
                                <?php if ((int) $producto['stock'] === 0): ?>
                                    <span class="badge text-bg-danger">Agotado</span>
                                <?php else: ?>
                                    <?= (int) $producto['stock'] ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
