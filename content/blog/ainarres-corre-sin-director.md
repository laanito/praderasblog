---
Title: AINARRES corre sin director
Description: La tercera entrega de AINARRES: el día en que arranqué el bucle, me fui, y una funcionalidad real llegó sola a la rama principal —sin que ninguna persona ni ningún director secuenciara, reencaminara o arreglara nada por el camino—. Y por qué los dos arranques fallidos enseñaron más que el que salió bien.
Date: 2026-06-30 12:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 3
Lang: es
Translation_Key: ainarres-hands-off
Image: /assets/images/ainarres-03-hands-off-hero.webp

---

# AINARRES corre sin director

La [segunda entrega](/blog/ainarres-se-construye-a-si-mismo) terminó con una confesión y una promesa. La confesión: aunque AINARRES ya construía partes de sí mismo, **todavía había un director humano asistiendo** —yo secuenciaba los pasos, detectaba a mano cuándo escalar del modelo barato al caro, y limpiaba algún desorden—. La promesa: la tercera entrega iría a por eso, **quitar al director del bucle**. Esta es la entrega en la que ocurrió. Arranqué el proceso, me levanté de la silla, y una funcionalidad real apareció fusionada en la rama principal sin que yo tocara nada.

Pero —como en la entrega anterior— el hito limpio es lo de menos. Lo interesante son los **dos arranques que fallaron antes**, porque enseñaron exactamente qué significa que un sistema funcione *sin nadie llevando la batuta*.

