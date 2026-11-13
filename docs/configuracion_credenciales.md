# Configuración de credenciales MySQL

`config/env.php` está excluido de Git. No añada secretos reales al repositorio. Configure variables de entorno cuando sea posible; en XAMPP el archivo usa valores locales de desarrollo como alternativa.

## Aplicación

`config/env.php` lee primero estas variables:

- `BODEGA_DB_USER`
- `BODEGA_DB_PASSWORD`
- `BODEGA_DB_HOST` (opcional; alternativa local: `localhost`)
- `BODEGA_DB_NAME` (opcional; alternativa local: `bodega`)

Si las variables no existen, utiliza `localhost`, usuario `root`, contraseña vacía y base `bodega`, exclusivamente para facilitar el desarrollo local con XAMPP. En un hosting, cree `config/env.php` desde `config/env.ejemplo.php` en el administrador de archivos del hosting y configure los valores proporcionados por su panel.

## Pruebas de integración

`config/env_test.php` requiere estas variables para ejecutar pruebas de integración:

- `BODEGA_TEST_DB_USER`
- `BODEGA_TEST_DB_PASSWORD`
- `BODEGA_TEST_DB_HOST` (opcional; por defecto `localhost`)

La base queda fijada a `bodega_test`. El usuario de pruebas necesita permisos de lectura, inserción, actualización y borrado en esa base, porque las pruebas recrean datos y limpian tablas. No reutilice este usuario para la aplicación.

## Crear usuarios MySQL

`sql/bodega_usuarios.sql` contiene los permisos mínimos requeridos. Antes de ejecutarlo como administrador MySQL, reemplace ambos marcadores de contraseña por secretos distintos. No guarde las contraseñas resultantes en el repositorio. Configure cada secreto en el entorno de Apache y en el entorno del proceso PHPUnit correspondiente.

No use el usuario administrador MySQL (`root`) en un hosting. El ejemplo de XAMPP con `root` sin contraseña es solo local y no debe subirse.
