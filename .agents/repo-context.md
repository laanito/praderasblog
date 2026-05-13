# Praderas Blog - Repository Context for Agents

## Project Snapshot
- **Type:** Flat-file blog powered by Pico CMS (PHP + Twig + Markdown).
- **Primary live URL:** `https://blog.praderas.org/` (not `https://praderas.org/`).
- **Language:** Spanish-first site with **Phase 5** English subtree (`content/blog/en/`, `content/en/`); theme labels switch for EN pages where wired (`content_lang`).
- **Current active theme:** `bootstrap-blog` (configured in `config/config.yml`).
- **Content size:** Spanish posts under `content/blog/*.md` plus incremental English posts under `content/blog/en/` (see tracker for pair coverage).

## High-Level Architecture
- **Core runtime:** `index.php` boots Pico and loads `config/`, `plugins/`, and `themes/`.
- **Configuration:** `config/config.yml` controls site title, base URL, theme, and pagination settings.
- **Content model:** Markdown files under `content/` with YAML front matter.
- **Rendering:** Twig templates in `themes/bootstrap-blog`.
- **Extensions:** Custom plugins in `plugins/` for pagination, search, tags, robots/sitemap. **Optional:** local ComfyUI HTTP API for AI-generated cover images — see `.agents/comfyui-cover-images.md` (not part of production `index.php` runtime).

## Agent docs (backlog & roadmaps)
- **`README.md`** — **hub index:** reading order + one-line map of every `.agents/*.md` file (start here for consolidation).
- `proposed-improvements.md` — prioritized backlog and phases 1–6 summary.
- `phase-5-6-plan.md` — Phase **5** (multilingual) **first slice shipped** (2026-04-28); Phase **6** (JSON) still future; read before extending either.
- `translation-migration-tracker.md` — ES→EN migration ledger: translation backlog table, editorial-era reference, vocabulary, checklist for new pairs.
- `translation-batches.md` — **how to run translation in batches:** context-window rationale, whole-series rule, glossary updates, honest human wall-clock vs specialist estimates, merge checklist (read before shipping ES/EN pairs).
- `multilingual-ui-backlog.md` — **non-post EN gaps** (search, archive, footers, sitemap index): what shipped vs pending for Twig/`content_lang` routes.
- `comfyui-cover-images.md` — **optional ComfyUI cover pipeline:** SDXL `/prompt`, `export_cover.py` (PNG + **`--webp`** + **`--patch-markdown`**), **`webp_cover.sh`**, **WebP** covers in `assets/images/`, checklist (**row 9** partial).
- `image-prompt-guidelines.md` — **cover prompt coherence:** house tone + anchoring ComfyUI positives to article metadata (`Title`, `Description`, tags); use with `export_cover.py`.
- `post-template.md` — front matter conventions for new posts (`Image:` may target **`.webp`**).
- `day5-consultant-feedback.md` — Day 5 sequence and status notes (visual + series completed, follow-up UX tweaks).

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
- `assets/` — static files served from site root (e.g. **`assets/images/*.webp`** for optional post **`Image:`** heroes and social previews; Comfy exports **PNG** then **`cwebp`** in-repo per **Day 20** / `comfyui-cover-images.md`). **Older posts** without **`Image:`** still use Picsum; a **retrofit** playbook (priority + batches) is in **`comfyui-cover-images.md`** § *Retrofit plan*.
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
  - `65-Multilingual.php` — `Lang` / `Translation_Key` metadata, `hreflang` + `og:locale` context, `alternate_language_page`, `pradera_home_url`, `content_lang` / `html_lang`
  - `PicoTags.php`
  - `PicoRobots/`

## Content Front Matter Conventions
- Common fields used: `Title`, `Description`, `Date`, `Author`, `Template`, `Tags`. Optional bilingual: `Lang`, `Translation_Key` (same key on paired ES/EN files).
- Current coverage in `content/blog`:
  - `Description`: 56/56
  - `Date`: 56/56
  - `Author`: 56/56
  - `Template: post`: 56/56
  - `Tags`: 56/56 (normalized in Phase 3)
