---
Title: 'AINARRES: el intaker — dar forma a la petición antes del trabajo'
Description: El proyecto ya tiene un auditor que vigila el final del trabajo; esta entrega nombra el rol del principio — el intaker, quien convierte una petición en bruto en un encargo bien formado antes de que empiece nada. Lo bonito es lo poco que costó: la creación en dos niveles, donde el intaker puede abrir una petición pero solo el diseñador puede convertirla en trabajo, salió de una regla que el proyecto ya tenía, sin código nuevo. Con ambos extremos nombrados, toda la cadena — de la persona con una petición al auditor que revisa el resultado — queda por fin nombrada de punta a punta.
Date: 2026-08-16 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 10
Lang: es
Translation_Key: ainarres-intaker
Image: /assets/images/ainarres-10-intaker-hero.webp

---

# AINARRES: el intaker — dar forma a la petición antes del trabajo

La entrega anterior le dio al proyecto un **auditor** con un segundo sentido — vigilar la *salud* de la máquina y su *gasto* una vez hecho el trabajo. Eso es el **final** de la tubería. Esta nombra el rol del **principio**: el **intaker**, quien toma una petición en bruto, a medio formar, y la moldea hasta convertirla en un encargo lo bastante claro como para construir a partir de él.

> ¿Nuevo por aquí? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) es un **sustrato** — un terreno común donde se coordina el trabajo — construido sobre una base de datos. Las tareas son filas; el flujo de trabajo son datos; los agentes son deliberadamente simples y solo preguntan "dame la siguiente tarea que puedo hacer" y "esta ya está". **No hay orquestador.** Entregas anteriores construyeron eso, mostraron que podía desarrollarse *a sí mismo*, luego con muchos agentes a la vez, luego con modelos de *fabricantes distintos* como iguales, y luego le enseñaron a gobernarse y a vigilar su propia salud. Un humano solo arranca el bucle; AINARRES desarrolla AINARRES.

Durante toda esta serie ha habido un rol que el humano seguía interpretando a mano en silencio: **convertir una idea vaga en un encargo bien formado.** Cada vez que le he dado una funcionalidad al bucle, primero me sentaba a *darle forma* — qué se pide exactamente, qué entra en el alcance, qué significa "hecho". Ese moldeado es trabajo de verdad, y tiene un nombre en cualquier equipo que reciba peticiones: el **intaker**, o consultor — la persona que se sitúa entre el cliente y los constructores y fija la petición antes de escribir una línea de código. Esta entrega lo convierte en un rol de primera clase dentro del sustrato, de modo que toda la cadena — **Cliente → Intaker → Diseñador → constructores → Auditor** — queda nombrada de punta a punta.

---

## Dos niveles de "crear trabajo", y una regla que ya teníamos

Aquí está la parte que me parece genuinamente bonita, porque no costó casi nada.

Hay **dos** actos distintos de "crear trabajo" en este proyecto, y no deben ser decisión de la misma persona:

- El **intaker** abre una *petición* — "aquí hay algo que probablemente deberíamos hacer". Eso es el inicio de una conversación, no una orden de trabajo.
- El **diseñador** convierte una petición aceptada en *tareas reales* — la descomposición que recogen los constructores. Eso es comprometer a la máquina a construir.

Quieres un muro entre ambos. Quien puede *plantear* una petición no debería ser automáticamente quien puede *comprometer al equipo a construirla*, y viceversa. Dos creadores, dos niveles.

Ahora bien — el proyecto ya tenía, de la temporada de la federación, una pequeña regla sobre quién puede crear trabajo: **solo puedes crear una tarea en un carril si tienes el rol que da el primer paso sobre ella.** Crear trabajo *es* empezarlo, así que solo quien pudiera dar ese primer paso puede abrirlo. Es guiada por datos — la regla lee el propio flujo de trabajo, sin el nombre de ningún carril escrito en el código.

Así que nombrar al intaker no requirió **ninguna maquinaria nueva.** Añadí un segundo carril — un carril de **admisión** — cuyo primer paso pertenece al *intaker*. Y la regla que el proyecto ya tenía hizo el resto de inmediato:

- un **intaker** puede abrir una petición en el carril de admisión (tiene ese primer paso) — pero **no puede** crear tareas en el carril de construcción (ese primer paso es del diseñador);
- un **diseñador** puede crear tareas en el carril de construcción — pero **no puede** abrir una petición en el carril de admisión (ese primer paso es del intaker).

Dos creadores, dos niveles, **una regla sin tocar.** El núcleo de todo el hito fue un poco de configuración — un carril nuevo, un rol nuevo — y *cero* lógica nueva. Cuando una funcionalidad que esperabas engorrosa resulta ser una consecuencia de una primitiva que ya tenías, suele ser señal de que la primitiva era la correcta.

---

## En qué se convierte el encargo

Una petición que pasa la admisión se convierte en un **encargo**, y el encargo es algo pequeño y deliberado. Cuando el diseñador lo descompone después en tareas de construcción, cada una de esas tareas lleva una discreta referencia de vuelta al encargo del que salió. Ese enlace importa más de lo que parece: es el hilo desde *la petición original* hasta *todo lo construido para ella* — justo el hilo que el auditor necesitaba y no tenía. La temporada pasada el auditor podía señalar un trabajo entregado y preguntar "¿fue una buena entrega?"; ahora puede hacer la pregunta más afilada — "¿respondió todo esto realmente a lo que se **pidió**?". El extremo delantero le entrega al trasero el contrato contra el que audita.

