# Proposed Improvements: Structure, Navigation, and Usability

## Modernization Goals
- Make the blog easier to browse and search in under 2 clicks.
- Improve trust and readability (clear taxonomy, consistent language, cleaner UI).
- Reduce maintenance friction by standardizing content metadata and template behavior.
- Keep changes compatible with Pico's flat-file workflow and existing URLs.

## Priority 0 - Stabilize Current Experience (High Impact, Low Risk)
- **Fix `blog.twig` corruption**
  - Rebuild `themes/bootstrap-blog/blog.twig` from a clean baseline.
  - Remove broken trailing markup/script fragment currently leaking into HTML.
- **Restore working blog search UX**
  - Use one consistent search widget markup (`search_input` + `search_submit`) across templates.
  - Ensure Enter key and click both navigate to `/search/<term>`.
- **Re-enable visible pagination controls on `/blog`**
  - Render previous/next links (or page numbers) using existing pagination variables.
  - Add clear labels in Spanish (e.g., "Entradas anteriores / recientes").
- **Remove placeholder "Side Widget"**
  - Replace with useful blocks: "Artículos recientes", newsletter CTA, or "Sobre el blog".

## Priority 1 - Navigation and Information Architecture
- **Introduce topic-first navigation**
  - Keep top nav simple: Inicio, Blog, Categorías, Acerca.
  - Move less frequently visited pages out of primary nav.
- **Create a dedicated categories index page**
  - Show each category with description + post count.
  - Link to category landing pages (`/tags/?tag=...`) with encoded URLs.
- **Improve post discovery**
  - Add "related posts" block on article pages using shared tags.
  - Add "previous/next post" links at the end of each article.
- **Add breadcrumbs**
  - Example path: `Inicio > Blog > Categoría > Artículo`.
  - Helps orientation, especially from search and tag landing pages.

## Day 5 Consultant Priorities (Adopted, pre-Phase 5)
- **Status (2026-04-26):** Task **A) Visual & usability** is **shipped** in the repo: `themes/bootstrap-blog/css/praderas-theme.css` (design tokens, reading line-height ~1.75, nav/sidebar/footer, related block, post cards), plus template hooks in `index` / `post` / `blog` / `sidebar` / `breadcrumbs` / `categories` / `tags` / `search`. Post `content/blog/reviviendo-praderas-dia-5-pulido-visual-y-lectura.md` documents the work.
- **Follow-up (same day, after live review):** Second CSS/Twig pass for consultant nits: stronger shadows + hover *lift* on related links and listing cards, `rounded-pill` + `pradera-pill-tag` for tags, explicit date string spacing (`Publicado el` + date), darker in-body link hover, mobile `1rem` / `1.0625rem` from `sm` — merged into the same Día 5 post “Actualización” section.
- **External review:** First pass ~**8,7/10** (“clean, calm, much more pleasant to read”); remaining gap was mostly micro-polish (addressed in follow-up).
- **Task B) Series / collections** — **shipped** (2026-04-27, with post-merge UX iteration): optional front matter (`Series`, `Series_Slug`, `Series_Order`), plugin `plugins/60-SeriesCollections.php`, series indexes at `/series` and `/series/<slug>/` via `content/series.md` + `themes/bootstrap-blog/series.twig`, **series link in top nav**, and **series navigation moved to the sidebar** to reduce below-content density. Also mapped the legacy “Control de Tiempo Desacoplado” sequence (13 posts, from `desarrollo-de-arquitecturas-desacopladas-creando-una-aplicacion-de-control-de-horas` to `creacion-de-usuarios-en-tu-aplicacion-de-control-de-tiempo-con-react`). See post `content/blog/reviviendo-praderas-dia-6-series-y-colecciones.md`.

- **Visual & Usability Refinement (original scope — for reference)**
  - Sequence: whitespace → hierarchy → cards/related → typography → tags → sidebar → footer → micro-interactions.
  - Heuristics: Nielsen (Aesthetic/Minimalist + Consistency); keep implementation lightweight.
- **Reference:** `.agents/day5-consultant-feedback.md`.

