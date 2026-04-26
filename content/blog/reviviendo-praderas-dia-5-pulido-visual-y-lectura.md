---
Title: Reviviendo Praderas (Día 5) — Pulido visual: espacio, legibilidad y un tono de marca sin frameworks nuevos
Description: Capa de diseño (tokens CSS, tipografía de lectura, tarjetas, barra lateral y pie) y una segunda iteración con feedback de consultor: sombras, «lift» en hover, pastillas y contraste de enlaces.
Date: 2026-04-26 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
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
