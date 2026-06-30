---
Title: AINARRES en enjambre: cuando un trabajador cae, el trabajo sigue
Description: La cuarta entrega de AINARRES: el salto de hacer una tarea cada vez a muchos agentes trabajando a la vez. Y el momento que mejor lo explica —un trabajador se quedó colgado a mitad de tarea, lo maté, y otro agente terminó el trabajo solo, sin que nadie reasignara nada—.
Date: 2026-07-01 12:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 4
Lang: es
Translation_Key: ainarres-swarm
Image: /assets/images/ainarres-04-swarm-hero.webp

---

# AINARRES en enjambre: cuando un trabajador cae, el trabajo sigue

A mitad de una ejecución pasó algo pequeño que resume toda esta entrega mejor que cualquier diagrama.

Uno de los trabajadores baratos —un modelo de IA de bajo coste, sobre una API gratuita— se quedó **colgado** a mitad de su tarea. La API había alcanzado su límite de peticiones y devolvía un error tras otro. El trabajador no iba a terminar. Así que hice lo único que hace falta hacer con un proceso atascado: lo **maté**.

Y entonces, sin que yo tocara nada más, ocurrió lo interesante. El sustrato se dio cuenta de que esa tarea había quedado **huérfana** —reclamada por alguien que ya no estaba—, la **soltó**, y en la siguiente pasada **otro agente, más capaz, la reclamó y la terminó**. Nadie reasignó la tarea. Nadie dijo "oye, que se ha caído fulano, que la coja mengano". Yo solo maté un proceso colgado; el sistema se encargó del resto.

Ese es, en una frase, el corazón de la cuarta entrega: **un trabajador puede morirse a mitad de tarea y el trabajo igual llega a su destino**. Pero para llegar ahí primero hubo que dar un salto más grande —pasar de *uno cada vez* a *muchos a la vez*— y, como siempre en este proyecto, lo que más enseñó fue el intento que falló.

