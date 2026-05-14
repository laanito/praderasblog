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

At **two pairs per working day**, Tier A (**16** pairs) clears in roughly **8** active days of cover work (spread across calendar weeks as needed).

---

## Tier A — queue (Series_Order 1 → 16)

| Series_Order | `Translation_Key` | Hero `Image:` | Notes |
|--------------|-------------------|---------------|-------|
| 1 | `praderas-day-1-technical-audit` | todo | |
| 2 | `praderas-day-2-phase-1-listing-search-pagination` | todo | |
| 3 | `praderas-day-3-phase-2-navigation-categories-breadcrumbs-related` | todo | |
| 4 | `praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint` | todo | |
| 5 | `praderas-day-5-visual-polish` | todo | |
| 6 | `praderas-day-6-series-and-collections` | todo | |
| 7 | `praderas-day-7-phase-4-seo-discoverability` | todo | |
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

- **2026-05-15:** Initial queue + **daily cadence** (target 2 pairs / floor 1 / ceiling discretionary).
