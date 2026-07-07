---
Title: AINARRES: un enjambre más amplio, y el trabajador que se quedó girando
Description: Un interludio tras la temporada de gobernanza. AINARRES pasó de un único trabajador de IA barato a una flota escalonada de modelos de distintos fabricantes — y al hacerlo chocó con tres lecciones que riman: algunos trabajadores fallan de formas que ninguna regla detecta, la política de la organización decide quién puede siquiera trabajar, y cada modelo cuenta sus tokens a su manera. Esta es la historia de ampliar el enjambre y enseñarle a medirse a sí mismo — y de por qué esa medición se convierte en el próximo trabajo del auditor.
Date: 2026-07-07 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 8
Lang: es
Translation_Key: ainarres-wider-swarm
Image: /assets/images/ainarres-08-fleet-hero.webp

---

# AINARRES: un enjambre más amplio, y el trabajador que se quedó girando

La temporada anterior consistió en enseñar al sistema a decir **no** — a retirar una capacidad a una familia que demuestra hacer mal su trabajo, y a derivar a una persona los juicios que una regla no debería tomar. Esa temporada se cerró. Esta entrega es un *interludio*: no un hito nuevo, sino un tramo real de trabajo con lecciones que merece la pena anotar. Me propuse algo mundano — dar al enjambre **más trabajadores** — y acabó siendo un pequeño recorrido por lo desordenada que es de verdad una flota *diversa* de modelos de IA.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo es datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que se me permite hacer" y "esta ya está". **No hay orquestador.** Las entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *distintos fabricantes* como iguales, y luego le enseñaron a autogobernarse. AINARRES desarrolla AINARRES; una persona solo arranca el bucle.

Hasta ahora, casi todo el trabajo real de programación lo hacía un único modelo barato. Eso es frágil — una sola API gratuita, un solo conjunto de manías. Así que amplié el banquillo.

---

## De un trabajador a una escalera graduada

Los implementadores del enjambre pasaron de uno a una flota pequeña y ordenada — modelos de distintos fabricantes, en distintos puntos de precio y calidad:

- un nivel **más barato** pensado para hacer un primer intento con el trabajo fácil,
- el **caballo de batalla** principal de API gratuita (que corre varios a la vez para dar caudal),
- un par de **80 mil millones de parámetros**, de coste similar pero temperamento distinto,
- un respaldo de **mayor calidad** de otro fabricante más,
- y por encima de todos, el modelo de frontera que revisa, integra y rescata lo que los niveles baratos no logran terminar.

La idea es vieja y sensata: los modelos baratos hacen el grueso, los caros recogen lo que se les cae. Añadir un trabajador se convirtió en una receta conocida — registrar la identidad del modelo en el sustrato, cablearlo en la escalera, listo. Lo que *no* esperaba es que cada trabajador nuevo me enseñara algo que el anterior no.

---

## El trabajador que se quedó girando

El modelo más barato que añadí se metió directo en la cuneta. Ante una tarea real, echó mano de una herramienta llamada `str_replace_editor` — una herramienta que no existe en su entorno. El entorno la rechazó. Así que lo intentó otra vez. Y otra. Se quedó unos diez minutos pidiendo una herramienta que nunca iba a tener, sin producir nada, mientras el sistema lo contaba como *ocupado trabajando*.

Una persona mirando el tablero se dio cuenta en minutos, y otro trabajador recogió la tarea sin ruido y la terminó. No hubo daño — el sustrato está construido para que un trabajador atascado no pueda corromper nada (sus resultados tardíos se rechazan, el código real sigue limpio). Pero lo interesante es el fallo. **Nada rebotó.** Ningún revisor rechazó el trabajo; ningún contador se movió. El modelo no estaba *equivocado*, estaba *girando en el vacío* — un modo de fallo que no deja marca en ninguna de las señales que el sistema vigila. Deshabilité ese modelo el mismo día: un trabajador que no sabe manejar sus propias herramientas es peor que no tener trabajador.

Guarda esa idea. Importa más adelante.

---

## Quién tiene siquiera *permiso* para trabajar

El siguiente trabajador — un agente de programación de otro fabricante — se negó a arrancar. Para operar sin supervisión quería que se activara un interruptor de "permitirlo todo", y el administrador de mi organización lo tenía **deshabilitado** de forma central. Razonable: "deja que este agente ejecute cualquier comando sin preguntar" es justo el tipo de cosa que un administrador debería poder apagar.

El interruptor bruto ya no estaba, pero había un instrumento más fino todo el tiempo: una **lista de permitidos**. En lugar de "permítelo todo", pre-apruebas acciones *concretas* — deja que este agente ejecute `git`, el comando de pruebas, el motor del lenguaje, y nada más. Con esa lista corta en su sitio, el agente corre sin supervisión perfectamente, del todo **dentro** de la política del administrador. La lección tiene una forma bonita: cuando te quitan el mazo, echa mano del bisturí. **Pre-permite, no fuerces.** Además es mejor postura de seguridad — el agente puede hacer su trabajo y *solo* su trabajo.

---

## Cada uno cuenta a su manera

La otra mitad de este interludio fue sobre **medición**. Hace unas temporadas el sistema empezó a llevar un historial por trabajador — cuánto entregaba cada uno, con qué frecuencia rebotaba su trabajo — y, sobre todo, *cuántos tokens quemaba*. Los tokens son el combustible bruto de un modelo de IA; contarlos te dice lo que un trabajador **cuesta**, algo que se mantiene deliberadamente separado de si es **bueno** (un modelo caro que siempre pasa es *caro*, no *malo*).