- Existing tag taxonomy includes:
  - `Aplicaciones Moviles`, `Ciberseguridad`, `Crypto`, `Desarrollo Web`, `Economia`,
    `Inteligencia Artificial`, `Privacidad`, `Productividad`, `Sistemas`, `Sociedad`.

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

## Phase 1 theme work (2026-04, completed in repo)
- `themes/bootstrap-blog/blog.twig` was rebuilt: malformed trailing HTML/JS is removed, pagination is visible, layout matches the rest of the site.
- Shared `sidebar.twig` + `search-behavior.twig`: one search field (`#search_input`, `#search_submit`) wired for click and Enter, Spanish labels, **Artículos recientes** (5) replaces the default “Side Widget” placeholder, category tag links are URL-escaped in the partial.
- **Human feedback (post-launch):** the first “recent posts” pass shipped as plain, unstyled links. Review said the block *worked* but looked unpolished. A follow-up (list group + `sidebar-recent` CSS) made it *better*; the maintainer is fine leaving it as-is for now (not a final art direction, just acceptable). Document this so the next pass does not assume the UI is “finished.”
- `index.twig` and `post.twig` use the same sidebar and search behavior; **`html lang`** is driven by `Multilingual` (`html_lang`) after Phase 5.
- `config/config.yml` sets Spanish labels for the pagination plugin (`pagination_prev_text` / `pagination_next_text`) for any consumer of the plugin’s link strings; the blog template uses explicit Spanish labels for the pager UI.
- If `gh` (GitHub CLI) is unavailable, open a pull request from the branch manually after `git push`.

## Phase 2 (2026-04, completed in repo)
- Primary **navbar** was fixed to four items in Phase 2; after Day 6 series rollout it is now five items: Inicio, Blog, Series, Categorías, Acerca.
- **`/categorias`**: new markdown page + `categories.twig` lists each site tag (with short blurb, post count, link to `/tags/?tag=...`).
- **Breadcrumbs** on `index` (Inicio on home), `blog`, `post` (chained through first tag when present), `search`, `tags`, and `categorias`.
- **Article footer** (only `content/blog/*` with `post` template): *Te puede interesar* (tag overlap) + prev/next by **time** via `50-BlogNeighbors.php`.

## Phase 3 (2026-04, completed in repo)
- Front matter normalization pass across legacy posts: missing `Tags` filled, lowercase `tags` standardized to `Tags`, and one date outlier normalized.
- Canonical taxonomy is now complete across all posts (`Tags` present everywhere).
- Added `scripts/frontmatter_audit.py` (schema/date/taxonomy checks) for repeatable verification.
- Added `.agents/post-template.md` as starter editorial template for new entries.

## Phase 4 (SEO & discoverability, 2026-04)
- Shared SEO/social head partial (`page-meta.twig`): canonical URL, Open Graph, Twitter Cards; article vs website `og:type` for `blog/*` vs other pages.
- **`base_url`** in `config/config.yml` documents canonical deployment host (`https://blog.praderas.org`); root-domain redirects remain infrastructure-side.
- **`/archivo`**: year/month grouped index of **Spanish** `blog/*` posts only (`archive.twig` excludes `blog/en/*`), linked from sidebar “Archivo”.
- Search page (`content/search.md`) includes `Description` for cleaner snippets/metadata where applicable.

## Phase 5 (Multilingual — first slice, 2026-04-28)
- **URLs:** no migration for existing Spanish posts; English lives under `/blog/en/...` and top-level EN under `/en/...`.
- **Metadata:** optional `Lang`, `Translation_Key` (see `.agents/post-template.md`); plugin **`65-Multilingual.php`** wires alternates for Twig + `hreflang` + `og:locale` / `og:locale:alternate`.
- **Theme:** `lang-switcher.twig`, `blog-en.twig`, bilingual `nav.twig` / `sidebar.twig` strings, dynamic `html lang` on `index` / `post` / `blog` / `blog-en`.
- **Plugins:** pagination excludes EN from `/blog`; neighbors + related + series maps respect language; `PicoSearch` uses English stopwords on EN pages.
- **Article:** Día 8 ES `content/blog/reviviendo-praderas-dia-8-fase-5-multilingue-modelo-y-metadatos.md` + EN twin in `content/blog/en/`.

