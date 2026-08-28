---
Title: 'AINARRES: el asiento del operador'
Description: Durante doce entregas hubo una persona en la terminal haciendo lo que ningún rol cubría — refinar una petición, crear el trabajo, arrancar el servicio. Esa persona nunca fue una identidad. Era quien tuviera la clave de firma, poniéndose la cara del trabajador que el acto requiriera en cada momento. Esta entrega le da un nombre a ese asiento, y luego descubre lo que un nombre hace visible: meses de trabajo atribuidos a las familias equivocadas, toda una clase de gasto que el sistema no podía ver, y una definición de "operador" que nadie había tenido nunca que escribir.
Date: 2026-08-29 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 13
Lang: es
Translation_Key: ainarres-operator-seat
Image: /assets/images/ainarres-13-operator-seat-hero.webp

---

# AINARRES: el asiento del operador

La entrega anterior terminaba con una lista de cosas que deliberadamente no había hecho, y una era esta: *"Todavía no hay sobre de credenciales. El rol de solo lectura existe; la maquinaria que le entrega al asiento del operador credenciales cortas que no puede ampliar, no. Hasta que exista, delegar más allá de leer es confiar, y prefiero decirlo claro a fingir otra cosa."*

Esta entrega lo construye. Pero el sobre resultó ser la segunda mitad de la historia, y la primera me pilló completamente desprevenido.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que puedo hacer" y "esta ya está". **No hay orquestador.** Entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *fabricantes distintos* como iguales, luego le enseñaron a gobernarse y a vigilar su propia salud, nombraron los roles de los dos extremos de la tubería, convirtieron el bucle en un servicio permanente, y le enseñaron a despertar solo a los trabajadores que el trabajo en espera necesita. Ningún humano arranca nada por cada funcionalidad; AINARRES desarrolla AINARRES.

## La persona que nunca fue una persona

Cada entrega de esta serie ha tenido un personaje silencioso. No el diseñador, ni los implementadores, ni el revisor o el integrador — esos son todos *roles*, con nombres, capacidades y un historial. El personaje silencioso es quien refina una petición hasta convertirla en un encargo, decide que el encargo está listo, crea el trabajo de desarrollo, arranca el servicio, lo para, cambia qué modelos están sentados, y desatasca las cosas cuando se atascan.

Ese es el **operador**. Y hasta esta semana, el operador no era una identidad en absoluto.

El operador era *quien tuviera la clave de firma*. El sistema se autentica con tokens firmados que llevan un nombre de familia y un conjunto de capacidades, y el operador tenía el secreto con el que se firman esos tokens. Así que cuando el operador necesitaba refinar un encargo, acuñaba un token diciendo que era `human+intaker` — la identidad que usa la puerta de entrada. Cuando necesitaba crear trabajo de desarrollo, acuñaba uno diciendo que era `claude-code+opus`, el modelo de diseño. Cuando necesitaba desatascar una tarea, se convertía en `loop+driver`.

No es suplantación en ningún sentido dramático. Era lo obvio: esas son las identidades que tienen las capacidades adecuadas, y no había otra identidad que ser. Pero tenía una consecuencia que nunca había seguido hasta el final, y cuando lo hice resultó incómoda.

**Cada uno de esos actos aterrizaba en el historial de la familia suplantada.**

Hace dos entregas el sistema aprendió a llevar un historial: qué familia entregó qué, con qué frecuencia se rechazó su trabajo, cuántos tokens quemó. Ese registro es lo que una versión futura usará para decidir si se puede confiar una capacidad a una familia. Es la base de evidencia de toda la capa de gobernanza.

Y había estado registrando *mi* trabajo como suyo. Un operador que refinaba diez peticiones hacía que `human+intaker` — una puerta, no un trabajador — pareciera un contribuidor prolífico. Un operador cuyo intento de crear trabajo fallaba le acreditaba el fallo a un modelo de diseño que ni siquiera estaba corriendo. El sistema de gobernanza construido para medir competencia estaba, en parte de su entrada, midiendo a la familia equivocada.

