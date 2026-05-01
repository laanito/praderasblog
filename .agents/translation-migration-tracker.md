# Translation & content migration tracker (ES → EN)

**Purpose:** Plan and record the move from Spanish-only posts to paired EN content (`content/blog/en/`, same **`Translation_Key`** as the Spanish file). This file is the **working ledger** for agents and humans: status, vocabulary, and language rules—not the public site.

**Related:** `phase-5-6-plan.md` (Phase 5 goals), `post-template.md` (`Lang`, `Translation_Key`), `translation-batches.md` (batching rules, context explainer, honest time reporting), homepage pair `content/index.md` ↔ `content/en/index.md`.

---

## Editorial eras (reference for translators & agents)

These facts are also stated on the **homepages** (`index.md` / `en/index.md`). Keep wording aligned when you touch either language.

| Period | Archive / site | Human vs IA (high level) |
|--------|----------------|---------------------------|
| **~2020** | Articles recovered from an older blog | **100% human** authorship in the source material we kept. |
| **2023–2024** | Site built with human intent | **Human-built** stack and structure; **published body text** in that phase was **IA-generated** (human oversight varied by post). |
| **2026** | Current rebuild (*Reviviendo Praderas*, Phase 5, etc.) | **IA-driven workflows** for planning, implementation, review, and audit; a person sets **direction / “what to solve”** (CEO/mind), **without** routine hands-on coding or the kind of line-by-line editorial review a traditional team would do. |

When in doubt, prefer **honest, dated** statements over vague “AI-assisted” language if the homepage has already committed to a stronger formulation.

---

## Language & URL rules (short)

- **Spanish posts:** `content/blog/<slug>.md` → `/blog/<slug>` (canonical bulk of the archive).
- **English posts:** `content/blog/en/<slug>.md` → `/blog/en/<slug>`.
- **Paired pages:** identical **`Translation_Key`** in front matter; optional **`Lang`**: `es` / `en`.
- **UI copy in EN only:** `content/en/*.md` (e.g. `/en`, `/en/blog`, `/en/series`, `/en/categorias`).
- **Tag taxonomy:** Currently **shared** (Spanish labels on both sites). If we introduce EN-only tag display names, record the mapping in **Vocabulary** below.

---

## Translation backlog (posts)

**Legend:** `done` · `draft` · `todo` · `n/a` (no EN planned)

