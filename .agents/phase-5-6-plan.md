# Phases 5 & 6 — reference

**Status (2026-05-26):** **Phase 5 shipped** (2026-04-28 → 2026-05-19). **Phase 6 complete** through **v1.2** (JSON v1 → v1.1 `search.json` + schema 1.1 → `/for-ai-agents`).

**Open work:** JSON extras and all non-phase backlog → `proposed-improvements.md`. **Machine contract:** `blog-json-api.md`.

---

## Phase 5 — shipped model

- **Trees:** Spanish `content/blog/*.md`; English `content/blog/en/*.md`; hubs `content/en/*.md`.
- **Pairing:** `Lang` + shared `Translation_Key`; Spanish canonical `Tags`; EN labels via `scripts/tag_vocabulary.json` + `65-Multilingual.php`.
- **URLs:** Spanish posts keep legacy `/blog/...` slugs; EN at `/blog/en/...` and `/en/...`.
- **UI:** `content_lang` branching, `lang-switcher.twig`, `blog-en.twig`, per-language search/archive/sitemaps.
- **Closure doc:** `multilingual-ui-backlog.md` (deferred: bilingual YAML tags only if product requests it).

Ship logs: Días 8–16. Translation ledger: `translation-migration-tracker.md`. Batching: `translation-batches.md`.

---

## Phase 6 — shipped slices

| Slice | Deliverable | Shipped |
|-------|-------------|---------|
| **v1** | `/blog.json`, `/blog/en.json`, per-post `.json` | 2026-05-20 — `70-BlogJson.php` |
| **v1.1** | `/search.json`, `/en/search.json`; listing `word_count`, `estimated_tokens`, `modified_at`; schema **1.1** | 2026-05-24 |
| **v1.2** | `/for-ai-agents`, `/en/for-ai-agents` | 2026-05-25 |

**Why a Pico plugin:** same request lifecycle as HTML, no second service; cache-friendly dedicated paths.

**Editorial:** Human posts explain goals/trade-offs — `editorial-guidelines.md`; not command-only logs.

### Extras

- **Done 2026-05-28:** Listing `?tag=` filter (schema **1.2**); `scripts/pregenerate_blog_json.py` + CI; `/for-ai-agents` pages updated.
- **Done:** Sitemap policy — JSON API URLs omitted; HTML only in language sitemaps (`blog-json-api.md`).

---

## Dependencies (historical)

| Block | Status |
|-------|--------|
| Phases 1–4 | Done |
| Phase 5 | Done |
| Phase 6 | Done (v1.2); extras open |

---

## For agents

1. `README.md` → `repo-context.md` → task doc from hub table.
2. New articles: `article-authoring-guide.md` + `editorial-guidelines.md`.
3. ES↔EN batches: `translation-batches.md` + tracker.
4. Optional external `grok-consultant-context.md` — not in-repo; use `repo-context.md` + this file.

---

## Changelog

- **2026-05-26:** Compressed — removed duplicate goals/Option B essay; pointers to `proposed-improvements.md`.
- **2026-05-25:** v1.2 `/for-ai-agents`.
- **2026-05-24:** v1.1 `search.json`, schema 1.1.
- **2026-05-20:** v1 JSON plugin + `blog-json-api.md`.
- **2026-04-24:** Initial canonical backlog.
