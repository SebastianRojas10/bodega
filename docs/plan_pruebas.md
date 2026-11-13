# Plan de pruebas — Registro de ventas

## 1. Objetivo y alcance

Verificar que el flujo de registro de ventas cumple las reglas R1–R4 de `requirements.md` y que los cálculos mostrados y persistidos coinciden. El plan se limita a estas reglas; no agrega reglas de negocio.

**Módulos incluidos**

- `src/ServicioVenta.php`: validación de cantidades y stock, subtotal, descuento y total.
- `src/RepositorioMySQL.php`: persistencia de venta y detalle, reducción de stock y atomicidad de la transacción.
- `venta.php` y `guardar_venta.php`: flujo manual de selección, cálculo mostrado y confirmación de venta.

**Fuera del alcance de estas pruebas**

- Registro y mantenimiento de productos (Requirement 1), salvo la preparación de datos de prueba.
- Inventario completo y otras páginas ajenas al flujo de venta.
- Autenticación, reportes y facturación electrónica, que `requirements.md` excluye del sistema.
- Rendimiento, carga, seguridad y compatibilidad entre navegadores.

## 2. Cobertura por regla de negocio

| Regla | Nivel de prueba | Técnica | Datos de prueba | Criterio de aceptación |
|---|---|---|---|---|
| **R1 · Stock**: no vender más unidades que las disponibles; vender exactamente el stock es válido y lo deja en cero. | Unitaria (`ServicioVenta`), integración (`RepositorioMySQL`) y E2E manual (`venta.php`). | Valores límite y partición de equivalencia: cantidad menor, igual y mayor que el stock; verificar también líneas repetidas del mismo producto. | Stock 5 con venta de 4, 5 y 6 unidades; dos líneas del mismo producto por 3 y 2 unidades con stock 5; repetir con stock 4. | Se aceptan las cantidades 4 y 5, y se rechaza 6. La venta exacta deja stock 0. Las líneas repetidas se contabilizan en conjunto y, si exceden el stock, no se registra parcialmente la venta. |
| **R2 · Descuento**: aplicar 5 % solo si el subtotal supera S/ 100,00; el total es subtotal menos descuento. | Unitaria (`ServicioVenta`) y E2E manual (`venta.php`). | Valores límite alrededor del umbral y tabla de decisión: subtotal `<`, `=`, `>` S/ 100,00. | Subtotales S/ 99,99, S/ 100,00 y S/ 100,01, construidos con una o varias líneas. | Para S/ 99,99 y S/ 100,00, descuento S/ 0,00. Para S/ 100,01, descuento S/ 5,00 (redondeado a dos decimales) y total S/ 95,01. Los importes mostrados y registrados coinciden. |
| **R3 · Cantidad**: cada cantidad debe ser un entero mayor que cero; se rechazan cero, negativos y decimales. | Unitaria (`ServicioVenta`) y E2E manual (`venta.php`). | Partición de equivalencia y valores límite: entero positivo válido y entradas inválidas de cada clase indicada por el requisito. | `1` (válida); `0`, `-1` y `1.5` (inválidas). Probar tanto la validación del servicio como el envío desde el formulario. | Se acepta la cantidad entera positiva. Se rechazan cero, negativos y decimales; no se registra una venta ni se reduce el stock para entradas rechazadas. |
| **R4 · Actualización**: al confirmar, reducir el stock por lo vendido dentro de la misma transacción; ante un fallo, revertir toda la venta. | Integración (`RepositorioMySQL` con `bodega_test`) y E2E manual (`venta.php`). | Prueba de estado antes/después y tabla de decisión: transacción completa frente a fallo en una línea. | Caso exitoso: producto A con stock 8, vender 3. Caso de rollback: una primera línea válida y una línea posterior cuya cantidad supera el stock disponible; preparar y guardar los conteos de venta, detalle y stock iniciales. | En éxito, venta y detalles quedan persistidos y el stock de cada producto disminuye en la cantidad vendida. Ante fallo, no queda venta ni detalle parcial y todos los stocks vuelven a sus valores iniciales. |

## 3. Niveles que se ejecutarán hoy

1. **Unitarias — PHPUnit sobre `ServicioVenta`**  
   Ejecutar los casos de R1, R2 y R3 sin conexión a la base de datos. Afirmar cantidades aceptadas/rechazadas, agrupación por producto, disponibilidad, subtotal, descuento y total, incluidos los límites especificados en la tabla.

2. **Integración — `RepositorioMySQL` con `bodega_test`**  
   Usar una base de pruebas aislada, con el esquema requerido y datos preparados por cada caso. Comprobar persistencia de venta y detalles, reducción de stock y rollback completo cuando falla una actualización. No apuntar estas pruebas a la base de datos de uso normal. Limpiar los datos de prueba al terminar.

3. **E2E manual — `venta.php`**  
   En el navegador, elegir productos y cantidades, observar subtotal/descuento/total, confirmar una venta válida y comprobar el resultado y stock actualizado. Repetir con cantidades cero, negativas, decimales, sobreventa y cantidad igual al stock, según aplique. Confirmar que los cálculos visibles respetan el umbral de R2.

## 4. Criterio de salida

La versión queda lista para desplegar cuando:

- Todas las pruebas unitarias, de integración y los casos E2E manuales de este plan pasan.
- Los límites de R1–R3 producen exactamente los resultados esperados.
- Para R4, una venta exitosa persiste sus datos y stock actualizado; un fallo no deja cambios parciales.
- No quedan fallos conocidos en los casos incluidos ni se usó la base de datos normal para ejecutar integración.
