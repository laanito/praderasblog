---
Title: OpenTerminalUI — Retirar la cartera que estaba compartida en secreto
Description: La entrega anterior mostró que la cartera "heredada" era una única tabla compartida por todos los usuarios. Esta cuenta cómo la borramos de verdad — y cómo "solo borra una tabla" significó desenredar trece cosas que se apoyaban en ella en silencio.
Date: 2026-07-05 06:00PM
Template: post
Author: Luis Amigo
Tags: Privacidad, Sistemas, Desarrollo Web
Series: OpenTerminalUI — Bifurcando un terminal financiero
Series_Slug: openterminalui
Series_Order: 6
Lang: es
Translation_Key: openterminalui-retire-shared-portfolio
Image: /assets/images/openterminalui-06-retire-shared-portfolio-hero.webp

---

# OpenTerminalUI — Retirar la cartera que estaba compartida en secreto

[La última vez](/blog/openterminalui-la-cartera-se-vuelve-real) admití algo incómodo sobre OpenTerminalUI: la vieja cartera "heredada" era una única tabla global **sin noción de usuario**. En una instalación autoalojada de una sola persona es invisible; en cuanto dos personas comparten una instancia, verían y editarían *las mismas* posiciones. Para un terminal cuyo argumento incluye la palabra **privado**, eso no es un error pequeño — es una contradicción de la premisa.

Aquella entrega construyó la *rampa de acceso* (una forma de mover tus posiciones a la cartera moderna por usuario) pero se quedó a las puertas de lo difícil. La **v1.1.0** terminó el trabajo: la cartera compartida ya no existe. Esta es la historia de cómo — porque "solo borra la tabla" resultó significar algo mucho mayor.

