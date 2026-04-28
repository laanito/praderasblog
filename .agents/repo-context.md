# Praderas Blog - Repository Context for Agents

## Project Snapshot
- **Type:** Flat-file blog powered by Pico CMS (PHP + Twig + Markdown).
- **Primary live URL:** `https://blog.praderas.org/` (not `https://praderas.org/`).
- **Language:** Mostly Spanish content and labels, with some English leftovers in theme UI.
- **Current active theme:** `bootstrap-blog` (configured in `config/config.yml`).
- **Content size:** 58 posts in `content/blog` (including *Reviviendo Praderas* through Día 7).

## High-Level Architecture
- **Core runtime:** `index.php` boots Pico and loads `config/`, `plugins/`, and `themes/`.
- **Configuration:** `config/config.yml` controls site title, base URL, theme, and pagination settings.
- **Content model:** Markdown files under `content/` with YAML front matter.
- **Rendering:** Twig templates in `themes/bootstrap-blog`.
- **Extensions:** Custom plugins in `plugins/` for pagination, search, tags, robots/sitemap.

## Agent docs (backlog & roadmaps)
- `proposed-improvements.md` — prioritized backlog and phases 1–6 summary.
- `phase-5-6-plan.md` — **future** work: multilingual (Phase 5) and JSON/AI-ready API (Phase 6); read before implementing either.
- `day5-consultant-feedback.md` — Day 5 sequence and status notes (visual + series completed, follow-up UX tweaks).

## Directory Map
- `content/`
  - `index.md` homepage ("Bienvenidos") — product copy; describes Pico, *Reviviendo Praderas*, navigation, and AI-as-tooling (not a single chat product)
  - `blog.md` listing page (`Template: blog`)
  - `series.md` series hub (`Template: series`) for `/series` and `/series/<slug>/`
  - `archivo.md` chronological archive (`Template: archive`) — URL `/archivo`
  - `search.md` search page (`Template: search`)
  - `tags.md` tag page (`Template: tags`)
  - `categorias.md` category index (`Template: categories`) — URL typically `/categorias`
  - `blog/*.md` post content
- `themes/bootstrap-blog/`
  - `index.twig` base layout + sidebar + navbar
  - `blog.twig` listing cards
  - `post.twig` article page
  - `search.twig` and `tags.twig`
  - `categories.twig` category index (cards + tag counts from plugin)
  - `series.twig` series index/detail template
  - `archive.twig` archive by year/month (`/archivo`)
  - `page-meta.twig` shared `<title>`, meta description/robots, canonical + Open Graph + Twitter Card tags
  - `nav.twig` primary navigation (**Inicio, Blog, Series, Categorías, Acerca**; **Categorías** highlights when on `tags` too)
  - `breadcrumbs.twig` shared “migaja de pan”
  - `sidebar.twig` shared sidebar (Búsqueda, Serie on post pages, Categorías, Artículos recientes)
  - `search-behavior.twig` shared search (click + Enter) script include
  - `css/styles.css` — base Bootstrap (bundle); `css/praderas-theme.css` — Day 5 visual layer (tokens, ~1.75 body line-height, related + listing card elevation/hover, pill tags, breadcrumbs/sidebar/footer, in-body link hover; mobile `1rem` / `sm+` `1.0625rem` for long-form)
  - `styles.css` also includes scoped rules for the recent-posts list (class `sidebar-recent`)
- `plugins/`
  - `10-Pagination.php`
  - `40-PicoSearch.php`
  - `50-BlogNeighbors.php` — on `blog/*` posts: `post_prev_in_time`, `post_next_in_time` (chronological), `related_posts` (shared tags, max 5); on `categorias` page: `tag_post_counts` (map tag → int)
  - `60-SeriesCollections.php` — series routes (`/series/<slug>/`), series index context, and post-level series navigation data (used in sidebar widget)
  - `PicoTags.php`
  - `PicoRobots/`