Es también, a propósito, el movimiento más pequeño posible. El encargo es solo una **tarea en un carril** — reutiliza exactamente la misma maquinaria sobre la que ya corre todo lo demás. Ningún objeto "encargo" nuevo, ninguna segunda vía de creación, ningún caso especial. Lo más ligero que podía nombrar el rol, y nada más.

---

## Lo que deliberadamente no hice

La lista honesta, como siempre:

- **La admisión todavía no tiene conversación.** El alma del rol es un ida y vuelta — hacerle al peticionario las preguntas incómodas de aclaración hasta que la petición quede de verdad fijada. v6 no tiene canal para eso, así que en la práctica el "diálogo" sigo siendo yo, editando el encargo a mano. El intaker queda **nombrado y sentado** aquí; un intercambio real cliente↔intaker es tarea del *canal* que viene después.
- **El enlace encargo-trabajo es una convención, no una ley.** Es una referencia que escribe el diseñador, no algo que la base de datos imponga — porque todavía no hay un objeto "encargo" de primera clase contra el que imponerlo. Hacer de la entrega algo real y referenciable (y auditar el *conjunto entero* de tareas de un encargo a la vez) se aplaza, a propósito, a cuando se gane su peso.
- **Una sola persona aún puede llevar ambos sombreros.** En v6 esa persona soy yo — tengo ambos roles, así que puedo abrir una petición y descomponerla. La *separación* es real y demostrable (dale a dos trabajadores distintos los dos roles distintos y a cada uno se le rechaza en el carril del otro), pero si el sistema debería *prohibir* que un trabajador tenga ambos es una pregunta para la era federada, no para esta.
- **Una petición estancada no tiene un "abandonar" formal.** Un encargo que debería morir lo maneja un humano decidiéndolo así; una vía propia de caducar/rechazar en el carril de admisión es un refinamiento posterior. La v1 mantiene el flujo mínimo a propósito.

---

## Ambos extremos, y un enjambre que creció

Con el intaker nombrado, **ambos extremos que el humano solía sostener a mano son ya roles en el sustrato** — el intaker que da forma a la petición que entra, el auditor que vigila la salud y el resultado que sale. La cadena completa queda nombrada por primera vez.

Y una pequeña nota que me agrada, porque es el sentido mismo de este proyecto: esta funcionalidad — la parte del informe que muestra el tablero de admisión — la construyó **el enjambre, sin manos.** En el mismo tramo, la flota también creció: un modelo pequeño nuevo que corre enteramente en mi propio portátil se unió al grupo de constructores (validado antes, contra las herramientas que de verdad tendría que usar — una lección que un modelo anterior, que alucinaba herramientas, enseñó por las malas), y el revisor-integrador de frontera se actualizó a un modelo más nuevo. La frontera actualizada fusionó esta funcionalidad a la línea principal por su cuenta, sin humano haciendo la fusión. La máquina que desarrolla la máquina se hizo un poco más grande, y siguió corriendo.

---

## Dónde nos deja esto

v6 se propuso **sentar los extremos**, y está hecho: el auditor detrás, el intaker delante, ambos aditivos, ambos en manos humanas por ahora, sin cambiar la forma del sistema. Esa fue siempre la mitad modesta del plan.

La mitad inmodesta viene ahora. Ambos roles nuevos se ejercen todavía igual que todo desde la primera entrega — a mano, sobre un bucle que arranco en un portátil. El intaker quiere un **canal** para hablar de verdad con quien tenga una petición; la vigilancia del auditor quiere estar **siempre encendida**, no leerse en un informe cuando me da por mirar. Eso es el **servicio permanente** — AINARRES convertido de un script que ejecuto en algo que corre por sí solo, alimentado por una puerta real, vigilado por estos roles en lugar de por mí. Es también la primera vez que este proyecto daría la cara al mundo exterior, así que viene con una pregunta de seguridad hecha *por delante*, no atornillada después.

El bucle siempre fue andamiaje. Nombrar los extremos era lo último que quedaba antes de construir lo de verdad.

---

## Para seguir leyendo

- **Código:** AINARRES es software libre (Apache 2.0) en [github.com/laanito/ainarres](https://github.com/laanito/ainarres). Las notas de diseño, los planes y las retrospectivas por hito viven en la carpeta `.agents/`, escritas para que las lea cualquier persona o agente.
- Entrega **1**: [AINARRES — un sustrato para que las IAs coordinen su propio trabajo](/blog/ainarres-sustrato-para-que-las-ia-coordinen-su-trabajo).
- Entrega **5**: [AINARRES federado: dos fabricantes de IA en un mismo tablero](/blog/ainarres-federacion).
- Entrega **7**: [AINARRES y el auditor: ¿construimos lo correcto?](/blog/ainarres-el-auditor).
- Entrega **8**: [AINARRES: un enjambre más amplio, y el trabajador que giró en vano](/blog/ainarres-un-enjambre-mas-amplio).
- Entrega **9**: [AINARRES: el segundo sentido del auditor — vigilar salud y gasto](/blog/ainarres-el-segundo-sentido-del-auditor).

*(Nota de transparencia, como en cada entrega: este artículo lo escribió un agente de IA bajo dirección humana, sobre un proyecto cuyo propósito es que las IAs coordinen su propio trabajo. Todo lo descrito aquí es real y está en el repositorio — el rol del intaker y el carril de admisión, la creación en dos niveles que salió de la regla de creación ya existente sin código nuevo, el encargo que enlaza una petición con el trabajo hecho para ella, y el bloque del informe que un enjambre recién crecido construyó y fusionó para sí mismo.)*
