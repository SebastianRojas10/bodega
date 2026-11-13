# Bodega Doña Rosa

**URL pública:** https://bodega-rojascastillo.infinityfreeapp.com/

Aplicación PHP y MySQL para inventario y registro de ventas.

## Despliegue en InfinityFree

1. Cree una cuenta gratuita en [InfinityFree](https://www.infinityfree.com/) con el correo institucional y cree un hosting con un subdominio gratuito. La activación puede tardar; mientras espera, puede continuar con P12.
2. En **Control Panel → MySQL Databases**, cree una base. Anote el hostname (por ejemplo, `sqlXXX.infinityfree.com`), el nombre de base, el usuario y la contraseña que muestra el panel.
3. Abra el phpMyAdmin del hosting, seleccione la base recién creada y use **Import** para cargar `sql/bodega_produccion.sql`.
4. En **File Manager → htdocs**, suba el proyecto, excepto `.git/`, `tests/`, `phpunit.phar`, `phpunit-11.5.57.phar` y `config/env.php`. Deje `index.php` directamente dentro de `htdocs`.
5. Cree `config/env.php` en el servidor a partir de `config/env.ejemplo.php` y reemplace sus cuatro valores con el host, base, usuario y contraseña del panel. No suba ni comparta credenciales reales.
6. Abra el subdominio y compruebe que se ve el inventario. Como prueba de aceptación, desde un celular usando datos móviles registre una venta de prueba y vuelva al inventario para comprobar que el stock disminuyó.

**Advertencia:** esta aplicación no tiene autenticación. Al publicarla en un subdominio accesible por Internet, otras personas podrían registrar ventas y modificar el stock. Antes de hacer la prueba móvil, use datos de prueba y stock que pueda modificar; no deje el sistema con inventario real expuesto públicamente sin implementar control de acceso.

## Desarrollo local con XAMPP

`config/env.php` prioriza las variables `BODEGA_DB_*`; si no existen, usa `localhost`, usuario `root`, contraseña vacía y base `bodega` para el entorno local XAMPP. Este archivo está excluido de Git. No reutilice los valores locales en el hosting.

Para los tests de integración, configure por separado las variables `BODEGA_TEST_DB_HOST`, `BODEGA_TEST_DB_USER` y `BODEGA_TEST_DB_PASSWORD`; la base permanece fijada a `bodega_test`.