## Priority 2 - Usability and Content Readability
- **Unify interface language**
  - Translate all UI labels to Spanish (or fully bilingual), avoiding mixed English strings.
- **Replace random placeholder images**
  - **Shipped (2026-05-11, Day 18; cards + post hero 2026-05-12; export patch 2026-05-12; WebP weight 2026-05-13):** optional **`Image:`** hero + **`og:image`** / Twitter; **`Image:`** or **Picsum** when unset; **`export_cover.py`** **`--patch-markdown`** / **`--webp`**; **`.webp`** covers (~50 KiB) replace multi‑MiB PNGs for Day 17–19 assets. See **`.agents/README.md`** hub. **`ffmpeg`** row 9 remainder / **CI** still open.
  - **Shipped — Tier A retrofit (2026-05-25):** Days **1–16** of *Reviviendo Praderas* now have committed **`Image:`** WebP heroes (queue complete in `.agents/retrofit-cover-queue.md`). **Next — Tier B+:** series openers and long tail per `.agents/comfyui-cover-images.md` § *Retrofit plan* (~2 ES/EN pairs/day habit).
- **Improve readability defaults**
  - Increase line-height and content width balance for long-form reading.
  - Add styles for code blocks, tables, and callouts used in technical posts.
- **Improve accessibility baseline**
  - Better alt text policy for post images.
  - Visible keyboard focus on links/buttons.
  - Higher contrast checks on badges and muted text.

## Priority 3 - Content Structure and Editorial Quality
- **Enforce front matter schema**
  - Required for posts: `Title`, `Description`, `Date`, `Author`, `Template`, `Tags`.
  - After Phase 3 pass (2026-04-25), 0 posts in `content/blog` were missing `Tags` (56 posts as of Día 5).
- **Normalize date formatting**
  - Standardize `Date` format (prefer ISO-like consistently).
  - Avoid mixed 24h and AM/PM formats.
- **Tag taxonomy cleanup**
  - Define canonical category list and aliases to avoid drift.
  - Add lightweight lint/check script to flag new unknown tags.
- **Create editorial templates**
  - Starter post template with sections and metadata comments.
  - Reduces inconsistencies in future content uploads.

## Priority 4 - SEO and Discoverability Enhancements
- **Status (2026-04-28):** **Shipped in repo** — canonical URLs aligned with `base_url` (`https://blog.praderas.org`) via `<link rel="canonical">` + OG/Twitter URLs; shared head partial `themes/bootstrap-blog/page-meta.twig`; Spanish `PicoSearch.low_value_words` in `config/config.yml`; archive page `/archivo` (`content/archivo.md` + `themes/bootstrap-blog/archive.twig`) with sidebar link; search page description added in `content/search.md`. Post `content/blog/reviviendo-praderas-dia-7-fase-4-seo-y-descubrimiento.md` documents the work.
- **Clarify canonical domain strategy**
  - **Repo stance:** treat **`blog.praderas.org`** as canonical for generated links and metadata (matches `base_url`). Root domain behaviour remains hosting/DNS (redirects not expressed in this flat-file repo).
- **Improve meta consistency**
  - Unified title pattern and optional description; Open Graph + Twitter Card tags from `page-meta.twig`.
- **Upgrade search relevance**
  - Spanish stopwords list added under `PicoSearch:` in `config/config.yml`.
  - Highlighted snippets in results remain optional/future.
- **Add archive views**
  - Year/month grouping implemented at `/archivo`.

