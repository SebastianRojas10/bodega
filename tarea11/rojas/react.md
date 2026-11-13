# Flujo fijo frente a agente ReAct

1. El flujo fijo de n8n realiza siempre un mismo camino (webhook, modelo y respuesta) sin tomar ninguna decisión ni usar herramientas. Por eso, cuando le pregunté por los descuentos, me indicó que no tenía esa información.
2. Un agente ReAct alterna razonar y actuar: piensa lo que le falta para responder, usa una herramienta para conseguirlo, observa el resultado y vuelve a razonar hasta tener la respuesta. Además, decide solo si necesita herramientas y cuáles usar.
3. Esto ocurrió en la versión 2: al preguntar el precio de 3 leches, en los logs del AI Agent vi que llamó a la herramienta productos para obtener el precio (S/ 4,20) y con ese dato respondió 12,60; no llamó a Calculator porque hizo la multiplicación por sí mismo, lo que muestra que decide qué herramientas necesita según la pregunta, algo que el flujo v1 nunca hace.