## Day 5/6 (consultant track completed through series)
- **Visual / usability (Day 5):** delivered via `praderas-theme.css` and template updates; Día 5 post + consultant follow-up merged. External review ~8,7/10 after first live deploy; a second small CSS/Twig pass closed the main nits.
- **Series / collections (Day 6):** implemented with front matter `Series` / `Series_Slug` / `Series_Order`, index routes under `/series/...`, top-nav `Series` link, and a sidebar series widget on post pages.
- **Legacy series mapping:** “Control de Tiempo Desacoplado” was retrofitted across 13 historical posts (kickoff to React users chapter) using the same series fields.
- Details: `.agents/day5-consultant-feedback.md`, backlog: `proposed-improvements.md` (Day 5 section).

## Live Site Findings (Current State)
- Main nav (ES): **Inicio** (Bienvenidos), **Blog**, **Series**, **Categorías** (highlight also on `/tags`), **Acerca** → `acerca-de-picocms`. On EN pages: **Home**, **Blog** → `/en/blog`, **Series** → `/en/series`, **Categories** → `/en/categorias` (highlight also on `/en/tags`), **About** → `/en/about-picocms` (`nav.twig`).
- Sidebar on most pages includes: search, **Archivo** link card, category tags, and **Artículos recientes** (list-group + `sidebar-recent` styles; **Praderas** theme layer styles tags as pills with hover). On post pages that belong to a series, a **Serie** widget (prev/next/index) appears above categories.
- Blog listing, tag, and search cards use **`Image:`** when present, otherwise **Lorem Picsum** (`list-card-thumb.twig`). **Blog article** pages (`post.twig`, `id` under `blog/…`) use the **same seed** for listing thumb, **hero** (1200×630), and **`og:image` / Twitter** when `Image:` is unset (`page-meta.twig` + `praderas-macros.twig`).
- URL routing is canonical on subdomain (`blog.praderas.org`); treat `base_url` as the canonical origin for links and social meta. Root domain behaviour without redirects is a deployment/DNS concern outside this repo.

## Confirmed Technical/UX Issues
- (Resolved in tree for Phase 1) Historically, `blog.twig` was corrupted and showed broken HTML, inconsistent search `id`s, and no visible pager. **Current `blog.twig` + `sidebar.twig` / `search-behavior.twig` in this repo** address the listing, search, and pagination UI; verify again after deploy.
- UI language: Phase 5 improves **sidebar + post chrome** on English routes; footer legal line may still be Spanish on `blog.twig` / `post.twig` and English on `blog-en.twig` — acceptable short-term; full unification remains Priority 2 in `proposed-improvements.md`.
- `config/config.yml` points to `https://blog.praderas.org`; user-reported canonical site is `https://praderas.org` (domain strategy mismatch).
- Phase 3 fixed known metadata gaps in repo; re-run `python3 scripts/frontmatter_audit.py` after future content imports to prevent regressions.

## Agent Guardrails for Future Work
- For **UI-only** changes (e.g. sidebar, typography), assume **human design review** may be needed: agents can meet functional acceptance while still under-delivering on “feel” until a second pass.
- Keep this repo as a **content-first static-like CMS**; avoid introducing heavy backend complexity.
- Prioritize:
  1. Fixing template integrity and navigation/search behavior.
  2. Metadata consistency (tags, date normalization).
  3. Progressive UX improvements without breaking existing URLs.
- Preserve current slug paths under `content/blog` to avoid SEO regressions.
- Treat `themes/bootstrap-blog/blog.twig.bak` as historical reference only; verify before reuse.

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
