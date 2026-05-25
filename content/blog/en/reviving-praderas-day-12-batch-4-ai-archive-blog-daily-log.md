---
Title: "Reviving Praderas (Day 12) — batch 4 (AI), EN archive, /en/blog language switcher, and the missing daily log"
Description: Wrap-up for work already merged to main—six AI post pairs, /en/archivo, paired blog listings for the language switcher—plus this PR that adds the ES/EN log pair that should have shipped with the same batch.
Date: 2026-05-03 12:17PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 12
Lang: en
Translation_Key: praderas-day-12-batch-4-archive-blog-log
Image: /assets/images/day12-comfyui-sdxl-batch4-ai-archive-blog-hero.webp

---

# Reviving Praderas (Day 12) — transparency on the merge and this log pair

This post documents **changes already on `main`** from the earlier merge: **translation batch 4 (AI)**, an **English date archive**, and **paired blog listing pages** so the **language switcher** appears on `/en/blog`. What that merge did **not** include was this **daily log pair** (ES + EN); we add it now in a **small PR** from up-to-date `main`, matching the project’s “one transparent day note” convention.

## Wall clock (this log-only PR)

- **Session start (agreed reference stamp):** `2026-05-03 11:56:12 CEST`
- **Immediately before commit + push of this PR (reference):** `2026-05-03 12:16:46 CEST`

The useful comparison is **~20 minutes of wall clock** between those stamps (steering, ES/EN log, tracker, branch/PR, and this timestamp correction) versus the **manual** order-of-magnitude in the table below. The **heavy** translation and template work shipped in the **previous** merge; this PR closes the **narrative gap**.

## What the merged PR already contained (executive summary)

1. **Batch 4 (AI core)** — Six ES/EN pairs (`praderas-b4-ai-games-evolution`, `…-early-disease-detection`, `…-medicine`, `…-society-impact`, `…-entertainment`, `…-neural-nets`): games, early detection, medicine, society, entertainment, neural networks.
2. **`Translation_Key: praderas-nav-blog-listing`** on `content/blog.md` and `content/en/blog.md` — enables **`alternate_language_page`** so `lang-switcher.twig` can show **Español** from `/en/blog`.
3. **`/en/archivo`** paired with `archivo.md` (`praderas-nav-archive`) and a bilingual **`archive.twig`** (month labels, breadcrumbs, language-scoped post lists).
4. **`sidebar.twig`** — archive widget links to **`/en/archivo`** when `content_lang` is English.
5. **`.agents`** — tracker and backlog already reflected batch 4 and EN routes.

## Why a separate PR for the log

- **Do not rewrite merged history** — Code and AI posts were already reviewed; the clean fix is to **add** the public explanation layer.
- **Honesty with readers** — the *Reviving Praderas* series assumes a **dated engineering note** with rationale and time framing; skipping it left a visible gap after Day 11.

## Senior “by hand” order-of-magnitude (this log PR only)

| Workstream | Indicative range |
|------------|------------------|
| ES/EN log pair + `Translation_Key` + `Series_Order` | **1–2 h** |
| Tracker / changelog edits | **20–40 min** |
| Branch from `main`, PR hygiene | **20–40 min** |
| **Total** | **~2–3.5 h** |

With an assistant plus a clear owner brief, the elapsed wall time between the two timestamps above landed around **twenty minutes** (including correcting the published clocks and pushing); the senior band still describes **editorial** work without an assistant—not the earlier merged code batch.
