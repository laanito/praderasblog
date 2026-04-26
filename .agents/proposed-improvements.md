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
- **Task B) Series / collections** — **not started**; next up after this visual baseline (fields + in-post nav + `/series/...` indexes). See `.agents/day5-consultant-feedback.md`.

- **Visual & Usability Refinement (original scope — for reference)**
  - Sequence: whitespace → hierarchy → cards/related → typography → tags → sidebar → footer → micro-interactions.
  - Heuristics: Nielsen (Aesthetic/Minimalist + Consistency); keep implementation lightweight.
- **Reference:** `.agents/day5-consultant-feedback.md`.

## Priority 2 - Usability and Content Readability
- **Unify interface language**
  - Translate all UI labels to Spanish (or fully bilingual), avoiding mixed English strings.
- **Replace random placeholder images**
  - Use deterministic cover images per post (front matter field like `Image:`), with fallback.
  - Prevent visual inconsistency and low credibility from random picsum images.
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
- **Clarify canonical domain strategy**
  - Decide `praderas.org` vs `blog.praderas.org` as canonical.
  - Align `base_url`, redirects, and sitemap host consistently.
- **Improve meta consistency**
  - Ensure each page has meta description and meaningful title patterns.
  - Add Open Graph/Twitter tags in base template.
- **Upgrade search relevance**
  - Expand low-value words config for Spanish stopwords.
  - Optionally show highlighted matching snippets in results.
- **Add archive views**
  - Monthly/yearly archive index for older content discovery.

## Suggested Implementation Phases
- **Phase 1 (1-2 days):** Fix `blog.twig`, unify search widget, add pagination UI, remove placeholder widget. **→ Done in repo (2026-04-23):** `blog.twig` rebuild, `sidebar.twig` + `search-behavior.twig`, Spanish pager labels, recent-posts block; see `.agents/repo-context.md` and post `content/blog/reviviendo-praderas-dia-2-fase-1-plantilla-listado-busqueda-y-paginacion.md`. **Styling follow-up (human feedback):** the recent-posts list was first too plain; a second change added Bootstrap `list-group` and scoped CSS (`sidebar-recent` in `styles.css`). *Better, not a final look—acceptable to ship* until a later UI pass.
- **Phase 2 (2-4 days):** Navigation refresh, categories page, related posts, breadcrumbs. **→ Done in repo (2026-04-24):** fixed primary nav (`nav.twig`: Inicio, Blog, Categorías, Acerca), new `content/categorias.md` + `categories.twig`, `breadcrumbs.twig` on core templates, `plugins/50-BlogNeighbors.php` for related + prev/next by time + tag counts; see post `content/blog/reviviendo-praderas-dia-3-fase-2-navegacion-categorias-crumbs-y-posts-relacionados.md` and `.agents/repo-context.md`.
- **Phase 3 (2-3 days):** Metadata normalization pass and taxonomy cleanup across all posts. **→ Done in repo (2026-04-25):** completed missing `Tags` across legacy posts, normalized key casing (`Tags`), normalized outlier date format, added `scripts/frontmatter_audit.py`, and documented `.agents/post-template.md`; see post `content/blog/reviviendo-praderas-dia-4-fase-3-metadatos-taxonomia-y-lint-de-front-matter.md`.
- **Day 5 (consultant track, ~1–2 days + review):** Visual / usability layer (`praderas-theme.css` + twig) and Día 5 post. **→ Done in repo (2026-04-26),** with a second small iteration after consultant review; **series/collections (task B)** still open.
- **Phase 4 (2-3 days):** SEO polish, canonical redirects, social metadata, search relevance tuning. **Sequencing:** run **series support** (Day 5 task B) and any catch-up from Day 5 before or alongside early Phase 4, then the heavier **Phase 5** multilingual rollout when ready.
- **Phase 5 (medium, after 3–4 recommended):** Multilingual (e.g. ES + EN): split content layout, `lang` + `translation_key`, switcher, `hreflang`, sitemap. **Details:** `.agents/phase-5-6-plan.md` (Section Phase 5).
- **Phase 6 (low–medium, after 1–4 / coord. with meta):** AI-ready JSON endpoints (`blog.json`, per-post JSON or `?format=json`), schema v1, caching, optional public “for agents” doc. **Details:** `.agents/phase-5-6-plan.md` (Section Phase 6).

## Success Metrics
- **Navigation:** users reach a post from homepage in <=2 clicks.
- **Search UX:** search interaction works on all templates without dead controls.
- **Content quality:** 100% posts include tags and normalized date format.
- **Engagement:** increased depth (more page views per session) after related-post rollout.
- **Technical quality:** no malformed HTML in rendered source across core routes.
