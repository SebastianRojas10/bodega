<?php

return [
    'host' => getenv('BODEGA_DB_HOST') ?: 'MYSQL_HOST_FROM_CONTROL_PANEL',
    'usuario' => getenv('BODEGA_DB_USER') ?: 'MYSQL_USERNAME_FROM_CONTROL_PANEL',
    'clave' => getenv('BODEGA_DB_PASSWORD') ?: 'SET_THIS_IN_HOSTING_FILE_MANAGER',
    'base' => getenv('BODEGA_DB_NAME') ?: 'MYSQL_DATABASE_NAME_FROM_CONTROL_PANEL',
];