> Entrega **6** de la serie *OpenTerminalUI*. Continúa directamente desde la [#5](/blog/openterminalui-la-cartera-se-vuelve-real); los dos análisis en profundidad prometidos en la #1 (la auditoría del cableado y la historia del "corre sobre cualquier IA") siguen pendientes como #2 y #3.

---

## Contexto: dos carteras, una de ellas global

Un resumen rápido para quien aterrice aquí primero. OpenTerminalUI es un terminal financiero autoalojado que ejecutas en tu propia máquina. Arrastraba **dos** sistemas de cartera en paralelo:

- **Heredada** — una tabla única y global (`Holding`: ticker, cantidad, coste medio, sin `user_id`). Sin efectivo, sin transacciones. Pero era sobre la que estaban construidas las analíticas ricas.
- **Manager** — un sistema más nuevo, por usuario: varias carteras con nombre, un libro de transacciones de verdad, el trabajo de efectivo y P&L de la versión anterior. (*P&L = pérdidas y ganancias.*)

El plan siempre fue hacer del Manager la *única* cartera y borrar la global. Lo intimidante no era el borrado — era todo lo que colgaba de ella.

---

## El problema: "borrar una tabla" era mentira

Entré esperando quitar un modelo, un par de rutas y una página. Luego busqué de verdad quién lee esa tabla global. La respuesta fueron **trece** sitios — y no periféricos:

- las superficies obvias — inicio, cabina, la superposición de estado, el launchpad, el panel de correlación;
- y subsistemas analíticos enteros — **métricas de riesgo, pruebas de estrés, análisis de factores, dividendos, el generador de informes**, incluso el **contexto del plugin de IA** que entrega "tu cartera" a un modelo de lenguaje.

Varios tenían un comentario que decía, casi con vergüenza, *"traer todas las posiciones del usuario actual"* — sin hacer tal cosa; leían las de todos. Cada uno era a la vez una **fuga de privacidad** y una **dependencia dura**: en cuanto la tabla desapareciera, se romperían. No puedes borrar la cosa hasta que nada la necesite — y trece cosas la necesitaban.

---

## Qué hicimos

La forma del arreglo salió de una decisión y una negativa.

**Una única fuente de verdad para "tu cartera".** Añadí una **cartera principal** por usuario — sencillamente, la primera que creaste — y una única función pequeña que devuelve sus posiciones en la *forma exacta* que producía la vieja tabla global. Cada punto de llamada que antes preguntaba a la tabla global ahora pregunta a esta función. Como la forma coincidía, la mayoría de consumidores cambiaron en **una línea**. Los trabajos en segundo plano (ingesta de noticias, precarga de precios) que legítimamente necesitan *todos* los tickers en cartera recibieron un ayudante aparte que devuelve solo la unión de símbolos — sin datos por usuario, así que sin fuga.

**Una negativa a hacerlo de golpe.** Borrar una tabla que sostiene medio sistema en un solo commit es cómo se consigue un diff aterrador e irrevisable y un mal fin de semana. Así que se dividió limpiamente en dos:

1. **Reapuntar todo** a la fuente por usuario — mientras la vieja tabla aún existe físicamente. Tras este paso la fuga está cerrada *en todas partes*, pero nada se ha borrado, así que es fácil de verificar.
2. **Borrar** la tabla, sus rutas y el código muerto — ahora que está demostrado que nada la lee.

**Paridad antes del borrado.** La vista heredada tenía analíticas que el Manager no: una matriz de correlación, una curva de comparación con el índice, las métricas de riesgo más completas y la **atribución** de Brinson (una forma estándar de explicar *por qué* una cartera batió o quedó por detrás de su índice). Borrar la vieja vista habría quitado funciones en silencio. Así que primero lo porté todo al Manager por usuario — más la narrativa de riesgo con IA y el backtester — y solo entonces retiré la pantalla vieja. Un lector, revisando, incluso pilló dos que se me habían pasado (la narrativa de IA y el backtesting); entraron antes de borrar nada.

Entonces, y solo entonces: la tabla global `Holding`, sus endpoints `/api/portfolio`, el código de lotes fiscales y el propio botón de migración (su razón de existir había desaparecido) se eliminaron. El feed de listas de seguimiento, que siempre debió ser global, quedó exactamente igual.

---

## Por qué este camino

- **Imita la forma vieja y la migración se vuelve aburrida.** La tentación es "modernizar" cada punto de llamada ya que estás ahí. Resistirlo — devolver la forma *heredada* desde la fuente nueva — convirtió un refactor aterrador en trece ediciones de una línea y mantuvo el riesgo en una función pequeña y bien probada.
- **Reapuntar y luego borrar.** Dos pasos honestos y revisables ganan a un diff heroico. Tras el primer paso el problema de privacidad ya estaba resuelto; el segundo fue solo quitar código demostradamente muerto.
- **La paridad es una condición previa, no un seguimiento.** "Ya volveremos a añadir las analíticas" es como mueren en silencio las funciones en un refactor. Portarlas *primero* hizo que el borrado quitara solo duplicación, nunca capacidad.

---

## Impacto

La cartera compartida entre usuarios ya no existe en ninguna parte del código. Cada superficie — paneles, riesgo, estrés, factores, dividendos, informes, el contexto de IA — lee la cartera propia del usuario conectado, por construcción. La API de cartera es ahora enteramente por usuario. Y el cambio neto fueron **unas 850 líneas menos**: el tipo de versión más satisfactorio, donde "hecho" se ve como menos código, no más.

Para quien se autoaloja en solitario nada se rompe visiblemente — que es el punto; el arreglo era para el caso multiusuario que traicionaba en silencio la promesa de "privado". Por el camino, la cartera por usuario también ganó una pequeña mejora de ergonomía: un botón **Vender** en cada posición que precarga el formulario de operación para esa posición exacta, en vez de reescribirlo.

---

## Alcance: lo que esto *no* es

- **La contabilidad de lotes fiscales sigue aparcada.** Se fue con el código heredado y no vuelve pronto — es un agujero específico de cada jurisdicción donde un número *erróneo* es peor que ninguno, y fácil de confundir con asesoramiento fiscal.
- **La narrativa de riesgo con IA es bajo demanda.** Corre en tu modelo local (Ollama por defecto), así que espera un clic en vez de gastar un minuto de inferencia cada vez que se carga una cartera; sin modelo configurado, lo dice claramente en lugar de inventar un análisis.

---

## Qué viene después

Con las dos mitades — la integridad pasiva y la cartera privada — ya hechas, la próxima versión se apoya en el lado activo de la estrella polar: **investigación que interroga**, una capa de "¿esto es puro humo?" que use el modelo local y tus propias notas de forma *adversaria*, no como animadora. Y los dos análisis en profundidad aún pendientes de la #1: la auditoría del cableado y la reconstrucción del "corre sobre cualquier IA".

---

## Lecturas relacionadas

- [OpenTerminalUI — Hacer real la cartera (sin mentir sobre ella)](/blog/openterminalui-la-cartera-se-vuelve-real) — #5: el esqueleto de efectivo, el P&L honesto y dónde empezó esta historia de privacidad.
- [OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) — #4: por qué la primera versión estable lo gastó todo en no mostrar datos inventados como reales.
- [OpenTerminalUI — Bifurcar un terminal financiero para trabajar más allá de India](/blog/openterminalui-bifurcar-terminal-financiero-de-india) — #1: la premisa y la reconstrucción de la capa de datos.
- **Código:** el fork vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(Una nota de transparencia, en el espíritu de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que hizo la ingeniería que describe.)*
