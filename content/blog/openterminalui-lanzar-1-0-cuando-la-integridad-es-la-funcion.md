---
Title: OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función
Description: Cómo cortamos la primera versión estable de un terminal financiero autoalojado dedicando todo el presupuesto a una sola cosa — no mostrar nunca datos inventados como si fueran reales.
Date: 2026-06-29 07:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Economia
Series: OpenTerminalUI — Bifurcando un terminal financiero
Series_Slug: openterminalui
Series_Order: 4
Lang: es
Translation_Key: openterminalui-stable-1-0
Image: /assets/images/openterminalui-04-stable-1-0-hero.webp

---

# OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función

Tras meses bifurcando, reconstruyendo y "des-Indianizando" un terminal financiero al estilo Bloomberg, etiquetamos la **v1.0.0**. Lo curioso —y el sentido de este artículo— es que la 1.0 casi no añadió funciones nuevas. Gastamos toda la versión en una sola promesa, nada vistosa: **el terminal nunca te mostrará un número inventado como si fuera real.**

> Esta es la entrega **4** de la serie *OpenTerminalUI* — el hito de la 1.0. Las dos entregas en profundidad prometidas antes (la auditoría de cableado y la historia de "corre sobre cualquier LLM") siguen en camino como la #2 y la #3; esta es el broche.

---

## Contexto: un terminal en el que se supone que confías con tu dinero

OpenTerminalUI es un terminal financiero autoalojado —gráficos en vivo, *screeners*, carteras, analítica de opciones, una capa de investigación con IA— que ejecutas en tu propia máquina. Las entregas anteriores contaron [por qué lo bifurcamos y reconstruimos su capa de datos centrada en India](/blog/openterminalui-bifurcar-terminal-financiero-de-india) para mercados de EE. UU., Europa y cripto.

La bifurcación tiene una estrella polar: un terminal abierto y privado que ayuda a una persona a invertir **sin que la engañen** — ni el mercado, ni el ruido, ni la propia herramienta. Esa última parte resulta ser la difícil. Una app del tiempo que se equivoca es una molestia menor. Una herramienta financiera que te muestra un número preciso, seguro y **equivocado** es peligrosa, porque la precisión se lee como verdad.

Un par de términos, definidos una vez:

- **Datos simulados (*mock* / *fallback*)** — valores de relleno que un programa sirve cuando la fuente real falla, para que la pantalla no quede vacía.
- **Degradado** — nuestra etiqueta para "aquí no tenemos datos en vivo ahora mismo", mostrada con honestidad en lugar de rellenada.

---

## El problema: software que miente con educación

Al auditar el *backend* antes de la 1.0 encontramos un patrón recurrente y bienintencionado: cuando faltaba una fuente real de datos o estaba limitada por cuota, el código **se inventaba algo** para que la interfaz pareciera viva. Los ejemplos daban que pensar:

- El panel de materias primas servía `2100.0 + random()` como precio "en vivo" del oro.
- Los gráficos tenían un *fallback* de paseo aleatorio — los *backtests* podían correr sobre un histórico de precios que **nunca ocurrió**.
- El libro de órdenes se generaba a partir de un *hash* del nombre del símbolo — una escalera verosímil de pura ficción.
- Las pantallas de Fuerza Relativa devolvían *blue-chips* indios fijos en el código, presentados como rankings en vivo.

Nada de esto era malicioso. Es el camino de menor resistencia bajo la regla "nunca muestres un panel vacío". Pero para una herramienta cuya razón de ser es *no dejarse engañar*, las mentiras educadas son el peor error posible. No revientan, no dejan rastro en los registros: simplemente engañan en silencio.

---

## Lo que hicimos: convertir "no lo sé" en una respuesta de primera clase

El arreglo no fue un parche; fue una convención y un barrido.

| Bloque | Qué cubrió |
|--------|------------|
| **A — Integridad** | Auditar cada punto de invención; cablear datos reales o marcarlo como degradado |
| **B — Defaults sin India** | Valores por defecto occidentales para una identidad de versión coherente |
| **C — Robustez** | Replegarse ante APIs limitadas por cuota en vez de martillearlas |
| **D — Contrato de versión** | Un único número de versión real, expuesto con honestidad |
| **E — Documentación** | Escribir, en claro, qué funciona y qué no |

El núcleo es el bloque A. Establecimos una única **convención de datos degradados**: cuando no hay fuente en vivo, la API devuelve un resultado *vacío* más un pequeño marcador legible por máquina — un motivo (`sin fuente en vivo`, `falta clave de API`, `límite de cuota`) y la fuente que intentó. El *frontend* lee ese marcador y muestra un aviso. La regla cabe en una frase: **nada inventado puede parecer real.** Precio del oro, gráficos, libro de órdenes, fuerza relativa — cada punto de invención pasó a ser dato real o un vacío honesto con su motivo.

