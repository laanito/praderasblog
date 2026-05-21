# Retrofit cover queue — Tier A (*Reviviendo Praderas* Days 1–16)

**Purpose:** Turn the **retrofit** plan in `comfyui-cover-images.md` § *Retrofit plan* into a **concrete, tickable queue** so cover backfill does not stall. This file is the **editorial progress bar** for hero **`Image:`** on older paired ship logs (Picsum today → committed WebP tomorrow).

**How to use:** After merging a pair’s WebP + front matter, set **Hero `Image:`** to **`done`** and optionally note the **`assets/images/...webp`** basename in **Notes**. Prefer ascending **`Series_Order`** so the series reads consistently on `/blog`.

---

## Daily cadence (suggested)

| Pace | What ships | When to use it |
|------|------------|----------------|
| **Target** | **Two** ES/EN pairs = **two** new WebP files + **four** Markdown files touched | Normal “keep the leg moving” day (~two Comfy runs + `cwebp`, or batch `webp` if PNGs already exist). |
| **Floor** | **One** pair = **one** WebP + **two** `.md` files | Busy day — still **unblocks** the queue so the work never sits at zero. |
| **Ceiling** | More than two pairs | Only if review bandwidth and Comfy time are clearly available; avoid burning out the habit. |

**Mechanics:** `export_cover.py` with **`--translation-key`** + **`--webp --webp-delete-png`** keeps commands short; run **`python3 scripts/frontmatter_audit.py`** before merge. Prompts: `image-prompt-guidelines.md`.

**Docs-only PRs:** ticking queue rows + updating `.agents/*` does **not** require a new *Reviviendo Praderas* article — see **`editorial-guidelines.md`**.

At **two pairs per working day**, Tier A (**16** pairs) clears in roughly **8** active days of cover work (spread across calendar weeks as needed).

---

## Tier A — queue (Series_Order 1 → 16)

| Series_Order | `Translation_Key` | Hero `Image:` | Notes |
|--------------|-------------------|---------------|-------|
| 1 | `praderas-day-1-technical-audit` | done | `day01-comfyui-sdxl-technical-audit-hero.webp` (~106 KiB); seed `01052026`; Tier A retrofit on open Day 21 PR leg. |
| 2 | `praderas-day-2-phase-1-listing-search-pagination` | done | `day02-comfyui-sdxl-phase1-listing-search-pagination-hero.webp` (~57 KiB); seed `02052026`; Day 22 PR. |
| 3 | `praderas-day-3-phase-2-navigation-categories-breadcrumbs-related` | done | `day03-comfyui-sdxl-phase2-navigation-breadcrumbs-hero.webp` (~75 KiB); seed `03052026`; Day 22 PR. |
| 4 | `praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint` | done | `day04-comfyui-sdxl-phase3-metadata-taxonomy-frontmatter-hero.webp` (~112 KiB); seed `04052026`; Day 23 PR. |
| 5 | `praderas-day-5-visual-polish` | done | `day05-comfyui-sdxl-visual-polish-readability-hero.webp` (~55 KiB); seed `05052026`; Day 23 PR. |
| 6 | `praderas-day-6-series-and-collections` | done | `day06-comfyui-sdxl-series-collections-hero.webp` (~76 KiB); seed `06052026`; docs PR 2026-05-20. |
| 7 | `praderas-day-7-phase-4-seo-discoverability` | done | `day07-comfyui-sdxl-seo-discoverability-hero.webp` (~68 KiB); seed `07052026`; docs PR 2026-05-20. |
| 8 | `praderas-phase-5-multilingual` | todo | Day 8 announcement pair (key name omits `day-8`). |
| 9 | `praderas-day-9-translation-migration-batch-1` | todo | |
| 10 | `praderas-day-10-batch-2-multilingual-hubs` | todo | |
| 11 | `praderas-day-11-batch-3-security-ui` | todo | |
| 12 | `praderas-day-12-batch-4-archive-blog-log` | todo | |
| 13 | `praderas-day-13-batch-5-productivity-log` | todo | |
| 14 | `praderas-day-14-batch-6-7-8-translation-finale-log` | todo | |
| 15 | `praderas-day-15-ui-search-footer-log` | todo | |
| 16 | `praderas-day-16-sitemap-robots-lang-log` | todo | |

**Status values:** `todo` · `done` (and optionally `n/a` if a row is intentionally skipped — document why in **Notes**).

---

## Changelog (this file)

- **2026-05-20 (follow-up):** **Rows 6–7 done** — series/collections + SEO/discoverability WebP + `Image:`; no new ship log article (docs-only PR).
- **2026-05-20:** **Rows 4–5 done** — Tier A retrofit Days 4–5 WebP + `Image:`; Day 23 ship log + Phase 6 JSON slice 1.
- **2026-05-19:** **Rows 2–3 done** — Tier A retrofit Days 2–3 WebP + `Image:` (`praderas-day-2-*`, `praderas-day-3-*`); Day 22 ship log documents Phase 5 UI closure + this batch.
- **2026-05-15 (follow-up):** **Row 1 done** — Day 1 ES/EN hero WebP + `Image:` (`praderas-day-1-technical-audit`).
- **2026-05-15:** Initial queue + **daily cadence** (target 2 pairs / floor 1 / ceiling discretionary).