Solo había un problema: únicamente una familia de modelos estaba reportando de verdad sus tokens. El resto marcaba **"desconocido"**. Así que fui a cerrar esa brecha, y descubrí que **no hay dos entornos que reporten el uso igual**. Uno emite un recuento parcial por cada paso que hay que sumar. Otro imprime un único resumen limpio. Un tercero se niega a poner el uso en su salida normal — hay que pedir un registro de depuración y pescarlo de ahí — e incluso entonces cuenta los tokens de "entrada" de un modo que se solapa con su caché, así que hay que restar para obtener una cifra honesta. Cuatro modelos, cuatro dialectos para el mismo dato.

El arreglo fue enseñar al sistema cada dialecto, en el borde, para que el desorden nunca se filtre hacia dentro: el historial compartido solo ve números limpios. Y una pequeña disciplina se mantuvo en todo momento — **"desconocido" nunca se trata como "cero"**. Un trabajador que aún no podemos medir marca *sin medir*, jamás *gratis*. Fingir que un modelo sin medir no cuesta nada sería el número más peligroso de todo el sistema.

Al final, el gasto de cada trabajador quedó visible. El banquillo ahora no solo es más ancho — es **legible**.

---

## ¿Para qué medir? (la recompensa)

Aquí es donde vuelve el trabajador que giraba. ¿Qué *haces* con una medida de lo que gasta cada modelo?

La respuesta tentadora es "esquiva automáticamente los modelos caros" — pero eso es un **enrutador**, algo que decide quién hace qué tarea, y AINARRES deliberadamente no tiene uno (los trabajadores toman su propia siguiente tarea; nadie asigna). Así que enrutar está descartado, por diseño.

La respuesta *correcta* conecta las dos mitades de este interludio. ¿Recuerdas al trabajador que giraba — quemando esfuerzo, sin producir nada, invisible a toda regla? Una **señal de gasto es exactamente lo que atrapa eso.** Un modelo atascado en un bucle quema tokens de forma anómala. Un modelo que "piensa" atravesando cincuenta veces más tokens que sus pares para el mismo resultado es caro en silencio. Ninguno dispara una comprobación de corrección — pero ambos encienden una vigilancia del *gasto*.

Así que el siguiente paso no es un enrutador; es darle al **auditor** del proyecto — el rol en manos humanas presentado la temporada pasada, que revisa lo que las reglas no pueden juzgar — algo nuevo que vigilar: **anomalías de gasto.** El auditor señala a un trabajador que gira o que sobregasta de forma consistente, y una *persona* decide qué hacer. Es una señal, no un castigo; el sistema nunca prohíbe a un modelo solo por quemar tokens (eso sigue siendo decisión de una persona). La medición hace visible el fallo invisible, y lo deriva a quien tiene permiso para juzgarlo. El trabajador que giraba no fue solo un error — fue el argumento para la próxima función.

---

## Lo que deliberadamente no he hecho

Como siempre, la lista honesta:

- **Los trabajadores nuevos apenas se han ejercitado.** En cada ejecución de este tramo, el modelo barato principal hizo el trabajo y el de frontera lo revisó. Los demás niveles nuevos *corrieron* pero no encontraron nada que hacer — son un seguro, y un principal sano los deja ociosos. Su prueba real llega cuando los créditos gratuitos del principal se agoten y el trabajo caiga de verdad hasta ellos — no de una ejecución montada para forzarlo.
- **El agente de mayor calidad aún no ha terminado una tarea real.** Está cableado, con su lista de permitidos, y listo; simplemente no ha hecho *falta* todavía. Cableado ≠ probado.
- **La medición es tosca en un punto.** El modelo de frontera desempeña varios roles en una misma sesión de trabajo, y el sistema atribuye su gasto solo a uno de ellos — así que uno de sus roles todavía marca "desconocido". Honesto, documentado y arreglable más adelante; lo que importa ahora es la foto de toda la flota.

---

## Dónde queda esto

Esto fue trabajo de andamiaje, y se ganó el jornal dos veces: produjo una **flota medible**, y produjo el **fallo motivador** — el trabajador que giraba — que justifica la próxima función real. Las dos piezas encajan como una llave en su cerradura.

Lo que viene es más grande. El plan a partir de aquí: **nombrar los dos roles "de los extremos"** que el sistema todavía me deja a mí — el que *da forma a la petición* antes de que empiece el trabajo, y el que *audita la entrega y la salud de la flota* después (ahora incluyendo esa vigilancia del gasto). Y luego, por fin, aquello hacia lo que este proyecto lleva construyendo desde el principio: convertir AINARRES de un script que arranco en un portátil en un **servicio permanente** — siempre encendido, alimentado por un canal real, vigilado por los roles en vez de por mí. El bucle siempre fue andamiaje. El servicio es lo de verdad. Ese es el horizonte.

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

*(Nota de transparencia, como en cada entrega: este artículo lo escribió un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IA coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio — la flota escalonada, el modelo que giró sobre una herramienta que no tenía, la lista de permitidos que sustituyó al interruptor deshabilitado, y las cuatro formas distintas en que cuatro modelos reportan los tokens que queman.)*
