# Guion E2E manual — Registrar venta

**URL inicial:** `http://localhost/bodega/`  
**Preparación:** iniciar Apache y MySQL en XAMPP. Antes de cada escenario, abrir el inventario y anotar el stock actual de los productos utilizados. Los datos actuales de referencia se consultaron en `bodega` el 2026-11-06; si vuelven a cambiar, ajustar las cantidades conservando las condiciones indicadas. Ejecutar los escenarios en orden.

**Ejecución registrada:** los tres escenarios se ejecutaron el 2026-11-06 en el navegador integrado; no participó una segunda persona como navigator. La venta válida ejecutada agotó Arroz Costeño y redujo Aceite Primor. Por eso se actualizaron los datos de entrada de este guion para el stock actual; el resultado histórico de la ejecución anterior queda separado al final del escenario.

## Escenario 1 — Venta válida de dos líneas y descuento

Datos actuales: Atún Florida a S/ 6,50 con stock 18 y Aceite Primor a S/ 10,50 con stock 8. El arroz ahora está agotado.

| Paso | Acción exacta en pantalla | Dato a ingresar | Resultado esperado visible |
|---|---|---|---|
| 1 | En `http://localhost/bodega/`, comprobar el inventario antes de la venta. | Ninguno. | Atún Florida muestra stock `18` y Aceite Primor stock `8`. Anotar estos valores para comparar. |
| 2 | Pulsar **Registrar venta**. En **Nombre del cliente**, escribir el nombre de prueba. En la primera línea, elegir **Atún Florida**; pulsar **Agregar producto** y en la nueva línea elegir **Aceite Primor**. | Cliente: `Cliente E2E`; Atún Florida: cantidad `15`; Aceite Primor: cantidad `1`. | Antes de confirmar, la página muestra subtotal `S/ 108.00`, descuento `S/ 5.40` y total `S/ 102.60`. |
| 3 | Pulsar **Confirmar venta**. | Ninguno adicional. | Se muestra **Venta registrada correctamente.** y el detalle indica subtotal `S/ 108.00`, descuento `S/ 5.40` y total `S/ 102.60`. |
| 4 | Pulsar **Ver inventario**. | Ninguno. | En `index.php`, Atún Florida muestra stock `3` (antes `18`) y Aceite Primor muestra `7` (antes `8`). |

**Ejecución 13/11: PASÓ.** Antes de la venta, `index.php` mostraba Atún Florida con stock `18` y Aceite Primor con stock `8`. Se registró la venta de 15 Atún Florida y 1 Aceite Primor (subtotal esperado `S/ 108.00`, descuento `S/ 5.40`, total `S/ 102.60`) y, al volver a `index.php`, Atún Florida quedó en `3` y Aceite Primor en `7`: el stock se redujo exactamente en las cantidades vendidas. La ejecución anterior (venta #5 de 20 Arroz Costeño y 2 Aceite Primor: subtotal `S/ 111.00`, descuento `S/ 5.55`, total `S/ 105.45`) también pasó y dejó el arroz agotado.

## Escenario 2 — Intento de vender más que el stock

Datos actuales de referencia: Gaseosa Inca Kola conserva stock `29`. Este escenario es independiente del escenario 1; al ejecutarlo en pareja, anotar el stock inmediatamente antes del intento.

| Paso | Acción exacta en pantalla | Dato a ingresar | Resultado esperado visible |
|---|---|---|---|
| 1 | En el inventario, anotar el stock de Gaseosa Inca Kola. Pulsar **Registrar venta**. En el formulario, completar el nombre del cliente y seleccionar **Gaseosa Inca Kola**. | Cliente: `Cliente E2E`; cantidad: `30` (stock anotado `29` más uno). | La línea muestra la Gaseosa Inca Kola seleccionada. |
| 2 | Pulsar **Confirmar venta**. | Ninguno adicional. | Se muestra un error: **Stock insuficiente para Gaseosa Inca Kola (disponible: 29).** No aparece mensaje de venta registrada. |
| 3 | Pulsar **Ver inventario**. | Ninguno. | En `index.php`, Gaseosa Inca Kola conserva exactamente el stock anotado antes del intento (`29` en los datos de referencia). |

**Ejecución anterior: PASÓ.** La aplicación mostró el error esperado y `index.php` confirmó que Gaseosa Inca Kola conservó stock `29`. El stock actual sigue siendo `29`.

## Escenario 3 — Rechazo de cantidad cero

| Paso | Acción exacta en pantalla | Dato a ingresar | Resultado esperado visible |
|---|---|---|---|
| 1 | En `index.php`, anotar el stock de Azúcar rubia. Pulsar **Registrar venta**. En el formulario, completar el nombre del cliente y seleccionar **Azúcar rubia**. | Cliente: `Cliente E2E`; cantidad: `0`. | La cantidad cero queda en el campo para intentar confirmar. |
| 2 | Pulsar **Confirmar venta**. | Ninguno adicional. | El navegador bloquea el envío por la restricción `min="1"` del campo de cantidad y señala que el valor debe ser al menos `1`. Si el envío alcanza el servidor (por ejemplo, con la validación del navegador desactivada), la aplicación muestra **La cantidad debe ser un número entero mayor a cero.** |
| 3 | Volver a `http://localhost/bodega/` o pulsar **Ver inventario** desde la respuesta del servidor. | Ninguno. | `index.php` muestra el mismo stock de Azúcar rubia que se anotó antes del intento; no se registró la venta ni se redujo el inventario. |

**Ejecución anterior: PASÓ.** El navegador bloqueó el envío con el mensaje «El valor debe ser superior o igual a 1». Al volver a `index.php`, Azúcar rubia conservó stock `25`. El stock actual sigue siendo `25`.
