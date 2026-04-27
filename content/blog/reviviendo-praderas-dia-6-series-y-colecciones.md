---
Title: Reviviendo Praderas (Día 6) — series y colecciones: navegar por capítulos sin perder el hilo
Description: Implementamos soporte real de series en Pico: nuevos campos opcionales, índice por serie en /series/<slug>/ y navegación anterior/siguiente dentro de cada colección.
Date: 2026-04-27 10:50AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 6
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
3. **Navegación dentro del post**:
   - bloque “Serie” con parte actual,
   - enlace a parte anterior / siguiente,
   - acceso al índice de esa serie.
4. **Plantilla `series.twig`** para mostrar:
   - índice general de series en `/series`,
   - índice detallado de una serie concreta en `/series/<slug>/`.

## Integración con la serie Reviviendo Praderas

Para no dejarlo como infraestructura vacía, los capítulos Día 1 a Día 5 se actualizaron con metadatos de serie (`Series_Order: 1..5`) y esta entrada queda como **parte 6**.

Con eso, la navegación por capítulos ya funciona de extremo a extremo.

## Por qué compensa este paso

La diferencia entre “tenemos varias entradas parecidas” y “tenemos una serie” es que el lector no tiene que reconstruir el mapa mental en cada página. Es una mejora de experiencia, sí, pero también de mantenimiento editorial: queda claro dónde empieza, cómo sigue y qué falta.

## Qué sigue

Con Task B del Día 5 ya implementada, el siguiente bloque natural vuelve a ser **Phase 4** (SEO y discoverability), con la ventaja de que ahora la estructura narrativa del blog es más sólida para enlazado interno y rutas temáticas.
