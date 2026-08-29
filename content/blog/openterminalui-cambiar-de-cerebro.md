---
Title: OpenTerminalUI — Qué pasa cuando cambias de cerebro de verdad
Description: La entrega 3 prometía que el terminal funciona con cualquier modelo. Luego probé cuatro distintos — y la promesa hizo agua por dos sitios. Esto es lo que se rompió y los dos arreglos pequeños que hicieron que "cualquier cerebro" fuera cierto y no solo aspiracional.
Date: 2026-08-29 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 8
Lang: es
Translation_Key: openterminalui-swapping-brains
Image: /assets/images/openterminalui-08-swapping-brains-hero.webp

---

# OpenTerminalUI — Qué pasa cuando cambias de cerebro de verdad

Hace unas entregas escribí un artículo bastante confiado — [el #3, "Un terminal que funciona con cualquier cerebro"](/blog/openterminalui-funciona-con-cualquier-cerebro) — donde defendía que a las funciones de IA del terminal no debería importarles qué modelo ejecutas. Un cliente pequeño, una especificación compartida, apúntalo al cerebro que quieras. Era un buen diseño. También era, resulta, *una promesa que no había cobrado del todo*.

Porque esta semana hice de verdad lo que aquel artículo describía: cambié el cerebro. No una vez, sino cuatro, con modelos muy distintos. Y la abstracción hizo agua justo por los dos sitios por donde las abstracciones siempre hacen agua: donde una especificación limpia se topa con implementaciones reales y desordenadas, y donde "devolvió 200 OK" significa, en silencio, algo distinto de "funcionó".

Esta es la historia de esas dos fugas y de los dos arreglos pequeños que las cerraron. Es corto, es concreto, y es la diferencia entre que "cualquier cerebro" sea un eslogan o sea verdad.

---

## Contexto: por qué el terminal le pide JSON a un modelo

Un repaso rápido, porque importa para lo que se rompió. Algunas pantallas del terminal le pasan datos a un modelo y le piden una respuesta **estructurada**, no prosa. El informe de inversión es el ejemplo más claro: le das los fundamentales de una acción y sus titulares recientes, y devuelve un objeto ordenado — un resumen y tres secciones (Tesis alcista, Tesis bajista, Riesgos clave), cada una con un tono y unas pocas viñetas. (*JSON es simplemente un formato de texto para ese tipo de objeto estructurado — una forma que un programa puede leer, frente a un párrafo que lee una persona.*)

El cliente pide JSON por la vía oficial y educada: la especificación de chat-completions de OpenAI tiene un campo `response_format` donde puedes solicitar "dame JSON, y aquí está el esquema exacto". Los endpoints capaces lo respetan. Ese era el mecanismo en el que se apoyaba el #3. El problema es qué ocurre cuando un endpoint *no* lo respeta — y no te lo dice.

---

## Fuga #1: el modelo que dice "claro" y luego te ignora

Cambié el cerebro local a **gpt-oss**, un modelo abierto y capaz servido a través de Ollama. El chat normal funcionó al instante. Pero cada pantalla que necesitaba JSON — el informe, la tarjeta de "interroga esta acción", el sentimiento de noticias — se apagó, mostrando el honesto pero frustrante *"Arranca tu endpoint de IA y pulsa Regenerar"*. El endpoint estaba en marcha. Las preguntas normales se respondían bien. Entonces, ¿por qué "no disponible"?

Aquí está la trampa, y es buena. Cuando le pides a este modelo JSON con esquema estricto, devuelve **HTTP 200 OK** — ¡éxito! — y luego, en lugar de JSON, un ensayito parlanchín: *"Esto es lo que suele contener un objeto de sentimiento…"*. Ignoró el `response_format` por completo y escribió prosa *sobre* la petición en vez de responderla.

La lógica del cliente traía una suposición razonable de fábrica: si un proveedor *no admite* un modo JSON concreto, lo dirá con un código de error y bajaremos a un modo más simple. Pero este modelo no devolvió un error. Devolvió un triunfante 200 envuelto alrededor de lo que no era. El cliente se creyó el 200, le pasó la prosa al analizador de JSON, el analizador falló — y la función se declaró no disponible. Una luz verde sobre una tubería rota.

Los modelos de razonamiento lo empeoran de una forma casi graciosa: están *deseando explicar*. Pídeles un esquema y, libres de toda obligación de cumplirlo, te describen amablemente el esquema en lugar de rellenarlo.

### El arreglo: di la forma en voz alta

La idea es casi vergonzante de lo simple. Estos modelos ignoran la marca `response_format` a nivel de protocolo, pero siguen **las instrucciones del prompt** perfectamente. Así que, en lugar de fiarlo todo a un campo del sobre de la petición, el cliente ahora también **mete la forma requerida en la propia conversación** — un breve y explícito "responde solo con un objeto JSON que cumpla este esquema, sin prosa, sin markdown" — siempre que quien llama pide salida estructurada.

Ya está. Lleva el contrato en palabras que el modelo lee de verdad, no solo en una marca de protocolo que es libre de ignorar. Con la forma dicha en el prompt, gpt-oss dejó de explicar y empezó a responder: un informe limpio, las tres secciones, los campos correctos. Y es inofensivo para los endpoints bien educados que respetaban la marca desde el principio — cinturón y tirantes, sin contrapartidas.

---

## Fuga #2: el modelo que se queda sin tiempo de tanto pensar

Contento, pasé la misma validación por más modelos para asegurarme de que había terminado de verdad. La mayoría pasó. Luego **qwen3**, un modelo de *razonamiento*, falló de una forma completamente distinta — y esta no tenía nada que ver con `response_format`.

Los modelos de razonamiento hacen su trabajo en un cuaderno de "pensamiento" oculto antes de responder. Ese pensamiento gasta tokens — y la petición tenía un límite modesto de cuántos tokens podía usar la respuesta entera. El modelo se fundió el presupuesto *entero* pensando, chocó con el techo a mitad de idea, y devolvió… nada. Una respuesta vacía, marcada educadamente como "truncada porque se quedó sin sitio". El límite de tokens del informe, generoso para que un modelo normal escriba tres secciones, no bastaba para que un modelo de razonamiento *pensara hasta llegar* a tres secciones.

### El arreglo: dale a la respuesta truncada un intento más, con más sitio

El arreglo aquí tiene otra forma pero el mismo espíritu. Cuando una petición estructurada vuelve **truncada** — el modelo chocó con el techo de tokens antes de terminar — el cliente ahora le concede **un reintento con un presupuesto mucho mayor**, en la misma petición. Dale al pensador sitio para terminar de pensar, y luego responder.

Es deliberadamente estrecho: solo se dispara cuando la respuesta se cortó de verdad, y solo para llamadas estructuradas. Un modelo normal que termina su frase nunca paga el reintento. Pero un modelo de razonamiento que necesita espacio para los codos lo consigue, automáticamente, sin que nadie ajuste a mano un límite de tokens por modelo. qwen3 pasó de una tarjeta en blanco a un informe completo — más lento (está haciendo dos pasadas, y los modelos de razonamiento no tienen prisa), pero correcto.

---

## Por qué este camino

Dos cambios pequeños, una lección compartida: **apunta al comportamiento, no a la promesa.**

- **Los protocolos describen intenciones; los prompts describen requisitos.** Una marca `response_format` dice "me gustaría JSON". Es una petición que el servidor puede respetar, ignorar o malinterpretar. Repetir la forma en el prompt convierte un apretón de manos esperanzado en una instrucción que el modelo no puede esquivar fácilmente. Cuando das soporte a todo el zoo desordenado de endpoints reales, escribes para cómo se *comportan*, no para cómo la especificación dice que *deberían*.
- **Un 200 no es un éxito — analizar la respuesta lo es.** El bug sutil no fue una excepción; fue una luz verde sobre la carga equivocada. La respuesta de aspecto sano que falla más abajo es más peligrosa que un error honesto, porque nada te avisa. El arreglo trata "¿obtuvimos de verdad JSON usable?" como la condición real de éxito, no el código HTTP.
- **Degrada por escalones, pero no confundas un tropiezo con un muro.** La idea original del #3 — prueba lo más estricto, baja con elegancia — era correcta. Solo necesitaba aprender dos modos de fallo nuevos que no se anuncian: el 200-con-prosa y el truncado-por-pensar. Ahora ambos tienen un escalón en vez de un precipicio.

Y la meta-lección, la que merece el artículo entero: **la portabilidad no se demuestra con el diseño, se demuestra cambiando.** El #3 defendía que el terminal funciona con cualquier cerebro. Hizo falta enchufar de verdad cuatro cerebros distintos para encontrar los dos puntos donde "cualquiera" llevaba un asterisco. El diagrama limpio sobrevivió al contacto; las suposiciones de dentro, no del todo, hasta que se probaron contra modelos reales que rompen las reglas cada uno a su manera.

---

## Impacto: cuatro cerebros, un fallo honesto

Volví a pasar el informe por cuatro modelos genuinamente distintos, todos por el mismo código de función sin tocar:

| Cerebro | Tipo | Resultado |
|---------|------|-----------|
| gpt-oss (20B) | nube, vía Ollama | ✅ informe completo |
| lfm2 | local pequeño | ✅ informe completo, rápido |
| qwen3 (9B) | modelo de razonamiento local | ✅ informe completo (necesitó el reintento) |
| nemotron-nano (30B) | nube, vía Ollama | ✅ — una vez instalado de verdad |

Esa última fila es mi favorita, porque es el fallo *honesto*. Al principio Nemotron también volvía "no disponible" — pero la causa no era nuestro código. Ollama listaba el modelo pero no lo había descargado de verdad, así que el propio servidor devolvía un genuino "modelo no encontrado". El terminal lo reportó como no disponible, que era exactamente lo correcto. Una vez el modelo estaba ahí de verdad, fluyó por el mismo camino arreglado que los demás. Un buen recordatorio de que "hazlo robusto" y "tapa una dependencia que falta" son trabajos distintos — el segundo habría sido una mentira, y las mentiras son justo aquello contra lo que se organiza todo este fork.

---

## Alcance: lo que esto *no* es

- **No es una tabla de ajuste por modelo.** No hay un fichero de configuración que mapee cada modelo con sus manías. Los dos arreglos son comportamientos generales — di la forma, reintenta un truncado — que funcionan sin que el terminal sepa nada del cerebro concreto.
- **No es una mejora de velocidad para los modelos de razonamiento.** Son intrínsecamente más lentos, y el reintento por truncado supone una segunda pasada cuando salta. Aquí lo correcto gana a lo rápido, pero si quieres agilidad, un modelo sin razonamiento es la mejor elección — tú decides, que es justo la gracia.
- **Sigue sin streaming.** Las respuestas llegan enteras. Bien para análisis cortos por secciones; un detalle para más adelante.

---

## Qué viene después

Este es el cimiento callado bajo la próxima entrega — **la v1.3, "rebate mi tesis"**, un espacio de texto libre donde defiendes una postura y el terminal te lleva la contraria. Esa función solo merece la pena construirse si el cerebro que la mueve es genuinamente intercambiable y honesto bajo presión, porque vas a apoyarte en él para que te diga cosas que no quieres oír. Hacer que la salida estructurada sobreviva a cualquier modelo que le apuntes es lo que permite que las funciones adversariales sigan siendo fiables — con cualquier cerebro, en tu propia máquina.

---

## Lecturas relacionadas

- [OpenTerminalUI — Un terminal que funciona con cualquier cerebro](/blog/openterminalui-funciona-con-cualquier-cerebro) — #3: el cliente agnóstico al proveedor que este artículo pone a prueba. Léelo primero.
- [OpenTerminalUI — Un terminal que te lleva la contraria](/blog/openterminalui-una-investigacion-que-interroga) — #7: la capa de investigación adversarial que corre sobre el mismo cliente de modelos.
- [OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) — #4: por qué un "no disponible" honesto le gana a una respuesta inventada, en todas partes.
- **Código:** el fork vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(Nota de transparencia, según la tradición de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que depuró las dos fugas que describe. Lo que significa que un modelo escribió un artículo sobre cómo hacer fiable la fontanería que hay detrás de los modelos. Contenemos multitudes.)*
