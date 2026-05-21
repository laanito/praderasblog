---
Title: Reviviendo Praderas (Día 6) — series y colecciones: navegar por capítulos sin perder el hilo
Description: Implementamos soporte real de series en Pico y una iteración post-merge: enlace Series en menú principal, navegación de serie en el sidebar y mapeo de la serie histórica de control de tiempo.
Date: 2026-04-27 10:50AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 6
Lang: es
Translation_Key: praderas-day-6-series-and-collections
Image: /assets/images/day06-comfyui-sdxl-series-collections-hero.webp

---

# Reviviendo Praderas (Día 6) — cuando una mejora de navegación necesita modelo, no parche

Hasta el Día 5, la experiencia del blog ya estaba mucho más limpia: mejor lectura, mejor ritmo visual, mejor sensación de producto. Lo que faltaba del plan del consultor era la pieza de **series / colecciones** para recorrer contenidos conectados sin depender solo de etiquetas o del orden cronológico general.

Hoy tocó resolver eso de forma mantenible, respetando el enfoque del proyecto: **Pico, markdown y plugins ligeros**, sin meter complejidad artificial.

## Qué añadimos

1. **Front matter opcional para series** en posts:
   - `Series`
   - `Series_Slug`
   - `Series_Order`
2. **Plugin nuevo** (`plugins/60-SeriesCollections.php`) que:
   - detecta rutas `/series/<slug>/`,
   - carga `content/series.md` como archivo base,
   - construye la colección desde los posts `blog/*`,
   - ordena por `Series_Order` (y por fecha como fallback),
   - expone variables Twig para índice de serie y navegación in-post.
3. **Navegación de serie en páginas de post**:
   - bloque “Serie” con parte actual,
   - enlace a parte anterior / siguiente,
   - acceso al índice de esa serie.
4. **Plantilla `series.twig`** para mostrar:
   - índice general de series en `/series`,
   - índice detallado de una serie concreta en `/series/<slug>/`.

## Integración con la serie Reviviendo Praderas

Para no dejarlo como infraestructura vacía, los capítulos Día 1 a Día 5 se actualizaron con metadatos de serie (`Series_Order: 1..5`) y esta entrada queda como **parte 6**.

Con eso, la navegación por capítulos ya funciona de extremo a extremo.

## Actualización (mismo día, tras revisión en producción)

Con el despliegue en `main`, cerramos una iteración extra de UX:

- **Menú principal:** añadimos `Series` como enlace de primer nivel (junto a Inicio, Blog, Categorías y Acerca).
- **Menos carga al final del artículo:** movimos la navegación de serie al **sidebar** para no saturar la zona bajo el contenido (donde ya viven relacionados y navegación cronológica).
- **Serie histórica retrofiteada:** mapeamos la secuencia que arranca en
  `desarrollo-de-arquitecturas-desacopladas-creando-una-aplicacion-de-control-de-horas`
  y cierra en
  `creacion-de-usuarios-en-tu-aplicacion-de-control-de-tiempo-con-react`,
  quedando como **Control de Tiempo Desacoplado** (13 capítulos con `Series_Order`).

Así, el modelo de series no queda reservado solo para *Reviviendo Praderas*: también ordena y hace navegable una línea editorial antigua que ya existía.

## Por qué compensa este paso

La diferencia entre “tenemos varias entradas parecidas” y “tenemos una serie” es que el lector no tiene que reconstruir el mapa mental en cada página. Es una mejora de experiencia, sí, pero también de mantenimiento editorial: queda claro dónde empieza, cómo sigue y qué falta.

## Qué sigue

Con Task B del Día 5 ya implementada, el siguiente bloque natural vuelve a ser **Phase 4** (SEO y discoverability), con la ventaja de que ahora la estructura narrativa del blog es más sólida para enlazado interno y rutas temáticas.