## Darle un nombre al asiento

El arreglo es vergonzosamente pequeño, lo que suele ser señal de que el modelo era correcto y solo el *uso* estaba mal. El operador pasa a ser una familia registrada como cualquier otra: `agent+operator`, con exactamente lo que el trabajo necesita — trabajar el tramo intermedio de la entrada, crear trabajo de desarrollo — y nada más. Explícitamente **sin** la capacidad de integrar, que sigue siendo del único integrador independiente. Explícitamente **sin** el rol de auditor, que sigue siendo humano, y sigue siendo una identidad *distinta* de la que hace funcionar la máquina.

El efecto aparece de inmediato en el registro de eventos:

```
transition  01a04941-6d02…  agent+operator  (advance proposed_brief → briefed)
claimed     01a04941-6d02…  agent+operator
created     01a04941-ffd5…  human+intaker
```

La petición es de quien la pidió. El trabajo hecho sobre ella es del operador. Tres líneas, y la atribución es honesta por primera vez.

Entonces salió un segundo problema, y es uno con el que ya me he topado tres veces en este proyecto. Parte de lo que hace un operador no tiene ninguna tarea asociada. Arrancar el servicio no es trabajo sobre una tarea. Cambiar qué modelos están sentados no es trabajo sobre una tarea. Leer el informe y decidir no hacer nada *desde luego* no es trabajo sobre una tarea. Pero el registro de eventos exige que todo evento nombre una tarea — esa restricción está ahí desde la primera versión, y es buena.

Así que esos actos tienen su propio registro de solo-añadir. Es la tercera vez que el mismo muro produce la misma respuesta: una para las acciones de veto y levantamiento del humano, otra para las del auditor, ahora para las del operador. Cuando veo una forma repetirse así, lo tomo como que el diseño me está diciendo algo cierto, no como tres coincidencias. Los eventos son *sobre tareas*. Cualquier cosa sobre la **instancia** necesita vivir en otro sitio.

## Nombrar no es acotar

Aquí es donde estuve a punto de cantar victoria antes de tiempo, y lo que me frenó fue escribir la enmienda a mi propia nota de diseño.

Tenía un asiento con nombre, un conjunto acotado de capacidades y un registro. Habría sido fácil escribir "el operador ya está acotado" y seguir. Pero la frase no habría sobrevivido a un lector atento, por cómo funciona realmente la autenticación.

El sustrato lee las capacidades *del token firmado*. No vuelve a la base de datos a comprobar qué se le concedió de verdad a esa familia. Es una decisión deliberada de las primeras fases del proyecto: el aprovisionamiento se aplica cuando el token se **crea**, y el sustrato confía en una firma válida. Mantiene el camino caliente rápido y el modelo simple.

Lo que significa: mi asiento cuidadosamente acotado estaba acotado porque **la herramienta de línea de comandos elegía no pedir más**. Nada impedía a un asiento con la clave de firma acuñarse la capacidad de integrar, el rol de auditor, o la identidad entera de otra familia. Las prohibiciones eran reales en el sentido de estar escritas y probadas. No eran reales en el sentido de estar *aplicadas*.

Así que el asiento estaba **nombrado y registrado, y todavía no acotado** — y eso es lo que dice ahora la nota de diseño, con esas palabras. Prefiero un documento incómodo a uno halagador.

## El sobre llevaba tres versiones ahí puesto

El arreglo es un pequeño servicio que guarda la clave de firma. El asiento ya no firma nada; *pide*:

```
asiento ──pide──▶ intermediario ──pregunta──▶ base de datos
                       │                      (decide qué puede ir en el token)
                       └──firma lo que la base de datos le devuelve
```

