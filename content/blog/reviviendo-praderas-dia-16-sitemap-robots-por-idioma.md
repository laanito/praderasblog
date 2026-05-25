---
Title: Reviviendo Praderas (Día 16) — sitemap y descubrimiento por idioma (índice ES/EN)
Description: Implementación de `sitemap.xml` como índice y dos URL sets (`sitemap-es.xml`, `sitemap-en.xml`) filtrados con la misma regla de idioma que el resto del sitio (`Multilingual::inferLang`), alineado con hreflang y la cola UI multilingüe.
Date: 2026-05-10 10:30AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 16
Lang: es
Translation_Key: praderas-day-16-sitemap-robots-lang-log
Image: /assets/images/day16-comfyui-sdxl-day16-sitemap-robots-lang-hero.webp

---

# Reviviendo Praderas (Día 16) — sitemaps por idioma

Tras cerrar la cola UI prevista hasta la búsqueda EN y el footer no-post (Día 15), el siguiente ítem prioritario en `.agents/multilingual-ui-backlog.md` era **robots/sitemap por idioma**. Esta entrada documenta la decisión de diseño y el resultado en código.

## Por qué no bastaba un único `sitemap.xml` con todas las URLs

El sitio ya publica **pares ES/EN** con `hreflang` y URLs distintas (`/blog/...` vs `/blog/en/...`, hubs `/en/...`). Un sitemap monolítico sigue siendo válido técnicamente, pero:

1. **Coherencia operativa:** queremos que las herramientas de inspección (y futuros agentes) vean **conjuntos de URLs separados por idioma**, igual que la lista de posts que ya filtra Twig y los plugins por `content_lang`.
2. **Menos ambigüedad para rastreadores:** un **índice de sitemaps** (`sitemap.xml`) que apunta a dos hijos es un patrón estándar; los motores siguen el índice y consumen cada hijo sin mezclar “idioma activo” en una sola lista plana.
3. **Una sola regla de idioma:** el filtro reutiliza `Multilingual::inferLang` (rutas `blog/en/`, `en/`, `Lang` en front matter) para no inventar una segunda heurística en SEO.

`robots.txt` puede seguir declarando **una** línea `Sitemap:` hacia el índice; no hace falta duplicar la directiva por cada hijo.

## Qué se implementó

1. **`plugins/PicoRobots/PicoRobots.php`**
   - Rutas adicionales: `sitemap-es.xml` y `sitemap-en.xml`, además de `sitemap.xml` y `robots.txt`.
   - Construcción de entradas de sitemap **filtrada por idioma** cuando el fichero pedido es ES o EN; la lógica existente de `lastmod` / `changefreq` / `priority` y el evento `onSitemap` se mantienen sobre cada lista construida.
   - **`sitemap.xml`** deja de ser un único `<urlset>` y pasa a ser un **`<sitemapindex>`** con dos `<loc>`: `…/sitemap-es.xml` y `…/sitemap-en.xml`.

2. **`themes/bootstrap-blog/sitemap-index.twig`** y **`sitemap.twig`**
   - Plantillas en el tema para el índice y el URL set (misma forma que el plugin por defecto), de modo que Pico resuelve primero el tema activo.

## Verificación recomendada en despliegue

Tras publicar, conviene comprobar en el host:

- `GET /sitemap.xml` → índice con dos `<sitemap><loc>…</loc>`.
- `GET /sitemap-es.xml` y `GET /sitemap-en.xml` → solo URLs cuyo idioma inferido coincide.
- `GET /robots.txt` → sigue apuntando al sitemap canónico (`/sitemap.xml`).

## Estado del backlog

En `.agents/multilingual-ui-backlog.md`, la fila **Robots / sitemap por idioma** pasa a **enviada**; lo que queda en la tabla pendiente es sobre todo política de taxonomía opcional y otros temas no bloqueantes.

## Reloj de pared (implementación + docs de agente)

- **Inicio (referencia):** `2026-05-10 10:15:00 CEST`  
- **Fin (referencia):** `2026-05-10 10:45:00 CEST`

Ventana orientativa: **~30 min** de calendario para rama, cambios en plugin/Twig, actualización de ledgers y esta bitácora (el commit y push pueden quedar justo después).

Como en notas anteriores, el reloj es **indicativo** y no sustituye mediciones en CI o en el entorno de producción.
