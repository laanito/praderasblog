---
Title: 'AINARRES: despertar solo a quien hace falta'
Description: La máquina que se ejecuta sola tenía una costumbre cara — cuando había cualquier tarea esperando, arrancaba a todos sus trabajadores, y la mayoría no encontraba nada que hacer. Esta entrega le enseña al sustrato a informar de lo que hay esperando en términos de capacidades, para que el servicio despierte exactamente a quien puede atenderlo. Lo interesante no fue el ahorro. Fue lo que pasó cuando la máquina encontró una capacidad que nadie tiene — y, con toda la razón, me aconsejó contratar a alguien para un trabajo que yo me había reservado a propósito.
Date: 2026-08-28 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 12
Lang: es
Translation_Key: ainarres-demand-shaped
Image: /assets/images/ainarres-12-demand-shaped-hero.webp

---

# AINARRES: despertar solo a quien hace falta

La entrega anterior retiró la manivela: AINARRES dejó de ser un script que yo ejecutaba y se convirtió en un proceso que corre, inactivo cuando el tablero está vacío y despertándose cuando aparece trabajo. Funcionó. También tenía una costumbre cara que la inactividad ocultaba por completo: **cuando despertaba, despertaba a todos.**

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que puedo hacer" y "esta ya está". **No hay orquestador.** Entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *fabricantes distintos* como iguales, luego le enseñaron a gobernarse y a vigilar su propia salud, nombraron los roles de los dos extremos de la tubería, y por fin convirtieron el bucle en un servicio permanente con una puerta autenticada. Ningún humano arranca nada por cada funcionalidad; AINARRES desarrolla AINARRES.

## La factura que nadie estaba leyendo

Un servicio inactivo cuesta casi nada. Despertarlo cuesta dinero de verdad, y el despertar antiguo era indiscriminado: el servicio le hacía al tablero una sola pregunta — *¿hay algo de trabajo tomable?* — y si la respuesta era sí, arrancaba a **todos los trabajadores configurados**. Una única tarea esperando en *revisión* levantaba la piscina entera de implementadores: un proceso y una sesión de modelo cada uno, para trabajadores que mirarían el tablero, no encontrarían nada que les esté permitido tocar, y saldrían.

Lo vi ocurrir dos veces seguidas en una ejecución real la semana pasada, y solo entonces se me hizo evidente la forma del desperdicio. El tablero tenía exactamente una tarea. El servicio arrancó seis trabajadores. Cinco no tenían nada que hacer y lo hicieron perfectamente.

Esa es la compuerta más basta posible, y fue el primer corte correcto: la pregunta más tonta es la que *no puede* colar una decisión de enrutamiento, que es justo la propiedad que este proyecto entero existe para proteger. Pero lo basto es caro, y el coste se paga en tokens — el único recurso que esta máquina consume en cantidad.

## Demanda, en términos de capacidades

El arreglo tenía que pasar por el ojo de una aguja. Para despertar a menos trabajadores, algo tiene que saber quién *podría* atender el trabajo que espera. Y eso suena exactamente al comienzo de un despachador, que es precisamente lo que llevo doce entregas negándome a construir.

La salida está en darse cuenta de que el sustrato ya sabe la respuesta, y en hacer que diga solo eso. Una tarea la puede mover quien tenga un conjunto concreto de capacidades — eso no es una idea nueva que añadir, es el mismo test que el sustrato ya aplica cuando un agente pide trabajo. Así que al sustrato le creció una vista de solo lectura que informa del trabajo pendiente como **paquetes de capacidades con recuentos**:

```
bundle                                             pending
{lane:dev, role:designer}                                3
{lane:dev, role:implementer}                             1
{lane:dev, role:reviewer}                                2
{capability:integrate, lane:dev, role:integrator}        1
```

Léelo con cuidado, porque lo que *falta* es el punto. Ningún identificador de tarea. Ninguna prioridad. Ningún contenido. Ningún nombre de trabajador, ninguna mención a un "nivel", ninguna idea de que exista un servicio. El sustrato dice "hay esta cantidad de cosas pendientes que necesitan a alguien con al menos estas capacidades" — y nada más.

