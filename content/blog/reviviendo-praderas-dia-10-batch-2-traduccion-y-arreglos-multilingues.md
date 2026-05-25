---
Title: Reviviendo Praderas (Día 10) — batch 2, hubs EN y arreglos de rutas (bitácora del día)
Description: Traducción completa de la serie Control de Tiempo Desacoplado, páginas /en/series y /en/categorias, cambios en plugins y plantillas, y comparación de tiempos (~35 min de reloj vs localización tradicional).
Date: 2026-05-01 12:30PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 10
Lang: es
Translation_Key: praderas-day-10-batch-2-multilingual-hubs
Image: /assets/images/day10-comfyui-sdxl-batch2-multilingual-hubs-hero.webp

---

# Reviviendo Praderas (Día 10) — qué se hizo hoy y por qué

Entrada de **bitácora diaria**: no es un tutorial largo; es **transparencia** sobre un bloque de trabajo concreto (código + contenido + criterio) y los números de reloj que pedimos en el Día 9.

## Cambios publicados (resumen ejecutivo)

1. **Batch 2 de traducción** — Los **13 capítulos** de *Control de Tiempo Desacoplado* tienen ahora par **ES/EN** (`Translation_Key` `praderas-ctd-01` … `13`), con la serie en inglés titulada **Decoupled time tracking** y el mismo `Series_Slug` para no romper enlaces de serie.
2. **Hubs que faltaban en inglés** — Añadimos **`/en/series`** y **`/en/categorias`** emparejados con las páginas españolas (`Translation_Key` de navegación). El menú EN ya no mandaba a listados solo en castellano: eso era un **agujero real de producto**, no un detalle cosmético.
3. **Rutas de serie por idioma** — El plugin de series reconoce **`/en/series/<slug>/`**, construye la rejilla de colecciones según el idioma de la página y hace que el enlace “índice de serie” del lateral apunte al **prefijo correcto** (`/en/series/...` vs `/series/...`).
4. **Categorías EN con conteos honestos** — En **`/en/categorias`** los conteos cuentan solo posts bajo **`blog/en/`**; las etiquetas siguen siendo el vocabulario compartido (decisión explícita hasta que exista capa de nombres EN).
5. **Plantillas** — `series.twig` y `categories.twig` ramifican textos y migas según idioma; **`nav.twig`** apunta a `en/series` y `en/categorias` en la rama EN.
6. **Corrección menor** — Un `</div` roto en un bloque de código del artículo español de gestión de proyectos en React (calidad de repo, no “feature”).

## Razonamiento (por qué en el mismo bloque de trabajo)

- **Serie completa en un solo lote** — Era la regla que nos dimos en el Día 9: evitar capítulos mezclados entre idiomas para quien lee en orden.
- **Hubs + rutas en la misma PR** — Sin rutas EN para series, la traducción del arco quedaba **ilegible en navegación**: el lector llegaba al inglés del blog y seguía aterrizando en **Series** y **Categorías** en español. Arreglar “solo texto” y dejar el fallo de UX hubiera sido peor que hacer el corte completo.
- **Conteos por idioma en categorías** — Mostrar el conteo del archivo entero en una página que dice “inglés” sería **engañoso**; el plugin de vecinos/tag counts se acotó por `inferLang` de la página actual.

## Comparación de tiempos (reloj humano ~35 min)

Para **este bloque** (13 pares de posts + dos hubs + plugins + plantillas + tracker + bitácora + PR), el **tiempo de calendario del lado humano** rondó **~35 minutos** (revisión, pruebas manuales ligeras, apertura/merge de PR según flujo).

Como **orden de magnitud** frente a un **especialista de localización** + revisión editorial clásica para el mismo alcance (texto + metadatos + verificación de enlaces internos + QA de rutas en dos idiomas), seguimos en un rango razonable de **~10 a ~18 horas**, según profundidad de revisión y si hay memoria de traducción previa.

De nuevo: no es una carrera “IA vs humano”, sino **qué comprime el calendario** cuando el repo ya tiene convenciones (`Translation_Key`, lotes, glosario) y el alcance del PR está acotado.

## Limitaciones honestas (para no vender humo)

- **`/tags` y el buscador del lateral** siguen siendo **principalmente flujo español**; lo dejamos anotado en la página EN de categorías. Cerrarlo bien es otro PR.
- **`hreflang` en detalle de serie** puede seguir siendo fino según cómo Pico resuelva la misma plantilla para dos URL; el gancho grande era **navegación y listados coherentes por idioma**.

## Qué sigue

**Batch 3** (cluster ciberseguridad / privacidad / geolocalización) cuando toque —misma regla de lote y glosario vivo.
