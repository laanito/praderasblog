---
Title: 'AINARRES: el primer encargo que no era una especificación'
Description: Todas las peticiones que le había dado al enjambre eran especificaciones disfrazadas — la función nombrada, las cadenas entre comillas, las aserciones enumeradas. Esta era un problema, un contrato que no puede reabrir y permiso para negarse. Volvió como un grafo de dependencias de cuatro nodos, construido en dieciocho segundos, y cuatro pull requests fusionadas en setenta y un minutos: la primera vez que a esta máquina se le da algo que decidir en lugar de algo que transcribir. Vinieron con ello dos hallazgos, y los dos eran sobre mi trabajo y no sobre el suyo.
Date: 2026-09-02 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 15
Lang: es
Translation_Key: ainarres-first-problem-brief
Image: /assets/images/ainarres-15-first-problem-brief-hero.webp

---

# AINARRES: el primer encargo que no era una especificación

En la entrega anterior le pasé el asiento del operador a un agente que no había construido nada del sistema, no le di ninguna clave de firma, y miré cómo sacaba adelante una entrega. Aguantó. También encontró dos sitios donde la máquina se comportaba correctamente y se explicaba mal, que es una categoría que yo no tenía forma de ver desde dentro.

Esta semana ese mismo operador dejó de estar a prueba y se puso a trabajar. Y lo que cambió no fue el operador. Fue la forma de lo que le di.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que puedo hacer" y "esta ya está". **No hay orquestador.** Entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *fabricantes distintos* como iguales, luego le enseñaron a gobernarse y a vigilar su propia salud, convirtieron el bucle en un servicio permanente, le enseñaron a despertar solo a los trabajadores que el trabajo en espera necesita, y le dieron a la silla del operador un nombre y una credencial que no se emite a sí mismo.

## Catorce entregas de especificaciones disfrazadas

Tengo que confesar algo sobre todos los "encargos" de esta serie hasta ahora.

Eran especificaciones. No en la intención — de hecho. Cuando escribí la petición que produjo la última entrega, nombré la función a extender, puse entre comillas las cadenas exactas que debía imprimir, enumeré las aserciones de aceptación una por una, y especifiqué en qué fichero iban las pruebas. Luego un modelo diseñador lo leyó y produjo una tarea, y yo anoté el coste de diseño como una partida.

Lo que significa que llevaba tiempo pagándole a un diseñador para que reformulara mi propia especificación en el vocabulario del sistema. Los 860.000 tokens que gastó el diseñador en la entrega 14 compraron una *traducción*. El pensamiento ya había ocurrido — en mi cabeza, delante del teclado, antes de que la máquina viera nada. Lo llamaba encargo porque llegaba por el canal de entrada, y al canal le da igual lo que lleve dentro.

La petición de esta semana era distinta, y quiero citar la parte que importaba:

> Este encargo deliberadamente no es una especificación de implementación. El diseñador lo descompone. Si el DAG no se puede construir a partir del ADR sin inventar un enrutador ni eliminar el sondeo, dilo y para — no publiques un diseño distinto.

Tres cosas en un párrafo. Un **problema** en lugar de una solución: *el supervisor en reposo solo se da cuenta del trabajo nuevo en su siguiente tic de sondeo, hasta quince segundos tarde; elimina esa latencia*. Un **contrato que no puede reabrirse**: la decisión ya estaba tomada en un registro de arquitectura hace meses, y los invariantes estaban enumerados — el sondeo se queda como red permanente, el servicio nunca se convierte en enrutador, la notificación no carga ninguna verdad. Y por último, **permiso para negarse**: si esto no se puede construir dentro de esas restricciones, dilo y para.

Esa última frase es la que nunca había escrito antes. Todo lo anterior delega una tarea. Ella delega un juicio.

## Lo que volvió

Setenta y un minutos, cuatro pull requests fusionadas y — la parte que no esperaba — un **grafo de dependencias** en lugar de una lista.

En dieciocho segundos el diseñador creó cuatro tareas. Dos sin requisitos previos. Una condicionada a la primera. La última condicionada a dos de las otras. Nadie le dijo que troceara el trabajo así; la forma salió de leer el registro de arquitectura y darse cuenta de qué piezas no podían probarse hasta que existieran otras.

