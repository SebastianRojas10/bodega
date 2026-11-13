# Casos de prueba — Registro de ventas

Los casos cubren R1–R4 conforme a [plan_pruebas.md](./plan_pruebas.md). Para pruebas de integración, usar una base aislada `bodega_test` con los productos indicados como datos de fixture; no usar la base de uso normal. Cada caso debe ejecutarse con el estado inicial restablecido.

| ID | regla | técnica | entrada exacta | resultado esperado | nivel |
|---|---|---|---|---|---|
| CP-R1-01 | R1 · Stock | Valores límite: uno por debajo del stock | `bodega_test`: producto A, stock inicial `5`, precio `S/ 1,00`; venta de `4` unidades de A. | Venta aceptada; queda stock `1`. Se persisten venta y detalle. | Integración |
| CP-R1-02 | R1 · Stock | Valor límite: cantidad igual al stock | `bodega_test`: producto A, stock inicial `5`, precio `S/ 1,00`; venta de `5` unidades de A. | Venta aceptada; queda stock `0`. | Integración y E2E manual |
| CP-R1-03 | R1 · Stock | Valores límite: uno por encima del stock | `bodega_test`: producto A, stock inicial `5`, precio `S/ 1,00`; venta de `6` unidades de A. | Venta rechazada; no se persiste la venta ni el detalle y el stock permanece en `5`. | Integración y E2E manual |
| CP-R1-04 | R1 · Stock | Partición de equivalencia: stock cero | Producto A, stock inicial `0`, precio `S/ 1,00`; intentar vender `1` unidad. | Venta rechazada por stock insuficiente; stock permanece en `0` y no se persiste la venta. | Integración y E2E manual |
| CP-R1-05 | R1 · Stock | Suma de líneas repetidas, límite igual | `bodega_test`: producto A, stock inicial `5`, precio `S/ 1,00`; dos líneas para A: `3` y `2` unidades. | Se considera cantidad total `5`; venta aceptada y stock final `0`. | Integración |
| CP-R1-06 | R1 · Stock | Suma de líneas repetidas, límite excedido | `bodega_test`: producto A, stock inicial `4`, precio `S/ 1,00`; dos líneas para A: `3` y `2` unidades. | Cantidad total `5` excede stock; venta rechazada, sin cambios parciales y stock final `4`. | Integración |
| CP-R2-01 | R2 · Descuento | Valores límite: subtotal un céntimo debajo del umbral | Una línea: precio unitario `S/ 99,99`, cantidad `1`. | Subtotal `S/ 99,99`; descuento `S/ 0,00`; total `S/ 99,99`. | Unitaria y E2E manual |
| CP-R2-02 | R2 · Descuento | Valor límite exacto | Una línea: precio unitario `S/ 100,00`, cantidad `1`. | Subtotal `S/ 100,00`; descuento `S/ 0,00`; total `S/ 100,00`. | Unitaria y E2E manual |
| CP-R2-03 | R2 · Descuento | Valores límite: un céntimo encima del umbral | Una línea: precio unitario `S/ 100,01`, cantidad `1`. | Subtotal `S/ 100,01`; descuento `S/ 5,00` (5 %, redondeado a dos decimales); total `S/ 95,01`. | Unitaria y E2E manual |
| CP-R3-01 | R3 · Cantidad | Límite inferior válido: entero positivo mínimo | Cantidad enviada `1`; producto A con stock `2` y precio `S/ 1,00`. | Cantidad aceptada; si se confirma, venta de una unidad y stock final `1`. | Unitaria y E2E manual |
| CP-R3-02 | R3 · Cantidad | Límite: cero | Cantidad enviada `0`; producto A con stock `2`. | Cantidad rechazada; no se registra venta ni se reduce el stock. | Unitaria y E2E manual |
| CP-R3-03 | R3 · Cantidad | Partición de equivalencia: negativo | Cantidad enviada `-1`; producto A con stock `2`. | Cantidad rechazada; no se registra venta ni se reduce el stock. | Unitaria y E2E manual |
| CP-R3-04 | R3 · Cantidad | Partición de equivalencia: decimal | Cantidad enviada `1.5`; producto A con stock `2`. | Cantidad rechazada; no se registra venta ni se reduce el stock. | Unitaria y E2E manual |
| CP-R4-01 | R4 · Actualización | Estado antes/después: cantidad inferior al stock | `bodega_test`: producto A con stock inicial `2`; confirmar venta de `1` unidad. | Venta y detalle persistidos; stock final `1`, actualizado en la misma transacción. | Integración |
| CP-R4-02 | R4 · Actualización | Límite: vender todo el stock | `bodega_test`: producto A con stock inicial `2`; confirmar venta de `2` unidades. | Venta y detalle persistidos; stock final `0`, actualizado en la misma transacción. | Integración y E2E manual |
| CP-R4-03 | R4 · Actualización | Límite excedido y rollback | `bodega_test`: producto A con stock `2` y producto B con stock `0`; enviar una venta con línea A `1` y línea B `1`. | La venta completa falla; no quedan venta ni detalles parciales; stock de A sigue en `2` y stock de B en `0`. | Integración |

