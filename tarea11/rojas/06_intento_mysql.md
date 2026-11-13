# Intento de conexión MySQL desde n8n

**Host:** sql211.infinityfree.com:3306 · **Base:** if0_43374452_bodega · **Usuario:** if0_43374452
**Nodo:** MySQL Tool (credencial "MySQL account") en el workflow "Consulta bodega – Rojas v3 MySQL"
**Consulta prevista:** `SELECT nombre, precio, stock FROM producto`

**Error exacto:**
```
Couldn't connect with these settings
getaddrinfo ENOTFOUND sql211.infinityfree.com
```

![error](06_error_mysql.png)

## Análisis
1. (Escribe aquí por qué falló)
El host sql211.infinityfree.com solo existe dentro de la red de InfinityFree, por eso sale ENOTFOUND. Tu PHP sí se conecta porque está en el mismo hosting; n8n está afuera.
2. (Escribe aquí qué pieza falta entre el agente y los datos)
Una API REST, es decir, un endpoint PHP en el mismo hosting que devuelva los productos en JSON. El agente la llamaría con un HTTP Request Tool.
3. (Escribe aquí por qué esa pieza hace la conexión segura y controlada)
Las credenciales quedan solo en el servidor, el agente solo puede hacer lo que la API permite (leer nombre, precio y stock) y se pueden agregar validación, autenticación y registro.