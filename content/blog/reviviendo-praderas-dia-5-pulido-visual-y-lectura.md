---
Title: Reviviendo Praderas (Día 5) — Pulido visual: espacio, legibilidad y un tono de marca sin frameworks nuevos
Description: Capa de diseño, iteración con consultor, y datos de tiempo: ~26 min para el seguimiento (segundo PR) frente a un orden de magnitud humano (10–14 h) comentado por el consultor.
Date: 2026-04-26 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 5
Lang: es
Translation_Key: praderas-day-5-visual-polish
Image: /assets/images/day05-comfyui-sdxl-visual-polish-readability-hero.webp

---

# Reviviendo Praderas (Día 5) — Cuando toca cuidar la forma, no solo la función

Después de metadatos, migas, posts relacionados y búsqueda, el sitio **funcionaba**. Lo que aún **hacía falta** era el aspecto: demasiada sensación de plantilla genérica y poco **ritmo vertical** para leer con calma.

Ese es el criterio del *Day 5* aprobado: **pulido visual y de uso** antes de meter piezas gordas como fases multilíngües o series. Aquí vamos, con un agente de **copiloto** en el repo, como en los días anteriores.

## Qué tocamos (a alto nivel)

- **Fichero nuevo** `praderas-theme.css` encima de `styles.css`: **variables** (color, sombras, radios, interlineado de cuerpo ~1,75) sin arrastrar otro framework.
- **Plantillas** (`index`, `post`, `blog`, `sidebar`, `breadcrumbs`…): clases y marcas mínimas para títulos, cuerpo del post, *Te puede interesar*, lateral y pie, alineado con el feedback del consultor.
- **Acento** coherente (verde bosque) integrado vía **tokens de Bootstrap 5** (`--bs-primary`, etc.) para no pelear con botones y enlaces.
- **Pie** unificado: texto de atribución en **castellano** y con menos ruido visual.

## Por qué importa (y un abuso honesto con la IA)

La parte “bonita” se puede menospreciar; en un blog, **leer cinco minutos** sin notar fricción con el ojo importa. Aquí el LLM aporta sobre todo **volumen de prueba y consistencia** en el CSS, pero las decisiones (tono, contraste, no meter dependencias gordas) vienen de criterio humano y de lo que **ya** habíamos escrito en `.agents/day5-consultant-feedback.md`.

## Qué queda (sin inventarnos cierre falso)

- **Series / colecciones** sigue en cola, con el lenguaje visual de esta fase.
- Más afinado fino: tipografía web opcional, modo oscuro si algún día lo pide alguien de verdad.

Hasta el siguiente día, en el repositorio.

## Actualización: revisión del consultor (puntuación **8,7/10**)

Tras publicar, el consultor revisó el sitio en producción (inicio, listado de blog, entrada Día 5) y resumió el veredicto: **sólida mejora** — de «Bootstrap genérico» a **limpio, calmado y más agradable de leer**; la sensación incómoda, en buena medida, **desaparecida**.

Afinamos un segundo paso en código:

- **«Te puede interesar» y tarjetas de listado:** sombra algo más rica, hover con **ligero lift** (translate + escala) para dejar de sentirse plano.
- **Etiquetas:** clases `rounded-pill` + `pradera-pill-tag`, sombra e hover con **más empuje** (escala, sombra, brillo suave) en el post y en el lateral.
- **Fecha:** la línea a veces salía `Publicado el` pegada a la fecha; lo fijamos con concatenación en Twig: siempre un espacio entre `el` y `{{ meta.date_formatted }}`.
- **Enlaces en el cuerpo del texto:** hover a un **verde más oscuro** (`#145233`) y subrayado alineado para más contraste.
- **Móvil:** cuerpo de artículo y *homepage* en `1rem` de base; a partir de **576px** se sube a `1.0625rem` para quien tenga más anchura.
- **Token global** `--bs-link-hover-color` pasa a ese verde para coherencia con el hover en párrafos (donde aplica el tema).

Esto cierra el «**un pasito más hacia un aspecto redondo**» que el consultor pedía sin reabrir el saco a frameworks pesados. Series y fases siguientes, cuando toque, sobre esta base.

## Datos de tiempo, segundo PR y el «10–14 h» (transparencia)

Nos importa dejar constancia, **con números**, de cómo se movió el trabajo en **abril de 2026** — no para fanfarronear, sino para que quien lea *Reviviendo Praderas* tenga **contexto** sobre asistentes de código, revisiones y flujo con Git.

- Tras el **merge** del PR que llevaba el pulido Day 5 y el seguimiento a la primera lectura del consultor, el resto (última pasada al artículo, alineación con su feedback, **sincronización de notas** en `.agents` y cierre de la narrativa) fue un **segundo pull request** aparte. A partir de ahí, **cualquier cambio nuevo** (como añadir este bloque) vuelve a exigir **otro PR**: es la forma sana de mantener trazabilidad.
- De punta a punta, el bloque de trabajo descrito en el párrafo anterior — humano + agente en el repo, con el consultor aportando criterio **por escrito** — rondó **veintiséis minutos** de reloj (orden de magnitud, no un laboratorio; medido de forma informal).

El consultor, con el resultado encima, comentó que un **desarrollador senior** en solitario habría podido emplear del orden de **diez a catorce horas** en un entregable **equivalente** (misma clase de ajustes de interfaz, coherencia y documentación asociada).

**¿De acuerdo?** A **grosso modo, sí, como orden de magnitud** — con matices. Ese rango es creíble si la persona hace de **diseño ligero**, prueba en varios viewports, itera, escribe notas y se detiene a leer el feedback; también podría ser **menor** si el criterio visual ya estaba cerrado de antemano, o **mayor** si hubiera habido rondas largas de marca, accesibilidad formal o research. Lo que no me parece discutible es el **multiplicador** cuando el repositorio ya va encarrilado: el asistente se lleva buena parte del **tecleo, el barro y la consistencia** en diffs, y el humano aporta **dirección, criterio y traca** (y el consultor, un ojo producto). No es “la IA en 26 minutos sustituye a un senior 12 horas” en abstracto: es “**con este alcance y este contexto**, el tiempo de calendario se comprime mucho”.

Guardamos esto en el blog porque **sin datos** la conversación sobre IA en el flujo de trabajo se queda en anécdotas. Con **números aproximados y limitaciones explícitas**, al menos se puede discutir con un poco más de seriedad.
