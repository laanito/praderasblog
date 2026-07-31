---
Title: AINARRES: el segundo sentido del auditor — vigilar salud y gasto
Description: El auditor del proyecto juzgaba una sola cosa — si una entrega cumplía su encargo. Esta entrega le da un segundo sentido: vigilar la salud operativa de la flota y su gasto en tokens, para que un trabajador atascado o uno silenciosamente caro se le presenten a una persona. Y cuenta la historia honesta de la ejecución que trajo esta función — que primero se atascó cuando un modelo fue retirado de la noche a la mañana, y luego se terminó sola, sin manos, en cuanto se arregló el arnés.
Date: 2026-07-31 11:30PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 9
Lang: es
Translation_Key: ainarres-operational-facet
Image: /assets/images/ainarres-09-second-sense-hero.webp

---

# AINARRES: el segundo sentido del auditor — vigilar salud y gasto

El último hito de verdad le dio al proyecto un **auditor** — un rol en manos humanas que revisa lo único que una regla no debería juzgar sola: si una entrega cumplió de verdad la petición para la que se construyó. Ese es el sentido de *calidad* del auditor. Esta entrega le da un **segundo sentido**, uno *operativo*: vigilar si la máquina está sana, y si alguien está quemando combustible para nada.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo es datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que se me permite hacer" y "esta ya está". **No hay orquestador.** Las entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *distintos fabricantes* como iguales, y luego le enseñaron a autogobernarse — a retirarle una capacidad a un trabajador que demuestra hacer mal su trabajo. Una persona solo arranca el bucle; AINARRES desarrolla AINARRES.

El interludio anterior terminó con un suspense. Había ampliado el enjambre a una flota de modelos, y un modelo barato se había quedado **girando** — dio vueltas diez minutos pidiendo una herramienta que no tenía, no produjo nada, y *nada rebotó*. Ningún revisor lo rechazó; ningún contador se movió. Prometí que ese fallo volvería como el argumento para la próxima función. Aquí está.

---

## Dos formas de malgastar esfuerzo — y por qué necesitan capturas distintas

Diseñar la vigilancia del gasto me obligó a una corrección que conviene decir claramente, porque cambió lo que construí. Hay dos formas en que un trabajador de IA malgasta esfuerzo, y **no** se atrapan igual:

- Un **trabajador que gira** da vueltas sin producir nada. Y lo clave: nunca *avanza* una tarea — así que el sistema no le registra gasto alguno (el gasto se anota cuando un trabajador mueve una tarea hacia adelante; uno que nunca la mueve no deja rastro). Una señal de gasto, por sí sola, es **ciega** ante un trabajador que solo gira.
- Un **sobregastador** sí entrega — simplemente quema, pongamos, cincuenta veces los tokens que sus pares para el mismo resultado. Como entrega, su coste *sí* queda registrado. A este una señal de gasto lo atrapa limpiamente.

Así que la foto honesta es un reparto de tareas, no una única señal mágica. El interludio decía "una señal de gasto es exactamente lo que atrapa al que gira" — eso es *aspiracionalmente* cierto (un modelo que gira sí quema tokens) pero *mecánicamente* falso, y la diferencia importa. La captura del que gira es una vigilancia de **salud**: una tarea tomada sin progreso, un atasco, una tarea abandonada. La captura del sobregastador es la vigilancia del **gasto**. Juntas cubren los fallos operativos que ninguna revisión por tarea puede ver — el punto ciego exacto que el modelo que giró dejó al descubierto.

---

## La vigilancia es tonta; el aviso es el juicio

La regla que sostuvo toda la temporada de gobernanza se traslada directa: **el sistema mide, una persona decide.** Así que esta función se parte limpiamente en dos.

La **vigilancia** es tonta y de solo lectura. Una vista compara el coste-por-entrega de cada trabajador, *para un rol dado*, contra la mediana de sus pares *en ese mismo rol* (comparas revisores con revisores, no con integradores), y saca a la luz a quien pase de un múltiplo ajustable. También saca el caso raro de un trabajador que registró algo de gasto pero no entregó nada. No clasifica a nadie como bueno o malo; solo dice "este es caro". Una segunda vista lista los avisos que una persona ya ha levantado. La salud reutiliza maquinaria que ya existía — la lista de tareas atascadas y abandonadas.

