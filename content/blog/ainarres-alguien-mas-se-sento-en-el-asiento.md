---
Title: 'AINARRES: alguien más se sentó en el asiento'
Description: La entrega anterior construyó un asiento para un agente operador y un sobre de credenciales para que no tuviera que sostener la clave de firma. Y luego lo conduje yo, lo cual no demuestra nada. Esta vez se sentó un agente que no había construido nada de ello, sin clave, y sacó adelante una entrega completa. La frontera aguantó. Lo que se rompió fueron dos cosas que funcionaban exactamente como estaban diseñadas — e ilegibles para cualquiera que no supiera ya la respuesta.
Date: 2026-08-30 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 14
Lang: es
Translation_Key: ainarres-first-external-operator
Image: /assets/images/ainarres-14-first-external-operator-hero.webp

---

# AINARRES: alguien más se sentó en el asiento

La entrega anterior terminaba con una promesa que no había cumplido: *"Todavía sin aislamiento forzado. Ejecutar el asiento como un usuario distinto sin acceso de lectura al secreto convertiría la detección en prevención. Está documentado, es un paso de despliegue, y no está hecho."*

Sigue sin estarlo. Pero hice algo que llevaba aún más tiempo posponiendo, y resultó importar más: me levanté de la silla.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que puedo hacer" y "esta ya está". **No hay orquestador.** Entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *fabricantes distintos* como iguales, luego le enseñaron a gobernarse y a vigilar su propia salud, nombraron los roles de los dos extremos de la tubería, convirtieron el bucle en un servicio permanente, le enseñaron a despertar solo a los trabajadores que el trabajo en espera necesita, y le dieron a la silla del operador un nombre propio.

## Construir un asiento para alguien, y luego sentarte tú

La entrega 13 construyó dos cosas. Un **asiento**: una identidad registrada para el operador, con una lista escrita de lo que puede sostener y — más al grano — de lo que nunca podrá. Y un **sobre**: un pequeño intermediario que guarda la clave de firma y no hace más que firmar, mientras es la *base de datos* la que decide qué puede ir dentro del token.

El argumento a favor del sobre era que un agente operador no debería sostener la clave. La evidencia a favor era que yo lo había escrito.

Porque después de construirlo, lo probé como se prueba cualquier cosa que acabas de construir: usándola. Yo sabía dónde estaba el intermediario. Sabía qué comando pide su propio token y cuál necesita que se lo pases. Sabía, sin tener que mirarlo, que la credencial de solo lectura no puede escribir. Cada una de esas piezas de conocimiento es invisible para ti cuando ya la tienes, y cada una estaba haciendo un trabajo que yo le atribuía al código.

Así que esta semana se sentó, en mi lugar, un agente que no había construido nada de aquello. Recibió el repositorio, la carpeta de habilidades como puerta de entrada, la ruta a la clave del intermediario — y ninguna clave de firma. Sus instrucciones fueron las que recibe cualquier operador: mete un trabajo en el sistema, refínalo, mira cómo el enjambre lo entrega, e intervén solo si algo va mal.

## Lo que aguantó

La medida que me importaba es una vista llamada `unbrokered_operator_acts`. Lista la actividad del operador sin ninguna credencial emitida detrás — el asiento firmándose sus propios permisos, o un humano en la terminal. En la entrega anterior la describí como la pieza que carga con toda la decisión de diseño, porque en una sola máquina el sobre no puede *impedir* que un asiento con acceso a una shell lea el fichero de la clave. Solo puede hacerlo visible.

Después de una entrega completa:

```
api.unbrokered_operator_acts  →  []
```

Vacía. Ocho credenciales, todas emitidas por el intermediario, todas con las capacidades aprovisionadas del asiento sin editar, ninguna con más de quince minutos de vida. Seis de las ocho eran de solo lectura — pidió por defecto la credencial *más débil* y solo escaló cuando necesitaba escribir, que es el comportamiento que esperas y no puedes dar por supuesto.

La segunda medida es más simple y me gusta más. Estos son todos los eventos que el asiento produjo en el tablero:

```
17:54:10  claimed      intake
17:54:10  transition   intake
```

Dos. Los dos en el carril de entrada. **Cero** en el carril de desarrollo. Metió el trabajo, lo entregó, y luego miró cómo otras cuatro familias — un diseñador, un implementador, un revisor, un integrador — hacían el trabajo de verdad durante los diez minutos siguientes sin tocar nada. El asiento no tiene rol de implementador ni capacidad de integración, así que el sustrato lo habría rechazado. Nunca llegó a que lo rechazaran.

Hay una recursión agradable en lo que el enjambre construyó mientras era observado: la línea de informe que muestra *el gasto que no movió nada* — la clase de derroche que la entrega 13 descubrió que el sistema estaba diseñado para no ver. La carga de trabajo de la prueba era hacer visible el gasto. Guarda esa idea.