> Si llegas nuevo: AINARRES (acrónimo en inglés de *AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** —el terreno común sobre el que se coordina el trabajo— hecho con PostgreSQL. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo saben “dame la siguiente tarea que me toca” y “esta ya está”. No hay orquestador: cada agente *tira* de la cola lo que tiene permiso de hacer.

---

## Las tres promesas, cumplidas

La entrega anterior dejó por escrito qué hacía falta para quitar al director. Eran tres cosas concretas, y conviene contar cómo se resolvió cada una, porque juntas son lo que hace posible un bucle sin nadie.

**1. Que validar una tarea no “ensucie” el estado compartido.** Cada tarea, antes de darse por buena, se valida ejecutando pruebas de verdad. El problema: esas pruebas levantan y tiran su propia base de datos —y si esa base de datos es *la misma* sobre la que el enjambre coordina su trabajo, validar una tarea borra el tablero de las demás. La solución fue separar en dos: una instancia para que el enjambre coordine (el tablero “en vivo”) y otra, idéntica, para que las pruebas hagan lo que quieran. Ahora un agente puede reconstruir su entorno de pruebas desde cero sin rozar el trabajo de nadie.

**2. Que la escalada por capacidad sea una regla automática del sustrato.** En la entrega anterior, cuando el modelo barato se atascaba, *yo* detectaba que tocaba pasarle la tarea a uno más capaz. Ahora no: el sustrato lleva la cuenta de los intentos de cada tarea y, pasados unos pocos, **la propia tarea exige automáticamente un nivel superior** —y a partir de ahí solo un modelo de la categoría frontera puede reclamarla—. Nadie reencamina nada; la regla vive en la base de datos.

**3. Que los agentes corran como procesos independientes que sondean el tablero.** En lugar de que yo lanzara a cada agente cuando tocaba, ahora hay un **lanzador tonto**: recibe el encargo, se lo da una sola vez a un diseñador para que lo parta en tareas, y luego barre a los trabajadores **de menor a mayor capacidad, por rondas**. Los modelos baratos se llevan el grueso del trabajo; el modelo caro es el *techo*, no la opción por defecto. Y —detalle que importa más de lo que parece— **sabe parar**: cuando una ronda completa no mueve nada en el tablero, el bucle termina, en vez de seguir gastando dinero para siempre.

---

## El día sin director

Con las tres piezas en su sitio, el experimento es de una sencillez casi decepcionante de contar. Escribí un encargo en un fichero de texto —“añade un comando que diga cuánto le queda de vida a una credencial”—, ejecuté una orden, y me aparté.

A partir de ahí, sin que yo interviniera:

- Un **diseñador** (modelo frontera) leyó el encargo y lo convirtió en una tarea autocontenida, con su especificación y su criterio de validación.
- Un **implementador barato** (un modelo de bajo coste sobre el arnés *opencode*) reclamó la tarea, escribió el código y la prueba, validó por su cuenta y la pasó a revisión.
- El **revisor** y el **integrador** (el arnés *grok*, independiente) leyeron el cambio, lo aprobaron, abrieron el *pull request* y lo fusionaron de verdad a la rama principal.

El tablero se vació en **una sola ronda**. La funcionalidad —`ainarres ttl`, que además se apoya en otra que el propio enjambre había construido en una tanda anterior— quedó viva en la rama principal, con su prueba en verde. Ninguna persona secuenció, reencaminó ni remendó nada mientras corría. Eso es, exactamente, quitar al director del bucle.

---

## Lo que de verdad aprendimos (los arranques fallidos)

Antes de ese día limpio hubo dos intentos que terminaron en un estado raro: la funcionalidad se construía bien… pero el tablero acababa con basura encima, y el lanzador, con buen criterio, se negaba a decir “hecho”. Lo fascinante es **por qué** fallaban: no porque ningún agente fuera incompetente, sino por algo mucho más sutil.

Resulta que uno de los agentes, al validar, ejecutaba *toda* la batería de pruebas (no debía, pero lo hacía). Y esa batería, por la forma en que estaba conectada, **apuntaba a la base de datos en vivo** —la del enjambre— en lugar de a la suya. El resultado: las pruebas sembraban sus propios datos de mentira sobre el tablero real, y los demás agentes se quedaban dando vueltas alrededor de tareas fantasma.

La tentación, ahí, es obvia: *“dile al agente que no ejecute toda la batería de pruebas”*. Y es la lección equivocada. En un sistema sin director **no puedes apoyarte en que cada componente se porte bien**; tienes que hacer que portarse mal sea *inofensivo*. Así que la corrección no fue pedirle nada al agente: fue hacer que la batería de pruebas, da igual quién la lance y desde dónde, sea **incapaz** de tocar el tablero en vivo. La frontera la pusimos en el sitio, no en la buena voluntad.

Es la misma lección que la entrega anterior, vista desde otro ángulo. Allí descubrimos que quien integra el código tiene que ser *independiente* de quien dirige, porque una prohibición que se puede esquivar delegándola no es una prohibición. Aquí: un invariante que depende de que un agente “se acuerde de no hacer algo” no es un invariante. **El sustrato obliga; tú no confías.** Esa es, en una frase, toda la apuesta del proyecto.

(La segunda lección, más pequeña pero querida: **saber parar es una funcionalidad**. Un bucle de agentes que no sabe cuándo no queda trabajo es una factura abierta. Que el lanzador termine solo cuando una ronda entera no mueve nada es lo que separa un experimento de un sangrado de recursos.)

---

## Lo que aún no hemos hecho (seamos honestos)

Como siempre, conviene marcar la frontera entre lo demostrado y lo prometido:

- **Yo seguí eligiendo el encargo y arrancando el bucle.** “Sin director” se refiere a lo que pasa *durante* la ejecución: nadie secuencia ni rescata. Pero alguien sigue apretando el botón de salida y decidiendo qué se construye.
- **El integrador lo lanza una persona**, no el bucle —y a propósito: es la frontera de seguridad que contamos en la entrega 2, donde el director tiene *prohibido* fusionar por su cuenta.
- Todo corrió en **una sola instancia, con un trabajador por nivel**. Nada de varios modelos frontera cooperando aún.

Dicho de otro modo: hemos quitado al director de la *conducción*, no del *arranque*. El siguiente escalón —que el sistema funcione desatendido contra un criterio que ni siquiera elige la persona— es el cierre de esta versión.

---

## Lo que viene

A partir de aquí el horizonte es el de siempre, pero más cerca. Primero, cerrar la autonomía: una prueba **desatendida** de verdad, donde el único papel humano sea encender el sistema. Después, las dos ideas grandes que llevan desde el principio sobre la mesa: **gobernanza** —que el propio flujo retire permisos a quien demuestre no hacer bien su trabajo— y **federación**: varios modelos frontera formando equipos hacia una meta común, coordinándose solo a través de un sustrato que ya ha demostrado, de extremo a extremo, que puede coordinar agentes independientes sin nadie llevando la batuta.

---

## Para leer y explorar

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). El diseño, los planes y las retrospectivas de cada hito viven en la carpeta `.agents/`, pensados para que los lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **2**: [AINARRES se construye a sí mismo](/blog/ainarres-se-construye-a-si-mismo).

*(Nota de transparencia, como en las entregas anteriores: este artículo lo ha escrito un agente de IA con dirección humana, sobre un proyecto cuyo fin es, precisamente, que las IA coordinen su propio trabajo. El bucle sin director que se cuenta aquí —y los dos arranques que fallaron antes— ocurrieron tal cual.)*
