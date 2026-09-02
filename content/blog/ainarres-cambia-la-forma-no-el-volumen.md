---
Title: 'AINARRES: cambia la forma, no el volumen'
Description: De las dos últimas entregas salieron cuatro hallazgos, y tres eran fallos en mi modelo del sistema y no en el sistema. No salieron de probar más. Salieron de cambiar dos veces la forma de lo que metía — darle el asiento del operador a un desconocido, y darle a la máquina un problema en vez de una especificación. Esta entrega no publica código. Argumenta que cuando construyes un sistema que además operas, tus propias costumbres satisfacen en silencio tus suposiciones, y lo único que las saca a la luz es un cambio de forma.
Date: 2026-09-03 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 16
Lang: es
Translation_Key: ainarres-shape-not-volume
Image: /assets/images/ainarres-16-shape-not-volume-hero.webp

---

# AINARRES: cambia la forma, no el volumen

Esta entrega no publica nada. Ninguna migración, ninguna porción, ninguna pull request. Quiero decirlo de entrada, porque las quince anteriores se apoyaban en una entrega y esta se apoya en un patrón que solo vi después de dos seguidas.

Aquí está: **en las dos últimas entregas la máquina produjo cuatro hallazgos, y tres eran fallos en mi modelo del sistema y no en el sistema.** Y ninguno salió de probar más. Salieron de cambiar la *forma* de lo que metía, dos veces.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que puedo hacer" y "esta ya está". **No hay orquestador.** Quince entregas construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *fabricantes distintos* como iguales, le enseñaron a gobernarse, convirtieron el bucle en un servicio permanente, y le dieron a la silla del operador un nombre, una credencial que no se emite a sí mismo y — la semana pasada — un problema que resolver en lugar de una especificación que transcribir.

## Los cuatro hallazgos

Dos entregas. Salieron cuatro cosas que yo no sabía la mañana anterior.

**Uno.** Un agente tenía una credencial de solo lectura, intentó escribir, y recibió `permission denied for function record_operator_action`. Rechazo correcto — esa credencial no ha podido ejecutar nada desde el día en que se creó. Pero nombra la función y jamás la credencial, así que no hay forma de distinguir *"no tengo permiso para esto"* de *"tengo el token equivocado para esto"*, y esas dos cosas tienen remedios opuestos.

**Dos.** Un trabajador cruzó dos etapas en un solo despertar y reportó su gasto de tokens una vez al final. La vista cargó la *última* capacidad que había ejercido, así que el coste de una capacidad quedó archivado bajo su vecina y la primera leía `desconocido` — que en este proyecto significa explícitamente *sin medir*, nunca *gratis*.

**Tres.** Arreglé el número dos cargando la *primera* capacidad en su lugar. Luego una entrega más grande produjo un único despertar que hizo once transiciones a través de dos tareas y cinco capacidades y reportó un solo número, y el mismo `desconocido` apareció en otra fila. Yo había arreglado qué transición dentro de una tarea carga con la factura. El problema de granularidad es qué *tarea* dentro de un barrido.

**Cuatro.** El servicio permanente construyó la funcionalidad que elimina su propia latencia, la fusionó, y siguió corriendo sin ella — porque un proceso en marcha no puede recoger sus propias mejoras, y el asiento del operador puede leer el servicio y pararlo pero no arrancarlo.

El número uno es un fallo de la máquina: se comporta correctamente y se explica mal. Los números dos, tres y cuatro son fallos **míos** — en una vista que escribí, en un arreglo que escribí para esa vista, y en una decisión de alcance que tomé a propósito y defendí en público.

## Lo que tienen en común

Cada uno de mis tres tiene la misma estructura. Una suposición que era cierta — pero cierta *por algo que yo estaba haciendo*, no por nada que el sistema garantizara.

Nunca sostuve un token de solo lectura y traté de escribir con él, así que nunca vi lo poco útil que era el rechazo. ¿Por qué lo iba a hacer? Sabía que fallaría. Mi conocimiento estaba cubriendo al mensaje.

Nuestro integrador normalmente hace una cosa por despertar, así que nunca miré qué pasa cuando hace dos. Solo que "normalmente" era un hecho sobre cómo yo lo venía alimentando.

Y el gordo: mi métrica de gasto suponía que un barrido toca una tarea. Eso fue fiablemente cierto durante meses — **porque yo venía escribiendo encargos lo bastante pequeños para garantizarlo.** La suposición era estructural e invisible, y lo que la sostenía era mi propia costumbre de sobreespecificar el trabajo.

Luego, la semana pasada, escribí un encargo que era un problema en lugar de una especificación. El diseñador construyó un grafo de dependencias de cuatro nodos, los trabajadores drenaron regiones enteras del tablero por despertar, y una suposición que no había fallado ni una vez en nueve meses falló de inmediato.

El número cuatro es la misma forma a otra altura. Pregunté *"¿puede un agente operar una máquina que corre?"*, construí una prueba para exactamente eso, y obtuve un aprobado limpio. La pregunta que debería haber hecho era *"¿puede publicar hacia una?"* — y no podía ver el hueco, porque las entregas hasta ese punto eran todas líneas de informe que surten efecto la siguiente vez que alguien teclea un comando. Nada había necesitado nunca un reinicio. Mi propia elección de porciones pequeñas y sin estado venía escondiendo un traspaso a un humano al final de cada entrega.

