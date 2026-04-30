---
Title: Reviving Praderas (Day 7) — Phase 4: SEO, social metadata, and date archive
Description: Phase 4 unified canonical and social metadata patterns, improved Spanish search stopwords, and added a date-based archive page linked from the sidebar.
Date: 2026-04-28 11:30AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 7
Lang: en
Translation_Key: praderas-day-7-phase-4-seo-discoverability
---

# Reviving Praderas (Day 7) — Phase 4: helping Google and humans find things without drama

After stabilizing navigation, content metadata, and series, the next roadmap block was **Phase 4**: SEO and discoverability without compromising Pico's flat-file simplicity.

This phase does not replace infrastructure-level domain decisions. Instead, it documents them and keeps site behavior coherent with canonical configuration in `config/config.yml`.

## What we implemented

1. **Unified HTML metadata** in `themes/bootstrap-blog/page-meta.twig`, included from `index.twig`, `post.twig`, and `blog.twig`:
   - consistent title pattern (`... | Blog Praderas`, with tags-page variant),
   - `meta description` and `robots` when present,
   - canonical link using `current_page.url`,
   - Open Graph (`og:title`, `og:description`, `og:url`, `og:type`, `og:locale`, `og:site_name`),
   - Twitter cards (`twitter:card`, `twitter:title`, `twitter:description`),
   - `og:type` as `article` for `blog/*` and `website` elsewhere.

2. **Better Spanish search behavior**: configured `PicoSearch.low_value_words` with Spanish stopwords so multi-word queries are less dominated by filler terms.

3. **Date archive page**: added `content/archivo.md` (`Template: archive`) and `themes/bootstrap-blog/archive.twig`, grouping `blog/*` entries by year and month (Spanish labels), plus sidebar link ("Ver archivo ->").

4. **Minimal discoverability copy**: added `Description` to `content/search.md` so search page metadata is useful when indexed or shared.

## Honest out-of-scope notes

- Root domain vs blog subdomain is still a DNS/hosting decision. In code, canonical `base_url` remains `https://blog.praderas.org`.
- Highlight snippets, heavy JSON-LD, and `hreflang` were intentionally deferred to future iterations.

## Day 7 closing

Phase 4 left the site with stronger HTML signals, a human-friendly archive, and better Spanish query behavior while keeping Pico lightweight. The formal next step was **Phase 5 (multilingual)**.
