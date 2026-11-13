# Registro de prompts · Laboratorio S11 · Bodega

Registro de cada consulta a la IA durante el laboratorio y la decisión tomada.

## P1 · D0.md · Sin IA
**Herramienta:** ninguna
**Decisión:** Redacté D0.md a mano con las reglas R1–R4 copiadas literalmente de la guía.

## P2 · requirements.md · Kiro (spec)
**Prompt:** «Actúa como analista de sistemas. A partir del archivo D0.md adjunto genera requirements.md…» (prompt P2 de la guía)
**Resultado:** Kiro generó requirements.md en .kiro/specs/bodega-ventas-registro/.
**Decisión:** Revisé que tuviera 5 historias (el máximo permitido) y que no incluyera autenticación, reportes ni facturación. Observación de auditoría: los criterios EARS están en inglés y solo citan explícitamente R1 por su código; R2–R4 están cubiertos (Requirements 4, 2 y 5) pero sin la etiqueta. Lo acepté y copié el archivo a la raíz.

## P3 · design.md · Kiro (spec)
**Prompt:** «A partir de requirements.md genera design.md con arquitectura en tres capas…» (prompt P3 de la guía)
**Resultado:** design.md con capas, modelo de datos y diagrama Mermaid.
**Decisión:** Verifiqué las tres capas, el modelo de datos, el diagrama Mermaid del caso «registrar venta» y la sección de transacción para R4. Kiro agregó contenido que no se pidió (interfaz IRepositorio, clases de dominio opcionales y pruebas basadas en propiedades); no lo implementé para mantener el alcance. Verifiqué que ServicioVenta no tenga HTML, SQL ni PDO y copié el archivo a la raíz.

## P4 · sql/bodega.sql · Agente en VS Code
**Prompt:** prompt P4 de la guía.
**Resultado:** 6 productos; Leche Gloria con stock 0.
**Decisión:** Verifiqué que el script tuviera las tres tablas con claves foráneas y un producto con stock 0 (Leche Gloria) para probar R1. Lo importé en XAMPP.

## P5 · Código de la aplicación · Agente en VS Code
**Prompt:** prompt P5 de la guía.
**Resultado:** index.php, venta.php, guardar_venta.php, src/ y config/.
**Decisión:** Busqué el 0.05 y el umbral de 100: quedaron solo en ServicioVenta::calcularDescuento. Revisé que RepositorioMySQL use sentencias preparadas y transacción con rollBack. Registré una venta real en localhost y el stock bajó.

## P7 · Refactorización para testeabilidad · Agente en VS Code
**Prompt:** prompt P7 de la guía.
**Resultado:** ServicioVenta con validarCantidad, parsearCantidad, hayStock, agruparCantidadesPorProducto, calcularSubtotal, calcularDescuento y calcularTotal.
**Decisión:** Verifiqué que ServicioVenta no tuviera echo, PDO ni $_POST y que la app siguiera funcionando. parsearCantidad rechaza «1.5» antes de convertir a entero, lo que evita que PHP convierta 1.5 en 1.

## T1–T2 · Plan y casos de prueba
**Prompt:** prompts T1 y T2 de la guía.
**Resultado:** docs/plan_pruebas.md (una fila por regla) y docs/casos_prueba.md (16 casos).
**Decisión:** Verifiqué que el plan tuviera una fila por regla R1–R4 con R4 como integración, y que los casos incluyeran los 8 obligatorios con valores exactos (stock 5/6, subtotal 99,99 / 100,00 / 100,01, cantidad 0, -1 y 1,5).

## T3–T4 · Suite unitaria y auditoría
**Prompt:** prompts T3 y T4 de la guía.
**Resultado:** La suite inicial tenía 7 pruebas.
**Decisión:** Con ayuda de Claude agregué 4 pruebas de R1 (vender igual al stock, uno más que el stock, producto agotado y suma de líneas repetidas) para llegar a 11. Verifiqué que fallan si se cambia `<=` por `<` en hayStock.

## T5 · Integración con bodega_test
**Prompt:** prompt T5 de la guía.
**Decisión:** Verifiqué que la prueba use bodega_test y nunca la base bodega. env_test.php lee las credenciales desde variables de entorno y apunta a bodega_test.

## T6 · Guion E2E
**Prompt:** prompt T6 de la guía.
**Decisión:** Ejecuté los escenarios en el navegador. Escenario 2 (vender más que el stock) y escenario 3 (cantidad 0) pasaron; el resultado del escenario 1 está registrado en docs/e2e_manual.md.

## T7 · Análisis del defecto plantado
**Prompt:** prompt T7 de la guía.
**Decisión:** Hipótesis correcta: defecto en el código (>= en lugar de >). No se modificó el test. Ver docs/analisis_fallo.md.

## T8 · Seguridad OWASP
**Prompt:** prompt T8 de la guía.
**Decisión:** Confirmé a mano cada hallazgo: el ID de producto se valida con FILTER_VALIDATE_INT y las consultas usan sentencias preparadas; los errores van a error_log() con mensaje genérico al usuario; env.php está fuera de Git. La falta de autenticación quedó como riesgo condicional (fuera de alcance según D0). Ver docs/seguridad.md.

## P12 · Despliegue en InfinityFree · Claude
**Consulta:** Pedí los pasos para desplegar el contenido de la carpeta en InfinityFree.
**Decisión:** Subí solo index.php, venta.php, guardar_venta.php, src/ y config/conexion.php; creé config/env.php en el servidor con los datos del panel (sql211.infinityfree.com). Al subir bodega_subir.zip se sobrescribió env.php y se perdió la contraseña; la volví a escribir. Desde entonces subo solo los .php modificados.

## Cambio de nombre · Claude
**Consulta:** La docente pidió que la app diga «Bodega Rojas Castillo».
**Decisión:** Cambié el título en index.php, venta.php y guardar_venta.php y los volví a subir al hosting.