Lo importante es la separación. La cosa que tiene la clave no puede *construir* una credencial — solo puede firmar una que le han pasado. La base de datos decide: qué roles se permiten (uno de trabajo o uno de solo lectura, nunca el que lleva los poderes irreversibles del humano, y nunca el que podría emitir credenciales — eso sería entregar la clave con pasos extra); qué familia puede ocupar el asiento (aquella a la que el dueño le concedió la marca de operador, lo que lo convierte en configuración y no en un nombre enterrado en el código); qué capacidades entran (exactamente las aprovisionadas, enteras y sin editar — no hay ningún parámetro para pedir un conjunto distinto, porque pedir no forma parte del protocolo); y cuánto dura (con tope, porque una credencial de larga vida es solo una clave con más ceremonia).

Y aquí está la parte que más he disfrutado. Cuando fui a buscar dónde poner esta lógica, me la encontré ya escrita. Una función del segundo hito del proyecto lee las capacidades aprovisionadas de una familia y las devuelve **como declaraciones para que las firme un acuñador aparte**. Su propio comentario lo dice: *"el acuñador las firma"*. Se construyó exactamente para esto, tres versiones antes de que nada lo necesitara, y no se había llamado ni una sola vez.

No tuve que diseñar el sobre. Tuve que *aparecer y usarlo*. El yo del pasado dejó una puerta, la etiquetó con claridad, y se fue durante tres versiones. Pasa lo bastante poco como para querer registrarlo, sobre todo como evidencia a favor de la práctica de escribir la forma de una cosa aunque todavía no la estés construyendo.

## El límite que elegí, y por qué te lo cuento

Ahora la parte incómoda, y la decisión que creo que más importa en esta entrega.

El sobre detiene a un asiento que *pide*. No detiene a un asiento que lee el secreto del disco y firma lo que le apetezca. En una sola máquina, con un solo usuario del sistema operativo, todo lo que el dueño puede leer, un agente con acceso a la consola puede leerlo. Ninguna cantidad de código dentro de la base de datos cambia eso.

Había dos opciones. Hacer la frontera **aplicada** — ejecutar el asiento como un usuario del sistema distinto, o en un contenedor, sin acceso de lectura al secreto — que es perfectamente alcanzable y cuesta un paso de configuración único. O hacerla **auditada**: el sobre acota a un asiento que coopera, y *expone* a uno que no.

Elegí auditada, para esta versión. Lo que significa que la pieza interesante no es el sobre en absoluto. Es la vista que informa de **acciones de operador sin ninguna credencial detrás** — porque cada credencial que el sobre emite registra la identidad a la que se emitió, así que un acto de una identidad de operador a la que nunca se le emitió una credencial fue firmado por algo que tiene la clave directamente. Eso es el asiento saltándose al intermediario, o soy yo en la terminal. Ambas cosas merecen verse; ninguna debería ser silenciosa.

Esa vista carga con toda la decisión, y salió con dos errores. Su propia prueba los cazó.

Miraba solo el registro de eventos — así que cuando un asiento auto-acuñado escribía en el *registro* del operador y no en el de eventos, no veía nada. Ese es uno de los rodeos más probables, y el rastro de auditoría era ciego a él. Y contaba mal: un cruce contra las capacidades de la familia multiplicaba cada acción por el número de capacidades que esa familia tiene, informando de cinco veces el total real.

Ambos están arreglados. Pero quiero detenerme en lo que estuvo a punto de pasar: un rastro de auditoría ciego exactamente al rodeo para el que existía, y que habría informado *limpio*. Una frontera que informa limpio cuando no está mirando es peor que ninguna frontera, porque fabrica confianza. Si hubiera escrito esa vista y no su prueba, habría publicado una ficción cómoda y me la habría creído.

## Ingeniería honesta: el gasto que nadie podía ver

Mientras medía todo esto, comprobé lo que cuesta realmente una entrega. El sistema registra el gasto de tokens por familia, así que debería haber sido una pregunta de dos minutos.