Alrededor de eso, el trabajo de apoyo:

- **Robustez (C).** Un reintento con *jitter* compartido más un *circuit breaker*, de modo que cuando una API de cuota gratuita empieza a devolver "demasiadas peticiones", el terminal se repliega y sirve datos en caché en vez de aporrear a un proveedor ya caído. Las respuestas de los proveedores de datos ahora se cachean en una capa persistente, así peticiones idénticas no vuelven a gastar la cuota diaria.
- **Un contrato de versión real (D).** El proyecto había derivado a *dos* números de versión — el *frontend* decía `0.4.0`, el *backend* `0.2.0`. Los reconciliamos en un único `1.0.0`, hicimos que el *frontend* derive su versión de una sola fuente, y lo expusimos en el *endpoint* de salud y en el pie de la interfaz.
- **Documentación honesta (E).** Una matriz de "qué funciona sin claves vs. qué necesita una clave de API", y una página de **Limitaciones** que dice en voz alta lo incómodo: no hay fuente gratuita de calendario económico en vivo (servimos una muestra claramente etiquetada); las fechas futuras de dividendos son *estimaciones*; varias pantallas —bonos, *movers*, operaciones de *insiders*, la cinta— son vacíos honestos hasta que se cablee un *feed* real.

---

## Por qué integridad antes que funciones

Habría sido más *divertido* añadir funciones. A propósito no lo hicimos, por tres razones.

- **La confianza es el producto.** Para un terminal de investigación, una función que miente de vez en cuando vale *menos que cero* — envenena la confianza en cualquier otro número de la pantalla. Un panel vacío que dice "sin fuente en vivo" no te cuesta nada; un precio del oro falso te puede costar una operación.
- **Un número de versión es una promesa.** Llamar a algo `1.0` dice "puedes construir sobre esto". No queríamos que la primera etiqueta estable se asentara sobre una capa de gráficos de paseo aleatorio y libros de órdenes con *hash*. La 1.0 tenía que significar *honesto*, no *completo en funciones*.
- **Lo vacío es recuperable; lo falso no.** Un marcador de degradado es una lista de tareas — te dice (a ti y a quien contribuya en el futuro) exactamente dónde falta cablear una fuente real. Los datos inventados ocultan ese hueco y lo hacen parecer resuelto.

---

## Impacto

El terminal ahora falla con honestidad. Donde tiene datos en vivo —gráficos y cotizaciones de EE. UU./Europa/cripto vía fuentes gratuitas, fundamentales de cripto, la capa de investigación con LLM local— los muestra. Donde no, lo dice, con un motivo, en lugar de inventar un número. Para quien lo usa para tomar decisiones reales, esa es la diferencia entre una herramienta y un juguete. Y para quien contribuya después, los marcadores de degradado son un mapa preciso de lo que queda por construir.

---

## Alcance: lo que la 1.0 *no* es

Honestidad sobre los bordes, en el espíritu de la propia versión:

- **Los vacíos degradados siguen siendo vacíos.** Fuerza Relativa, bonos, *hotlists*, operaciones de *insiders* y la cinta de tiempo y ventas devuelven resultados vacíos honestos — los motores de datos *reales* tras ellos son trabajo futuro, no parte de la 1.0.
- **No existe profundidad de Nivel 2 gratuita** para renta variable de EE. UU./Europa, así que ese libro de órdenes está vacío-y-etiquetado en vez de falseado; se planea un adaptador a una API de bróker.
- **La matriz de pruebas de humo de la versión es manual por ahora.** Verificamos los flujos principales a mano en distintas combinaciones de base de datos y claves; automatizar esa matriz es una tarea pendiente declarada, no un hueco silencioso.

---

## Qué viene después

- **v1.1 — profundidad multiactivo:** ampliar la cobertura real (mapas de calor de Europa/cripto, efectivo y transacciones en cartera, más fuentes de libro de órdenes en vivo).
- **v1.2 — profundizar lo nativo de IA:** apoyarse en el "segundo cerebro" privado que fundamenta las respuestas solo en tus propias notas.
- Y las dos entregas en profundidad que debemos desde la entrega 1: la auditoría de cableado ("el error que parecían 33 errores") y la reconstrucción "corre sobre cualquier LLM".

---

## Lecturas relacionadas

- [OpenTerminalUI — Bifurcar un terminal financiero para que funcione fuera de India](/blog/openterminalui-bifurcar-terminal-financiero-de-india) — entrega 1: la premisa y la reconstrucción de la capa de datos sin India.
- **Código:** la bifurcación vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI), etiquetada `v1.0.0`. Las notas de ingeniería viven en las carpetas estilo `.agents/` y `docs/wiki/` del repo, escritas para personas y agentes por igual.

*(Una nota de transparencia, en el espíritu de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que hizo la ingeniería que describe.)*
