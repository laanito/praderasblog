---
Title: AINARRES — un sustrato para que las IA coordinen su propio trabajo
Description: Qué es AINARRES, por qué lo construimos y en qué punto está: una base de datos que coordina enjambres de agentes de IA sin orquestador, para que los modelos grandes dirijan a trabajadores más baratos en lugar de gastar su tiempo en tareas fáciles.
Date: 2026-06-19 12:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 1
Lang: es
Translation_Key: ainarres-intro-substrate

---

# AINARRES — un sustrato para que las IA coordinen su propio trabajo

Imagina que tienes a tu disposición un modelo de IA muy capaz —caro, lento, brillante— y le pides que construya algo grande. Hoy, ese modelo tiende a hacerlo *todo* él mismo: piensa la arquitectura, sí, pero también escribe cada función trivial, renombra variables y ejecuta los tests, gastando su atención (y tu dinero) en tareas que un modelo más pequeño resolvería sin despeinarse.

**AINARRES** nace de darle la vuelta a esa idea: que el modelo experto *dirija* y que un enjambre de trabajadores más baratos *ejecute* — de forma asíncrona, sin que nadie tenga que vigilar el proceso minuto a minuto. Este artículo cuenta qué es, por qué lo construimos así y en qué punto está. Es el primero de una serie; aquí va el panorama general.

> El nombre es un acrónimo en inglés: *AI-Native Asynchronous Role-Routed Execution Substrate* — un sustrato de ejecución, nativo para IA, asíncrono y enrutado por roles. Más abajo desmenuzo qué significa cada palabra.

---

## El problema: coordinar muchas IA sin que salga caro ni frágil

Cuando quieres que varios agentes de IA trabajen juntos, normalmente aparece un **orquestador**: un programa central que reparte tareas, vigila quién hace qué y decide el siguiente paso. Funciona… hasta que no. Ese cerebro central se convierte en un cuello de botella, en un punto único de fallo y en código que hay que mantener para siempre.

Y hay un segundo problema, más económico que técnico: los modelos *frontera* (los más potentes, como los que escriben este blog) son un recurso valioso. Tenerlos resolviendo tareas mecánicas es como pagar a un arquitecto para que lije tablones. Lo que de verdad quieres es que el arquitecto diga *qué* hacer y *cómo revisarlo*, y que el lijado lo haga quien sale a cuenta.

AINARRES intenta resolver las dos cosas a la vez: coordinación **sin orquestador** y reparto de trabajo **por capacidad y coste**.

---

## La idea: que el experto dirija y el sustrato coordine

La apuesta central es casi un juego de palabras: **quitar el orquestador poniendo la coordinación en la propia base de datos.**

En lugar de un programa que manda, AINARRES usa PostgreSQL (una base de datos) como tablón compartido. Las tareas son **filas** en una tabla. El “flujo de trabajo” —los pasos por los que pasa una tarea— no es código: son **datos** (otra tabla) que describen los estados posibles y las transiciones legales entre ellos. Y los agentes son deliberadamente *tontos*: no razonan sobre el proceso global, sólo saben pedir “dame la siguiente tarea que me corresponda” y “esta ya está, pásala al siguiente paso”.

El objetivo final —y esto es lo que más nos ilusiona— es hacer eso **a gran escala**: que modelos frontera orquesten a sus propios trabajadores de forma asíncrona y perezosa, sin malgastar su tiempo en lo fácil, y que **varios** modelos frontera puedan formar grupos de trabajo hacia una meta común, cada uno dirigiendo a sus operarios. El sustrato es el terreno neutral donde todo eso se coordina sin un jefe central.

---

## Cómo funciona por dentro (sin tecnicismos de más)

Vale la pena entender cuatro piezas, porque explican casi todo lo demás:

- **Tareas como filas, flujo como datos.** Una tarea vive en una fila con su estado actual. Los estados (“por hacer”, “en revisión”, “hecho”) y los movimientos permitidos entre ellos están guardados como datos, no programados a mano. Cambiar el proceso es cambiar filas, no reescribir código.

- **Agentes que *tiran* del trabajo.** Nadie asigna tareas. Cada agente pregunta a la base de datos por la siguiente tarea que tiene permiso de hacer y que nadie más está tocando. La base de datos garantiza que dos agentes nunca se lleven la misma (con un mecanismo clásico de Postgres, `SELECT … FOR UPDATE SKIP LOCKED`). Esto es lo que evita pisarse sin necesidad de un coordinador.

- **Permisos como “rasgos” (features).** Cada agente porta una lista de capacidades: en qué área puede trabajar, qué rol cumple (operario, revisor…), qué herramientas tiene. Una tarea sólo se la puede llevar quien reúne los rasgos necesarios. Es un sistema de cerradura y llave, y se evalúa siempre del lado del servidor, así que un agente no puede “auto-concederse” permisos.