## Lo que se rompió, parte uno: un "desconocido" que no lo era

Al terminar la ejecución leí el historial — la señal por familia y por capacidad que dice quién entregó qué y cuánto costó. Una línea estaba mal de una forma que no esperaba:

```
grok+grok-4.6   role:reviewer      1 entregada    52.536 tokens
grok+grok-4.6   role:integrator    1 entregada    desconocido
```

¿El integrador entregó algo y costó *nada*? La línea temporal lo explica de inmediato:

```
18:02:04  integrating → validating   "fusionado"        necesita role:integrator
18:03:54  validating  → done         "verde en main"    necesita role:reviewer
18:04:05  usage        52.536 tokens
```

Un agente, un despertar, dos etapas. Fusionó la pull request, luego confirmó que la fusión estaba verde en la rama principal, y reportó su gasto de tokens una sola vez al final — como hace cualquier arnés. La vista resolvió ese gasto contra la transición **más reciente** de la familia, que era la segunda. Así que el coste completo de la fusión quedó archivado bajo *revisor*, y *integrador* se quedó sin ningún registro de gasto.

Y aquí está el motivo por el que eso es peor que un hueco. Este proyecto tiene una regla explícita sobre el gasto no medido, escrita hace tres versiones: **desconocido nunca es cero**. Una familia que no hemos medido debe leerse como *desconocida*, porque "cero" dejaría que una familia sin medir parezca barata y gane comparaciones que no se ha ganado.

Pero este `desconocido` no era ninguna de las dos cosas. El gasto existía. Se había capturado. Estaba archivado en el cajón de al lado. Y no hay nada en la vista que distinga *"esto no lo medimos"* de *"lo medimos y lo guardamos en el sitio equivocado"* — lo que significa que la palabra de aspecto honesto era la deshonesta.

Y empeora un paso más. Archivar mal no solo vacía un cajón: **llena otro**. El coste por entrega del revisor incluía ahora una fusión que jamás realizó. Una señal cuyo propósito entero es mantener separados "caro" y "fallando" había empezado, en silencio, a describir a una familia como cara por el trabajo de otra.

El arreglo carga el gasto a la **primera** capacidad que ejerció el barrido en lugar de a la última: el trabajo que se ganó el gasto, no el trabajo en el que casualmente terminó. La mitad sutil es la frontera. "Primera" necesita una ventana, o un trabajador rechazado que rehace algo tendría todos sus intentos posteriores cargados a su primerísima acción, para siempre. La ventana ya estaba ahí: el gasto se reporta una vez por barrido, así que el reporte *anterior* es exactamente donde empieza este.

Es un cambio en una vista: ninguna tabla nueva, ningún verbo nuevo, ningún permiso nuevo. Lo que significa que es una relectura de la historia y no una forma nueva de escribirla, y la ejecución que lo destapó se relee correctamente en el momento en que el arreglo aterriza. El fallo queda corregido retroactivamente en su propia evidencia.

## Lo que se rompió, parte dos: un rechazo que nadie podía leer

La otra nota del operador era más pequeña y, una vez la vi junto a la primera, obviamente lo mismo con otra ropa.

Tenía en la mano una credencial de solo lectura e intentó escribir una línea en el registro del operador. El sistema dijo:

```
42501  permission denied for function record_operator_action
```

Ese rechazo es **correcto**. El rol de solo lectura no ha podido ejecutar nada desde el día en que se creó; ese es todo su sentido. No había nada roto. No había nada inseguro.

Pero léelo como quien sostiene un token cuyo interior no puede ver. Nombra la función. No nombra jamás la credencial. No hay manera de distinguir *"no tengo permiso para esto"* de *"tengo el token equivocado para esto"* — y esas dos cosas tienen remedios completamente distintos. Una significa parar. La otra significa pedírselo al intermediario, lo que lleva dos segundos. El operador lo dedujo y siguió adelante, y luego hizo lo verdaderamente útil: lo mencionó.

Ahora el fallo dice qué credencial tienes en la mano y cuál es la salida. El rechazo no ha cambiado.

## Lo que tienen en común los dos hallazgos

Ninguno era un fallo en el sentido corriente. El sistema de permisos hizo exactamente aquello para lo que fue construido. La vista de gasto siguió la regla que le dieron, y esa regla es correcta para cualquier barrido que toque una sola etapa — que son casi todos.

Los dos eran **comportamiento correcto con una cara ilegible**.

Esa es una categoría que yo no tenía forma de encontrar por mi cuenta, y quiero ser preciso sobre por qué. No por falta de destreza, ni por descuido. Porque en cada uno de estos casos yo ya sabía la respuesta. Sabía que el token de solo lectura no podía escribir, así que nunca sostuve uno y lo intenté. Sabía que nuestro integrador normalmente hace una cosa por despertar, así que nunca miré qué pasa cuando hace dos. Mi conocimiento estaba parcheando los huecos de las explicaciones de la máquina más rápido de lo que yo podía notar que los huecos existían.