```
A  el disparador del sustrato    pickle → sonnet → grok       listo 16:50
B  las primitivas de despertar   pickle✗ grok → grok → grok   listo 17:01
C  cablear el supervisor         pickle (20 min) → grok       listo 17:31
D  demostrar el criterio de      grok → grok → grok           listo 17:39
   aceptación
```

La funcionalidad en sí es pequeña y merece una frase, porque su diseño es la parte interesante. El tablero ahora emite una notificación de base de datos cada vez que una escritura podría hacer que hubiera trabajo reclamable — una tarea nueva, un avance, un desbloqueo. El supervisor permanente escucha ese canal y despierta de inmediato en vez de dormir su intervalo completo. Y el intervalo **se queda**, de forma permanente, porque una tarea que se vuelve reclamable porque un arrendamiento caducó en silencio no escribe ninguna fila y por tanto no emite nada. La notificación es una optimización sobre el sondeo, nunca un reemplazo. Una notificación que no llega cuesta latencia. Nunca puede costar trabajo.

Esa distinción — *esta señal no carga verdad, así que perderla solo puede salir lento* — está escrita en la cabecera de la propia migración, junto con una nota de que es una excepción deliberada y acotada a una regla que el proyecto por lo demás respeta, y de que una migración que *sí* persistiera o decidiera algo sería una señal de parar y repensar, no una porción para construir.

Ese párrafo no lo escribí yo. Yo escribí la restricción sobre la que razona.

## Dónde se fue el dinero, y qué dice eso

Diez millones cuatrocientos mil tokens para el epic. La distribución es lo interesante:

```
claude-code+opus     diseñador      6.042.899      58%
opencode+big-pickle  implementador  2.583.742      25%
claude-code+sonnet   revisor        1.317.000      13%
grok+grok-4.6        revisor          198.819
grok+grok-4.6        diseñador        201.343
grok+grok-4.6        integrador        47.342
                     barridos vacíos  159.707
```

El diseño es el 58% de la ejecución. En la entrega anterior llamé a esa forma un impuesto, y lo es — pero esta vez compró un grafo de dependencias derivado de un documento de restricciones, no una reformulación de algo que yo ya había decidido. El mismo coste, una compra completamente distinta. Si quieres un número para lo que cuesta "darle un problema en vez de una especificación", son unos seis millones de tokens de alguien pensando.

Otras dos cosas de esa tabla merecen nombrarse.

**El implementador barato falló una porción real.** La porción B la implementó el modelo local barato, la revisó el modelo de frontera y la **rechazó** — luego la reimplementó la propia familia del revisor y se publicó. Ese mismo modelo barato se pasó después veinte minutos largos con la porción C, la más difícil, y la pasó por revisión a la primera. Así que "por defecto, lo barato" es correcto y tiene un modo de fallo, y el modo de fallo es visible en el registro y no en una intuición. Que es exactamente la evidencia desde la que debería tomarse una decisión de asientos.

**Y una fila vuelve a decir `desconocido`.** El modelo de frontera publicó tres implementaciones y su coste como implementador está sin medir.

## La lección que aprendí la semana pasada, llegando otra vez un nivel más arriba

El hallazgo entero de la entrega anterior fue un `desconocido` que no era desconocido: un barrido cruzaba dos etapas, reportaba sus tokens una vez al final, y la vista cargaba la *última* capacidad — así que el integrador leía `desconocido` mientras su coste estaba bajo el revisor. Lo arreglé cargando la **primera** capacidad que ejerció el barrido, escribí una enmienda sobre por qué un número mal archivado es peor que uno que falta, y publiqué mil palabras sobre cómo no se puede auditar la legibilidad de un sistema desde dentro de tu propia familiaridad con él.

Luego esta ejecución produjo la misma palabra en otra fila, y al ir a mirar descubrí que había arreglado el caso y me había perdido la clase.

Aquí va un barrido real del miércoles por la tarde. Un agente, un despertar, **un** reporte de tokens:

```
17:26:45  reviewing    → integrating     (porción C)
17:28:01  integrating  → validating      (porción C)
17:31:11  validating   → done            (porción C)
17:32:04  proposed     → designing       (porción D)
17:32:25  designing    → implementing    (porción D)
17:35:48  implementing → reviewing       (porción D)
17:36:29  reviewing    → implementing    (porción D — rechazó su propio trabajo)
17:37:03  implementing → reviewing       (porción D)
17:37:18  reviewing    → integrating     (porción D)
17:38:39  integrating  → validating      (porción D)
17:39:42  validating   → done            (porción D)
17:39:53  USAGE  201.343 tokens
```

Once transiciones. Dos tareas. Cinco capacidades distintas — revisor, diseñador, implementador, integrador y revisor otra vez. Un número al final.

Yo había estado discutiendo sobre *qué transición dentro de una tarea* debía cargar con la factura. El problema real de granularidad es **qué tarea dentro de un barrido**, y un barrido no es una tarea. Es todo lo que un agente consigue drenar del tablero antes de parar. El reporte individual más grande del diseñador — cinco millones de tokens — cubre un barrido que diseñó *dos* porciones y está anclado, entero, a una de ellas. La otra registra una entrega que aparentemente no costó nada.

Así que las cifras por entrega de la tabla de arriba están dividiendo un número a nivel de barrido por un recuento a nivel de transición. Son aritmética. No son medición. Y `implementador: desconocido` para el modelo de frontera no es un hueco en los datos — es el mismo archivado erróneo de la semana pasada, un nivel de anidamiento más arriba, producido por el mismísimo arreglo que escribí para evitarlo.

Y el motivo por el que se rompió vale más que el fallo. El arreglo de la semana pasada era correcto para la evidencia de la semana pasada — y esa evidencia venía de ejecuciones en las que cada barrido tocaba exactamente una tarea, porque yo venía escribiendo encargos lo bastante pequeños para que siempre fuera así. **Un encargo con forma de problema produce barridos más grandes.** La suposición no falló por descuido; falló porque lo que se estaba midiendo se volvió más capaz que la medición. Ese es el tipo bueno de roto. El arreglo que queda es la opción cara que me salté — reportar el gasto por transición y no por barrido — y es ya lo más valioso de la lista.

## Dos avisos que levantó el operador, y que se negó a cerrar

El operador escribió su propio resumen de la ejecución, e hizo algo que quiero dejar anotado: **se negó a dar el visto bueno a su propio trabajo.** Dos puntos, los dos correctos.

**La revisión entre familias distintas fue tres de cinco, no cinco de cinco.** Dos de las porciones las revisó la misma familia que las implementó. Este proyecto trata la revisión cruzada como algo *medido, no impuesto*, a propósito — imponerla significaría que una entrega se atasca cuando un par está caído, y se juzgó que la resiliencia valía más que la garantía. Así que esto es el diseño funcionando exactamente como se especificó, y produciendo un resultado más débil del que querrías. Nombrarlo es todo el sentido de medirlo.

**Y el mejor.** El supervisor en vivo lleva una hora y cincuenta y seis minutos corriendo. Reporta un intervalo de sondeo de quince segundos. El despertar por notificación está en la rama principal — las cuatro porciones fusionadas, las pruebas en verde — y el código que fija el intervalo a su nueva cadencia de red de sesenta segundos no está en el proceso que corre, porque ese proceso arrancó antes de que existiera nada de aquello.

Léelo otra vez. **La máquina construyó la funcionalidad que elimina su propia latencia, la fusionó, y sigue corriendo sin ella.** No puede recoger sus propias mejoras sin ser reiniciada, y el operador puede *leer* el proceso, y *pararlo*, y no puede *arrancarlo*.

Ese hueco no es nuevo. Lo dejé sin construir a propósito en la entrega 14 — el asiento no tiene forma de arrancar el servicio sin `make`, y dije que dejaría que la prueba me dijera si importaba. La prueba ya me lo ha dicho. No importa para *operar* una máquina que corre, que es lo que yo pregunté. Importa enteramente para *publicar hacia* una. Un servicio siempre encendido que no puede redespegarse a sí mismo tiene un traspaso a un humano enterrado al final de cada entrega que hace, y nadie se había dado cuenta porque hasta esta semana las entregas eran líneas de informe que surtían efecto la siguiente vez que alguien ejecutaba un comando.

## Lo que deliberadamente no hice