El **aviso** es el juicio registrado del auditor. Leyendo la vigilancia, el auditor en manos humanas anota que un trabajador *gira* o *sobregasta* — una nota en un registro solo-de-adición, y **nada más**. No escribe castigo alguno. Un trabajador señalado sigue trabajando hasta que una persona decide otra cosa. Es deliberado: "¿esto es girar de verdad, o solo una tarea legítimamente difícil?" es un juicio, y los juicios en este sistema viven con una persona, jamás en una regla.

Y una línea se traza más nítida que las demás: **un aviso de sobregasto nunca empuja hacia una prohibición.** Un modelo caro que aprueba es *caro*, no *inepto* — el coste no es la competencia. El informe hasta lo etiqueta así: una preocupación de *girar* se lee como **"revisar"**, un sobregasto se lee como **"coste"**. Una persona todavía puede escalar a prohibición a quien *gira* (un trabajador que no produce nada en un puesto está fallando en él) — pero eso sigue siendo un acto humano, por el mismo camino auditado y exclusivamente humano que construyó la temporada de gobernanza. El gasto nunca puede convertirse en silencio en una prohibición.

---

## La ejecución que trajo esto — y se atascó, y se terminó sola

Aquí viene la parte honesta, porque el blog de este proyecto siempre ha contado la versión desordenada.

La función llegó por el camino de siempre: el núcleo crítico para la confianza — el aviso, el registro, las vistas de vigilancia — lo construí a mano y lo comprobé antes de que corriera en vivo, porque una regla sobre *registrar un juicio contra un trabajador* tiene que estar bien de entrada. La última pieza — el bloque del informe que renderiza todo el conjunto — se la dejé al enjambre para que se la construyera solo, sin manos, validada con una prueba unitaria que no toca la base de datos.

Esa última ejecución **se atascó.** El diseñador la redactó, un implementador barato la construyó, un revisor de otro fabricante la comprobó y la aprobó — y entonces el único trabajador con permiso para *fusionar* se cayó al arrancar. De la noche a la mañana, su modelo había sido **retirado**: el identificador exacto de modelo al que estaba fijado el integrador ya no existía, y la herramienta se negaba a lanzarse. El cambio revisado se quedó ahí, terminado y sin fusionar, sin nadie capaz de aterrizarlo.

Merece la pena señalar dos cosas. Primero, **nada se rompió.** La tarea simplemente esperó en el paso de "lista para fusionar"; el sustrato está construido para que un trabajador ausente *detenga* el progreso, nunca lo *corrompa*. El tablero le contó la verdad a la primera persona que miró. Segundo, **el arreglo fue pequeño y la recuperación limpia.** Apunté el integrador al nombre actual del modelo (y, como este sistema mantiene separado el historial de cada modelo, traté al nuevo modelo como una identidad de trabajador genuinamente nueva — un renombrado, no un cambiazo silencioso). Luego re-lancé el bucle. Se dio cuenta de que la tarea ya estaba casi terminada, *saltó directo a donde se había quedado*, y el integrador ya funcional fusionó el cambio por su cuenta. Ninguna persona hizo la fusión. La propiedad de un-único-integrador que estableció la temporada de federación se mantuvo de principio a fin.

Y luego, la recompensa. La primerísima vez que el informe terminado se renderizó sobre datos reales de la flota, el nuevo bloque operativo **atrapó algo**:

> operativo (vigilancia de salud y gasto):
> — el par de revisión, como revisor: sobregasto — ~15× la mediana de sus pares (coste)

