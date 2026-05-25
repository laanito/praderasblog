# Praderas Blog - Repository Context for Agents

## Project Snapshot
- **Type:** Flat-file blog powered by Pico CMS (PHP + Twig + Markdown).
- **Primary live URL:** `https://blog.praderas.org/` (not `https://praderas.org/`).
- **Language:** Spanish-first site with **Phase 5** English subtree (`content/blog/en/`, `content/en/`); theme labels switch for EN pages where wired (`content_lang`).
- **Current active theme:** `bootstrap-blog` (configured in `config/config.yml`).
- **Content size:** ~79 Spanish + ~79 English posts under `content/blog/` (158 total per `frontmatter_audit.py`); paired via `Translation_Key` — see `translation-migration-tracker.md`.

## High-Level Architecture
- **Core runtime:** `index.php` boots Pico and loads `config/`, `plugins/`, and `themes/`.
- **Configuration:** `config/config.yml` controls site title, base URL, theme, and pagination settings.
- **Content model:** Markdown files under `content/` with YAML front matter.
- **Rendering:** Twig templates in `themes/bootstrap-blog`.
- **Extensions:** Custom plugins in `plugins/` for pagination, search, tags, robots/sitemap. **Optional:** local ComfyUI HTTP API for AI-generated cover images — see `.agents/comfyui-cover-images.md` (not part of production `index.php` runtime).

## Agent documentation

**Hub:** `.agents/README.md` — reading order, active vs reference vs historical docs, script index.

**Backlog (open work):** `proposed-improvements.md`, `visual-qa-backlog.md`, `retrofit-cover-queue.md` (Tier B+).

**New posts:** `article-authoring-guide.md` → `editorial-guidelines.md` → `post-template.md`.

Phase-by-phase ship narrative lives in *Reviviendo Praderas* posts (`content/blog/reviviendo-praderas-dia-*`), not in this file.

## Directory Map
- `content/`
  - `index.md` homepage ("Bienvenidos") — product copy, Pico, *Reviviendo Praderas*, navigation, and **explicit editorial/AI transparency** (2020 human recoveries, 2023–24 human-built site + IA-generated prose, 2026 IA-led engineering with human direction); paired with `content/en/index.md` via `Translation_Key`
  - `en/index.md`, `en/blog.md`, `en/tags.md`, `en/about-picocms.md`, `en/archivo.md` — English home, blog listing (`blog-en` → `/en/blog`), tag hub (`/en/tags`), About Pico (`/en/about-picocms`), date archive (`/en/archivo`; paired with `archivo.md` via `Translation_Key: praderas-nav-archive`)
  - `blog.md` listing page (`Template: blog`)
  - `series.md` series hub (`Template: series`) for `/series` and `/series/<slug>/`
  - `archivo.md` chronological archive (`Template: archive`) — URL `/archivo`
  - `search.md` search page (`Template: search`)
  - `tags.md` tag page (`Template: tags`)
  - `categorias.md` category index (`Template: categories`) — URL typically `/categorias`
  - `blog/*.md` post content (Spanish URLs, `/blog/...`)
  - `blog/en/*.md` English posts (`/blog/en/...`)
- `assets/` — static files served from site root (e.g. **`assets/images/*.webp`** for optional post **`Image:`** heroes and social previews; Comfy exports **PNG** then **`cwebp`** in-repo per **Day 20** / `comfyui-cover-images.md`). **Older posts** without **`Image:`** still use Picsum; a **retrofit** playbook (priority + batches) is in **`comfyui-cover-images.md`** § *Retrofit plan*; **Tier A tick list + daily cadence** in **`retrofit-cover-queue.md`**.
- `themes/bootstrap-blog/`
  - `index.twig` base layout + sidebar + navbar
  - `blog.twig` listing cards (Spanish paginated `/blog`)
  - `blog-en.twig` English-only listing (`/en/blog`)
  - `lang-switcher.twig` header language link when `Translation_Key` has a pair
  - `list-card-thumb.twig` optional `Image:` thumbnail or **Picsum** fallback (stable seed) for blog / EN blog / tags / search cards
  - `praderas-macros.twig` shared **`resolve_visual_cover_url`** macro: committed/absolute `Image:` or Lorem Picsum (stable seed) for matching surfaces
  - `post.twig` article page; **`Image:`** hero when set, else **Picsum** hero for `blog/` articles; related posts + prev/next when wired by `50-BlogNeighbors.php`
  - `search.twig` and `tags.twig`
  - `categories.twig` category index (cards + tag counts from plugin)
  - `series.twig` series index/detail template
  - `archive.twig` archive by year/month (`/archivo`)
  - `page-meta.twig` shared `<title>`, meta description/robots, canonical + Open Graph + Twitter Card tags (**`og:image`** / large Twitter card when a cover URL exists — committed **`Image:`** or **Picsum** fallback on `blog/…` posts)
  - `nav.twig` primary navigation (ES: **Inicio, Blog, Series, Categorías, Acerca**; EN: **Home, Blog, Series, Categories, About** → `en/about-picocms`; **Categorías** / **Categories** highlight when on `tags` / `en/tags`) + language switcher include
  - `breadcrumbs.twig` shared “migaja de pan”
  - `sidebar.twig` shared sidebar (Búsqueda, Serie on post pages, Categorías, Artículos recientes)
  - `search-behavior.twig` shared search (click + Enter) script include
  - `css/styles.css` — base Bootstrap (bundle); `css/praderas-theme.css` — Day 5 visual layer (tokens, ~1.75 body line-height, related + listing card elevation/hover, pill tags, breadcrumbs/sidebar/footer, in-body link hover; mobile `1rem` / `sm+` `1.0625rem` for long-form)
  - `styles.css` also includes scoped rules for the recent-posts list (class `sidebar-recent`)
