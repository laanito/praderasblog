# Agent documentation hub (`.agents/`)

**Purpose:** One **index** for humans and Cursor agents so discovery does not depend on chat memory. Topic files stay **split by subject** (translation vs Comfy vs UI backlog) to avoid a single unmaintainable mega-doc; **this README is the consolidation layer**.

## Start here

1. **`repo-context.md`** — architecture, directory map, how Pico + theme + plugins fit together, links into content conventions.
2. **`translation-migration-tracker.md`** — canonical **ES↔EN post ledger**, backlog table, vocabulary, changelog for paired work.
3. **`post-template.md`** — front matter fields, `Image:` / `Translation_Key` / `Lang` expectations.
4. **`editorial-guidelines.md`** — human-first articles (what/why/how); JSON for agents is secondary.

## All documents (quick map)

| File | Use when you need… |
|------|---------------------|
| `repo-context.md` | Repo layout, live URL, Phase 5 behaviour, “where does X live?”. |
| `translation-migration-tracker.md` | Pairing status, `Translation_Key` naming, tracker changelog. |
| `translation-batches.md` | How to batch ES→EN work without splitting series across PRs. |
| `multilingual-ui-backlog.md` | Non-post EN UI (Phase 5 **closed**); deferred items only. |
| `proposed-improvements.md` | Prioritized product backlog (phases, Priority 2 covers, etc.). |
| `phase-5-6-plan.md` | Multilingual (Phase 5) vs JSON/API (Phase 6) roadmap detail. |
| `blog-json-api.md` | Phase 6 v1: `/blog.json`, per-post `.json`, schema 1.0 (`70-BlogJson.php`). |
| `comfyui-cover-images.md` | ComfyUI API, `export_cover.py` (**`--translation-key`**, `--patch-markdown`, `--webp`), checklist rows 7–9, **archive cover retrofit plan** (tiers + batches). |
| `retrofit-cover-queue.md` | **Tier A tick list** (*Reviviendo Praderas* Days 1–16): suggested **daily cadence** (≈2 pairs/day target, 1 pair floor) so hero backfill does not stall. |
| `image-prompt-guidelines.md` | House style + article-linked CLIP prompts before export. |
| `post-template.md` | Authoring rules and optional fields for new posts. |
| `editorial-guidelines.md` | Human-readable posts: explain decisions in prose, not command-only logs. |
| `day5-consultant-feedback.md` | Day 5 visual/series consultant notes (historical context). |

## Operational scripts (outside `.agents/` but tied to these docs)

- `scripts/frontmatter_audit.py` — required fields, tags, **`Image:`** on-disk check, **`Translation_Key`** duplicate / ES+EN pairing guard, **`tag_vocabulary.json`** parity with canonical tags.
- `scripts/tag_vocabulary.json` — canonical tag **`label_en`** + **`blurb_es`** / **`blurb_en`** (loaded by `65-Multilingual.php`).
- `scripts/comfyui/export_cover.py` — Comfy export + optional **`--patch-markdown`**, **`--translation-key`**, **`--webp`**.
- `scripts/comfyui/webp_cover.sh` — PNG → WebP via **`cwebp`** (`brew install webp`).

## Changelog (this index)

- **2026-05-24:** Day 24 — Tier A retrofit rows 8–9 + Phase 6 v1.1 (`search.json`, schema 1.1); `blog-json-api.md` updated.
- **2026-05-20 (follow-up):** `editorial-guidelines.md` — human-first writing; Phase 6 v1.1–v1.2 roadmap in `blog-json-api.md` / `phase-5-6-plan.md`.
- **2026-05-20:** Phase 6 v1 — `blog-json-api.md`, `plugins/70-BlogJson.php` (Day 23).
- **2026-05-19:** Phase 5 UI closure — `tag_vocabulary.json`, vocabulary audit, EN `/en/blog` pagination; `multilingual-ui-backlog.md` marked complete.
- **2026-05-15:** **`retrofit-cover-queue.md`** — Tier A table + **daily cadence** (≈2 ES/EN pairs/day target) linked from `comfyui-cover-images.md` § *Retrofit plan*.
- **2026-05-14:** Hub scripts — `export_cover.py` **`--translation-key`**; `frontmatter_audit.py` **`Translation_Key`** guard (`comfyui-cover-images.md` rows 6–7).
- **2026-05-14:** `post-template.md` / `image-prompt-guidelines.md` / `comfyui-cover-images.md` — **one dedicated `Image:` asset per article** (no borrowing another post’s cover).
- **2026-05-13 (follow-up):** Hub table — `comfyui-cover-images.md` now indexes **retrofit** playbook for older posts.
- **2026-05-13:** Initial **README** — consolidation by **index + reading order**, not merging other files.