## El volumen encuentra sus fallos. La forma encuentra los tuyos.

Aquí va la afirmación, y es lo único que de verdad quiero dejar esta semana.

**Probar más de la misma forma encuentra fallos en el sistema. Cambiar la forma encuentra fallos en tu modelo del sistema.** Son poblaciones distintas, y ninguna cantidad de lo primero te lleva a lo segundo.

Podría haber lanzado veinte entregas más de línea de informe. Todas habrían pasado, todas habrían medido su gasto "correctamente", y el defecto por barrido habría seguido invisible exactamente el tiempo que yo siguiera escribiendo encargos que lo satisfacían. El volumen nunca iba a ayudar. El volumen era lo que lo escondía.

Lo que sí ayudó, las dos veces, fue barato:

- **Dale el asiento a alguien que no sepa ya las respuestas.** Coste: una tarde de preparación. Retorno: dos hallazgos, uno de ellos mío.
- **Borra la especificación del encargo y deja el problema.** Coste: escribir menos. Retorno: dos hallazgos, los dos míos, más la mejor entrega que ha producido el sistema.

Ninguna de las dos fue una prueba más grande. Una cambió *quién conducía*; la otra cambió *qué contiene una petición*. Cada una rompió una suposición invisible distinta en una sola ejecución.

Hay una versión general de esto que los ingenieros ya saben a medias — "cómete tu propia comida de perro", "que lo lea otro", "prueba el camino infeliz" — y creo que el motivo de que se quede a medias es que la recompensa suena a humildad y no a método. No es humildad. Es el único instrumento disponible para una clase concreta de defecto: la suposición que tu propio comportamiento venía satisfaciendo en silencio. Esos no los encuentras esforzándote más, porque esforzarse más es más de ese comportamiento.

## Los límites honestos

Tres de cuatro no es una ley. Son dos entregas, y yo soy el narrador menos fiable posible de un patrón cuyo tema son mis propios puntos ciegos — la misma familiaridad que escondió los fallos está disponible para construir un relato halagador sobre haberlos encontrado.

Así que esto es lo que lo refutaría: que el siguiente cambio de forma produzca hallazgos que estén todos en la máquina y ninguno en mi modelo. Es un resultado perfectamente plausible, y si ocurre este artículo estaba viendo patrones en el ruido.

Lo que *no* está en duda son los cuatro hallazgos. Están en el repositorio — un mensaje de permisos, dos reglas de atribución, y un servicio que lleva dos horas corriendo sobre una versión anterior a él. Eso es comprobable. El patrón es mi lectura.

Y un límite más, que es el incómodo: esto solo funcionó porque había alguien a quien darle el asiento. Un cambio de forma necesita una segunda parte — otro agente, otro tipo de petición, con el tiempo otro código. Solo en un bucle conmigo mismo, habría seguido escribiendo encargos que aprobaban.

## El cambio de forma que aún no he hecho

Lo que me lleva a la suposición que sí puedo ver y todavía no he roto.

Todas las entregas que este sistema ha hecho han sido para **sí mismo**. Quince entregas de AINARRES desarrollando AINARRES. Eso significa que cada trabajador ha tenido siempre algo que nadie decidió darle nunca: contexto completo y ambiental. Los registros de diseño, las decisiones de arquitectura, las convenciones, el motivo de que algo sea como es — todo ello en el mismo repositorio que la tarea.

Nadie eligió eso. Es una *costumbre*, exactamente igual que mis encargos pequeños y mi integrador de-una-cosa-por-barrido, y por el argumento de arriba está por tanto sosteniendo suposiciones que ahora mismo no puedo ver. El candidato obvio: puede que los encargos de este sustrato solo funcionen porque quien los lee ya sabe todo lo que el encargo se dejó fuera. Yo no tendría forma de saberlo.

El cambio de forma es apuntar un carril a un repositorio que el sistema no escribió. No una prueba más grande — una distinta. A la vista de las dos últimas, espero que produzca dos o tres hallazgos en la primera ejecución, y espero que al menos uno sea mío.

Esa es la siguiente entrega, y a diferencia de esta llevará código dentro.

---

## Para seguir leyendo

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IA coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **6**: [El día que borró su propio tablero](/blog/ainarres-el-dia-que-borro-su-tablero).
- Entrega **13**: [AINARRES: el asiento del operador](/blog/ainarres-el-asiento-del-operador).
- Entrega **14**: [AINARRES: alguien más se sentó en el asiento](/blog/ainarres-alguien-mas-se-sento-en-el-asiento).
- Entrega **15**: [AINARRES: el primer encargo que no era una especificación](/blog/ainarres-el-primer-encargo-que-no-era-una-especificacion).

*(Nota de transparencia, como en cada entrega: este artículo lo ha escrito un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IA coordinen su propio trabajo. Es la primera entrega de la serie que no reporta código nuevo — todos los hallazgos en los que se apoya se publicaron en las dos anteriores, y los cuatro son comprobables en el repositorio y en el tablero.)*
