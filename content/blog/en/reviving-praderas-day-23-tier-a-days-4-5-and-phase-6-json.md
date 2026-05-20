---
Title: Reviving Praderas (Day 23) — Tier A retrofit (Days 4–5) and Phase 6 JSON kickoff
Description: Two ES/EN pairs with Comfy heroes (Phase 3 metadata and visual polish) plus plugin 70-BlogJson for /blog.json and siblings, documented in .agents/blog-json-api.md.
Date: 2026-05-20 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 23
Lang: en
Translation_Key: praderas-day-23-tier-a-days-4-5-phase-6-blog-json-log
Image: /assets/images/day23-comfyui-sdxl-tier-a-json-phase6-hero.webp

---

# Reviving Praderas (Day 23) — covers through Phase 3, JSON without HTML

Today we ran the agreed **dual track**: keep **Tier A retrofit** rhythm (~two ES/EN pairs per day) and **start Phase 6** with a stable JSON surface for agents and tooling.

## Wall clock (order of magnitude)

- **Retrofit Days 4 and 5 (Comfy + WebP + `--translation-key`):** ~**1 h** (two SDXL runs + audit).  
- **Phase 6 slice 1 (`70-BlogJson.php` + docs):** ~**45 min** agent time.  
- **This ES/EN log:** same session.

---

## Block A — Tier A retrofit: Days 4 and 5

| `Translation_Key` | WebP | Seed | ~KiB |
|-------------------|------|------|------|
| `praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint` | `day04-comfyui-sdxl-phase3-metadata-taxonomy-frontmatter-hero.webp` | `04052026` | ~112 |
| `praderas-day-5-visual-polish` | `day05-comfyui-sdxl-visual-polish-readability-hero.webp` | `05052026` | ~55 |

Visual metaphors: **Day 4** — ordered tags and document silhouettes (taxonomy / lint); **Day 5** — typographic rhythm and airy cards (visual polish without new frameworks).

### Reproduction command (Day 4)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day04-comfyui-sdxl-phase3-metadata-taxonomy-frontmatter-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract organized tag chips and gentle taxonomy tree shapes suggesting metadata consistency and front matter lint, subtle checklist and document silhouettes without readable text, calm grid of soft data cards, grass-green and warm neutral tones, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 04052026 \
  --prefix praderas_day04_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint
```

### Reproduction command (Day 5)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day05-comfyui-sdxl-visual-polish-readability-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract typography rhythm and generous vertical whitespace suggesting calm long-form reading, subtle design token swatches and refined card elevations without readable text, gentle grass-green accent on warm neutrals, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 05052026 \
  --prefix praderas_day05_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-5-visual-polish
```

Tick **`.agents/retrofit-cover-queue.md`** rows **4–5** → `done`. Next Tier A target: **Days 6–7**.

---

## Block B — Phase 6: JSON v1 (listings + article)

New plugin **`plugins/70-BlogJson.php`** and contract **`.agents/blog-json-api.md`**.

| Endpoint | Content |
|----------|---------|
| `GET /blog.json` | **Spanish** post listing (`blog/*`, excluding `blog/en/*`) |
| `GET /blog/en.json` | **English** post listing |
| `GET /blog/{slug}.json` | ES article + markdown body |
| `GET /blog/en/{slug}.json` | EN article + markdown body |

Each item exposes `translation_key`, `alternate_url` when paired, **canonical Spanish** `tags` (same as YAML), estimated `reading_time_minutes`, and `Cache-Control: public, max-age=3600`.

**Not in this slice:** `search.json`, tag filters, static pre-generation, JSON URLs in the sitemap.

### Quick test (local stack)

```bash
curl -sS 'http://localhost:8080/blog.json' | head
curl -sS 'http://localhost:8080/blog/reviviendo-praderas-dia-4-fase-3-metadatos-taxonomy-y-lint-de-front-matter.json' | jq '.post.title'
```

---

## Next focus

- **Tier A** — queue rows **6–16** (~two pairs per day).  
- **Phase 6 v1.1** — `search.json` or pre-generation if a specific integrator needs it.  
- **This log’s hero** — `day23-comfyui-sdxl-tier-a-json-phase6-hero.webp` (seed `23052026`).
