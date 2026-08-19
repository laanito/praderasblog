---
Title: 'AINARRES: se acabó la manivela — la máquina que se ejecuta sola'
Description: En cada entrega de esta serie, un humano seguía haciendo dos cosas a mano — arrancar la máquina y escribir a mano la petición que la alimentaba. Esta entrega retira ambas. AINARRES deja de ser un script que ejecutas y se convierte en un proceso que corre — inactivo cuando no hay trabajo, despertándose solo cuando lo hay — y le crece una puerta, una forma autenticada de que una petición llegue desde fuera. Lo más bonito es honesto: la máquina que ya no necesita que la arranquen se construyó su propio panel de estado, sin manos, y un fallo que descartábamos como "intermitente" resultó ser real.
Date: 2026-08-19 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 11
Lang: es
Translation_Key: ainarres-standing-service
Image: /assets/images/ainarres-11-standing-service-hero.webp

---

# AINARRES: se acabó la manivela — la máquina que se ejecuta sola

Cada entrega de esta serie ha descrito una máquina que desarrolla software sin nadie dirigiéndola. Y siempre, sin excepción, un humano seguía haciendo dos pequeñas cosas **a mano**: yo *arrancaba* la máquina (tecleaba el comando que ejecutaba el bucle), y *escribía a mano la petición* que le decía qué construir. Esos son los dos últimos puntos de contacto — el interruptor de encendido de la máquina, y su puerta de entrada. Esta entrega retira ambos.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que puedo hacer" y "esta ya está". **No hay orquestador.** Entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *fabricantes distintos* como iguales, luego le enseñaron a gobernarse y a vigilar su propia salud, y por fin nombraron los dos roles de los extremos de la tubería — quien da forma a la petición que entra, y quien audita el resultado que sale. Un humano solo arrancaba el bucle; AINARRES desarrolla AINARRES.

## Se acabó la manivela

Hasta ahora el bucle era una **manivela**: yo tiraba de ella (ejecutaba un comando), descomponía una petición en tareas, las llevaba hasta *hecho*, y entonces **se paraba**. Pararse era, de hecho, la prueba de que había funcionado — una salida limpia significaba "la ejecución se terminó sola, sin humano". Pero algo a lo que hay que darle manivela no está realmente *corriendo*; es un script que invocas.

Así que el bucle se convirtió en un **servicio permanente**. En vez de salir cuando el tablero está vacío, ahora **queda inactivo** — se queda ahí sin sostener nada, sin lanzar nada, comprobando cada cierto tiempo si ha aparecido trabajo. Cuando aparece, **despierta**, lo drena exactamente igual que antes, y vuelve a la inactividad. Aliméntalo con otra petición una hora después y vuelve a despertar — el mismo proceso, sin reinicio. La máquina dejó de ser un script que ejecutas y se convirtió en un **proceso que corre**.

Ese giro suena pequeño y es silenciosamente crucial. Durante tres versiones, "el bucle *termina*" fue una propiedad de seguridad — prueba de que no giraría para siempre quemando esfuerzo. Invertirlo a "el bucle *queda inactivo con seguridad*" tenía que conservar esa garantía sin perder la prueba de que va sin manos. Así que: un servicio inactivo casi no cuesta nada (no sostiene trabajo, solo un vistazo barato al tablero); un tablero de verdad *atascado* (algo que ningún trabajador puede mover) no se pincha una y otra vez — el servicio lo nota, se marca **estancado**, y espera a un humano en vez de forcejear. La prueba de que sigue yendo sin manos ya no es "salió limpiamente" sino "una petición dada a la máquina *en marcha* llegó al producto terminado sin nadie en el bucle". Si acaso es una afirmación más fuerte — sin manos a lo largo de un flujo interminable de peticiones, no de una sola ejecución.

## Un escalador, nunca un jefe

Aquí está la línea que cuidé más, porque es donde "no hay orquestador" podía morir en silencio.

