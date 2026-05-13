---
Title: Reviving Praderas (Day 19) — `export_cover.py` patches `Image:` in Markdown
Description: New --patch-markdown (and --skip-comfy) on the ComfyUI export script; dedicated Day 19 PNG; checklist row 7 update in agent docs; wall-clock log.
Date: 2026-05-12 11:15AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 19
Lang: en
Translation_Key: praderas-day-19-export-cover-image-frontmatter-patch
Image: /assets/images/day19-comfyui-sdxl-export-frontmatter.webp

---

# Reviving Praderas (Day 19) — from PNG to front matter in one command

This closes the **“script + `Image:`”** gap in **`.agents/comfyui-cover-images.md`**: `scripts/comfyui/export_cover.py` can, after downloading the raster from ComfyUI, **insert or replace** the **`Image:`** key in one or more Markdown files (for example this log’s ES/EN pair). **`--skip-comfy`** optionally patches only when the PNG already exists.

## Wall clock (implementation + article + docs)

- **ComfyUI + `--patch-markdown` on this file pair:** `2026-05-12 11:03:57 CEST` → `11:04:31 CEST` (**~35 s** wall clock on the local machine; GPU queue + download + UTF-8 patch).  
- **Remainder of the session** (branch, Python, ES/EN copy, `.agents`, commit/push): not second-timed in this log; order of magnitude **tens of minutes** of agent work plus light review.

## What shipped

1. **`export_cover.py`** — new flags:
   - **`--patch-markdown`** — repo-relative paths; after export (or with **`--skip-comfy`**) **adds or replaces** **`Image:`** in the **first** YAML block (UTF-8, line-based regex).
   - **`--image-value`** — explicit site path (`/assets/...`); if omitted, **derived** from **`--output`** against the repo root (`config/config.yml` + `content/`).
   - **`--skip-comfy`** — skips the API; requires an existing **`--output`** file and **`--patch-markdown`** (front-matter-only flow).
   - **`--dry-run-patch`** — prints patch actions without writing.
2. **Day 19 PNG** — **`assets/images/day19-comfyui-sdxl-export-frontmatter.webp`** (same SDXL ubersimple graph; **seed `19052026`**; positive CLIP below).
3. **`.agents/comfyui-cover-images.md`** — checklist **row 7**: export + **`--patch-markdown`** shipped; **open** optional **`Translation_Key`** path resolver. Plan **“next steps”** order: (1) that polish, (2) **row 9** (weight / **`ffmpeg`**), (3) **CI** (row 8).

## Positive CLIP prompt (this entry’s cover)

> Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft meadow at dusk, abstract floating shapes suggesting Markdown YAML front matter and automation, subtle terminal green accents, faint branch or commit metaphors, no readable text, no logos, calm professional atmosphere, high detail, tasteful color grading

## How to reproduce the patch (example)

After the PNG exists (or using **`--skip-comfy`**):

```bash
python3 scripts/comfyui/export_cover.py --skip-comfy \
  --output assets/images/day19-comfyui-sdxl-export-frontmatter.webp \
  --patch-markdown \
    content/blog/reviviendo-praderas-dia-19-export-cover-parche-image-frontmatter.md \
    content/blog/en/reviving-praderas-day-19-export-cover-image-frontmatter.md
```

On the **same** invocation as Comfy, append **`--patch-markdown …`** to your existing **`--positive` / `--seed` / `--prefix`** command.

## Still open (when prioritized)

- Optional resolver for **ES/EN pairs** via **`Translation_Key`** without passing two paths.  
- **Row 9** in the plan: **`ffmpeg`** / WebP or PNG optimizers before commit.
