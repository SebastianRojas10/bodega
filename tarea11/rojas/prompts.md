# Registro de consultas a la IA · Tarea 11

## 1 · Guía paso a paso de la tarea · 13/11
**Herramienta:** Claude
**Consulta:** Pedí una guía paso a paso de la tarea y en qué momento tomar cada captura.
**Decisión:** La usé como checklist; construí los nodos del flujo a mano en la interfaz de n8n.

## 2 · Error del modelo Gemini · 13/11
**Herramienta:** Claude
**Consulta:** El nodo Google Gemini Chat Model dio 404 ("gemini-2.5-flash is no longer available to new users"). Al cambiar a gemini-3.8-flash la ejecución se quedó colgada y la terminal devolvió error 524.
**Decisión:** Cambié el modelo a Groq Chat Model con openai/gpt-oss-120b, que es la alternativa que permite la guía. Descarté llama3-8b-8192 porque ya no existe en Groq.

## 3 · Webhook 404 en el flujo v2 · 13/11
**Herramienta:** Claude
**Consulta:** Al probar el v2 la terminal devolvió 404 "webhook is not registered".
**Decisión:** Descubrí que al duplicar el workflow n8n cambió el Path del Webhook por un código aleatorio. Lo devolví a "bodega" y pulsé Execute workflow antes de cada petición.

## 4 · Respuesta vacía del AI Agent · 13/11
**Herramienta:** Claude
**Consulta:** Cómo devolver la respuesta del AI Agent en el Respond to Webhook.
**Decisión:** Cambié el Response Body de $json.text a $json.output, porque el AI Agent entrega su texto en el campo output.

## 5 · Corpus de la bodega · 13/11
**Herramienta:** Claude
**Consulta:** Pedí un borrador de 20 preguntas frecuentes que cubriera los 7 temas.
**Decisión:** Lo usé como base; revisé que los precios y la regla del 5 % coincidieran con mi app.

## 6 · react.md · 13/11
**Herramienta:** Claude
**Consulta:** Pedí ideas guía y una versión de referencia para comparar mis 3 líneas.
**Decisión:** Escribí mi versión y la comparé. Al revisar mis logs vi que el agente no usó Calculator, así que corregí la línea 3 para que refleje lo que realmente pasó.

## 7 · Capturas y archivos de entrega · 13/11
**Herramienta:** Claude
**Consulta:** Pedí recortar mis capturas con los nombres exactos y ordenar 04_respuestas_v2.txt.
**Decisión:** Revisé que las capturas fueran de mis propias ejecuciones y que las respuestas coincidieran con lo que mostró mi terminal.