No puedes auditar la legibilidad de un sistema desde dentro de tu propia familiaridad con él. Tienes que dárselo a alguien que no sepa ya la respuesta, y leer después lo que tuvo que deducir por su cuenta. Cada uno de esos momentos es un sitio donde el sistema está siendo correcto *contra* alguien en vez de *para* alguien.

Existe una versión de este artículo que dice que la prueba pasó y enumera las casillas verdes. Pasó — el sobre aguantó, el asiento se quedó en su asiento, la entrega salió. Pero las casillas eran la parte que yo podía predecir. Los dos hallazgos eran la parte que no, y venían de la misma fuente: un agente sin historia en este código, haciendo trabajo corriente, chocando con dos paredes que estaban exactamente donde debían estar y que no dijeron nada útil al ser golpeadas.

Ah — y una confesión, porque esta serie no hace triunfalismo. En algún punto de todo esto me convencí de que había roto seis pruebas. Reconstruí la base de datos, lancé la batería, vi seis fallos, reconstruí otra vez, vi los mismos seis. Tardé tres ejecuciones completas en revisar el comando que estaba usando para "resetear" la base de datos, el cual — resulta — lanza la batería entera como último paso. Llevaba rato ejecutando la batería dos veces sobre el mismo tablero y mirando cómo se acumulaban los datos de prueba. Los fallos eran perfectamente reproducibles y enteramente míos. La forma barata de haberlo sabido en diez segundos: también se reproducen en una copia limpia del repositorio.

## Lo que deliberadamente no hice

- **Sigue sin haber aislamiento.** La misma confesión que la vez anterior, sin cambios: el asiento corre en una máquina como un usuario y podría leer el fichero de la clave si decidiera hacerlo. No lo hizo, y la vista demuestra que no lo hizo, y detección no es prevención. Es ya la promesa más vieja del cajón.
- **El asiento sigue sin poder arrancar el servicio.** Puede leer el estado del servicio y pararlo. Arrancarlo significa o un comando en primer plano que lo bloquearía para siempre, o un script que lee el fichero del secreto al levantarse. Así que el humano sigue arrancando la máquina que el operador opera. Lo dejé deliberadamente sin construir para que la prueba le pusiera precio — y la respuesta volvió: no urgente, pero ya no teórico.
- **Los dos poderes irreversibles siguen siendo humanos.** Los vetos permanentes, y su levantamiento, siguen siendo inalcanzables desde cualquier credencial que el sobre vaya a emitir. Verificado, no supuesto: cinco palancas exclusivamente humanas, las cinco rechazadas antes siquiera de entrar en el cuerpo de la función.
- **Sigue sin haber enrutador.** El operador puede decidir quién está sentado. Nunca puede decidir qué tarea va a quién. Cada entrega repite esto. Es el muro de carga.

## Dónde nos deja esto

La entrega 13 escribió una descripción de puesto para un asiento que nadie había tenido nunca que definir. Esta sentó en él a alguien que no me había leído la mente primero, que es la única prueba que una descripción de puesto puede llegar a suspender.

Aguantó. Y produjo exactamente el tipo de hallazgo que no puedes generar desde dentro: dos sitios donde el sistema tenía razón, y era ilegible, y donde tener razón no bastaba. La distancia entre correcto y legible es invisible para quien construyó la cosa, porque su familiaridad está haciendo en silencio el trabajo que debería estar haciendo la explicación.

Nueve meses de este proyecto han ido de sacar humanos del camino crítico. Esta semana fue de otra cosa — de la diferencia entre una máquina que se comporta correctamente y una máquina capaz de *explicarse a un desconocido*. La primera puedes probarla solo. La segunda, por construcción, no.

---

## Para seguir leyendo

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **6**: [El día que borró su propio tablero](/blog/ainarres-el-dia-que-borro-su-tablero).
- Entrega **7**: [AINARRES y el auditor: ¿construimos lo correcto?](/blog/ainarres-el-auditor).
- Entrega **12**: [AINARRES: despertar solo a quien hace falta](/blog/ainarres-despertar-solo-a-quien-hace-falta).
- Entrega **13**: [AINARRES: el asiento del operador](/blog/ainarres-el-asiento-del-operador).

*(Nota de transparencia, como en cada entrega: este artículo lo ha escrito un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IA coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio — la vista vacía que era justamente el objetivo, los dos eventos en el carril de entrada y ninguno en ningún otro sitio, los 52.536 tokens archivados bajo la capacidad equivocada, el error de permisos que nombraba la función y jamás la credencial, y el comando de reseteo que llevaba todo el rato ejecutando la batería de pruebas a mis espaldas.)*
