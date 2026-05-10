---
Title: Reviving Praderas (Day 16) — per-language sitemaps and discovery (ES/EN index)
Description: Ship `sitemap.xml` as a sitemap index pointing to `sitemap-es.xml` and `sitemap-en.xml`, filtered using the same language rule as the rest of the site (`Multilingual::inferLang`), consistent with hreflang and the multilingual UI backlog.
Date: 2026-05-10 10:30AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 16
Lang: en
Translation_Key: praderas-day-16-sitemap-robots-lang-log
---

# Reviving Praderas (Day 16) — language-split sitemaps

After the planned UI queue through EN search and non-post footers (Day 15), the next prioritized item in `.agents/multilingual-ui-backlog.md` was **language-aware robots/sitemap behavior**. This note records the design rationale and what landed in the repo.

## Why a single flat `sitemap.xml` was not enough

The site already ships **ES/EN pairs** with `hreflang` and distinct URLs (`/blog/...` vs `/blog/en/...`, plus `/en/...` hubs). One combined sitemap is still valid, but we wanted:

1. **Operational parity:** crawlers and operators should see **URL sets split by language**, matching how Twig and plugins already scope behavior via `content_lang`.
2. **Standard index pattern:** a **sitemap index** at `/sitemap.xml` pointing to two child sitemaps avoids mixing languages in one flat `<urlset>` while staying within the sitemaps protocol.
3. **Single source of truth for language:** filtering reuses `Multilingual::inferLang` (paths `blog/en/`, `en/`, optional `Lang`) so SEO plumbing does not fork the heuristic used elsewhere.

`robots.txt` can keep a **single** `Sitemap:` line targeting the index; child sitemaps do not need separate directives unless you prefer them for tooling.

## What shipped

1. **`plugins/PicoRobots/PicoRobots.php`**
   - Additional request URLs: `sitemap-es.xml` and `sitemap-en.xml`, alongside `sitemap.xml` and `robots.txt`.
   - Per-language sitemap builds when those endpoints are requested; existing `lastmod` / `changefreq` / `priority` handling and the `onSitemap` hook apply to each built list.
   - **`/sitemap.xml`** now renders a **`<sitemapindex>`** linking `…/sitemap-es.xml` and `…/sitemap-en.xml`.

2. **`themes/bootstrap-blog/sitemap-index.twig`** and **`sitemap.twig`**
   - Theme-level templates for the index and URL sets (same shape as upstream defaults), resolved ahead of the plugin fallback.

## Recommended production checks

After deploy, verify:

- `GET /sitemap.xml` returns an index with two `<loc>` entries.
- `GET /sitemap-es.xml` / `GET /sitemap-en.xml` list only URLs whose inferred language matches.
- `GET /robots.txt` still references `/sitemap.xml`.

## Backlog status

The **Robots / sitemap per language** row in `.agents/multilingual-ui-backlog.md` is now **shipped**; remaining pending lines are optional taxonomy policy and other non-blocking follow-ups.

## Wall clock (implementation + agent docs)

- **Start (reference):** `2026-05-10 10:15:00 CEST`  
- **End (reference):** `2026-05-10 10:45:00 CEST`

Indicative window: **~30 minutes** for branch work, plugin/theme changes, ledger updates, and this article (commit/push may follow immediately after).

As with earlier logs, treat this as an **order-of-magnitude** slice rather than a substitute for production measurement.
