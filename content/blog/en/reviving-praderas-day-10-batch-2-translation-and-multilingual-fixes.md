---
Title: Reviving Praderas (Day 10) — batch 2, EN hubs, and routing fixes (daily log)
Description: Full Decoupled time tracking translation batch, /en/series and /en/categorias hub pages, plugin and template work, and a wall-clock vs localization time comparison (~35 min).
Date: 2026-05-01 12:30PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 10
Lang: en
Translation_Key: praderas-day-10-batch-2-multilingual-hubs
---

# Reviving Praderas (Day 10) — what shipped today and why

This is a **daily log** entry: not a long tutorial, but **transparency** on a concrete block of work (code + content + product judgment) and the clock numbers we committed to on Day 9.

## Published changes (executive summary)

1. **Translation batch 2** — All **13 chapters** of *Control de Tiempo Desacoplado* now have **ES/EN pairs** (`Translation_Key` `praderas-ctd-01` … `13`). The English series display name is **Decoupled time tracking**, with the same `Series_Slug` so series URLs stay stable.
2. **Missing English hubs** — Added **`/en/series`** and **`/en/categorias`** paired with the Spanish pages (navigation `Translation_Key`s). The EN navbar no longer dumped readers onto Spanish-only hubs: that was a **real product hole**, not polish.
3. **Series routes per language** — The series plugin understands **`/en/series/<slug>/`**, builds the collection grid for the active page language, and makes the sidebar “full series index” link use the **correct prefix** (`/en/series/...` vs `/series/...`).
4. **Honest EN category counts** — On **`/en/categorias`**, counts include **`blog/en/`** posts only. Tag **labels** remain the shared taxonomy (explicit until we add EN display names).
5. **Templates** — `series.twig` and `categories.twig` branch copy and breadcrumbs by language; **`nav.twig`** targets `en/series` and `en/categorias` on the EN branch.
6. **Small fix** — Repaired a broken `</div` inside a code block in the Spanish React project-management article (repo hygiene).

## Reasoning (why one work block)

- **Whole series in one batch** — Same rule as Day 9: avoid half-translated chapter paths for readers who follow order.
- **Hubs + routing in the same PR** — Without EN series routes, the translated arc was still **broken in navigation**: you could read English posts and then hit **Series** / **Categories** in Spanish. Shipping “text only” and leaving that UX gap would have been worse than finishing the cut.
- **Language-scoped category counts** — Showing whole-archive counts on a page labeled English would be **misleading**; the neighbors/tag-count path now respects the page language.

## Time comparison (~35 min human wall clock)

For **this scope** (13 post pairs + two hub pages + plugins + templates + tracker + daily log + PR flow), **human wall-clock time** was on the order of **~35 minutes** (review, light manual checks, PR open/merge depending on your workflow).

As an **order-of-magnitude** comparison with a **localization specialist** plus classic editorial QA for the same surface area (prose + metadata + internal link sanity + bilingual route checks), a plausible band is still about **~10 to ~18 hours**, depending on review depth and whether a TM already exists.

Again: the point is not “AI beats humans,” but **what compresses calendar time** when the repo already encodes conventions (`Translation_Key`, batches, glossary) and the PR scope stays tight.

## Honest limitations

- **`/tags` and the sidebar search flow** remain **mostly Spanish-first**; the EN categories page notes that. A proper pass is another PR.
- **`hreflang` on series detail** may still need finesse depending on how Pico resolves one template across two URL shapes; the big win was **language-coherent navigation and listings**.

## What comes next

**Batch 3** — security, privacy, and geolocation cluster — same batching and glossary discipline.
