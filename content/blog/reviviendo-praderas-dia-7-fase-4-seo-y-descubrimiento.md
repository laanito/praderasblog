---
Title: Reviviendo Praderas (Día 7) — Fase 4: SEO, metadatos sociales y archivo por fechas
Description: Fase 4 del plan en .agents: canonical y URLs coherentes con base_url, plantilla única de title/description/Open Graph/Twitter, stopwords en español para la búsqueda, página de archivo por año/mes y enlace en el lateral.
Date: 2026-04-28 11:30AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 7
Lang: es
Translation_Key: praderas-day-7-phase-4-seo-discoverability
---

# Reviviendo Praderas (Día 7) — Fase 4: que Google y las personas encuentren sin drama

Tras estabilizar navegación, metadatos de contenido y series, el siguiente bloque del roadmap era **Phase 4**: SEO y descubrimiento sin reventar el modelo flat-file de Pico.

Esta fase no sustituye decisiones de infraestructura (por ejemplo redirecciones en el dominio raíz): las **documentamos** y dejamos el sitio **coherente con la URL canónica ya configurada** en `config/config.yml`.

## Qué implementamos

1. **Metadatos HTML unificados** (`themes/bootstrap-blog/page-meta.twig`), incluidos desde `index.twig`, `post.twig` y `blog.twig`:
   - Patrón de título consistente (`… | Blog Praderas`, con variante cuando hay etiqueta activa en `/tags`).
   - `meta description` y `robots` donde existan en el front matter.
   - **`link rel="canonical"`** usando la URL de página que expone Pico (`current_page.url`), alineada con `base_url`.
   - **Open Graph** (`og:title`, `og:description`, `og:url`, `og:type`, `og:locale`, `og:site_name`) y **Twitter Cards** (`twitter:card`, `twitter:title`, `twitter:description`), con `og:type` `article` en entradas `blog/*` y `website` en el resto.

2. **Búsqueda más útil en castellano**: configuración `PicoSearch.low_value_words` en `config/config.yml` con una lista razonable de **palabras vacías** en español para que consultas multi-palabra no queden dominadas por términos de relleno.

3. **Archivo por fechas**: nueva página `content/archivo.md` (`Template: archive`) y plantilla `themes/bootstrap-blog/archive.twig` que lista entradas `blog/*` agrupadas por **año** y **mes** (etiquetas en español). Enlace desde el **sidebar** (“Ver archivo →”) para descubrir histórico sin depender solo del listado paginado.

4. **Copy mínimo para descubrimiento**: `Description` añadida en `content/search.md` para que la página de búsqueda tenga texto útil en metadatos sociales y buscadores cuando aplique.

## Qué queda fuera de alcance (honesto)

- **Dominio raíz (`praderas.org`) vs subdominio del blog**: sigue siendo decisión de DNS / hosting; en código queda explícito que **`base_url` canónico es `https://blog.praderas.org`**.
- **Snippets resaltados en resultados de búsqueda**, JSON-LD pesado o `hreflang`: candidatos a futuras iteraciones; Phase 4 intenta cerrar el núcleo sin inflar plantillas.

## Cierre del Día 7

Fase 4 deja el blog con **mejor señal HTML**, **mejor archivo humano** y **búsqueda algo más sensata en español**, manteniendo Pico ligero. Lo siguiente en el plan formal sigue siendo **Phase 5 (multilingüe)** cuando haya hueco y criterio producto.