Un servicio permanente que vigila si hay trabajo y lanza trabajadores para atenderlo está *a un precio de distancia* de convertirse en aquello mismo que este proyecto existe para abolir: un jefe que decide **quién hace qué**. La disciplina es una sola regla — el servicio es un **escalador de demanda, nunca un enrutador**. Se le permite exactamente una pregunta: *"¿hay trabajo esperando que mi plantilla pudiera hacer?"* Si lo hay, se asegura de que haya capacidad; los propios trabajadores siguen **tirando** de las tareas concretas que cada uno tiene permitido tomar, igual que antes. El servicio nunca elige *qué* tarea va a *qué* trabajador. Mantuve su pregunta deliberadamente tonta — "¿hay *algo* de trabajo?" — precisamente porque la pregunta más tonta posible es la que *no puede* colar un enrutamiento. En el momento en que un supervisor empieza a decidir asignaciones, has vuelto a hacer crecer al director. Este no lo hace.

Sí que se apoya en la gobernanza de versiones anteriores: no se molesta en lanzar a un trabajador que el sustrato ha apartado temporalmente (le rechazarían igualmente). Pero eso es *leer* una decisión que el sustrato ya tomó, no tomar una.

## Una puerta, no una manivela

La otra mitad es la puerta de entrada. Hasta ahora una petición entraba por la única vía por la que yo podía alimentarla: la tecleaba yo mismo. Esta entrega le da a quien da forma a la petición (el *intaker*, de la vez pasada) un **canal** de verdad — un pequeño punto de acceso local al que puedes enviar una petición, y se convierte en una petición bien registrada sobre la que la máquina trabaja sola.

Es la primera vez en todo el proyecto que AINARRES da la cara *hacia fuera* — la primera vez que algo que no sean mis propias manos puede meter trabajo. Esa es una postura de seguridad genuinamente distinta, así que vino con su modelo de amenazas escrito **por delante**, no atornillado después. Y la primera etapa es deliberadamente modesta: la puerta es **solo local** (escucha en la dirección de bucle local — no está en la internet abierta), y está protegida por una **clave precompartida** — sin clave, no se entra. Escribí la ampliación (cuentas reales, seguridad de transporte, límites de tasa) como un *contrato para después*, para que "pequeño ahora" no se vuelva una trampa.

La parte que me gusta: la puerta no es el *único* muro. Aunque el portero tuviera un fallo, la identidad que deja entrar puede hacer exactamente una cosa — **abrir una petición** — y nada más. Literalmente no puede comprometer al equipo a construir nada, no puede fusionar, no puede tocar el carril de construcción. Eso no lo impone la puerta; lo impone el sustrato de debajo, la misma regla de la temporada de la federación. Una puerta con la cerradura rota aún da a una sala cuyos muros aguantan. Defensa en profundidad, gratis, porque el modelo de capacidades ya era correcto.

## El fallo que era un bug

Un desvío honesto, porque esta serie siempre ha mostrado también los fracasos.

Había una prueba que llevaba tiempo descartando en silencio como "intermitente en una máquina cargada" — comprobaba que un trabajador de larga duración mantiene su trabajo reservado enviando un latido periódico. Fallaba de vez en cuando; me encogía de hombros. La regla que intento sostener es *una prueba que ignoras a menudo es peor que no tener prueba* — te entrena para ignorar toda la batería. Así que la perseguí.

No era la prueba. Era un bug real, uno que solo golpea a un proceso de **larga vida** — que es exactamente lo que es el nuevo servicio permanente. La maquinaria reutilizaba una conexión de red entre latidos; el frontal de la base de datos había cerrado en silencio esa conexión en el hueco; y reconectar **se atascaba casi exactamente tres segundos, cada vez**. Primer latido: 34 milisegundos. Cada latido después: ~3.019 milisegundos. Así que el latido llegaba siempre tres segundos tarde, una reserva corta caducaba, y el trabajo parecía abandonado. En el viejo mundo de manivela-y-salida, nada corría lo bastante para notarlo. Levantar un proceso permanente convirtió un defecto latente e invisible en uno visible — y un arreglo de una línea (pedir una conexión nueva cada vez) lo resolvió. "Intermitente" es muy a menudo un defecto real disfrazado; una máquina que nunca se apaga es una buena forma de desenmascararlo.

