---
Title: AINARRES federado: dos fabricantes de IA en un mismo tablero
Description: La quinta entrega de AINARRES: dejar de depender de un solo modelo puntero y dejar que modelos de fabricantes distintos —xAI y Anthropic— compartan los papeles como iguales. Y el instante que lo resume: dos modelos de dos empresas revisando el trabajo a la vez, sin más contacto que un tablero común.
Date: 2026-07-01 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 5
Lang: es
Translation_Key: ainarres-federation
Image: /assets/images/ainarres-05-federation-hero.webp

---

# AINARRES federado: dos fabricantes de IA en un mismo tablero

A mitad de la ejecución, el tablero enseñó un instante que llevo toda la serie persiguiendo:

```
en curso (2):
  - …d25d  [revisando]  grok+grok-build       hace 22s
  - …bc64  [revisando]  claude-code+sonnet    hace 28s
```

Dos modelos de **dos empresas distintas** —grok, de xAI; sonnet, de Anthropic— revisando trabajo **en el mismo instante**, cada uno con una tarea diferente entre manos, sin hablar entre ellos ni con nadie: su único punto de contacto es el tablero común. Ninguno manda sobre el otro. Nadie repartió las tareas. Cada uno cogió la que tenía libre.

Esa foto es, en una frase, la quinta entrega: **AINARRES ya no depende de un solo modelo puntero; varios, de fabricantes distintos, se reparten el trabajo como iguales.** Y llegar ahí obligó a resolver un problema bonito —¿cómo demuestras que dos revisores independientes de verdad se reparten el trabajo, y no que uno se lo lleva todo?— que, como siempre en este proyecto, enseñó más por cómo se resistió que por cómo se resolvió.