- **No reinicié el servicio.** El operador lo señaló y lo dejó en paz, que fue lo correcto — rebotar un supervisor en vivo es exactamente la clase de acto que debería ser de un humano hasta que exista un comando de ciclo de vida con un registro detrás. Así que la propia funcionalidad de la ejecución sigue esperándome.
- **No arreglé la medición por barrido.** Nombrarlo la misma semana en que publiqué un arreglo para su hermano pequeño es más útil que publicar un segundo parcial. Primero va al registro.
- **No toqué la garantía de revisión.** Hacer obligatoria la revisión entre familias cambiaría resiliencia por una propiedad que ya puedo ver. La medición se queda; la imposición sigue apagada.
- **Sigue sin haber enrutador.** El servicio ahora despierta más rápido. Sigue sin tener opinión alguna sobre *quién* reclama qué. Es la decimocuarta entrega consecutiva en la que aparece esta frase, y sigue siendo el muro de carga.

## Dónde nos deja esto

El resultado interesante de esta semana no fue la funcionalidad. Fue descubrir qué pasa cuando la petición deja de contener la respuesta.

Durante catorce entregas había estado escribiendo especificaciones y llamándolas encargos, y pagando a un diseñador para traducirlas, y leyendo el coste resultante como sobrecarga. Era sobrecarga — porque no quedaba nada que decidir. Dale a la misma maquinaria un problema de verdad, un contrato que no puede reabrir, y permiso explícito para negarse, y el mismo gasto compra un grafo de dependencias, una excepción acotada argumentada en la cabecera de una migración, y cuatro porciones que no podrían haberse escrito en el orden equivocado.

También hizo eso que ya espero de un sistema que de verdad funciona: produjo dos hallazgos que yo no podría haber generado solo, y los dos eran sobre *mi* trabajo y no sobre el suyo. Mi métrica de gasto se apoyaba en una suposición que mis propias costumbres venían imponiendo. Mi decisión de dejar sin construir el ciclo de vida del servicio era correcta para la pregunta que hice y equivocada para la que debería haber hecho. Una máquina que solo confirma tu diseño no te está contando nada.

Así que déjame decir el hito con claridad, porque los dos hallazgos de arriba son la parte interesante y no la importante. **A los nueve meses, esta cosa cogió el enunciado de un problema y un documento de restricciones y devolvió una descomposición correcta** — cuatro porciones en orden de dependencia que nadie especificó, derivado de darse cuenta de qué piezas no podían probarse hasta que existieran otras; una excepción acotada a una de las propias reglas del proyecto, argumentada en la cabecera de la migración que la toma; y una red de seguridad permanente mantenida en su sitio para un modo de fallo que el registro de arquitectura predijo y la implementación respetó. Cuatro familias, dos fabricantes, setenta y un minutos, ningún humano en el bucle entre la petición y las fusiones.

Nueve meses de esto han ido de sacar humanos del camino crítico. Esta semana resultó que el último paso humano estaba justo al final, sosteniendo un comando de reinicio, delante de una máquina que acababa de terminar de construir aquello que estaba esperando.

---

## Para seguir leyendo

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **6**: [El día que borró su propio tablero](/blog/ainarres-el-dia-que-borro-su-tablero).
- Entrega **11**: [AINARRES: se acabó la manivela — la máquina que se ejecuta sola](/blog/ainarres-se-acabo-la-manivela).
- Entrega **13**: [AINARRES: el asiento del operador](/blog/ainarres-el-asiento-del-operador).
- Entrega **14**: [AINARRES: alguien más se sentó en el asiento](/blog/ainarres-alguien-mas-se-sento-en-el-asiento).

*(Nota de transparencia, como en cada entrega: este artículo lo ha escrito un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IA coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio y en el tablero — las cuatro tareas creadas en dieciocho segundos con dos de ellas condicionadas a las otras, la porción rechazada del modelo barato y su éxito de veinte minutos en la difícil, el barrido único de once transiciones a través de dos tareas y cinco capacidades reportado como un solo número, los cinco millones de tokens anclados a una de las dos porciones que diseñaron, y el supervisor que lleva una hora y cincuenta y seis minutos encendido reportando el intervalo de sondeo de una versión anterior a él.)*
