# Verificación de seguridad — Flujo de selección de producto

Fecha de revisión: 2026-11-06

## Prueba de entradas tipo SQL injection

Se revisó el flujo de `venta.php` y `guardar_venta.php`. `venta.php` no lee el ID de producto desde `$_GET`; el producto se envía en el campo `items[][producto_id]`. En `guardar_venta.php`, ese valor se valida con `filter_var(..., FILTER_VALIDATE_INT)` antes de consultar el producto. `RepositorioMySQL::obtenerProducto()` enlaza el ID con una sentencia preparada.

| Entrada de producto | Resultado del validador entero | Resultado esperado del flujo |
|---|---|---|
| `1 OR 1=1` | `false` | Rechazo como producto inválido; no se consulta ni registra venta. |
| `' OR '1'='1` | `false` | Rechazo como producto inválido; no se consulta ni registra venta. |

La comprobación directa de `FILTER_VALIDATE_INT` en PHP CLI devolvió `false` para ambas entradas. **No se pudo completar la comprobación dinámica a través del formulario**: las credenciales no estaban visibles para los procesos Apache/PHP en esa sesión. `venta.php` mostró «No se pudo cargar el catálogo» e `index.php` mostró el error genérico de conexión. Por tanto, las entradas no se reportan como probadas de extremo a extremo. No se observó una respuesta SQL ni datos indebidos. Repetir la prueba cuando la conexión esté configurada en el hosting o entorno local.

## Revisión estática OWASP solicitada

| Categoría | Resultado y evidencia |
|---|---|
| A01 · Control de acceso | No se encontró autenticación en `guardar_venta.php`. El requisito del sistema establece uso en una sola computadora y sin autenticación; no se confirma un defecto en ese despliegue. Si el servidor se expone a usuarios no confiables, limitarlo a la computadora/red autorizada o agregar autorización. |
| A02 · Fallas criptográficas | `env.php` lee primero variables de entorno y conserva valores de desarrollo XAMPP como alternativa local; está excluido de Git. `env_test.php` usa variables de entorno. No poner los valores locales en hosting; crear allí `env.php` con datos del panel. La configuración de transporte/cifrado del servidor MySQL no se verificó. |
| A03 · Inyección | El ID de producto se valida como entero y las consultas del repositorio usan sentencias preparadas y parámetros enlazados. No se encontraron consultas que concatenen entradas del usuario. La prueba web dinámica queda pendiente por la conexión ausente descrita arriba. |
| A05 · Configuración insegura | En XAMPP, los valores locales funcionan como alternativa si no hay variables de entorno. En hosting, configure variables `BODEGA_DB_*` o edite el `env.php` privado del servidor con los datos del panel, siguiendo [configuracion_credenciales.md](./configuracion_credenciales.md). |
| A09 · Registro y monitoreo | Los errores PDO se envían a `error_log()` y el usuario recibe un mensaje genérico. No se observó exposición de SQL o rutas del servidor en la respuesta. No se verificó la configuración ni retención del log de Apache/PHP. |

No se encontraron vulnerabilidades de inyección confirmadas en la revisión estática. El hallazgo de acceso sin autenticación permanece como riesgo condicional al despliegue, no confirmado bajo el alcance local descrito.

## Pruebas ejecutadas

- `php phpunit.phar .` (en la revisión original): 7 pruebas unitarias aprobadas; 2 pruebas de integración con error porque `BODEGA_TEST_DB_USER` y `BODEGA_TEST_DB_PASSWORD` no estaban visibles en el proceso PHPUnit.
- Suite unitaria aislada: 7 pruebas, 18 aserciones, aprobadas.
- Prueba dinámica de los payloads en el formulario: pendiente en la revisión original por la conexión ausente; repetirla una vez configurado el hosting.
