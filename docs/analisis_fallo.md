# Análisis del fallo de descuento

## 1. Esperado y obtenido

En la salida de PHPUnit, `testCalculaImporteExactoDelUmbralSinDescuento` esperaba un descuento de `0.0` para un subtotal de `100.00`, pero obtuvo `5.0`. El fallo ocurre en la aserción del descuento.

## 2. Hipótesis, ordenadas por probabilidad

1. **Defecto en el código — muy probable.** Si `calcularDescuento()` usa `>= 100.00`, incluye el subtotal exacto en la condición y devuelve `round(100.00 * 0.05, 2)`, es decir, `5.00`. El requisito establece que solo se descuenta cuando el subtotal es mayor que S/ 100,00.
2. **Defecto en el test — poco probable.** El esperado `0.0` coincide con el requisito para exactamente S/ 100,00. Sería incorrecto solo si el requisito pidiera descuento también en el límite, lo cual no es el caso.
3. **Defecto en el dato — muy poco probable.** `100.00` es precisamente el valor límite que debe verificarse. No hay conversión ni cálculo intermedio en la llamada directa a `calcularDescuento(100.00)`.

## 3. Verificación de cada hipótesis en un minuto

1. **Código:** abrir `src/ServicioVenta.php` y revisar el operador de comparación en `calcularDescuento()`. Ejecutar `phpunit.phar --testsuite unitarias`; con `>=` debe reproducirse el fallo del caso exacto.
2. **Test:** comparar la expectativa `0.0` y el subtotal `100.00` con R2 en `docs/plan_pruebas.md` y `requirements.md`; ambos indican que no hay descuento en el límite.
3. **Dato:** revisar en la llamada del test que se pasa literalmente `100.00`, y ejecutar `var_dump(100.00)` si se sospecha una alteración. El valor debe ser `float(100)`.

## 4. Cambio mínimo si se confirma la hipótesis más probable

Cambiar la condición de `calcularDescuento()` de `>= 100.00` a `> 100.00`. No cambiar el test: su expectativa representa la regla requerida.

## Estado de verificación

El código actual ya usa la comparación estricta `> 100.00`; no se modificó el test para hacerlo pasar. La suite unitaria se ejecutó después de restaurar ese código y terminó en **OK (7 tests, 18 assertions)**.

La hipótesis principal se confirma para la mutación indicada (`>=`): produce el descuento observado de `5.00` en S/ 100,00. El estudiante debe elegir la hipótesis antes de aplicar cualquier nuevo cambio. No se creó un commit: Git no está disponible en este entorno.