El servicio hace luego el emparejamiento **contra su propia configuración**, que es donde el conocimiento sobre trabajadores ha vivido siempre: sabe qué arnés ejecuta qué rol, porque ese es su fichero de configuración. Un trabajador arranca si algún paquete demandado cabe dentro de las capacidades que declara, y está vivo, y la gobernanza no lo ha suspendido. Tres condiciones, una sola compuerta.

Lo bonito es la limpieza con que los paquetes aterrizan sobre la plantilla. La última línea de arriba — la que necesita la capacidad de integrar — encaja en exactamente una familia, el único integrador que siempre ha sido la cola de fusión. Nadie tuvo que codificar eso. Sale de lo que cada participante declara que sabe hacer.

La piscina también se dimensiona a la demanda: una tarea pendiente arranca un implementador en vez de tres. El techo no cambia; solo se movió el suelo.

Y cada pieza degrada al comportamiento antiguo. Si la vista de demanda no se puede leer, todo trabajador vivo cuenta como demandado. Si no se puede sondear el backend de un trabajador, cuenta como vivo. Una caída en una optimización debe cambiar tu *factura*, nunca tu *corrección* — así que ambos fallos colapsan a "despierta a todos", que es simplemente caro.

## La capacidad que nadie tiene, a propósito

Aquí viene la parte que no vi venir, y es la razón por la que esta entrega merece leerse.

En el momento en que la compuerta entró en servicio, la máquina informó de que no podía atender un trabajo pendiente, y me dijo qué hacer al respecto:

```
⚠ unserviceable: 1 task(s) need {lane:intake, role:intaker}
  — no configured family provides it; seat one
```

Tenía razón en los hechos y se equivocaba de la peor manera posible. Ese pendiente era una petición cruda que había llegado por la puerta — y la capacidad de convertir una petición cruda en un encargo trabajable es una que **ningún trabajador tiene, deliberadamente**. Dos entregas atrás le puse nombre a ese trabajo y lo reservé para una persona, a propósito: el sustrato se lo niega a todos los agentes, así que una petición sin dar forma es *invisible* para el enjambre hasta que un humano la ha moldeado. Eso no es un hueco en la plantilla. Eso es la frontera.

Así que la máquina, razonando correctamente a partir de lo que podía ver, me aconsejó desmantelar lo único que separa "un enjambre que construye lo que le pido" de "un enjambre que decide qué pedir".

El arreglo es pequeño y la lección no. La configuración ahora *declara* qué capacidades son de tenencia humana — dar forma a una petición, y la auditoría cualitativa del otro extremo — y la demanda de esas se lee distinto:

```
⚠ awaiting a human: 1 task(s) need {lane:intake, role:intaker}
  — this capability is human-held by design; a person must act
```

El mismo hecho, la instrucción opuesta. Y salió un beneficio de segundo orden: el servicio antes gastaba un ciclo de despertar completo en descubrir que una petición sin forma era intrabajable. Ahora lo lee del informe de demanda y no arranca nada — nombra aquello que está esperando, que resulta ser yo, y aguanta.

No dejo de encontrarme con que la versión honesta de una funcionalidad es más barata que la lista. Una máquina que sabe cuáles de sus huecos *deben* ser huecos no gasta nada intentando rellenarlos.

## Delegar el terminal, no las llaves

En paralelo a esto iba otra pregunta: ¿podría un agente ocupar el asiento en el que llevo yo sentado? No hacer el trabajo — el trabajo lo hace el enjambre — sino *operar la instancia*. Vigilar el tablero, sentar y retirar modelos, desatascar cosas, arrancar y parar el servicio.