El total registrado para una funcionalidad pequeña fue de unos **2,25 millones de tokens** entre el modelo de diseño, el implementador y el revisor. Interesante saberlo, aunque el 94% de esa cifra es contexto en caché releído, que cuesta más o menos una décima parte de la entrada fresca — así que el recuento de tokens exagera la factura casi en un orden de magnitud. Es una señal de volumen, no una factura, y prefiero decirlo a dejar que un número grande se quede sin matizar.

Pero algo no cuadraba. El registro de esa ejecución mostraba *cuatro* modelos implementadores arrancando, y solo uno había registrado gasto.

Los otros tres se habían despertado, habían descubierto que la única tarea disponible ya la había cogido el primero, y habían parado. Uno lo dijo en su propio registro: *"no hay tarea que ramificar, implementar, validar o avanzar"*. Entre los tres habían quemado unos **474.000 tokens** — alrededor del 21% del coste registrado de esa entrega — descubriendo que no había nada que hacer. Y **nada de eso quedó registrado en ningún sitio**, por una regla que yo mismo había escrito y de la que estaba bastante satisfecho:

> *Un barrido que no hizo trabajo no tiene transición → no tiene tarea → NO hay evento: la invariante del barrido vacío, aplicada en el sustrato y no solo por disciplina del driver.*

El razonamiento era que un trabajador que no movió ninguna tarea no había hecho nada digno de atribuir. Es sencillamente falso. Un barrido vacío cuesta cargar un modelo, un prompt de sistema, una lectura del tablero y una decisión de parar. La regla medía lo que no era: no *se gastó dinero* sino *se movió una tarea*.

Y la ceguera era **sesgada**, que es lo que la hace merecer más que una nota al pie. Ocultaba el gasto precisamente donde se concentra el desperdicio — trabajadores redundantes, un pool dando vueltas, una familia que no coge nada en todo el día. La señal era más silenciosa exactamente cuando debería haber sido más ruidosa. Lo que además significaba que el arreglo que elimina ese desperdicio — enseñar al servicio a volver a comprobar lo que hay esperando antes de arrancar cada trabajador, de modo que los tres redundantes nunca despierten — no se podía *demostrar* que funcionara, porque lo que ahorra nunca se había contado.

El gasto que no mueve ninguna tarea ahora aterriza en su propio registro. Y actualicé la nota de diseño con la medición que refutó mi propia invariante, junto a la invariante, para que el siguiente lector tenga las dos.

## Tres fallos silenciosos más

**La batería de pruebas me estaba sobrescribiendo la llave de la puerta.** La puerta de entrada se autentica con una clave compartida que genera y guarda en un fichero. Una prueba que arranca una copia de esa puerta le pasaba una clave de laboratorio — y la puerta guardaba obedientemente la clave de laboratorio encima de la real. Así que después de cualquier ejecución de pruebas, una puerta ya en marcha con la clave real respondía a todo con un escueto *401 no autorizado*, y nada en ninguno de los dos mensajes apuntaba al porqué. Es la misma especie que un error de la entrega seis: estado de pruebas escapándose al estado real por una puerta que nadie consideraba una puerta.

**Un argumento perdía contra una variable de entorno.** Persiguiendo eso, encontré que el cliente prefería en silencio una clave ambiental del entorno antes que una ruta de fichero que el operador *acababa de teclear en la línea de comandos*. Un argumento no puede perder nunca contra el entorno. Peor: cuando el fichero nombrado no se podía leer, caía a una clave *distinta* en vez de fallar — la misma sustitución silenciosa con otro disfraz. Ambos arreglados: un fichero nombrado explícitamente es ahora la única fuente, e ilegible significa "sin clave", en voz alta.

**Un arreglo que no pude probar rompió justo lo que lo prueba.** El arreglo de la demanda aterrizó mientras un servicio corría sobre el tablero, y las dos pruebas que lo habrían ejercitado empiezan *destruyendo ese tablero*. Así que lo publiqué marcado como no verificado en vez de arrasar un sistema en marcha. Estaba mal. Una guarda que había puesto al principio de tres funciones hacía que ignoraran un valor puesto a mano — que es exactamente como una fase de las pruebas prepara sus comprobaciones. En cuanto el tablero quedó libre, esa fase falló en una línea.

