---
Title: Reviving Praderas (Day 25) — Tier A complete (Days 10–16), Tuqan PR #44, and /for-ai-agents
Description: Sprint of seven WebP heroes to finish the Tier A queue, public JSON agent discovery page (Phase 6 v1.2), and Tuqan article on PHP 8 Docker-only migration.
Date: 2026-05-25 14:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 25
Lang: en
Translation_Key: praderas-day-25-tier-a-complete-tuqan-for-ai-agents-log
Image: /assets/images/day25-comfyui-sdxl-tier-a-complete-for-ai-agents-hero.webp

---

# Reviving Praderas (Day 25) — Tier A closed and two parallel tracks

Today we pushed **faster** than the usual cadence: finish **Tier A** (Days 10–16), ship **Phase 6 v1.2** with a public page for tools, and publish the **Tuqan** milestone aligned with application repo [PR #44](https://github.com/laanito/tuqan/pull/44).

## Wall clock (order of magnitude)

- **Retrofit Days 10–16 (7 pairs, Comfy + WebP):** ~**3.5–4 h** (batch script `scripts/comfyui/batch_tier_a_10_16.sh`).
- **Phase 6 v1.2 (`/for-ai-agents` ES/EN):** ~**30 min**.
- **Tuqan ES/EN article:** ~**25 min**.
- **This log + Day 25 / Tuqan covers:** same session.

---

## Block A — Tier A: rows 10–16 → `done`

| Order | `Translation_Key` | WebP (seed) | ~KiB |
|-------|-------------------|-------------|------|
| 10 | `praderas-day-10-batch-2-multilingual-hubs` | `day10-…-batch2-multilingual-hubs-hero.webp` (`10052026`) | ~102 |
| 11 | `praderas-day-11-batch-3-security-ui` | `day11-…-batch3-security-privacy-ui-hero.webp` (`11052026`) | ~114 |
| 12 | `praderas-day-12-batch-4-archive-blog-log` | `day12-…-batch4-ai-archive-blog-hero.webp` (`12052026`) | ~111 |
| 13 | `praderas-day-13-batch-5-productivity-log` | `day13-…-batch5-productivity-tools-hero.webp` (`13052026`) | ~60 |
| 14 | `praderas-day-14-batch-6-7-8-translation-finale-log` | `day14-…-batch678-translation-finale-hero.webp` (`14052026`) | ~106 |
| 15 | `praderas-day-15-ui-search-footer-log` | `day15-…-day15-search-footer-ui-hero.webp` (`15052026`) | ~56 |
| 16 | `praderas-day-16-sitemap-robots-lang-log` | `day16-…-day16-sitemap-robots-lang-hero.webp` (`16052026`) | ~69 |

**Pace decision:** instead of ~2 pairs/day, we closed **seven** so *Reviving Praderas* Days 1–16 no longer use Picsum heroes. The batch script cuts friction; `frontmatter_audit.py` still gates merge.

**Status:** `.agents/retrofit-cover-queue.md` — Tier A **complete**. Next retrofits: tiers B+ per `comfyui-cover-images.md`.

---

## Block B — Phase 6 v1.2: `/for-ai-agents`

Public pages (ES `/for-ai-agents`, EN `/en/for-ai-agents`, `Translation_Key: praderas-for-ai-agents`) summarising:

- JSON **1.1** endpoints (`/blog.json`, search, RAG fields).
- Language and canonical tag rules.
- Links to `.agents/blog-json-api.md`.

**Why HTML, not only `.agents/` in git:** production tools do not read the repository; they need a stable URL on `blog.praderas.org`.

---

## Block C — Tuqan (parallel)

New chapter in **Tuqan Modernization** (`Series_Order: 2`): PHP 8 plan, **Docker-only** dev, PHPUnit in containers, and the eight-stage roadmap after [PR #44](https://github.com/laanito/tuqan/pull/44). The post links the repo; checklists live in Tuqan, not here.

---

## What’s next

- **Tuqan Stage 1** — Docker foundation in the application repo.
- **Retrofit Tier B** — series openers and long tail (~2 pairs/day or batches).
- **Phase 6 extras** — tag filters on JSON, static pre-generation (backlog).
