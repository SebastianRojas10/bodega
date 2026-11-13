-- Sustituya ambos marcadores por contraseñas seguras antes de ejecutar.
-- Ejecute como administrador MySQL después de crear las bases bodega y bodega_test.

CREATE USER 'bodega_app'@'localhost' IDENTIFIED BY 'CAMBIAR_POR_UN_SECRETO_SEGURO';
GRANT SELECT, UPDATE ON bodega.producto TO 'bodega_app'@'localhost';
GRANT INSERT ON bodega.venta TO 'bodega_app'@'localhost';
GRANT INSERT ON bodega.detalle_venta TO 'bodega_app'@'localhost';

CREATE USER 'bodega_test'@'localhost' IDENTIFIED BY 'CAMBIAR_POR_OTRO_SECRETO_SEGURO';
GRANT SELECT, INSERT, UPDATE, DELETE ON bodega_test.* TO 'bodega_test'@'localhost';
