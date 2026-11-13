<?php

require_once __DIR__ . '/../src/ServicioVenta.php';

use PHPUnit\Framework\TestCase;

class ServicioVentaTest extends TestCase
{
    public function testCalculaImporteDebajoDelUmbralSinDescuento(): void
    {
        $items = [
            ['precio_unitario' => 99.99, 'cantidad' => 1],
        ];

        self::assertEqualsWithDelta(99.99, ServicioVenta::calcularSubtotal($items), 0.001);
        self::assertEqualsWithDelta(0.0, ServicioVenta::calcularDescuento(99.99), 0.001);
        self::assertEqualsWithDelta(99.99, ServicioVenta::calcularTotal(99.99), 0.001);
    }

    public function testCalculaImporteExactoDelUmbralSinDescuento(): void
    {
        $items = [
            ['precio_unitario' => 100.00, 'cantidad' => 1],
        ];

        self::assertEqualsWithDelta(100.00, ServicioVenta::calcularSubtotal($items), 0.001);
        self::assertEqualsWithDelta(0.0, ServicioVenta::calcularDescuento(100.00), 0.001);
        self::assertEqualsWithDelta(100.00, ServicioVenta::calcularTotal(100.00), 0.001);
    }

    public function testCalculaDescuentoSobreElUmbral(): void
    {
        $items = [
            ['precio_unitario' => 100.01, 'cantidad' => 1],
        ];

        self::assertEqualsWithDelta(100.01, ServicioVenta::calcularSubtotal($items), 0.001);
        self::assertEqualsWithDelta(5.00, ServicioVenta::calcularDescuento(100.01), 0.001);
        self::assertEqualsWithDelta(95.01, ServicioVenta::calcularTotal(100.01), 0.001);

        $subtotalConRedondeo = 100.10;
        self::assertEqualsWithDelta(
            5.01,
            ServicioVenta::calcularDescuento($subtotalConRedondeo),
            0.001
        );
        self::assertEqualsWithDelta(
            95.09,
            ServicioVenta::calcularTotal($subtotalConRedondeo),
            0.001
        );
    }

    public function testAceptaCantidadEnteraPositivaMinima(): void
    {
        self::assertSame(1, ServicioVenta::parsearCantidad('1'));
        self::assertSame(true, ServicioVenta::validarCantidad(1));
    }

    public function testRechazaCantidadCero(): void
    {
        self::assertSame(null, ServicioVenta::parsearCantidad('0'));
        self::assertSame(false, ServicioVenta::validarCantidad(0));
    }

    public function testRechazaCantidadNegativa(): void
    {
        self::assertSame(null, ServicioVenta::parsearCantidad('-1'));
        self::assertSame(false, ServicioVenta::validarCantidad(-1));
    }

    public function testRechazaCantidadDecimal(): void
    {
        self::assertSame(null, ServicioVenta::parsearCantidad('1.5'));
    }

    public function testAceptaVentaIgualAlStock(): void
    {
        self::assertSame(true, ServicioVenta::hayStock(10, 10));
    }

    public function testRechazaVentaDeUnoMasQueElStock(): void
    {
        self::assertSame(false, ServicioVenta::hayStock(11, 10));
    }

    public function testRechazaVentaDeProductoAgotado(): void
    {
        self::assertSame(false, ServicioVenta::hayStock(1, 0));
    }

    public function testSumaCantidadesDeLineasRepetidasDelMismoProducto(): void
    {
        $items = [
            ['producto_id' => 3, 'cantidad' => 3],
            ['producto_id' => 3, 'cantidad' => 2],
            ['producto_id' => 5, 'cantidad' => 1],
        ];

        self::assertSame([3 => 5, 5 => 1], ServicioVenta::agruparCantidadesPorProducto($items));
    }
}
