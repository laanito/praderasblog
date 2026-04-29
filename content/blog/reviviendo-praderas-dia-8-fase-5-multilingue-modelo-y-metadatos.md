---
Title: Reviviendo Praderas (Día 8) — Fase 5: multilingüe (ES/EN), pares por Translation_Key y hreflang
Description: Fase 5 del plan en .agents: contenido en inglés bajo blog/en y páginas en/en, metadatos Lang y Translation_Key, plugin Multilingual, conmutador de idioma, hreflang y og:locale, paginación y vecinos acotados por idioma, listado EN en /en/blog.
Date: 2026-04-28 12:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 8
Lang: es
Translation_Key: praderas-phase-5-multilingual
---

# Reviviendo Praderas (Día 8) — Fase 5: dos idiomas sin romper URLs históricas

Tras SEO (Fase 4), el siguiente bloque acordado era **Phase 5: multilingüe**. La restricción principal era **no mover de golpe** las decenas de entradas en español que ya viven en `/blog/...`: el modelo adoptado mantiene el español donde está y añade inglés en rutas nuevas y previsibles.

## Qué implementamos

1. **Árbol de contenido (Opción A, afinada en código)**  
   - Entradas **en español**: siguen en `content/blog/*.md` → URLs `/blog/slug`.  
   - Entradas **en inglés**: `content/blog/en/*.md` → `/blog/en/slug`.  
   - Páginas **solo UI/copy en inglés** (por ahora): `content/en/*.md` → `/en/slug` (por ejemplo portada y listado **`/en/blog`**).

2. **Front matter para emparejar traducciones**  
   - `Lang: es | en` (opcional si el idioma se deduce de la ruta).  
   - **`Translation_Key`**: misma cadena en el par ES/EN para que el sitio sepa que son la misma pieza editorial.  
   La portada española (`content/index.md`) y la inglesa (`content/en/index.md`) comparten clave `praderas-home` para que el conmutador aparezca también en la home.

3. **Plugin `plugins/65-Multilingual.php`**  
   - Registra metadatos, construye pares por clave y expone a Twig: `content_lang`, `html_lang`, `og_locale`, `og_locale_alternate`, `alternate_language_page`, `pradera_home_url`, `hreflang_alternates`.  
   - Inferencia de idioma: `Lang` explícito; si no, ruta `blog/en/` o prefijo `en/` → inglés; si no, español.

4. **Tema y SEO social**  
   - `page-meta.twig`: `og:locale` dinámico, `og:locale:alternate` cuando hay par, y `<link rel="alternate" hreflang="...">` (incluido `x-default` hacia la URL española cuando existen las dos).  
   - Plantillas base: `html lang="..."` según página; marca del navbar apunta a la home del idioma actual (`pradera_home_url`).  
   - **`lang-switcher.twig`** incluido desde `nav.twig` cuando existe página alterna.

5. **Listados y plugins coherentes con el idioma**  
   - **Paginación** (`10-Pagination.php`): el índice `/blog` solo considera posts `blog/*` **excluyendo** `blog/en/*`. El inglés se lista en **`blog-en.twig`** (`/en/blog`).  
   - **Vecinos y relacionadas** (`50-BlogNeighbors.php`): prev/next y relacionadas **mismo idioma** que el post actual; conteos de etiquetas en **Categorías** solo desde posts ES.  
   - **Series** (`60-SeriesCollections.php`): mapas por idioma; el hub `/series` sigue centrado en la narrativa en español; en posts EN se usa el mapa inglés cuando aplica.  
   - **Archivo** (`archive.twig`): solo entradas españolas (misma URL `/archivo` que antes).  
   - **Búsqueda** (`40-PicoSearch.php` + `config/config.yml`): lista `low_value_words_en` usada cuando la página actual es **en**.

6. **Sidebar bilingüe**  
   - Etiquetas de widget y “Artículos recientes” en inglés cuando `content_lang == 'en'`; recientes EN solo bajo `blog/en/`.

## Qué queda fuera (por ahora)

- **Traducir masivamente** el archivo histórico: solo hay una entrada EN de ejemplo emparejada con esta (Día 8); el resto puede ir sumando claves cuando haya criterio editorial.  
- **Sitemap por idioma** y **JSON (Phase 6)**: siguen en el plan; no bloquean el arranque de Phase 5.  
- **Página “Acerca” en inglés** dedicada: el nav EN enlaza aún al markdown español; se puede añadir `content/en/about.md` en una iteración pequeña.

## Cierre del Día 8

Fase 5 deja un **cimiento técnico** claro: rutas, metadatos, SEO entre idiomas y UX mínima (conmutador + copy lateral). La deuda editorial es **contenido**, no el modelo del repo.