> Si llegas nuevo: AINARRES (acrónimo en inglés de *AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** —el terreno común sobre el que se coordina el trabajo— hecho con PostgreSQL. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo saben "dame la siguiente tarea que me toca" y "esta ya está". **No hay orquestador**: cada agente *tira* de la cola lo que tiene permiso de hacer. Las entregas anteriores construyeron eso, demostraron que un solo agente podía desarrollar el propio AINARRES, y luego que **varios a la vez** podían hacerlo sin pisarse.

---

## De un solo cerebro a varios fabricantes

Hasta ahora, los papeles "de más responsabilidad" —diseñar (partir un encargo en tareas), revisar e integrar— los hacía siempre la **misma familia**: un único modelo puntero. Funcionaba, pero tenía una debilidad silenciosa: si ese modelo tiene un punto ciego, *nadie más lo ve*. Un revisor que es el mismo tipo de cerebro que el autor tiende a tropezar en las mismas cosas.

La federación levanta eso. La idea es que **más de un modelo puntero, de fabricantes distintos, compartan esos papeles como compañeros** —ninguno por encima de otro—. En esta entrega entra por primera vez un segundo fabricante: **claude**, de Anthropic, junto al **grok** de xAI que ya venía haciendo de puntero. Y para afinar, partimos a claude en dos: un modelo más potente (Opus) para **diseñar**, y uno más ligero (Sonnet) para **revisar** —cada combinación de herramienta y modelo es una "familia" distinta, igual que ya teníamos dos implementadores baratos distintos—.

---

## Por qué la variedad importa (y por qué NO es "separar tareas")

Aquí hay un matiz que conviene aclarar, porque yo mismo me equivoqué al principio. Uno podría pensar que el valor de tener varios revisores es **separar responsabilidades**: que quien revisa no sea quien programó. Pero eso ya lo teníamos —cada agente que trabaja es una instancia distinta, así que el que revisa nunca es el que implementó—.

Lo que la federación añade es otra cosa, y más profunda: **fallos no correlacionados**. Un revisor de *otro fabricante* pilla cosas que al modelo autor se le escapan, precisamente porque es un cerebro distinto, entrenado por otra gente, con otros puntos ciegos. No es "más ojos"; es **ojos que fallan en sitios diferentes**. Esa es la apuesta de valor de traer un segundo fabricante, y es una cuestión de calidad, no de seguridad.

---

## La línea que nadie cruza

Federar "quién diseña" y "quién revisa" es seguro: ni diseñar ni revisar tocan la rama principal, así que dos iguales haciéndolo no pueden romper nada. Pero hay **una** cosa que deliberadamente **no** federamos: **quién fusiona**.

Fusionar a la rama principal —el único momento en que el trabajo sale al mundo real— lo sigue haciendo **un solo integrador, lanzado por una persona**. Es la frontera de seguridad que montamos en la segunda entrega: quien tiene el poder de fusionar no puede ser cualquiera, y ningún fabricante nuevo lo recibe. Los compañeros de claude **nunca** pueden fusionar; si por lo que fuera les cayera una tarea de integración, el sustrato simplemente no se la entrega —no es una promesa en el texto de instrucciones, es que la llave no existe—.

¿Por qué esa cautela? Porque federar *quién puede fusionar* es federar *confianza*, y todavía no tenemos una forma justa de medir si un fabricante se porta bien. Eso es un problema para más adelante (la gobernanza, ver abajo). Esta entrega federa a los **compañeros**; la confianza se federa otro día.

Y un tercer cerrojo, pequeño pero necesario: ahora **solo quien tiene el papel de diseñador puede crear tareas**. Antes, cualquier agente del carril podía inventarse trabajo por su cuenta; ahora, crear una tarea exige el permiso de dar el primer paso con ella. Un implementador barato ya no puede colar tareas de la nada.

---

## El detalle honesto: la federación no se puede fingir

El primer intento de "ver" la federación no la enseñó. Le dimos al enjambre **una sola tarea**, y pasó lo lógico: con una única revisión que repartir, el revisor más rápido (grok) la cogió y el de claude se quedó **sin hacer nada**. Cero. No fue un fallo: es pura física del sistema. Con una revisión y dos revisores a la vez, uno se la lleva.

La lección es bonita porque es estructural, no un parche: **para que se vea que un papel se comparte, hay que dar más trabajo del que un compañero puede barrer de una pasada**. Así que la prueba de fuego fue un encargo de **tres tareas independientes**. Con tres revisiones sobre la mesa a la vez, los dos revisores *tuvieron* que repartírselas —y ahí sí, el revisor de Anthropic se puso a trabajar, revisando el trabajo que había programado el implementador barato, en paralelo con grok—.

El veredicto lo dio el propio informe de fin de ejecución:

```
entregadas (3):
  - …d25d  por familia: implementó=opencode  revisó=grok      integró=grok
  - …bc64  por familia: implementó=opencode  revisó=claude    integró=grok
  - …e943  por familia: implementó=opencode  revisó=claude    integró=grok
      revisión entre familias: 3 de 3 tareas revisadas por una familia distinta de la que las implementó
```

**Tres de tres revisadas por una familia distinta de la que las escribió; dos de ellas por el revisor de Anthropic** —revisión entre fabricantes, en paralelo, sin que nadie coordinara, todo fusionado a una rama principal coherente por el único integrador—.

---

## Y quién construyó todo esto

La parte que más me gusta: ese informe que acabas de leer —el que *mide* la revisión entre familias— **lo construyó el propio enjambre federado** en el paso anterior. Y el encargo de tres tareas que sirvió de prueba también. Es decir: **la entrega que añade un fabricante nuevo la construyeron, en buena parte, los propios fabricantes** —claude diseñando, el implementador barato programando, grok y claude revisando entre ellos, grok integrando—, con una persona limitándose a arrancar el proceso.

Solo lo primero, el "cableado" (enseñar al sustrato quién es cada nuevo compañero y qué puede hacer), lo hice yo a mano, porque el reparto de poderes tiene que ser de fiar *antes* de dejar que el enjambre corra. Todo lo demás lo hizo el enjambre.

---

## Lo que aún no hemos hecho (seamos honestos)

- Todo corrió en **un solo portátil**. La federación hace de verdad lo de "fabricantes distintos", pero lo de "máquinas y redes distintas" sigue siendo una simulación fiel, no la realidad.
- Hemos demostrado que el revisor de otro fabricante **funciona y trabaja en paralelo**, pero aún no hemos medido lo importante: si de verdad **pilla más cosas** que un revisor del mismo tipo que el autor. Eso necesita volumen y una forma de medir la calidad de cada revisión.
- **Quién fusiona sigue sin federarse**, a propósito. Y **una persona sigue eligiendo el encargo y apretando el botón de salida.**

---

## Lo que viene

El siguiente escalón es el que lleva rondando desde el principio: **gobernanza**. Ahora mismo confiamos en que cada familia se porte bien. La gobernanza es que **el propio flujo retire permisos** a quien demuestre no hacer bien su trabajo —degradar a un revisor que aprueba basura, apartar a un modelo que quema recursos sin entregar—. Y fíjate en el detalle: el tablero ya **registra quién revisó qué y quién devolvió qué**. Esa es, exactamente, la materia prima que la gobernanza necesitará para decidir. La hemos empezado a fabricar sin darnos cuenta.

Con esto, el ciclo de esta temporada se cierra: de *una tarea cada vez*, a *muchas a la vez*, a *muchas, de fabricantes distintos, como iguales*.

---

## Para leer y explorar

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). El diseño, los planes y las retrospectivas de cada hito viven en la carpeta `.agents/`, pensados para que los lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **2**: [AINARRES se construye a sí mismo](/blog/ainarres-se-construye-a-si-mismo).
- Entrega **3**: [AINARRES corre sin director](/blog/ainarres-corre-sin-director).
- Entrega **4**: [AINARRES en enjambre](/blog/ainarres-en-enjambre).

*(Nota de transparencia, como en las entregas anteriores: este artículo lo ha escrito un agente de IA con dirección humana, sobre un proyecto cuyo fin es que las IA coordinen su propio trabajo. La ejecución que se cuenta aquí —dos modelos de dos empresas revisando a la vez, tres de tres revisiones entre familias— ocurrió tal cual.)*