Escribir ese asiento como documento, en vez de como costumbre, dejó a la vista algo incómodo de inmediato. Todos los demás actores de este sistema están modelados: los trabajadores son familias con capacidades declaradas, el integrador es la única familia con confianza para publicar, los dos roles de los extremos tienen sus propias características. El *operador* no estaba modelado en ningún sitio, porque el operador siempre había sido yo, y yo tengo la clave de firma. Un agente puesto en ese asiento hereda la clave — y con ella la capacidad de levantar cualquier suspensión que el sustrato le haya puesto a cualquiera, concederse a sí mismo la capacidad de fusionar, o actuar como cualquier familia. Todas las fronteras que construyó el trabajo de gobernanza se aplican contra los trabajadores y son absolutamente vacías contra el operador.

Así que la decisión, escrita: **delegar el terminal, no las llaves.** Tres cosas se quedan con una persona — la custodia de la clave de firma, los dos actos irreversibles de gobernanza (una prohibición permanente y su levantamiento), y la auditoría cualitativa. Todo lo demás se puede entregar.

El primer ladrillo fue más pequeño de lo esperado y más interesante. "Dale al agente un token de solo lectura" resultó no existir: el rol que venía usando para supervisión tiene permiso para invocar los verbos de prohibición permanente y de levantarla. Lee todo, y además puede hacer las dos cosas que acababa de reservarme. Delegar solo la observación, con ese token, es solo-observación por *instrucción* — una frase en un documento — y no por estructura.

Ahora hay un cuarto rol de base de datos que lee todas las vistas de supervisión y no puede invocar **nada**. Su prueba es la parte que me gusta: la misma identidad, la misma lista vacía de capacidades, solo cambia el rol — y donde una alcanza el cuerpo del verbo de prohibición permanente, la otra es rechazada en la puerta. La frontera es el rol, así que el asiento no puede ensancharse a sí mismo pidiendo más capacidades.

Lo otro que necesita un vigilante delegado es un detector para el fallo que un humano habría notado por instinto. Los arrendamientos cubren al trabajador que muere — la reclamación caduca y otro toma la tarea. No cubren al trabajador que está *vivo y no va a ningún sitio*: sostiene una reclamación viva, la renueva, y el tablero parece ocupado para siempre. Conmigo en el terminal eso es una curiosidad en el informe diario. Sin nadie leyendo, es el fallo silencioso. Así que el sustrato ahora informa también de esos, en dos sabores: sostenido-y-callado, y renovando-la-reclamación-sin-progresar-nunca. La segunda firma es la interesante — la renovación es invisible en el registro de eventos, pero la aritmética sobre el arrendamiento la delata.

## Tres formas de parecer muerto

La sección de honestidad de esta entrega es buena, porque los tres hallazgos son de la misma especie: cosas que fallan *en silencio*.

**El paso de diseño había sido un revisor durante una versión entera.** El envoltorio que ejecuta el modelo de diseño elegía sus instrucciones por inferencia: si le pasaban un fichero de encargo, actúa como diseñador; si no, actúa como revisor. Razonable, hasta que el servicio permanente introdujo un paso de diseño que corre *sin* fichero de encargo, barriendo el tablero de forma continua. Ese paso recibía las instrucciones de revisor, mientras sostenía solo capacidades de diseñador. Nunca descompuso nada. Había incluso un comentario en el código describiendo alegremente un comportamiento que no existía. Nada falló; simplemente faltaba una capacidad entera, y las pruebas no cubrían la forma porque la forma era nueva. Los modos ahora se declaran, no se infieren.

**Un tablero atascado mataba al supervisor.** El diseño dice: cuando una ronda completa no mueve nada, aguanta — márcate estancado y espera a un humano, en vez de forcejear. La implementación en cambio *salía*, por una sola línea donde el retorno no-cero de una función se propagaba fuera del bucle bajo una opción estricta del shell. Nunca nada había llevado el tablero a un atasco genuino, así que nada había ejercitado ese camino. Un proceso siempre-encendido que muere cuando el tablero se atasca es justo el fallo que no notas: ni informe de caída, ni alerta, solo que no pasa nada.

