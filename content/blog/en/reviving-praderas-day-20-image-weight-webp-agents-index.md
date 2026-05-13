---
Title: Reviving Praderas (Day 20) — cover weight as WebP and an `.agents` index
Description: Comfy covers shipped as WebP (~50 KiB vs ~1 MiB PNG), `export_cover.py --webp`, `webp_cover.sh`, checklist row 9; new `.agents/README.md` as consolidated documentation hub.
Date: 2026-05-13 11:45AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 20
Lang: en
Translation_Key: praderas-day-20-image-webp-agents-readme-consolidation
Image: /assets/images/day20-comfyui-sdxl-webp-agents-index.webp
---

# Reviving Praderas (Day 20) — keep the hero from blocking the HTML

The **1024×768** SDXL **PNG** files we committed on **Days 17–19** were about **0.9–1.0 MiB** each: the browser fought the document and the **hero** felt late or “stuck”. Today we prioritized checklist **row 9** (weight) with **WebP** via **`cwebp`**, and added a **single entry point** for the **`.agents/`** folder.

## Wall clock

- **`cwebp` for three covers + path rewrites:** **seconds** on a local machine.  
- **Full session** (branch, script, ES/EN posts, `.agents`, commit/push): **tens of minutes** of agent work plus light review (not second-timed in this log).

## What changed

1. **WebP in repo** — `assets/images/day17-comfyui-sdxl-example.webp`, `day18-comfyui-sdxl-cover-responsive.webp`, `day19-comfyui-sdxl-export-frontmatter.webp`, **`day20-comfyui-sdxl-webp-agents-index.webp`** (~**45–76 KiB** per cover at **quality 82**). Large **PNGs** are **removed** from git so we do not ship dead weight.
2. **`export_cover.py`** — **`--webp`** and **`--webp-delete-png`**: after Comfy’s PNG download, optionally emit **`.webp`** and delete the PNG.
3. **`scripts/comfyui/webp_cover.sh`** — thin **`cwebp`** wrapper for batch use (`brew install webp` if needed).
4. **Front matter + series copy** — each Comfy-cover day has its **own** **`.webp`** (Days **17–20**); the ES/EN pair shares the same **`Image:`** path; `scripts/frontmatter_audit.py` still checks on-disk paths.
5. **`.agents/README.md`** — **hub** with a table of files and a suggested reading order; “consolidation” means **index + links**, not merging everything into one mega-doc.
6. **Cross-linked docs** — `comfyui-cover-images.md` (2026-05-13 status, row **9 → Partial**), `repo-context.md`, `post-template.md`, `image-prompt-guidelines.md`, `translation-migration-tracker.md`, `proposed-improvements.md`, `multilingual-ui-backlog.md`.
7. **This log’s cover (Day 20)** — **`Image: /assets/images/day20-comfyui-sdxl-webp-agents-index.webp`** (~**76 KiB**); a **dedicated** raster (Day 18’s file was mistakenly reused at first ship). **`.agents` rule:** **one file per article** / `Translation_Key`, unless the prose documents intentional reuse.

## Positive CLIP prompt (Day 20 cover)

> Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract feather-light layers and stacked translucent cards suggesting compressed image weight and a documentation index hub, subtle floating grid shapes like folded README pages without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading

- **`--seed`:** `20052026`  
- **Comfy `--prefix`:** `praderas_day20_export`

## How to reproduce this cover (example)

With ComfyUI running (default **`http://127.0.0.1:8188`**):

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day20-comfyui-sdxl-webp-agents-index.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract feather-light layers and stacked translucent cards suggesting compressed image weight and a documentation index hub, subtle floating grid shapes like folded README pages without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 20052026 \
  --prefix praderas_day20_export \
  --webp --webp-delete-png \
  --patch-markdown \
    content/blog/reviviendo-praderas-dia-20-peso-imagen-webp-indice-agents.md \
    content/blog/en/reviving-praderas-day-20-image-weight-webp-agents-index.md
```

## Useful commands

```bash
# After a PNG already exists:
bash scripts/comfyui/webp_cover.sh assets/images/my-cover.png

# Same run as Comfy:
python3 scripts/comfyui/export_cover.py --output assets/images/my.png ... \
  --webp --webp-delete-png --patch-markdown content/blog/foo.md
```

## Next focus

- Optional **row 7** polish (**`Translation_Key`** → paths for `--patch-markdown`).  
- Remaining **row 9** if we need **`ffmpeg`** or another codec; **CI** (row 8) when generation leaves the laptop.