- `plugins/`
  - `10-Pagination.php`
  - `40-PicoSearch.php`
  - `50-BlogNeighbors.php` — on `blog/*` posts: `post_prev_in_time`, `post_next_in_time` (chronological), `related_posts` (shared tags, max 5); on `categorias` page: `tag_post_counts` (map tag → int)
  - `60-SeriesCollections.php` — series routes (`/series/<slug>/`), series index context, and post-level series navigation data (used in sidebar widget); **per-language** series maps (ES vs EN posts)
  - `65-Multilingual.php` — `Lang` / `Translation_Key` metadata, `hreflang` + `og:locale` context, `alternate_language_page`, `pradera_home_url`, `content_lang` / `html_lang`, tag display maps from **`scripts/tag_vocabulary.json`** (`tag_label_en`, `tag_blurb_es`, `tag_blurb_en`)
  - `70-BlogJson.php` — JSON: `/blog.json`, `/blog/en.json`, per-post `.json`, `/search.json`, `/en/search.json` (see `blog-json-api.md`)
  - `PicoTags.php`
  - `PicoRobots/`

## Content Front Matter Conventions
- Common fields: `Title`, `Description`, `Date`, `Author`, `Template`, `Tags`. Bilingual: `Lang`, `Translation_Key` (same key on paired ES/EN files). Optional: `Image:`, `Series` / `Series_Slug` / `Series_Order`.
- **Audit:** `python3 scripts/frontmatter_audit.py` — required fields, canonical tags, `Image:` paths, `Translation_Key` pairing (158 posts as of 2026-05-26).
- **Canonical tags** (Spanish in YAML): `Aplicaciones Moviles`, `Ciberseguridad`, `Crypto`, `Desarrollo Web`, `Economia`, `Inteligencia Artificial`, `Privacidad`, `Productividad`, `Sistemas`, `Sociedad`. EN labels: `scripts/tag_vocabulary.json`.

## Plugin Behavior Summary
- **Pagination (`10-Pagination.php`)**
  - Parses `/blog/<n>` style URLs via `pagination_page_indicator`.
  - Exposes `paged_pages`, `page_number`, `total_pages`, and link variables to Twig.
  - `config/config.yml` sets limit 10 and indicator `blog`.
  - On the `blog` listing page, the paginated pool is **Spanish only** (`blog/*` excluding `blog/en/*`).
- **Search (`40-PicoSearch.php`)**
  - Uses canonical route format `/search/<term>`.
  - Registers Twig filter `apply_search`.
  - Fallback redirect for non-JS query flow (`?q=`).
  - `PicoSearch.low_value_words` in `config/config.yml` (Spanish stopwords); **`low_value_words_en`** used when the current page language is English (Phase 5).
- **Tags (`PicoTags.php`)**
  - Registers `Tags` and `Filter` front matter.
  - Exposes `get_all_tags()` and `apply_tag_filter`.
- **Robots/Sitemap (`PicoRobots`)**
  - Serves `robots.txt`; **`sitemap.xml`** is a **sitemap index** listing `sitemap-es.xml` and `sitemap-en.xml`, each a `<urlset>` filtered by `Multilingual::inferLang` (Day 16).
  - Theme overrides: `themes/bootstrap-blog/sitemap-index.twig`, `sitemap.twig`.