**Y luego el arnés de pruebas le disparó al paciente.** Este costó horas. Mientras probaba la nueva compuerta, el supervisor desaparecía a mitad de ejecución — sin error, sin señal, con su fichero de estado congelado en el último latido bueno, que se lee *exactamente* como un cuelgue. No se estaba colgando. Lo mataba mi propia prueba: bash ejecuta un *trap* de salida **dentro de una subshell que muere**, y una de mis comprobaciones canalizaba la salida hacia un `grep -q`, que sale en la primera coincidencia y se lleva por delante al escritor con una tubería rota. El trap se disparó en esa subshell moribunda, ejecutó la limpieza que mata al supervisor, y el script principal siguió como si nada. Así que la comprobación pasó, y una fase posterior falló noventa segundos después contra un cadáver.

Quiero nombrar la lección general, porque es la que me llevo: **haz imposible confundir "muerto" con "no está de acuerdo".** Cuando ahora falla una prueba, imprime el pid del supervisor, su estado, y la edad de su último latido. Dos minutos de diseño de salida me habrían ahorrado casi toda esa tarde.

## Lo que deliberadamente no hice

- **Sin despertar por empuje.** El servicio sigue consultando a intervalos; la notificación de base de datos que lo despierta en el instante en que aparece trabajo está diseñada y sin construir. Eso es latencia, no coste, y el coste era el problema que tenía delante.
- **Sin sobre de credenciales todavía.** El rol de solo lectura existe; la maquinaria que le entrega al asiento de operador credenciales de vida corta que no puede ensanchar, no. Hasta que exista, delegar más allá de la lectura es *confiar*, y prefiero decirlo claro que disimularlo.
- **Sigue sin haber enrutador.** El servicio decide qué *tipos* de trabajador vivo arrancar. Nunca decide qué tarea va a quién, y nunca lee el contenido de una tarea para preferir una familia capaz sobre otra. Usar datos de coste para elegir modelo por tarea sería un despachador, y se queda fuera.
- **El auditor sigue siendo humano.** De hecho la decisión sobre el operador lo empuja más lejos: el asiento que opera la máquina y el asiento que juzga cómo lo hizo deben ser identidades distintas.

## Dónde nos deja esto

La máquina ahora despierta solo a los trabajadores que el trabajo esperando necesita, dimensiona su piscina al tamaño de la cola, se da cuenta cuando el backend de un modelo ha muerto en silencio, y — cuando no puede atender algo — dice qué capacidad falta y si eso significa *sienta a una familia*, *levanta un backend*, o *esto te está esperando a ti*.

Esa última rama es la que se me queda dando vueltas. Salí a reducir una factura de tokens, y lo que obtuve fue una máquina capaz de articular la forma de su propia dependencia de mí: no como una limitación que descubrió, sino como una frontera que le dijeron que respetase, y de la que ahora informa. Cada entrega de esta serie ha eliminado un punto de contacto humano. Esta es la primera en la que la máquina empezó a llevar la cuenta de los puntos de contacto que deben quedarse.

---

## Para seguir leyendo

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **6**: [El día que borró su propio tablero](/blog/ainarres-el-dia-que-borro-su-tablero).
- Entrega **10**: [AINARRES: el intaker — dar forma a la petición antes del trabajo](/blog/ainarres-el-intaker).
- Entrega **11**: [AINARRES: se acabó la manivela — la máquina que se ejecuta sola](/blog/ainarres-se-acabo-la-manivela).

*(Nota de transparencia, como en cada entrega: este artículo lo ha escrito un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IA coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio — la vista de demanda en términos de capacidades que no nombra ni tarea ni trabajador, la compuerta que arranca solo lo que el trabajo esperando necesita, las capacidades de tenencia humana de las que la máquina ahora informa en vez de intentar rellenar, el rol de base de datos de solo lectura que lee todo y no puede invocar nada, y la tarde perdida por un arnés de pruebas que mataba al mismísimo proceso que estaba midiendo.)*