## Content Front Matter Conventions
- Common fields used: `Title`, `Description`, `Date`, `Author`, `Template`, `Tags`.
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
- **Search (`40-PicoSearch.php`)**
  - Uses canonical route format `/search/<term>`.
  - Registers Twig filter `apply_search`.
  - Fallback redirect for non-JS query flow (`?q=`).
  - Optional `PicoSearch.low_value_words` in `config/config.yml` (Spanish stopwords list as of Phase 4).
- **Tags (`PicoTags.php`)**
  - Registers `Tags` and `Filter` front matter.
  - Exposes `get_all_tags()` and `apply_tag_filter`.
- **Robots/Sitemap (`PicoRobots`)**
  - Serves `robots.txt` and `sitemap.xml`.
  - Live check confirms both endpoints exist on `blog.praderas.org`.

## Phase 1 theme work (2026-04, completed in repo)
- `themes/bootstrap-blog/blog.twig` was rebuilt: malformed trailing HTML/JS is removed, pagination is visible, layout matches the rest of the site.
- Shared `sidebar.twig` + `search-behavior.twig`: one search field (`#search_input`, `#search_submit`) wired for click and Enter, Spanish labels, **Artículos recientes** (5) replaces the default “Side Widget” placeholder, category tag links are URL-escaped in the partial.
- **Human feedback (post-launch):** the first “recent posts” pass shipped as plain, unstyled links. Review said the block *worked* but looked unpolished. A follow-up (list group + `sidebar-recent` CSS) made it *better*; the maintainer is fine leaving it as-is for now (not a final art direction, just acceptable). Document this so the next pass does not assume the UI is “finished.”
- `index.twig` and `post.twig` use the same sidebar and search behavior; `lang` on the main layout templates is set to `es` where we touched.
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
- **`/archivo`**: year/month grouped index of `blog/*` posts (`archive.twig`), linked from sidebar “Archivo”.
- Search page (`content/search.md`) includes `Description` for cleaner snippets/metadata where applicable.

## Day 5/6 (consultant track completed through series)
- **Visual / usability (Day 5):** delivered via `praderas-theme.css` and template updates; Día 5 post + consultant follow-up merged. External review ~8,7/10 after first live deploy; a second small CSS/Twig pass closed the main nits.
- **Series / collections (Day 6):** implemented with front matter `Series` / `Series_Slug` / `Series_Order`, index routes under `/series/...`, top-nav `Series` link, and a sidebar series widget on post pages.
- **Legacy series mapping:** “Control de Tiempo Desacoplado” was retrofitted across 13 historical posts (kickoff to React users chapter) using the same series fields.
- **Before Phase 6 JSON endpoints:** with Phase 4 SEO shipped (2026-04-28), next planned major work is **Phase 5 multilingual**, then Phase 6 per `.agents/phase-5-6-plan.md`.
- Details: `.agents/day5-consultant-feedback.md`, backlog: `proposed-improvements.md` (Day 5 section).

## Live Site Findings (Current State)
- Main nav: **Inicio** (Bienvenidos), **Blog**, **Series**, **Categorías** (and primary highlight also when browsing `/tags`), **Acerca**.
- Sidebar on most pages includes: search, **Archivo** link card, category tags, and **Artículos recientes** (list-group + `sidebar-recent` styles; **Praderas** theme layer styles tags as pills with hover). On post pages that belong to a series, a **Serie** widget (prev/next/index) appears above categories.
- Blog cards and tag results currently use random images from `picsum.photos`.
- URL routing is canonical on subdomain (`blog.praderas.org`); treat `base_url` as the canonical origin for links and social meta. Root domain behaviour without redirects is a deployment/DNS concern outside this repo.

## Confirmed Technical/UX Issues
- (Resolved in tree for Phase 1) Historically, `blog.twig` was corrupted and showed broken HTML, inconsistent search `id`s, and no visible pager. **Current `blog.twig` + `sidebar.twig` / `search-behavior.twig` in this repo** address the listing, search, and pagination UI; verify again after deploy.
- UI language is still mixed in some areas not touched in Phase 1 (e.g. footer text, search results cards):
  - The blog listing sidebar and shared search are now in Spanish; remaining English strings are tracked in proposed-improvements (Priority 2).
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
- Local runtime depends on PHP + Composer dependencies (`vendor/`).
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
