---
Title: OpenTerminalUI — Un terminal que te lleva la contraria
Description: Las versiones anteriores hicieron honestos los datos y privada la cartera. La v1.2 apunta el modelo local contra la voz más peligrosa al invertir — tu propia convicción — con una tarjeta de "interrogar esto" que somete a presión la tesis alcista, y la tuya, apoyándose en tus propias notas.
Date: 2026-07-31 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Economia, Privacidad
Series: OpenTerminalUI — Bifurcando un terminal financiero
Series_Slug: openterminalui
Series_Order: 7
Lang: es
Translation_Key: openterminalui-research-interrogates
Image: /assets/images/openterminalui-07-research-interrogates-hero.webp

---

# OpenTerminalUI — Un terminal que te lleva la contraria

Cada versión de este fork ha girado alrededor de una idea: un terminal abierto y privado para invertir *sin que te engañen*. Las anteriores combatieron las formas en que una **herramienta** te engaña — mostrar números inventados como si fueran reales ([1.0](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion)), reportar los ingresos de una venta como si fueran ganancias, compartir en silencio "tu" cartera con todos los de la máquina ([1.1](/blog/openterminalui-retirar-la-cartera-compartida)).

La **v1.2** va a por una más difícil: la forma en que *tú* te engañas a ti mismo. La voz más cara al invertir no es un mal feed de datos — es tu propia convicción, la tesis de la que te has enamorado y para la que ahora solo lees la evidencia que la confirma. Así que esta versión construye una función cuyo único trabajo es **llevarte la contraria**.

