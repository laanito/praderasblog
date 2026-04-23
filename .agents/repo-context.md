# Praderas Blog - Repository Context for Agents

## Project Snapshot
- **Type:** Flat-file blog powered by Pico CMS (PHP + Twig + Markdown).
- **Primary live URL:** `https://blog.praderas.org/` (not `https://praderas.org/`).
- **Language:** Mostly Spanish content and labels, with some English leftovers in theme UI.
- **Current active theme:** `bootstrap-blog` (configured in `config/config.yml`).
- **Content size:** 51 posts in `content/blog`.

## High-Level Architecture
- **Core runtime:** `index.php` boots Pico and loads `config/`, `plugins/`, and `themes/`.
- **Configuration:** `config/config.yml` controls site title, base URL, theme, and pagination settings.
- **Content model:** Markdown files under `content/` with YAML front matter.
- **Rendering:** Twig templates in `themes/bootstrap-blog`.
- **Extensions:** Custom plugins in `plugins/` for pagination, search, tags, robots/sitemap.

## Directory Map
- `content/`
  - `index.md` homepage ("Bienvenidos")
  - `blog.md` listing page (`Template: blog`)
  - `search.md` search page (`Template: search`)
  - `tags.md` tag page (`Template: tags`)
  - `blog/*.md` post content
- `themes/bootstrap-blog/`
  - `index.twig` base layout + sidebar + navbar
  - `blog.twig` listing cards
  - `post.twig` article page
  - `search.twig` and `tags.twig`
- `plugins/`
  - `10-Pagination.php`
  - `40-PicoSearch.php`
  - `PicoTags.php`
  - `PicoRobots/`

## Content Front Matter Conventions
- Common fields used: `Title`, `Description`, `Date`, `Author`, `Template`, `Tags`.
- Current coverage in `content/blog`:
  - `Description`: 51/51
  - `Date`: 51/51
  - `Author`: 51/51
  - `Template: post`: 51/51
  - `Tags`: 38/51 (13 posts missing tags)
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
- **Tags (`PicoTags.php`)**
  - Registers `Tags` and `Filter` front matter.
  - Exposes `get_all_tags()` and `apply_tag_filter`.
- **Robots/Sitemap (`PicoRobots`)**
  - Serves `robots.txt` and `sitemap.xml`.
  - Live check confirms both endpoints exist on `blog.praderas.org`.

## Phase 1 theme work (2026-04, completed in repo)
- `themes/bootstrap-blog/blog.twig` was rebuilt: malformed trailing HTML/JS is removed, pagination is visible, layout matches the rest of the site.
- Shared `sidebar.twig` + `search-behavior.twig`: one search field (`#search_input`, `#search_submit`) wired for click and Enter, Spanish labels, **Artículos recientes** (5) replaces the default “Side Widget” placeholder, category tag links are URL-escaped in the partial.
- `index.twig` and `post.twig` use the same sidebar and search behavior; `lang` on the main layout templates is set to `es` where we touched.
- `config/config.yml` sets Spanish labels for the pagination plugin (`pagination_prev_text` / `pagination_next_text`) for any consumer of the plugin’s link strings; the blog template uses explicit Spanish labels for the pager UI.
- If `gh` (GitHub CLI) is unavailable, open a pull request from the branch manually after `git push`.

## Live Site Findings (Current State)
- Main nav currently includes: **Bienvenidos**, **Acerca de PicoCMS**, **Blog**.
- Sidebar appears on most pages with:
  - Search card
  - Categories/tags card
  - Placeholder "Side Widget" card (still default text)
- Blog cards and tag results currently use random images from `picsum.photos`.
- URL routing is canonical on subdomain (`blog.praderas.org`), while root domain returns 404.

## Confirmed Technical/UX Issues
- `themes/bootstrap-blog/blog.twig` appears corrupted/truncated near footer/scripts.
  - Live HTML includes a broken tail with `<!DOCTYPE html>` injected at the end.
  - The blog page search widget uses `id="button-search"` but lacks working JS wiring.
  - Pagination variables are available from plugin but listing template does not render pager UI.
- UI language is mixed (Spanish + English):
  - Example: "Search", "Go!", "Side Widget" in blog listing.
- `config/config.yml` points to `https://blog.praderas.org`; user-reported canonical site is `https://praderas.org` (domain strategy mismatch).
- Some post metadata quality is inconsistent (date formatting styles vary across posts).

## Agent Guardrails for Future Work
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
  - `/blog/<post>`
  - `/search/<term>`
  - `/tags` and `/tags/?tag=<tag>`
  - `/robots.txt` and `/sitemap.xml`