## Suggested Implementation Phases
- **Phase 1 (1-2 days):** Fix `blog.twig`, unify search widget, add pagination UI, remove placeholder widget. **→ Done in repo (2026-04-23):** `blog.twig` rebuild, `sidebar.twig` + `search-behavior.twig`, Spanish pager labels, recent-posts block; see `.agents/repo-context.md` and post `content/blog/reviviendo-praderas-dia-2-fase-1-plantilla-listado-busqueda-y-paginacion.md`. **Styling follow-up (human feedback):** the recent-posts list was first too plain; a second change added Bootstrap `list-group` and scoped CSS (`sidebar-recent` in `styles.css`). *Better, not a final look—acceptable to ship* until a later UI pass.
- **Phase 2 (2-4 days):** Navigation refresh, categories page, related posts, breadcrumbs. **→ Done in repo (2026-04-24):** fixed primary nav (`nav.twig`: Inicio, Blog, Categorías, Acerca), new `content/categorias.md` + `categories.twig`, `breadcrumbs.twig` on core templates, `plugins/50-BlogNeighbors.php` for related + prev/next by time + tag counts; see post `content/blog/reviviendo-praderas-dia-3-fase-2-navegacion-categorias-crumbs-y-posts-relacionados.md` and `.agents/repo-context.md`. **Follow-up (2026-04-27):** nav now includes **Series** as a fifth primary item after Day 6 series rollout.
- **Phase 3 (2-3 days):** Metadata normalization pass and taxonomy cleanup across all posts. **→ Done in repo (2026-04-25):** completed missing `Tags` across legacy posts, normalized key casing (`Tags`), normalized outlier date format, added `scripts/frontmatter_audit.py`, and documented `.agents/post-template.md`; see post `content/blog/reviviendo-praderas-dia-4-fase-3-metadatos-taxonomia-y-lint-de-front-matter.md`.
- **Day 5 (consultant track, ~1–2 days + review):** Visual / usability layer (`praderas-theme.css` + twig), follow-up polish pass, and **Task B series/collections**. **→ Done in repo (2026-04-27),** documented in Día 5 + Día 6 posts.
- **Phase 4 (2-3 days):** SEO polish, canonical alignment, social metadata, search relevance tuning, archives. **→ Done in repo (2026-04-28):** `page-meta.twig`, `PicoSearch` Spanish stopwords, `/archivo`, sidebar link, search page description; see post `content/blog/reviviendo-praderas-dia-7-fase-4-seo-y-descubrimiento.md`. **Sequencing:** next major bucket is **Phase 5** multilingual rollout when ready.
- **Phase 5 (medium, after 3–4 recommended):** Multilingual (ES + EN). **→ First slice shipped in repo (2026-04-28):** Spanish URLs unchanged (`content/blog/*.md`); English posts under `content/blog/en/`; top EN pages under `content/en/`; `Lang` + `Translation_Key`; `plugins/65-Multilingual.php`; language switcher + dynamic `html lang`, `hreflang`, `og:locale`; pagination/neighbors/series/archive/search scoped by language; `/en/blog` via `blog-en.twig`. Posts: `content/blog/reviviendo-praderas-dia-8-fase-5-multilingue-modelo-y-metadatos.md` + `content/blog/en/reviving-praderas-day-8-phase-5-multilingual-content-model.md`. **Still open:** none for sitemap (Day 16: index + `sitemap-es.xml` / `sitemap-en.xml`); post archive is fully paired per `translation-migration-tracker.md`. **EN About** shipped at `/en/about-picocms` (paired with `acerca-de-picocms`). **Details:** `.agents/phase-5-6-plan.md` (Section Phase 5).
- **Phase 6 (low–medium):** AI-ready JSON. **→ v1.2 shipped (2026-05-25):** `/for-ai-agents` + `/en/for-ai-agents`. **→ v1.1 (2026-05-24):** `search.json`, listing agent fields, schema **1.1**. **→ v1 (2026-05-20):** `70-BlogJson.php`, `/blog.json` + per-post `.json`. **Next (extras):** tag filters on JSON, static pre-gen — `phase-5-6-plan.md`. **Tier A covers:** **complete** (Days 1–16, Day 25 batch). **Editorial:** human-first (`editorial-guidelines.md`).

## Success Metrics
- **Navigation:** users reach a post from homepage in <=2 clicks.
- **Search UX:** search interaction works on all templates without dead controls.
- **Content quality:** 100% posts include tags and normalized date format.
- **Engagement:** increased depth (more page views per session) after related-post rollout.
- **Technical quality:** no malformed HTML in rendered source across core routes.