> Entrega **7** de la serie *OpenTerminalUI*. Cobra el pagaré que firmó la [#6](/blog/openterminalui-retirar-la-cartera-compartida) — la promesa de una "investigación que interroga" — y se apoya en el cliente de modelo local y agnóstico de proveedor de la [#3](/blog/openterminalui-funciona-con-cualquier-cerebro).

---

## La premisa: un analista que se niega a hacerte la ola

El terminal ya tenía un *informe con IA* — le pasas al modelo local unos fundamentales y titulares y devuelve un resumen ordenado de tesis alcista/bajista. (*IA = inteligencia artificial; el modelo aquí es un gran modelo de lenguaje, o LLM, corriendo en tu propia máquina.*) Es útil, pero tiene un modo de fallo: pregúntale a un asistente entusiasta por una acción y narrará encantado la historia que quieres oír. Un informe que es 60% tesis alcista no es un contrapeso a tu juicio — es un espejo que asiente.

Así que la v1.2 añade una segunda tarjeta, deliberadamente adversaria: **Interrogar esta acción** (y *esta moneda*, y *este índice* — funciona con todo activo que cubre el terminal). Mismo modelo, trabajo opuesto. En vez de vender la historia, la enjuicia. El prompt de sistema le dice al modelo que es un *abogado del diablo escéptico*, no un asistente, y que debe devolver exactamente cuatro secciones:

- **La narrativa alcista** — la historia dominante, dicha con claridad (incluida la *tuya*, si la has escrito).
- **Qué tendría que ser cierto** — los supuestos que la sostienen y en los que se apoya en silencio.
- **La tesis bajista y las tasas base** — qué la rompe, y con qué frecuencia decepcionan las historias con esta forma.
- **¿Ya está en el precio?** — si la valoración y la acción del precio ya se han tragado la narrativa que te emociona.

Tiene prohibido dar consejo de compra o venta, y se le dice con todas las letras que **no adule**. El objetivo no es un veredicto; es la fricción de ver tu propia tesis puesta sobre la mesa y pinchada.

---

## De lo que estoy más orgulloso: lee tus *otras* notas

En algún lugar del terminal hay un "segundo cerebro" privado — un almacén por usuario de tus propias notas, entradas de diario y tesis, indexado para que el modelo pueda recuperar sobre tu propia escritura. La interrogación lo usa en dos capas.

La capa obvia: tus notas **sobre este ticker exacto** se incorporan al prompt como *la tesis a cuestionar*. Escribiste "el margen de servicios sigue expandiéndose y compensa la caída del hardware"; el adversario ataca esa frase directamente.

La capa que de verdad me importa: además **recupera semánticamente tus notas relacionadas sobre *otros* tickers** — y le instruye al modelo que, cuando esas revelen un patrón recurrente, lo *nombre*. Porque lo que no puedes ver de tu tesis sobre Apple es que es la misma apuesta de "foso de software duradero" que hiciste en otros tres nombres, uno de los cuales anotaste en el diario como pérdida. Una persona es estructuralmente ciega a sus propios errores repetidos; un sistema que ha leído todas tus notas a la vez no lo es. Esa recuperación es de mejor esfuerzo y privada — nunca sale de tu máquina, y si el índice de notas aún no está construido, simplemente se apoya en menos, nunca en material inventado.

Ese último detalle produjo el arreglo menos vistoso pero más importante de la versión: escribir una nota ahora reindexa en silencio tu segundo cerebro en segundo plano, de modo que una idea que apuntas llega de verdad al adversario la próxima vez que interrogas — en lugar de quedarse sin ver hasta algún paso manual. Una función que lee tus notas no vale nada si no puede ver la nota que escribiste hace treinta segundos.

---

## Darle al adversario material de verdad

Una interrogación vale tanto como aquello en lo que se apoya, y dos puntos ciegos la estaban dejando sin comer.

**Noticias que existen de verdad.** La capa de noticias construía su búsqueda a partir del ticker — bien para `AAPL`, inútil para una moneda o un índice, donde `"BTC-USD stock"` y `"^GSPC stock"` no devuelven prácticamente nada. Así que una única función compartida ahora las resuelve a lo que la gente *realmente* escribe: `BTC-USD → "Bitcoin crypto"`, `^GSPC → "S&P 500"`. Como cada superficie — el feed de noticias, el informe y la interrogación — saca sus términos de esa única función, arreglarla una vez encendió titulares reales para cripto e índices en todas partes a la vez. Sin ningún proveedor de noticias de pago nuevo; solo haciéndoles la pregunta correcta a las fuentes gratuitas que ya estaban conectadas.

**Sentimiento que lee como un operador.** El medidor de sentimiento siempre activo es un contador de palabras clave — ve "beat" y aplaude, y se pierde por completo el "beat, pero con guía a la baja". La v1.2 añade una capa opcional que puntúa una página entera de titulares con el modelo local en **una sola** llamada por lotes (nunca una llamada por titular — ese camino corre en cada lista y tiene que ser barato), cacheada por titular. Lee como lo haría un participante del mercado: un beat de resultados con guía débil es bajista; un riesgo pendiente resuelto es alcista. Y se mantiene honesto — cada titular queda etiquetado con el motor que de verdad lo puntuó, y si el modelo está apagado, cae en silencio al conteo de palabras clave en vez de fingir.

---

## Por qué esta forma

- **Dos tarjetas, no una tarjeta más lista.** Podría haber hecho el informe "más equilibrado". Pero una única tarjeta que intenta ser a la vez vendedora y escéptica acaba en papilla. Dos tarjetas con instrucciones opuestas te dan una segunda opinión de verdad, y notas la diferencia entre ellas.
- **Apóyalo en *tus* palabras, de forma adversaria.** La generación aumentada por recuperación (*RAG* — dejar que el modelo responda desde documentos recuperados en vez de desde su memoria) se suele vender como forma de ser *útil* con tus datos. Apuntarla al revés — usar tus propias notas para hallar el agujero en tu propio argumento — es la misma maquinaria dirigida al único lector al que más puede ayudar: tú.
- **Bajo demanda, nunca automática.** La inferencia local es lenta, así que nada corre hasta que haces clic. No es solo rendimiento; encaja con el ritual. Interrogas una tesis cuando estás a punto de actuar sobre ella, no en cada carga de página.

---

## Alcance: lo que esto *no* es

- **No es asesoramiento.** Nunca te dice que compres o vendas; te entrega la tesis bajista y las tasas base y te deja decidir. Es una línea deliberada, no una limitación que "arreglaré" luego.
- **No inventa.** Sin modelo local corriendo, lo dice claramente y no muestra nada — la misma regla de no-fabricación por la que vive todo el proyecto. El apoyo son tus notas más los datos en pantalla; no echa mano de hechos que no se le dieron.
- **"Rebate mi tesis" se movió a la v1.3.** Un espacio de texto libre — pega cualquier argumento, sin atarlo a un ticker, y que lo despedacen — tentaba meterlo aquí. Es el siguiente paso natural, así que tendrá su propia habitación en la próxima versión en vez de un rincón apresurado de esta.

---

## Qué viene después

Con las piezas de integridad pasiva, cartera privada y ahora investigación adversaria en su sitio, la estrella polar está casi construida: un terminal que no te mostrará datos falsos, no filtrará tus posiciones y no adulará tu tesis. La **v1.3** abre la superficie de texto libre "rebate mi tesis" — la interrogación, desatada de un único ticker. Y la invitación permanente de todo este proyecto sigue en pie: el modelo es tuyo, corriendo en tu máquina, y su trabajo es ser el amigo lo bastante honesto para decirte que te equivocas.

---

## Lecturas relacionadas

- [OpenTerminalUI — Retirar la cartera que estaba compartida en secreto](/blog/openterminalui-retirar-la-cartera-compartida) — #6: el final de privacidad que prometió esta versión de "investigación que interroga".
- [OpenTerminalUI — Un terminal que funciona con cualquier cerebro](/blog/openterminalui-funciona-con-cualquier-cerebro) — #3: el cliente de modelo local y agnóstico de proveedor sobre el que cabalgan todas las funciones de IA de aquí.
- [OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) — #4: la primera forma, pasiva, de "que no te engañen".
- **Código:** el fork vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(Una nota de transparencia, en el espíritu de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que hizo la ingeniería que describe, y el mismo tipo de modelo que hace la interrogación.)*