La lección no es "ejecuta tus pruebas", que ya la sabía. Es que **la restricción era real y lo honesto era decirlo**, en vez de razonar hasta llegar a "probablemente esté bien". No estaba bien. Estuvo bien noventa minutos después, cuando pude comprobarlo de verdad.

## Lo que deliberadamente no hice

- **Los dos poderes irreversibles siguen siendo humanos.** Vetar permanentemente a una familia, y levantar ese veto, siguen siendo llamables solo por un rol que el sobre nunca emitirá. El asiento puede *leer* toda señal que justificaría uno, y *recomendarlo*. No puede hacerlo.
- **El auditor sigue siendo humano, y sigue siendo otra identidad.** Un operador que pudiera levantar avisos sobre su propio trabajo sería un bucle de supervisión cerrado sobre sí mismo. Ahora es un requisito estructural del asiento, no una preferencia.
- **Sigue sin haber enrutador.** El asiento puede cambiar qué familias están sentadas y qué mandos están puestos. Nunca puede decidir qué tarea va a quién. Esa prohibición está ahora escrita en la capa del operador, no inferida de la de abajo, precisamente porque un operador agente capaz es exactamente el actor que derivaría hacia enrutar.
- **Todavía sin aislamiento aplicado.** Ejecutar el asiento como un usuario distinto sin acceso de lectura al secreto convertiría la detección en prevención. Está documentado, es un paso de despliegue, y no está hecho.

## Dónde nos deja esto

Doce entregas fueron quitando puntos de contacto humanos de uno en uno. Esta hizo algo distinto, y solo me di cuenta al final.

Para entregarle el asiento del operador a un agente, primero tuve que responder una pregunta que nadie había hecho nunca: *¿qué está permitido hacer, exactamente, al operador?* No qué hace el operador — qué está permitido, qué está prohibido, y qué tiene que quedarse con una persona. Durante doce entregas la respuesta había sido "lo que haría el dueño", que no es una definición. Es la ausencia de una, oculta por el hecho de que solo una persona se sentaba ahí.

El asiento tiene ahora un nombre, una lista escrita de lo que puede y no puede tener, un registro de todo lo que hizo, credenciales que no se emite a sí mismo, y un informe que nombra cualquier cosa que actúe como operador sin una credencial detrás. Nada de eso es capacidad nueva. Es la misma máquina, haciendo lo mismo, con la respuesta a "¿quién hizo eso, y podía hacerlo?" escrita por primera vez.

Salí a construir un sobre. Lo que hice en realidad fue escribir una descripción del puesto — y luego descubrir que llevaba todo este tiempo haciendo ese trabajo bajo el nombre de otros.

---

## Para seguir leyendo

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **6**: [El día que borró su propio tablero](/blog/ainarres-el-dia-que-borro-su-tablero).
- Entrega **7**: [AINARRES y el auditor: ¿construimos lo correcto?](/blog/ainarres-el-auditor).
- Entrega **11**: [AINARRES: se acabó la manivela — la máquina que se ejecuta sola](/blog/ainarres-se-acabo-la-manivela).
- Entrega **12**: [AINARRES: despertar solo a quien hace falta](/blog/ainarres-despertar-solo-a-quien-hace-falta).

*(Nota de transparencia, como en cada entrega: este artículo lo ha escrito un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IA coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio — la identidad de operador que llevaba tomando prestados los nombres de otras familias, el registro para actos que no pueden ser eventos, el intermediario de credenciales que guarda la clave y no puede construir un token, la función del segundo hito que llevaba tres versiones esperando a su primera llamada, la vista que informa de acciones de operador para las que nadie emitió una credencial, y los 474.000 tokens de gasto que el sistema estaba diseñado para no ver.)*
