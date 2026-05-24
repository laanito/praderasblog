# Phases 5 & 6 — Multilingual and AI-Ready (JSON) — Adopted backlog

**Source:** Adapted from strategic planning (April 2026), consolidated for this repository.  
**Status:** **Phase 5 shipped** (2026-04-28 → 2026-05-19). **Phase 6 v1.1 shipped** (2026-05-24): `search.json`, listing agent fields, schema **1.1**. **Phase 6 v1** (2026-05-20): `plugins/70-BlogJson.php`, **`.agents/blog-json-api.md`**. **Next slice (backlog):** public **`/for-ai-agents`** discovery page (v1.2). See § Phase 6 roadmap below.  
**Supersedes:** One-off copy of an external `PHASE_5_MULTILINGUAL_…` file; this file is the **canonical** reference in-repo.

---

## Why these phases exist

- **Phase 5 (multilingual):** Widen the audience (e.g. Spanish + English) without painting ourselves into a corner for future content.
- **Phase 6 (AI-ready JSON):** Expose a clean, **machine-consumable** view of posts (RAG, agents, tooling) without forcing consumers to parse full HTML.

Both align with the blog’s “transparent, modern stack” story; neither replaces solid metadata and URL hygiene (phases 3–4).

---

## Phase 5: Multilingual support

### Goals

- Offer at least **Spanish + English** for the blog experience (content + UI strings where applicable).
- Clear **language switching** in the theme.
- **SEO:** `hreflang`, canonicals, sitemap entries per language (when URLs exist).

### Scope

- All blog posts (51+ and growing) + new posts; navigation/sidebar/pagination labels; meta/OG; sitemap/robots implications.

### Recommended approach (Option A — maintainable)

- **Split content trees** (implemented variant): Spanish posts remain in `content/blog/*.md`; English posts live in **`content/blog/en/*.md`**; optional English top pages in **`content/en/*.md`**. Shared **`Translation_Key`** in front matter pairs translations (see also `Lang`).
- Add explicit **`lang`** in front matter per file.
- Twig partial for a **language switcher**; Pico routing for language prefixes (exact URL scheme TBD: `/en/blog/...` vs `blog/en/...` — decide before build).
- Translations: LLM-assisted batches + **human review** on flagship posts and legal-sensitive lines.

**Option B** (single folder + on-the-fly translation plugin): possible short-term, higher long-term cost; only consider if we explicitly accept quality/maintenance risk.

### Caveats (agent guardrails)

- This is a **large** migration: URLs, internal links, plugins, and search must be re-tested.
- `base_url` and sitemap host strategy (see Priority 4) should be **decided** before or as part of Phase 5 to avoid double work.

### Success (high level)

- Both languages for all posts the project commits to support.
- No mixed-language **UI** on a single page; search scoped per language (or clear behavior if cross-language is intentional).

---

## Phase 6: AI-ready JSON (for agents and tools)

### Goals

- **Structured, low-noise** payloads: front matter + body without nav/sidebars/scripts.
- Position the blog as friendly to **agents** and RAG (optional public docs page “For AI / developers”).

### Suggested shape

Two endpoints (exact paths TBD; cache-friendly):

1. **Listing** — e.g. `/blog.json` (or `?format=json` on the listing page): latest N or paginated, minimal fields.
2. **Per post** — e.g. `/blog/{slug}.json` or `?format=json` on the post.

**Schema sketch (v1):** `meta` (version, `generated_at`, `language`, counts) + `posts[]` with `slug`, `title`, `date`, `author`, `tags`, `description`, `content` (markdown or plain), `url`, optional `reading_time_minutes`, `lang`.

**Implementation options in Pico:** dedicated Twig/PHP output, small plugin, or static generation at build time. Add **caching** headers — content is nearly static.

### Shipped (v1 — 2026-05-20)

- **`plugins/70-BlogJson.php`** — dedicated paths (same pattern as `PicoRobots`): no HTML template, `Content-Type: application/json`, `Cache-Control: public, max-age=3600`.
- **Why a plugin, not a separate service:** Pico already loads all pages and front matter; a small plugin reuses `Multilingual::inferLang`, `Translation_Key` alternates, and `base_url` without a second runtime or build pipeline.
- **Endpoints:** `/blog.json`, `/blog/en.json`, `/blog/{slug}.json`, `/blog/en/{slug}.json` — documented in **`.agents/blog-json-api.md`**.

### Roadmap (v1.1 → v1.2)

