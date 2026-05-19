---
Title: Reviving Praderas (Day 22) — Phase 5 UI closure (vocabulary) and Tier A retrofit (Days 2–3)
Description: Night session log: merged multilingual vocabulary PR (`tag_vocabulary.json`, EN pagination, audit) plus two Tier A Comfy hero WebPs (Phases 1 and 2) via `--translation-key`.
Date: 2026-05-19 10:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 22
Lang: en
Translation_Key: praderas-day-22-phase-5-vocabulary-tier-a-retrofit-log
Image: /assets/images/day22-comfyui-sdxl-phase5-vocabulary-tier-a-retrofit-hero.webp

---

# Reviving Praderas (Day 22) — Phase 5 UI closed, retrofit back on rhythm

Tonight we shipped **two planned tracks** in **one pull request**: finish the **multilingual UI layer** (what the tracker still called open after the post backlog closed) and resume **Tier A cover retrofit** on *Reviving Praderas* at the **~two ES/EN pairs per day** cadence. This post records **both** the work already merged to `main` and the cover batch on this branch.

## Wall clock (order of magnitude)

- **Phase 5 UI closure (vocabulary + templates + agent docs):** on the order of **one hour** of agent time plus merge to `main` before this branch.  
- **Tier A retrofit (Days 2 and 3) + Comfy + WebP + key patch:** ~**one more hour** (two SDXL runs + audit).  
- **This ES/EN ship log:** written in the same session.

This is **human direction and review time**, not “the model ran for two hours.”

---

## Block A — Phase 5 UI closed (already on `main`)

After merging **`feat/day-22-phase-5-vocabulary-and-ui-closure`**, the repo treats **Phase 5 as complete** under the current product model: **paired ES/EN posts** (done earlier) plus **UI surfaces** that do not mix languages on a single page.

### What landed in code and docs

1. **`scripts/tag_vocabulary.json`** — single source per canonical Spanish tag (`Tags` in YAML unchanged): **`label_en`**, **`blurb_es`**, **`blurb_en`**. Category blurbs used to be duplicated inline in `categories.twig`; **`65-Multilingual.php`** now exposes `tag_blurb_es` / `tag_blurb_en` alongside `tag_label_en`.
2. **`scripts/frontmatter_audit.py`** — verifies the JSON matches all ten `CANONICAL_TAGS` rows with non-empty fields (same spirit as the Day 21 **`Translation_Key`** guard).
3. **`/en/blog` pagination** — `10-Pagination.php` filters `blog/en/*` only; `blog-en.twig` uses `paged_pages` with **Older posts / Newer posts / Page N of M** (Spanish `/blog` listing stays Spanish on purpose).
4. **`categories.twig`** — EN footer links to **`/en/search`**; removed the stale “search UI still Spanish-only” note (superseded by Day 15).
5. **Agent docs** — `multilingual-ui-backlog.md` marked **closed**; vocabulary table expanded in `translation-migration-tracker.md`; `phase-5-6-plan.md` updated; **Phase 6 JSON** not started.

### Explicitly deferred

- **Bilingual `Tags` in front matter** — **deferred** (large content + URL migration). Current model: **one Spanish YAML taxonomy** + English display maps.

---

## Block B — Tier A retrofit: Days 2 and 3 (this PR)

We follow `.agents/retrofit-cover-queue.md` (**~2 pairs/day** target). After Day 1 on the Day 21 PR, tonight covers **Phase 1** and **Phase 2** ship logs.

| `Translation_Key` | WebP | Seed | ~KiB |
|-------------------|------|------|------|
| `praderas-day-2-phase-1-listing-search-pagination` | `day02-comfyui-sdxl-phase1-listing-search-pagination-hero.webp` | `02052026` | ~57 |
| `praderas-day-3-phase-2-navigation-categories-breadcrumbs-related` | `day03-comfyui-sdxl-phase2-navigation-breadcrumbs-hero.webp` | `03052026` | ~75 |

Both used **`export_cover.py`** with **`--webp --webp-delete-png --translation-key`** to patch **`Image:`** on each ES/EN pair without hand-listed paths.

### CLIP prompts (summary)

**Day 2 (listing, search, pagination):** cinematic Praderas meadow, abstract blog card grid, soft search motif, pagination dots on the horizon, no legible text.

**Day 3 (navigation, categories, breadcrumbs, related):** abstract breadcrumb trail, category hub nodes, distant related-card shapes, teal and meadow-green accents.

### Reproduce (Day 2)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day02-comfyui-sdxl-phase1-listing-search-pagination-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract blog listing grid with paired card silhouettes and a subtle magnifying glass shape suggesting search, gentle pagination dots along a calm horizon, responsive column hints without readable text, grass-green and warm neutral tones, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 02052026 \
  --prefix praderas_day02_phase1 \
  --webp --webp-delete-png \
  --translation-key praderas-day-2-phase-1-listing-search-pagination
```

### Reproduce (Day 3)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day03-comfyui-sdxl-phase2-navigation-breadcrumbs-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract breadcrumb trail and gentle hub nodes suggesting categories and site navigation, subtle linked paths between soft shapes without readable labels, related content motifs as quiet overlapping cards in the distance, teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 03052026 \
  --prefix praderas_day03_phase2 \
  --webp --webp-delete-png \
  --translation-key praderas-day-3-phase-2-navigation-categories-breadcrumbs-related
```

---

## Next focus

- **Tier A** — queue rows **4–16** (~2 pairs/day when capacity allows).  
- **Phase 6** — JSON endpoints when ready (`phase-5-6-plan.md`).  
- **Comfy checklist** — rows 8–9 (CI / `ffmpeg`) remain optional.