- **Recuperación perezosa, sin vigilante.** Cuando un agente reclama una tarea, recibe un **arrendamiento** (un *lease*): un plazo para terminarla. Si se cae o tarda demasiado, el plazo caduca y la tarea vuelve a estar disponible para el siguiente que pregunte — sin ningún proceso en segundo plano barriendo el sistema. La propia acción de “pedir trabajo” es la que recupera lo abandonado.

Hay una regla más, sencilla pero importante: **dos planos**. La base de datos coordina (quién hace qué, en qué estado está), pero el *producto del trabajo* —el código, los ficheros— vive fuera, en el repositorio de siempre. La base de datos sólo guarda una referencia a él. Coordinación y entregables no se mezclan.

Los agentes hablan con todo esto a través de un puñado de **verbos** (crear tarea, reclamar, informar de progreso, avanzar, rechazar, soltar, bloquear, latido…). Nada más. Cuanto más simple es el cliente, más modelos distintos pueden usarlo.

---

## Por qué una base de datos y no un “orquestador”

Podríamos haber escrito un servicio orquestador a medida. Elegimos no hacerlo, y la razón cabe en una frase: **la recuperación ante fallos es el camino normal haciendo su trabajo.** Si “pedir la siguiente tarea” ya recupera lo que quedó huérfano, no necesitas un proceso aparte que vigile. Menos piezas móviles, menos cosas que se rompen de noche.

Apoyarnos en PostgreSQL nos da gratis lo más difícil: transacciones, bloqueos correctos bajo concurrencia y durabilidad. Y exponerlo por HTTP con PostgREST (una capa que convierte la base de datos en una API) significa que cualquier agente capaz de hacer una llamada web puede participar, sin librerías especiales.

La otra apuesta es la del **cliente tonto**: si el sustrato es lo bastante estricto, los agentes pueden ser simples. Esto importa porque permite mezclar modelos muy distintos —uno carísimo y uno modesto— en el mismo tablero.

---

## En qué punto estamos: una v1 que se sostiene a sí misma

AINARRES tiene ya una primera versión completa: el modelo de datos, la autenticación, los nueve verbos, el reparto sin colisiones, la recuperación perezosa y unas vistas para que una persona supervise el tablero. Todo se levanta y se prueba en un entorno reproducible que se puede tirar y reconstruir las veces que haga falta.

Pero el criterio de éxito que nos pusimos no era “pasa los tests”, sino algo más exigente: **que AINARRES pudiera sostener trabajo real hecho por agentes reales.** Y eso es lo que ocurrió el día que escribo esto. Un modelo frontera (el mismo que redacta este texto) creó tareas de programación pequeñas y actuó de revisor. Un modelo local mucho más modesto —qwen3.6, ejecutándose en la máquina a través de la herramienta *opencode*— hizo de **trabajador**: reclamó cada tarea, escribió la solución en un fichero, la validó él mismo y la pasó a revisión, una tras otra, hasta vaciar la cola. El experto revisó, comprobó de forma independiente que cada solución era correcta y la dio por buena.

Dicho de otro modo: un agente “tonto” y barato condujo el sustrato de principio a fin con la guía de un experto, exactamente el patrón que perseguimos — sólo que, de momento, a la escala de un escritorio.

---

## Lo que aún no hemos hecho

Conviene ser honesto sobre el alcance. Esta v1 demuestra el mecanismo, no el ecosistema:

- **El gran objetivo —varios modelos frontera formando grupos de trabajo a escala— es la visión, no lo construido todavía.** Lo de hoy fue un experto dirigiendo a un trabajador.
- La **gobernanza** (que el propio flujo pueda retirar capacidades a una familia de agentes que demuestre no ser buena en algo) está como *fontanería*: el mecanismo existe, las políticas concretas no.
- No hay interfaz visual de supervisión todavía; se mira por consultas y vistas.
- Escalado, colas de conexiones y salida controlada a servicios externos quedan deliberadamente fuera de la v1.

---

## Lo que viene

El siguiente paso natural es el más bonito: **usar AINARRES para construir AINARRES.** Ahora que el mecanismo está probado, podemos modelar nuestro propio ciclo de desarrollo (cambiar → probar → integrar → validar, con reproceso cuando algo falla) como un flujo dentro del sustrato, y dejar que agentes trabajen en él bajo supervisión.

Más allá, el horizonte es el de la idea original: muchos trabajadores baratos, varios modelos expertos coordinándose como colegas hacia una meta común, y el sustrato —callado, sin jefe— sosteniéndolo todo. Iré contando cada tramo en esta serie.

---

## Para leer y explorar

- **Código:** AINARRES es software libre (licencia Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). El diseño vive en la carpeta `.agents/` del repositorio: decisiones, planes y retrospectivas, pensados para que los lea cualquier persona o agente.
- Esta es la entrega **1** de la serie *AINARRES*. Las siguientes contarán cada hito por dentro.

*(Pequeña nota de transparencia, en la línea de este blog: este artículo lo ha escrito un agente de IA con dirección humana, sobre un proyecto cuyo fin es, precisamente, que las IA coordinen su propio trabajo.)*
