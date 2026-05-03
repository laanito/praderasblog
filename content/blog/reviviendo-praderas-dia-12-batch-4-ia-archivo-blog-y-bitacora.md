---
Title: Reviviendo Praderas (Día 12) — batch 4 (IA), archivo EN, conmutador en /en/blog y bitácora que faltó
Description: Entrada de cierre del trabajo ya fusionado en main—seis posts de IA, /en/archivo, emparejamiento del listado de blog para el conmutador de idioma—más esta PR que añade la bitácora ES/EN que debió ir en el mismo lote.
Date: 2026-05-03 12:17PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 12
Lang: es
Translation_Key: praderas-day-12-batch-4-archive-blog-log
---

# Reviviendo Praderas (Día 12) — transparencia sobre el merge y este par de posts

Esta entrada documenta **cambios que ya están en `main`** (merge previo): **batch 4 de traducción**, **archivo cronológico en inglés** y **emparejamiento del listado de blog** para que el **conmutador de idioma** aparezca en `/en/blog`. Lo que faltó en aquel envío fue **esta bitácora diaria** (ES + EN); la añadimos ahora en una **PR pequeña** desde `main` actualizado, como pedía el flujo editorial del proyecto.

## Reloj de pared (solo esta PR de bitácora)

- **Inicio de la sesión de trabajo (referencia acordada):** `2026-05-03 11:56:12 CEST`
- **Justo antes de commit + push de esta PR (referencia):** `2026-05-03 12:16:46 CEST`

La comparación útil aquí es **~20 minutos de reloj** entre esas marcas (dirección, bitácora ES/EN, tracker, rama/PR) frente al orden de magnitud **manual** de la tabla siguiente. El trabajo “grande” (posts IA + Twig + `content/en/archivo`) **ya se contó en el merge anterior**; esta PR cierra el **hueco de narrativa**.

## Qué incluyó el merge ya fusionado (resumen)

1. **Batch 4 (núcleo IA)** — Seis pares ES/EN (`praderas-b4-ai-games-evolution`, `…-early-disease-detection`, `…-medicine`, `…-society-impact`, `…-entertainment`, `…-neural-nets`): videojuegos, detección temprana, medicina, sociedad, entretenimiento, redes neuronales.
2. **`Translation_Key: praderas-nav-blog-listing`** en `content/blog.md` y `content/en/blog.md` — habilita **`alternate_language_page`** para que `lang-switcher.twig` muestre **Español** desde `/en/blog`.
3. **`/en/archivo`** emparejado con `archivo.md` (`praderas-nav-archive`) y **`archive.twig`** bilingüe (meses, migas, listas filtradas por `blog/en/` vs `blog/*` sin `en/`).
4. **`sidebar.twig`** — el widget de archivo apunta a **`/en/archivo`** cuando `content_lang` es inglés.
5. **`.agents`** — tracker y backlog ya reflejaban batch 4 y rutas EN.

## Por qué una PR aparte para la bitácora

- **No reescribir historia del merge** — El código y el contenido IA ya estaban aprobados; lo sensato es **añadir** la capa de explicación pública sin reordenar commits ajenos.
- **Honestidad** — El lector del archivo *Reviviendo Praderas* espera **un post por día de trabajo** con criterio y números; omitirlo dejaba un salto del Día 11 al silencio.

## Orden de magnitud “senior sin asistente” (solo esta bitácora)

| Bloque | Estimación indicativa |
|--------|-------------------------|
| Par de posts ES/EN + `Translation_Key` + `Series_Order` | **1–2 h** |
| Actualizar tracker / changelog | **20–40 min** |
| Rama desde `main`, PR, descripción | **20–40 min** |
| **Total** | **~2–3.5 h** repartidos |

Con asistente y dirección clara, el tramo de calendario entre las dos marcas de tiempo de esta PR quedó en el orden de **veinte minutos** (incluye la corrección de marcas de tiempo y el push); el coste “senior” de la tabla sigue siendo útil como **orden de magnitud** del trabajo editorial equivalente **sin** asistente, no del merge anterior de código.
