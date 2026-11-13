<?php

require_once __DIR__ . '/../../src/RepositorioMySQL.php';

use PHPUnit\Framework\TestCase;

class RegistrarVentaTest extends TestCase
{
    private PDO $pdo;
    private RepositorioMySQL $repositorio;
    private int $productoConStockId;
    private int $productoSinStockId;

    protected function setUp(): void
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config/env_test.php';
        if ($config['base'] !== 'bodega_test') {
            throw new RuntimeException('Las pruebas de integración solo pueden usar bodega_test.');
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['base']
        );
        $this->pdo = new PDO($dsn, $config['usuario'], $config['clave'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $this->repositorio = new RepositorioMySQL($this->pdo);

        $this->pdo->exec('DELETE FROM detalle_venta');
        $this->pdo->exec('DELETE FROM venta');
        $this->pdo->exec('DELETE FROM producto');

        $insertarProducto = $this->pdo->prepare(
            'INSERT INTO producto (nombre, precio, stock) VALUES (:nombre, :precio, :stock)'
        );
        $insertarProducto->execute([
            'nombre' => 'Producto con stock',
            'precio' => 4.50,
            'stock' => 10,
        ]);
        $this->productoConStockId = (int) $this->pdo->lastInsertId();

        $insertarProducto->execute([
            'nombre' => 'Producto sin stock',
            'precio' => 4.20,
            'stock' => 0,
        ]);
        $this->productoSinStockId = (int) $this->pdo->lastInsertId();
    }

    protected function tearDown(): void
    {
        if (isset($this->pdo)) {
            $this->pdo->exec('DELETE FROM detalle_venta');
            $this->pdo->exec('DELETE FROM venta');
            $this->pdo->exec('DELETE FROM producto');
        }

        parent::tearDown();
    }

    public function testRegistraVentaYActualizaStock(): void
    {
        $ventaId = $this->repositorio->registrarVenta('Cliente de prueba', [
            [
                'producto_id' => $this->productoConStockId,
                'cantidad' => 3,
                'precio_unitario' => 4.50,
            ],
        ]);

        self::assertGreaterThan(0, $ventaId);
        self::assertSame(
            7,
            (int) $this->pdo->query(
                'SELECT stock FROM producto WHERE id = ' . $this->productoConStockId
            )->fetchColumn()
        );
        self::assertSame(
            1,
            (int) $this->pdo->query(
                'SELECT COUNT(*) FROM venta WHERE id = ' . $ventaId
            )->fetchColumn()
        );
        self::assertSame(
            1,
            (int) $this->pdo->query(
                'SELECT COUNT(*) FROM detalle_venta WHERE venta_id = ' . $ventaId
            )->fetchColumn()
        );
    }

    public function testRevierteVentaCuandoProductoNoTieneStock(): void
    {
        $excepcion = null;

        try {
            $this->repositorio->registrarVenta('Cliente de prueba', [
                [
                    'producto_id' => $this->productoSinStockId,
                    'cantidad' => 1,
                    'precio_unitario' => 4.20,
                ],
            ]);
        } catch (RuntimeException $e) {
            $excepcion = $e;
        }

        self::assertInstanceOf(RuntimeException::class, $excepcion);
        self::assertSame(
            0,
            (int) $this->pdo->query('SELECT COUNT(*) FROM venta')->fetchColumn()
        );
        self::assertSame(
            0,
            (int) $this->pdo->query('SELECT COUNT(*) FROM detalle_venta')->fetchColumn()
        );
        self::assertSame(
            0,
            (int) $this->pdo->query(
                'SELECT stock FROM producto WHERE id = ' . $this->productoSinStockId
            )->fetchColumn()
        );
    }
}