> Si llegas nuevo: AINARRES (acrónimo en inglés de *AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** —el terreno común sobre el que se coordina el trabajo— hecho con PostgreSQL. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo saben "dame la siguiente tarea que me toca" y "esta ya está". **No hay orquestador**: cada agente *tira* de la cola lo que tiene permiso de hacer. Las tres entregas anteriores construyeron eso y demostraron que un solo agente podía desarrollar el propio AINARRES, sin nadie llevando la batuta.

---

## De tubería a enjambre

Hasta ahora, AINARRES trabajaba como una **tubería**: una tarea cada vez. Un trabajador cogía algo, lo hacía, lo pasaba al siguiente, y solo entonces empezaba lo siguiente. Funcionaba y era *correcto* —la tercera entrega lo demostró— pero era lento por diseño, a propósito, para aislar una variable cada vez.

La cuarta entrega levanta esa restricción. La meta es un **enjambre**: varios agentes independientes trabajando **a la vez** hacia un objetivo común. El diseñador parte el encargo en tareas independientes donde se pueda; un grupo de trabajadores las coge en paralelo; y el resultado se integra de forma que la rama principal **nunca se rompe**.

Eso plantea tres problemas que no existían cuando solo había uno:

1. **Que no se pisen.** Dos agentes editando, haciendo commits y cambiando de rama en la *misma* copia del código se corrompen el trabajo mutuamente.
2. **Que no cojan la misma tarea.** Si tres tiran de la cola a la vez, ninguno debe llevarse lo que ya se llevó otro.
3. **Que la rama principal siga coherente.** Si tres cosas se terminan a la vez, no pueden fusionarse a ciegas unas sobre otras.

La gracia es que el segundo problema **ya estaba resuelto desde el principio**: la base de datos entrega tareas con un cerrojo que garantiza que cada una va a un solo agente, por muchos que tiren a la vez. La serialización —el "uno cada vez"— vivía únicamente en el *lanzador*, por elección. Quitarla fue, sobre todo, dejar de frenar a propósito.

---

## Una habitación limpia para cada trabajador

El primer problema —que no se pisen— se resuelve dándole a cada trabajador su propia **habitación limpia**: su propia copia del código (una *rama de trabajo* de git), montada al instante, donde puede editar y commitear sin que nadie más la vea. Cuando termina, su trabajo vive en una rama propia; la copia desechable se tira.

El tercer problema —la coherencia— se resuelve con un **único integrador**. Mientras muchos *implementan* a la vez, una sola pieza se encarga de **fusionar**, y lo hace de una en una: coge la rama, la **rebobina sobre la última versión de la rama principal**, **vuelve a validar** que sigue verde tras ese rebobinado, y solo entonces la fusiona. Si hay un conflicto que no puede resolver limpiamente, **devuelve la tarea** en vez de forzar una fusión sucia. Esa cola de fusión serializada es justo lo que permite que la implementación corra a lo ancho sin que la rama principal se rompa nunca.

---

## El fallo honesto (que es donde se aprende)

El primer intento de ejecución en enjambre parecía un éxito: tres tareas, tres *pull requests*, todo verde. Pero al mirar el registro de eventos —esa bitácora que la entrega anterior nos enseñó a leer— el enjambre era una ilusión: **un solo trabajador había hecho las tres tareas, una detrás de otra**. No hubo paralelismo.

¿Por qué? Los tres procesos arrancaron, sí, pero la herramienta que usan los trabajadores baratos (*opencode*) guarda su propio estado en una base de datos local… **compartida**. Tres procesos a la vez chocaban contra ese fichero —"base de datos bloqueada"— y dos de los tres morían en el arranque. El que sobrevivía hacía todo el trabajo en serie.

Y aquí está la lección, que es más bonita de lo que parece. El sustrato funcionaba perfectamente; el cerrojo de tareas funcionaba; las habitaciones limpias de git funcionaban. Lo que faltaba era aislar **el estado interno de la propia herramienta**, no solo los ficheros del proyecto. La corrección fue darle a cada trabajador no solo su copia del código, sino **su propia memoria de herramienta**.

Dicho de otro modo: estamos *fingiendo*, en un solo portátil, lo que en realidad serían tres máquinas distintas. Y en tres máquinas distintas cada agente tendría, gratis, su propia copia del código y su propia herramienta. Aislar ambas cosas en un portátil no es un truco sucio: es **simular fielmente** lo que la distribución real te daría sin pedirlo.

---

## Por qué "¿va más rápido?" era la pregunta equivocada

La tentación obvia para medir un enjambre es el cronómetro: ¿tarda menos que la tubería? Pero esa es una mala pregunta cuando, como aquí, estás **imitando un sistema distribuido en un único portátil**. En una sola máquina, el paralelismo real lo limitan cosas que el sustrato no controla: la CPU compartida, una API gratuita que de todas formas atiende las peticiones casi en fila, el estado local de cada herramienta. El cronómetro acaba midiendo el coste de la *imitación*, no la coordinación.

La estrella polar no es "más rápido en este portátil". Es que el sustrato coordine **trabajadores genuinamente independientes que podrían estar en máquinas distintas, redes distintas o universos distintos** —compartiendo nada más que el propio sustrato—. Así que la prueba de fuego no es un número de segundos, sino una pregunta de **corrección**: ¿hubo de verdad varios trabajadores distintos, cada uno aislado, llevando tareas distintas **a la vez**, y llegó todo a una rama principal coherente?

La respuesta fue sí. El tablero en vivo mostró, en un mismo instante, **tres trabajadores distintos sosteniendo tres tareas distintas en marcha**, reclamadas con segundos de diferencia. Las tres se fusionaron a una rama principal verde. AINARRES construyó una funcionalidad real de sí mismo —de varias piezas— con varios agentes a la vez, sin que nadie coordinara. La misma ejecución valdría igual con los trabajadores repartidos por N máquinas.

---

## Y de vuelta al trabajador que cayó

Con todo esto montado, aquel pequeño momento del principio cobra su sentido completo. Un trabajador se cayó —por una flaqueza muy real: el límite de una API gratuita—, y el trabajo **no se perdió ni se atascó**. El sustrato soltó la tarea huérfana y un compañero capaz la terminó. Nadie llevó la batuta.

Es la misma apuesta de siempre, vista desde otro ángulo. En un sistema sin director no puedes confiar en que cada pieza se porte bien: tienes que hacer que **portarse mal —o caerse— sea inofensivo**. Un trabajador que muere no es una emergencia; es un caso previsto. Y un detalle honesto: que yo lo *matara* solo aceleró lo que el sistema hace solo —en una ejecución del todo desatendida, el "alquiler" de la tarea habría caducado por su cuenta y otro la habría recogido igual, sin que nadie tocara nada—.

---

## Lo que aún no hemos hecho (seamos honestos)

- Todo corrió en **un solo portátil**. Imitamos la distribución (cada trabajador con su copia y su herramienta aisladas), pero aún no hay agentes de verdad en máquinas distintas.
- El **integrador lo sigue lanzando una persona**, a propósito: es la frontera de seguridad de la entrega 2 —quien fusiona no puede ser quien dirige—.
- El **revisor es único** y trabaja en serie; con tareas pequeñas, eso domina el reloj. Tiene arreglo (un grupo de revisores) el día que estorbe; la integración, en cambio, seguirá siendo de una en una para no romper la coherencia.
- Las tareas del experimento eran **pequeñas** a propósito, para que el enjambre fuera limpio de medir.
- Y, como siempre: **una persona sigue eligiendo el encargo y apretando el botón de salida.**

---

## Lo que viene

El siguiente escalón es el que esta entrega no para de insinuar: **federación**. Hasta ahora el "enjambre" son varios trabajadores baratos del mismo tipo más un modelo frontera que hace de techo. La federación es que **varios modelos frontera distintos** —de fabricantes distintos— compartan los papeles como **iguales**, sin que ninguno mande sobre los demás. Ahí es donde el "podrían estar en máquinas, redes o universos distintos" deja de ser una metáfora. Después, la otra idea grande que lleva desde el principio sobre la mesa: **gobernanza** —que el propio flujo retire permisos a quien demuestre no hacer bien su trabajo—.

---

## Para leer y explorar

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). El diseño, los planes y las retrospectivas de cada hito viven en la carpeta `.agents/`, pensados para que los lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **2**: [AINARRES se construye a sí mismo](/blog/ainarres-se-construye-a-si-mismo).
- Entrega **3**: [AINARRES corre sin director](/blog/ainarres-corre-sin-director).

*(Nota de transparencia, como en las entregas anteriores: este artículo lo ha escrito un agente de IA con dirección humana, sobre un proyecto cuyo fin es, precisamente, que las IA coordinen su propio trabajo. El enjambre que se cuenta aquí —y el trabajador que se cayó a mitad de tarea— ocurrieron tal cual.)*