| Slice | Deliverable | Notes |
|-------|-------------|--------|
| **v1.1 — Search** | `GET /search.json?q=…` (and language scope) | **Shipped 2026-05-24** — `PicoSearch::searchBlogPosts()` + `70-BlogJson.php`. |
| **v1.1 — Schema** | `word_count`, `estimated_tokens`, `modified_at` on **listings** too | **Shipped 2026-05-24** — schema version **1.1**. |
| **v1.2 — Discovery** | **`/for-ai-agents`** (HTML or markdown page + optional `.json` index) | Skill-style entry: available endpoints, schema version, language rules, canonical tags, “start here” links — for tools that do not read `.agents/` in git. |

**Editorial:** Human-facing explanation of Phase 6 choices lives in **`.agents/editorial-guidelines.md`**; future ship logs should describe goals/benefits in prose, not only `curl` examples.

### Extras (later)

- Tag filter query params on listings; static JSON pre-generation at deploy; sitemap policy for JSON URLs (likely omit).

### Success (high level)

- Valid JSON, documented, stable enough for clients; no regression on the HTML site’s performance or caching story.

---

## Order and dependencies

| Block | Depends on | Note |
|-------|------------|------|
| Phases 1–2 (done) | — | Base UX |
| Phases 3–4 (backlog) | — | Metadata, SEO, canonicals |
| **Phase 5** | 3–4 *recommended* before or tightly coordinated | Big content + URL work |
| **Phase 6** | 1–2 stable; 3–4 help for meta | Can often start after routing/meta is clear |

**Suggested order when ready:** finish **3 & 4** to a shippable bar → **Phase 5** (user-visible, heavy) → **Phase 6** (technical, narrative), unless product priority inverts (e.g. JSON first for a specific integration).

---

## For future agents

1. Read `repo-context.md` and `proposed-improvements.md` first.
2. Read **`editorial-guidelines.md`** before writing or extending **blog articles** (human narrative required).
3. Use **this file** for Phase 5/6 scope; open a **PR** with a short migration/risk section.
4. For **ES→EN migration batches** (series, glossary, PR sizing, honest “context” and wall-clock notes), read `translation-batches.md` alongside `translation-migration-tracker.md`.
5. If an external file references `.agents/grok-consultant-context.md`, treat it as optional; **this repo** may not include it — rely on `repo-context.md` and this document.

---

## Changelog in-repo

- **2026-04-30:** “For future agents” now points to `translation-batches.md` for translation PR workflow.
- **2026-04-29:** Added `.agents/translation-migration-tracker.md` (ES→EN backlog, vocabulary stub, editorial-era reference); homepages `index.md` / `en/index.md` aligned with explicit production model (2020 / 2023–24 / 2026).
- **2026-05-24:** Phase 6 **v1.1** — `search.json`, `/en/search.json`, listing `word_count` / `estimated_tokens` / `modified_at`; Day 24 ship log; Tier A retrofit rows 8–9.
- **2026-05-20 (follow-up):** Editorial guidelines + Phase 6 **v1.1/v1.2 roadmap** (`search.json`, agent schema fields, `/for-ai-agents`); Tier A retrofit rows 6–7 (docs-only PR, no new article).
- **2026-05-20:** Phase 6 **v1 slice** — `70-BlogJson.php` + `blog-json-api.md` (Day 23); listing + single-post JSON for ES/EN.
- **2026-05-19:** Phase 5 **UI closure** — `scripts/tag_vocabulary.json`, `tag_blurb_*` Twig context, EN blog pagination, vocabulary audit guard; `multilingual-ui-backlog.md` closed for current model.
- **2026-04-28:** Phase 5 **first slice** implemented: `plugins/65-Multilingual.php`, theme updates (`page-meta.twig`, `nav.twig`, `lang-switcher.twig`, `blog-en.twig`, `post.twig`/`index.twig`/`blog.twig`/`sidebar.twig`/`archive.twig`), `10-Pagination.php` / `50-BlogNeighbors.php` / `60-SeriesCollections.php` language scoping, `40-PicoSearch.php` + `low_value_words_en`, sample `content/en/*` + paired Día 8 posts. Documented in `content/blog/reviviendo-praderas-dia-8-fase-5-multilingue-modelo-y-metadatos.md` (+ EN twin under `content/blog/en/`).
- **2026-04-24:** Added as canonical backlog; aligned numbering with `proposed-improvements` (5 = multilingual, 6 = JSON); editor notes and caveats added.