| Translation_Key | ES path (content/blog/) | EN path (content/blog/en/) | Status | Notes |
|-------------------|-------------------------|----------------------------|--------|--------|
| `praderas-home` | _(N/A — use `content/index.md`)_ | _(N/A — use `content/en/index.md`)_ | done | Home pair; editorial-era text must stay in sync. |
| `praderas-day-1-technical-audit` | `reviviendo-praderas-dia-1-auditoria-tecnica-y-plan-con-agentes-ia.md` | `reviving-praderas-day-1-technical-audit-and-ai-agent-improvement-plan.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-2-phase-1-listing-search-pagination` | `reviviendo-praderas-dia-2-fase-1-plantilla-listado-busqueda-y-paginacion.md` | `reviving-praderas-day-2-phase-1-listing-search-and-pagination.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-3-phase-2-navigation-categories-breadcrumbs-related` | `reviviendo-praderas-dia-3-fase-2-navegacion-categorias-crumbs-y-posts-relacionados.md` | `reviving-praderas-day-3-phase-2-navigation-categories-breadcrumbs-and-related-posts.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint` | `reviviendo-praderas-dia-4-fase-3-metadatos-taxonomia-y-lint-de-front-matter.md` | `reviving-praderas-day-4-phase-3-metadata-taxonomy-and-frontmatter-lint.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-5-visual-polish` | `reviviendo-praderas-dia-5-pulido-visual-y-lectura.md` | `reviving-praderas-day-5-visual-polish-readability-and-brand-tone.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-6-series-and-collections` | `reviviendo-praderas-dia-6-series-y-colecciones.md` | `reviving-praderas-day-6-series-and-collections.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-7-phase-4-seo-discoverability` | `reviviendo-praderas-dia-7-fase-4-seo-y-descubrimiento.md` | `reviving-praderas-day-7-phase-4-seo-social-metadata-and-date-archive.md` | done | Batch 1: Reviving Praderas. |
| `praderas-phase-5-multilingual` | `reviviendo-praderas-dia-8-fase-5-multilingue-modelo-y-metadatos.md` | `reviving-praderas-day-8-phase-5-multilingual-content-model.md` | done | Phase 5 announcement pair. |
| `praderas-day-9-translation-migration-batch-1` | `reviviendo-praderas-dia-9-migracion-de-traducciones-batch-1-y-plan.md` | `reviving-praderas-day-9-translation-migration-batch-1-and-plan.md` | done | Batch 1 closure note + time estimates. |
| `praderas-day-10-batch-2-multilingual-hubs` | `reviviendo-praderas-dia-10-batch-2-traduccion-y-arreglos-multilingues.md` | `reviving-praderas-day-10-batch-2-translation-and-multilingual-fixes.md` | done | Daily log: batch 2 + EN hubs + ~35 min wall clock vs localization band. |
| `praderas-nav-series` | _(use `content/series.md`)_ | _(use `content/en/series.md`)_ | done | EN nav + `hreflang`; series detail also `/en/series/<slug>/`. |
| `praderas-nav-categories` | _(use `content/categorias.md`)_ | _(use `content/en/categorias.md`)_ | done | EN categories hub; tag counts scoped to EN posts. |
| `praderas-ctd-01` | `desarrollo-de-arquitecturas-desacopladas-creando-una-aplicacion-de-control-de-horas.md` | `decoupled-architectures-time-tracking-app-overview.md` | done | Batch 2. |
| `praderas-ctd-02` | `implementacion-de-postgrest-creacion-de-una-potente-api-rest.md` | `postgrest-rest-api-setup.md` | done | Batch 2. |
| `praderas-ctd-03` | `mejora-de-seguridad-y-acceso-con-nginx-en-postgrest.md` | `nginx-postgrest-https-hardening.md` | done | Batch 2. |
| `praderas-ctd-04` | `autenticacion-y-uso-de-tokens-jwt-en-postgrest.md` | `postgrest-jwt-authentication.md` | done | Batch 2. |
| `praderas-ctd-05` | `introduccion-a-react-es5-es6-y-su-ejecucion.md` | `react-es5-es6-intro.md` | done | Batch 2. |
| `praderas-ctd-06` | `disenando-la-base-de-datos-para-tu-aplicacion-de-control-de-tiempo.md` | `database-schema-time-tracking-app.md` | done | Batch 2. |
| `praderas-ctd-07` | `configuracion-de-la-base-de-datos-para-tu-aplicacion-de-control-de-tiempo.md` | `database-roles-permissions-time-tracking.md` | done | Batch 2. |
| `praderas-ctd-08` | `creacion-de-tablas-restantes-en-la-base-de-datos-para-tu-aplicacion-de-control-de-tiempo.md` | `database-extra-tables-time-tracking.md` | done | Batch 2. |
| `praderas-ctd-09` | `interactuando-con-la-api-rest-de-tu-aplicacion-de-control-de-tiempo.md` | `rest-api-client-time-tracking-app.md` | done | Batch 2. |
| `praderas-ctd-10` | `creando-un-frontend-en-react-para-la-gestion-de-roles.md` | `react-frontend-role-management-setup.md` | done | Batch 2. |
| `praderas-ctd-11` | `desarrollo-de-una-aplicacion-de-gestion-de-roles-en-react.md` | `react-role-management-list-edit.md` | done | Batch 2. |
| `praderas-ctd-12` | `desarrollo-de-una-aplicacion-de-gestion-de-proyectos-en-react.md` | `react-project-management-crud.md` | done | Batch 2. |
| `praderas-ctd-13` | `creacion-de-usuarios-en-tu-aplicacion-de-control-de-tiempo-con-react.md` | `react-create-user-time-tracking.md` | done | Batch 2. |
| *(add rows as you ship pairs)* | | | todo | Prefer one row per `Translation_Key`. |

