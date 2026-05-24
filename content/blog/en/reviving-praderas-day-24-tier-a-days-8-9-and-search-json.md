---
Title: Reviving Praderas (Day 24) — Tier A retrofit (Days 8–9) and Phase 6 v1.1 (search.json)
Description: WebP heroes for the Phase 5 multilingual announcement and batch migration plan (Days 8–9), plus search.json and agent-oriented listing fields without duplicating HTML search ranking.
Date: 2026-05-24 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 24
Lang: en
Translation_Key: praderas-day-24-tier-a-days-8-9-phase-6-search-json-log
Image: /assets/images/day24-comfyui-sdxl-tier-a-search-json-hero.webp

---

# Reviving Praderas (Day 24) — Phase 5 covers and agent-facing search

We resumed the agreed plan after a short break: keep **Tier A retrofit** at roughly two ES/EN pairs per day and **advance Phase 6** without a second server or breaking Pico’s flat-file model.

## Wall clock (order of magnitude)

- **Retrofit Days 8 and 9 (Comfy + WebP + `--translation-key`):** ~**1 h** (two SDXL passes + front matter audit).
- **Phase 6 v1.1 (`search.json` + listing fields):** ~**40 min** agent time.
- **This ES/EN log:** same session.

---

## Block A — Tier A retrofit: Days 8 and 9

Rows **8** and **9** in `.agents/retrofit-cover-queue.md` still used Picsum: the **Phase 5 multilingual** announcement and the post that formalizes the **batch migration plan**. Both are core series chapters; they deserved the same treatment as Days 1–7.

| `Translation_Key` | WebP | Seed | ~KiB | Visual metaphor |
|-------------------|------|------|------|-----------------|
| `praderas-phase-5-multilingual` | `day08-comfyui-sdxl-phase5-multilingual-hero.webp` | `08052026` | ~75 | Soft bridges between two languages (no readable text) |
| `praderas-day-9-translation-migration-batch-1` | `day09-comfyui-sdxl-translation-migration-batch1-hero.webp` | `09052026` | ~57 | Document lanes in orderly waves (batch migration) |

**Decision:** one WebP file per ES/EN pair (identical `Image:` in both `.md` files), consistent with the rest of the retrofit. We did not reuse another day’s cover: each `Translation_Key` gets its own raster under `assets/images/`.

### Reproduction (Day 8)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day08-comfyui-sdxl-phase5-multilingual-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract paired language glyphs and gentle bridge shapes suggesting bilingual content without readable text, subtle hreflang link motifs as soft curves, warm grass-green and neutral tones, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 08052026 \
  --prefix praderas_day08_export \
  --webp --webp-delete-png \
  --translation-key praderas-phase-5-multilingual
```

### Reproduction (Day 9)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day09-comfyui-sdxl-translation-migration-batch1-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract stacked document lanes and gentle batch queue shapes suggesting translation migration in orderly waves, subtle parallel paths converging without readable text, calm grass-green accents on warm neutrals, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 09052026 \
  --prefix praderas_day09_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-9-translation-migration-batch-1
```

Queue tick: rows **8–9** → `done`. Next usual target: **Days 10–11** (~two pairs on the next working day).

---

## Block B — Phase 6 v1.1: JSON search and agent fields

On Day 23 we shipped **`/blog.json`** and siblings at **schema 1.0**. Today we move to **1.1** with two items from `.agents/phase-5-6-plan.md`:

### Why a plugin, not a microservice

Same rationale as v1: **`70-BlogJson.php`** handles dedicated routes in Pico’s request cycle, reuses loaded pages, and delegates **ranking** to **`40-PicoSearch.php`** via `searchBlogPosts()`. Agents get the **same relevance order** as humans on `/search/<term>` without scraping HTML or running a second deploy.

### What shipped

| Deliverable | Detail |
|-------------|--------|
| **`GET /search.json?q=…`** | Spanish `blog/*` posts only |
| **`GET /en/search.json?q=…`** | English `blog/en/*` posts |
| **Listings and detail** | New fields: `word_count`, `estimated_tokens`, `modified_at` on **listings** too (previously `modified_at` was detail-only) |
| **Search response** | `results[]` (not `posts[]`) + `meta.query` + per-item `search_rank` |

`estimated_tokens` is a **rough budget** (`ceil(strlen(body) / 4)`) for RAG context windows — not a tokenizer from the model that consumes the JSON.

**Out of scope (v1.2):** public `/for-ai-agents` page, tag filters on JSON, JSON URLs in the sitemap.

### Quick check (local stack)

```bash
curl -sS 'http://localhost:8080/search.json?q=multilingual' | jq '.meta'
curl -sS 'http://localhost:8080/blog.json' | jq '.posts[0] | {title, word_count, estimated_tokens, modified_at}'
```

Full contract: **`.agents/blog-json-api.md`**.

---

## What’s next

- **Tier A** — rows **10–16** (~2 pairs/day).
- **Phase 6 v1.2** — `/for-ai-agents` discovery page for tools that do not read `.agents/` in git.
- **This log’s cover** — generated at PR close (`day24-comfyui-sdxl-tier-a-search-json-hero.webp`, seed `24052026`).