Eso es la función funcionando exactamente como se diseñó. Un revisor había gastado unas quince veces la mediana de sus pares para el mismo tipo de trabajo. No estaba *equivocado* — es un modelo de razonamiento pesado, y quizá esa minuciosidad merezca la pena. Pero es **caro**, la vigilancia lo dijo con todas las letras, lo etiquetó *coste* (no prohibición), y dejó la decisión a una persona. El segundo sentido del auditor abrió los ojos y de inmediato vio algo real.

---

## Lo que deliberadamente no he hecho

La lista honesta, como siempre:

- **La vigilancia del gasto necesita pares para significar algo.** Comparar a un trabajador con la mediana de su rol no dice nada cuando solo *un* trabajador ocupa ese rol — que es a menudo la realidad ahora mismo (un modelo hace casi todo el trabajo de implementar; los demás son seguro ocioso). Un rol de un solo trabajador no produce anomalía, correctamente y a propósito, en vez de una falsa alarma. La vigilancia se gana el jornal solo cuando la flota se ejercita de verdad.
- **El gasto de un trabajador todavía se mide de forma tosca.** El modelo de frontera desempeña varios roles en una misma sesión de trabajo, y el sistema atribuye su gasto solo a uno de ellos — así que como integrador todavía marca "desconocido". Honesto, documentado y arreglable más adelante.
- **El juicio operativo es subjetivo y sin puntuar.** El aviso registra una nota en lenguaje llano; "girar frente a tarea difícil" es la decisión de la persona. Un detector automático de giros que *proponga* avisos es un refinamiento posterior, no este.
- **Un trabajador que gira en silencio todavía se puede escapar.** La vigilancia de salud se apoya en atascos y tareas abandonadas; uno que gira *y* suelta su tarea limpiamente dentro de su ventana de tiempo deja poco rastro. Un latido de progreso más fino es trabajo futuro.

---

## Dónde queda esto

Esto asienta el **remate de atrás** — el rol que vigila la entrega y la salud de la flota *después* del trabajo, ahora con el sentido del gasto que el interludio reclamaba. Todavía se ejercita a mano, en el mismo bucle que arranco en un portátil; la versión siempre-encendida llega más tarde.

Lo que viene es el **remate de delante**: el *intaker*, el rol que da forma a una petición cruda hasta un encargo bien formado *antes* de que empiece el trabajo. Nombra los dos extremos, y toda la cadena — desde la persona con una petición, pasando por los trabajadores, hasta el auditor que revisa el resultado — queda nombrada en el sustrato de punta a punta. Y luego, el horizonte hacia el que este proyecto lleva construyendo desde la primera entrega: convertir AINARRES de un script que arranco en un portátil en un **servicio permanente** — siempre encendido, alimentado por un canal real, vigilado por estos roles en vez de por mí. El bucle siempre fue andamiaje. El servicio es lo de verdad.

---

## Para leer y explorar

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **2**: [AINARRES se construye a sí mismo](/blog/ainarres-se-construye-a-si-mismo).
- Entrega **3**: [AINARRES corre sin director](/blog/ainarres-corre-sin-director).
- Entrega **4**: [AINARRES: el enjambre](/blog/ainarres-en-enjambre).
- Entrega **5**: [AINARRES federado: dos fabricantes de IA en un mismo tablero](/blog/ainarres-federacion).
- Entrega **6**: [El día que el enjambre borró su propio tablero](/blog/ainarres-el-dia-que-borro-su-tablero).
- Entrega **7**: [AINARRES y el auditor: ¿construimos lo correcto?](/blog/ainarres-el-auditor).
- Entrega **8**: [AINARRES: un enjambre más amplio, y el trabajador que se quedó girando](/blog/ainarres-un-enjambre-mas-amplio).

*(Nota de transparencia, como en cada entrega: este artículo lo escribió un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IA coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio — los dos modos de fallo y sus dos capturas distintas, la vigilancia que mide mientras una persona decide, la retirada del modelo de la noche a la mañana que atascó la ejecución, la reanudación sin manos que fusionó el cambio sin que nadie hiciera la fusión, y la primerísima lectura real de la vigilancia del gasto señalando a un par de revisión a unas quince veces el coste de sus pares.)*