## Batch migration plan (to avoid context overflow)

1. **Batch 1 — Reviving Praderas complete series (Day 1 to Day 9)**  
   Status: **done in this PR**.
2. **Batch 2 — Control de Tiempo Desacoplado complete series (13 chapters)**  
   Status: **done** — EN posts + paired hub pages `/en/series`, `/en/categorias`, routing/theme fixes for EN navigation.
3. **Batch 3 — Core security + privacy cluster**  
   Status: todo.
4. **Batch 4 — AI and future-tech cluster**  
   Status: todo.
5. **Batch 5 — Productivity and collaboration tooling cluster**  
   Status: todo.
6. **Batch 6 — Mobile development fundamentals cluster**  
   Status: todo.
7. **Batch 7 — Crypto + blockchain cluster**  
   Status: todo.
8. **Batch 8 — General tech and education long-tail**  
   Status: todo.

---

## Vocabulary & terminology (ES ↔ EN)

Add rows as you fix recurring choices (tags, UI strings, series names).

| Context | ES (current) | EN (preferred) | Notes |
|---------|--------------|----------------|--------|
| Series name | Reviviendo Praderas | Reviving Praderas | EN posts use EN series title; same `Series_Slug`. |
| Series name | Control de Tiempo Desacoplado | Decoupled time tracking | Same `Series_Slug: control-de-tiempo-desacoplado`; EN detail URLs use `/en/series/<slug>/`. |
| Sidebar label | Artículos recientes | Recent posts | Keep sentence case in EN UI. |
| Sidebar CTA | Ver archivo | View archive | Keep concise CTA wording. |
| Roadmap wording | Fase 4: SEO y descubrimiento | Phase 4: SEO and discoverability | Prefer "discoverability" in EN series posts. |
| *(add)* | | | |

---

## Checklist before merging a new EN post

- [ ] `Translation_Key` matches the Spanish sibling (or documents intentional orphan).
- [ ] `Lang: en` on EN file if not obvious from path alone.
- [ ] `Tags` valid per `scripts/frontmatter_audit.py` / `post-template.md`.
- [ ] **This tracker:** row added or updated in **Translation backlog**.
- [ ] **Vocabulary:** any new canonical term added to the table.

---

## Changelog (in-repo)

- **2026-05-01:** Day 10 ES/EN daily log posts (`praderas-day-10-batch-2-multilingual-hubs`) documenting batch 2 + hub fixes + ~35 min human wall clock vs ~10–18 h localization order-of-magnitude.
- **2026-04-30 (batch 2):** Full *Control de Tiempo Desacoplado* EN pairs (`praderas-ctd-01`…`13`); `content/en/series.md` + `content/en/categorias.md` with `Translation_Key`; `SeriesCollections` + `BlogNeighbors` + `nav.twig` + bilingual `series.twig`/`categories.twig` for EN routes and counts.
- **2026-04-30 (agent docs):** Added `translation-batches.md` — operational instructions distilled from Day 9 (batches, context window, series integrity, glossary, wall-clock honesty); linked from `repo-context.md`, `post-template.md`, and this file.
- **2026-04-30 (follow-up):** Day 9 ES/EN posts updated: plain-language “context” explainer, 8-batch timeline table, batch rationale, and human wall-clock ~20 min (replacing an earlier inflated AI-assisted duration estimate).
- **2026-04-30:** Added batch-based migration plan (8 batches), completed Batch 1 for *Reviving Praderas* (Day 1-9 pairs now available in EN), and expanded vocabulary guidance.
- **2026-04-29:** Initial tracker: eras table, backlog schema, vocabulary stub, checklist; created alongside homepage transparency updates.