## Implemented capabilities (summary)

Phases 1–6 and Day 5/6 consultant work are **shipped** in this repo. Detail: *Reviviendo Praderas* Días 2–25 posts; JSON contract: `blog-json-api.md`; open product work: `proposed-improvements.md`.

| Area | In tree today |
|------|----------------|
| Listing / search / nav | Rebuilt `blog.twig`, shared sidebar + search, five-item nav, `/categorias`, breadcrumbs, related + prev/next |
| Metadata | Normalized `Tags`, `frontmatter_audit.py`, `post-template.md` |
| SEO / archive | `page-meta.twig`, `/archivo`, per-lang sitemaps |
| Multilingual | `65-Multilingual.php`, `/blog/en/…`, `/en/…` hubs, `tag_vocabulary.json`, language switcher |
| Series | `60-SeriesCollections.php`, `/series/…`, sidebar widget |
| JSON / agents | `70-BlogJson.php`, `/for-ai-agents`, schema 1.1 |
| Covers | Comfy + WebP pipeline; Tier A retrofit **complete** (`retrofit-cover-queue.md`) |

**Post-body layout debt** (narrow column, tables, code): `visual-qa-backlog.md` — not the same as Day 5 shell polish.

## Live site (verify after deploy)
- Main nav (ES): **Inicio** (Bienvenidos), **Blog**, **Series**, **Categorías** (highlight also on `/tags`), **Acerca** → `acerca-de-picocms`. On EN pages: **Home**, **Blog** → `/en/blog`, **Series** → `/en/series`, **Categories** → `/en/categorias` (highlight also on `/en/tags`), **About** → `/en/about-picocms` (`nav.twig`).
- Sidebar on most pages includes: search, **Archivo** link card, category tags, and **Artículos recientes** (list-group + `sidebar-recent` styles; **Praderas** theme layer styles tags as pills with hover). On post pages that belong to a series, a **Serie** widget (prev/next/index) appears above categories.
- Blog listing, tag, and search cards use **`Image:`** when present, otherwise **Lorem Picsum** (`list-card-thumb.twig`). **Blog article** pages (`post.twig`, `id` under `blog/…`) use the **same seed** for listing thumb, **hero** (1200×630), and **`og:image` / Twitter** when `Image:` is unset (`page-meta.twig` + `praderas-macros.twig`).
- URL routing is canonical on subdomain (`blog.praderas.org`); treat `base_url` as the canonical origin for links and social meta. Root domain behaviour without redirects is a deployment/DNS concern outside this repo.

## Known constraints
- **`/blog`** listing stays Spanish chrome; **`/en/blog`** is English (`blog-en.twig`).
- **`base_url`:** `https://blog.praderas.org` in `config/config.yml` (root `praderas.org` redirects are infra-side).
- Re-run **`frontmatter_audit.py`** after bulk imports.

## Agent guardrails
- **Human-first articles:** `article-authoring-guide.md`, `editorial-guidelines.md`. JSON feeds supplement prose; they do not replace it.
- **Docs-only / cover PRs** may skip a new ship log — see `retrofit-cover-queue.md`.
- **Theme changes:** check `visual-qa-backlog.md` (prose width, tables, code).
- **Content-first CMS** — avoid heavy backend; preserve Spanish post slugs under `content/blog/`.
- `blog.twig.bak` is historical only.

## Quick Operational Notes
- **PHP on the agent machine:** Many developer environments (including some Cursor sandboxes) **do not have `php` on PATH**. Do **not** treat `php -l` or a local Pico boot as a merge gate unless you have confirmed PHP is installed. Prefer **`python3 scripts/frontmatter_audit.py`** for content checks; leave PHP syntax/runtime verification to **CI or a human** with a local stack.
- Production/local preview of the site still depends on **PHP + Composer** (`vendor/`); that is unrelated to whether the agent shell can run `php`.
- No admin panel: all editorial changes are file-based.
- Any agent making structural changes should test:
  - `/`
  - `/blog`
  - `/categorias`
  - `/blog/<post>`
  - `/search/<term>`
  - `/tags` and `/tags/?tag=<tag>`
  - `/robots.txt` and `/sitemap.xml`
  - `/archivo`
  - `/en` and `/en/blog` (language switcher ↔ `/blog` when paired)
  - `/en/archivo`
  - `/en/tags` and `/en/tags?tag=Ciberseguridad` (sample)
  - `/en/about-picocms`
  - `/blog/en/<post>` (sample paired content)
  - `/blog.json`, `/search.json?q=test`, `/for-ai-agents`
