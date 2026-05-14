# Agent documentation hub (`.agents/`)

**Purpose:** One **index** for humans and Cursor agents so discovery does not depend on chat memory. Topic files stay **split by subject** (translation vs Comfy vs UI backlog) to avoid a single unmaintainable mega-doc; **this README is the consolidation layer**.

## Start here

1. **`repo-context.md`** — architecture, directory map, how Pico + theme + plugins fit together, links into content conventions.
2. **`translation-migration-tracker.md`** — canonical **ES↔EN post ledger**, backlog table, vocabulary, changelog for paired work.
3. **`post-template.md`** — front matter fields, `Image:` / `Translation_Key` / `Lang` expectations.

## All documents (quick map)

| File | Use when you need… |
|------|---------------------|
| `repo-context.md` | Repo layout, live URL, Phase 5 behaviour, “where does X live?”. |
| `translation-migration-tracker.md` | Pairing status, `Translation_Key` naming, tracker changelog. |
| `translation-batches.md` | How to batch ES→EN work without splitting series across PRs. |
| `multilingual-ui-backlog.md` | Non-post EN gaps (search, archive, footers, etc.). |
| `proposed-improvements.md` | Prioritized product backlog (phases, Priority 2 covers, etc.). |
| `phase-5-6-plan.md` | Multilingual (Phase 5) vs JSON/API (Phase 6) roadmap detail. |
| `comfyui-cover-images.md` | ComfyUI API, `export_cover.py` (**`--translation-key`**, `--patch-markdown`, `--webp`), checklist rows 7–9, **archive cover retrofit plan** (tiers + batches). |
| `image-prompt-guidelines.md` | House style + article-linked CLIP prompts before export. |
| `post-template.md` | Authoring rules and optional fields for new posts. |
| `day5-consultant-feedback.md` | Day 5 visual/series consultant notes (historical context). |

## Operational scripts (outside `.agents/` but tied to these docs)

- `scripts/frontmatter_audit.py` — required fields, tags, **`Image:`** on-disk check.
- `scripts/comfyui/export_cover.py` — Comfy export + optional **`--patch-markdown`**, **`--translation-key`**, **`--webp`**.
- `scripts/comfyui/webp_cover.sh` — PNG → WebP via **`cwebp`** (`brew install webp`).

## Changelog (this index)

- **2026-05-14:** Hub scripts line — `export_cover.py` gains **`--translation-key`** (see `comfyui-cover-images.md` checklist row 7).
- **2026-05-14:** `post-template.md` / `image-prompt-guidelines.md` / `comfyui-cover-images.md` — **one dedicated `Image:` asset per article** (no borrowing another post’s cover).
- **2026-05-13 (follow-up):** Hub table — `comfyui-cover-images.md` now indexes **retrofit** playbook for older posts.
- **2026-05-13:** Initial **README** — consolidation by **index + reading order**, not merging other files.