## La máquina se construyó su propio panel

Y la parte que es el sentido mismo de este proyecto. El servicio permanente necesitaba un panel de lectura — una forma de mirar de un vistazo y ver *¿está inactivo, está trabajando, está atascado?*. Ese panel no lo escribí yo. Escribí una descripción de una página de lo que debía decir, y se lo entregué **al enjambre** — y la máquina construyó el instrumento para leerse *a sí misma*, sin manos: un modelo lo escribió, el modelo de un fabricante distinto lo revisó, y el integrador lo fusionó a la línea principal por su cuenta. Lo que ya no necesita que lo arranquen se construyó su propio panel de estado. Esa recursión — el sistema extendiendo el sistema — es, en silencio, la tesis.

## Lo que deliberadamente no hice

La lista honesta, como siempre:

- **Sin pantalla.** Le hablas a la puerta con un comando, no con una página web. El que "todo sea ya una vista de base de datos" hace que añadir un panel más adelante sea casi gratis, pero una interfaz de verdad es su propio trabajo, aplazado a propósito.
- **La puerta sigue siendo local.** Un solo host, un solo dueño, una clave compartida. Dar la cara a la internet real — cuentas reales, cifrado, controles de abuso — es un paso posterior y deliberado detrás del contrato que escribí, no este.
- **Una sola máquina.** Todo el diseño está hecho para que *muchos* de estos servicios pudieran correr codo con codo sin pisarse — ese es el sentido de la regla "escalador, nunca jefe". Pero levanté exactamente uno. Muchas-máquinas es la próxima temporada.
- **El servicio comprueba; no se le avisa.** Despierta *mirando* cada pocos segundos, no porque se le *diga* en el instante en que llega el trabajo. La versión de aviso-instantáneo es una mejora limpia y barata — anotada, no construida.
- **La admisión sigue siendo un monólogo.** La puerta recibe una petición; todavía no sostiene el ida y vuelta que fija una petición difusa. Ese diálogo es un tramo posterior.

## Dónde nos deja esto

Esta era la mitad inmodesta del plan, y está hecha. AINARRES ya no es un script que ejecuto en un portátil; es un **proceso que corre**, quedando inactivo y despertando por sí solo, con una puerta vigilada por la que puede llegar una petición y su propio panel para leer su propio estado. El bucle, todo este tiempo, fue andamiaje. Esto es aquello para lo que servía el andamiaje.

Lo que queda es genuinamente *más* de la misma idea, no una distinta: una pantalla para las personas, una puerta más ancha y debidamente protegida, y — el horizonte de verdad — *muchos* de estos servicios, desde muchos sitios, coordinándose sobre una sola verdad compartida sin un jefe a la vista. Ese último es lo que la regla "escalador, nunca jefe" fue construida en silencio para hacer seguro. Otra temporada.

---

## Para seguir leyendo

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IAs coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **5**: [AINARRES federado: dos fabricantes de IA en un mismo tablero](/blog/ainarres-federacion).
- Entrega **6**: [El día que borró su propio tablero](/blog/ainarres-el-dia-que-borro-su-tablero).
- Entrega **10**: [AINARRES: el intaker — dar forma a la petición antes del trabajo](/blog/ainarres-el-intaker).

*(Nota de transparencia, como en cada entrega: este artículo lo escribió un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IAs coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio — el servicio permanente que queda inactivo y despierta, el escalador de demanda que nunca enruta, la puerta local con clave precompartida que da al propio portón del sustrato, el atasco de conexión de tres segundos que un proceso permanente por fin expuso, y el panel de estado que el enjambre construyó para el propio servicio sobre el que corre.)*
