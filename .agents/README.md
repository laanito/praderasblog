# Agent documentation hub (`.agents/`)

**Start here.** Topic files are split on purpose; this index is the only place that lists everything.

---

## Reading order (new agents)

| Step | File |
|------|------|
| 1 | `repo-context.md` — Pico layout, URLs, plugins (no phase history essays) |
| 2 | `article-authoring-guide.md` — **new blog post** workflow + checklist |
| 3 | `post-template.md` + `editorial-guidelines.md` — YAML + human tone |
| 4 | Task-specific doc from table below |

---

## Active docs (use these)

| File | When |
|------|------|
| `proposed-improvements.md` | **Open vs shipped** product backlog |
| `visual-qa-backlog.md` | Article layout, tables, code; theme QA process |
| `translation-migration-tracker.md` | ES↔EN pairs, `Translation_Key`, vocabulary |
| `translation-batches.md` | Batching rules for translation PRs |
| `retrofit-cover-queue.md` | Tier A **done**; Tier B+ cover cadence |
| `comfyui-cover-images.md` | ComfyUI + `export_cover.py` |
| `image-prompt-guidelines.md` | Cover prompt style |
| `phase-5-6-plan.md` | Phase 5/6 goals + JSON **extras** still open |
| `blog-json-api.md` | `/blog.json`, `search.json` contract (schema 1.2) |
| `man-in-the-loop.md` | Human-written section (`/man-in-the-loop`) |

---

## Reference (stable, rarely edited)

| File | When |
|------|------|
| `repo-context.md` | Architecture, directory map, plugin behaviour, test routes |
| `post-template.md` | Front matter fields |
| `editorial-guidelines.md` | What/why/how prose rules |
| `article-authoring-guide.md` | End-to-end new article process |
| `multilingual-ui-backlog.md` | Phase 5 UI **closed** + deferred items |

---

## Historical (archived context)

| File | When |
|------|------|
| `day5-consultant-feedback.md` | Pre–Phase 5 consultant brief — **implemented**; see Días 5–6 |

---

## Scripts

| Script | Role |
|--------|------|
| `scripts/frontmatter_audit.py` | Required fields, tags, `Image:`, `Translation_Key` |
| `scripts/tag_vocabulary.json` | EN tag labels + blurbs |
| `scripts/comfyui/export_cover.py` | Covers: `--translation-key`, `--webp`, `--patch-markdown` |

---

## Changelog (index only)

- **2026-05-26:** Hub cleanup — active / reference / historical split; backlog consolidated in `proposed-improvements.md`.
- **2026-05-26:** `article-authoring-guide.md`, `visual-qa-backlog.md`.
- **2026-05-13:** Initial README.
