---
Title: Reviving Praderas (Day 6) — series and collections: chapter-based navigation without losing context
Description: We added first-class series support in Pico, including menu integration, sidebar series navigation, and backfilling the historical time-control series.
Date: 2026-04-27 10:50AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 6
Lang: en
Translation_Key: praderas-day-6-series-and-collections
Image: /assets/images/day06-comfyui-sdxl-series-collections-hero.webp

---

# Reviving Praderas (Day 6) — when navigation needs a model, not a patch

By Day 5, reading quality and visual rhythm were stronger. The missing piece from the plan was **series / collections** so readers could follow connected posts without relying only on tags or global chronology.

## What we added

1. **Optional series front matter** for posts:
   - `Series`
   - `Series_Slug`
   - `Series_Order`
2. **New plugin** `plugins/60-SeriesCollections.php` that:
   - handles `/series/<slug>/` routes,
   - uses `content/series.md` as base file,
   - builds collections from `blog/*` posts,
   - sorts by `Series_Order` (date fallback),
   - exposes Twig variables for index and in-post series navigation.
3. **Series navigation in posts**:
   - current chapter marker,
   - previous/next chapter links,
   - link to series index.
4. **`series.twig` template** supporting:
   - global series index at `/series`,
   - per-series index at `/series/<slug>/`.

## Integration with Reviving Praderas

To avoid shipping empty infrastructure, Day 1 to Day 5 posts were updated with series metadata and this entry became part 6. Series navigation now worked end-to-end.

## Same-day production follow-up

After deploying to `main`, we applied another UX pass:

- added `Series` to the primary navbar,
- moved series navigation to the sidebar to avoid overcrowding post endings,
- mapped the historical sequence from
  `desarrollo-de-arquitecturas-desacopladas-creando-una-aplicacion-de-control-de-horas`
  to
  `creacion-de-usuarios-en-tu-aplicacion-de-control-de-tiempo-con-react`
  as **Control de Tiempo Desacoplado** (13 chapters).

## Why this pays off

The gap between "similar posts" and a true "series" is reader cognition: users no longer rebuild the map every page.

## What follows

With this task implemented, the next natural block returns to **Phase 4** (SEO and discoverability), now on top of stronger narrative structure.
