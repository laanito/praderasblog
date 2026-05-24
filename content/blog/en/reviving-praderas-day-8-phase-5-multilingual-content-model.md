---
Title: Reviving Praderas (Day 8) — Phase 5: ES/EN content model, translation pairs, and hreflang
Description: Phase 5 on this repo—English posts under blog/en, optional en/ pages, Lang and Translation_Key metadata, Multilingual plugin, language switcher, hreflang and og:locale, scoped pagination and neighbors, English listing at /en/blog.
Date: 2026-04-28 12:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 8
Lang: en
Translation_Key: praderas-phase-5-multilingual
Image: /assets/images/day08-comfyui-sdxl-phase5-multilingual-hero.webp

---

# Reviving Praderas (Day 8) — Phase 5: bilingual without rewriting history

After Phase 4 (SEO), the next agreed bucket was **Phase 5: multilingual**. The hard constraint was to **avoid mass-changing** existing Spanish URLs under `/blog/...`. The approach keeps Spanish posts where they are and adds English along predictable paths.

## What we shipped

1. **Content layout**  
   - **Spanish posts:** `content/blog/*.md` → `/blog/slug` (unchanged).  
   - **English posts:** `content/blog/en/*.md` → `/blog/en/slug`.  
   - **English top-level pages:** `content/en/*.md` → `/en/slug` (for now: **Home** and **`/en/blog`** listing).

2. **Pairing translations**  
   - Optional `Lang: es | en` (language can also be inferred from the path).  
   - **`Translation_Key`**: same string on both files so the site can wire alternates.  
   Spanish and English home pages share `praderas-home` so the header switcher appears on both.

3. **`plugins/65-Multilingual.php`**  
   - Registers metadata, indexes pairs, exposes Twig variables for language, alternates, home URL, and `hreflang` links (including `x-default` to Spanish when both exist).

4. **Theme + social metadata**  
   - Dynamic `og:locale` / `og:locale:alternate`, `hreflang` loop in `page-meta.twig`, `html lang`, navbar brand to the correct home, and a small **`lang-switcher.twig`** when a paired page exists.

5. **Plugins aligned with language**  
   - Pagination for `/blog` excludes `blog/en/*`; English listing uses **`blog-en.twig`**.  
   - Prev/next and related posts stay in the **same language** as the current post; category counts on the Spanish categories page use **Spanish** posts only.  
   - Series maps are **per language**; `/series` remains Spanish-first.  
   - `/archivo` lists **Spanish** posts only.  
   - Search stopwords: **`low_value_words_en`** in `config/config.yml` when the current page is English.

6. **Sidebar**  
   - English labels and “Recent posts” limited to `blog/en/*` when `content_lang == 'en'`.

## Honest scope limits

- We are **not** translating the full archive yet—only this paired Day 8 post plus the English home/blog stubs.  
- **Sitemap split** and **Phase 6 JSON** remain future work.  
- The EN nav still links **About** to the existing Spanish `acerca-de-picocms` page until a dedicated English page is added.

## Closing

Phase 5 gives a **maintainable skeleton**: URLs, metadata, `hreflang`, and minimal UI. The remaining work is mostly **editorial**—adding `Translation_Key` pairs as translations exist.
