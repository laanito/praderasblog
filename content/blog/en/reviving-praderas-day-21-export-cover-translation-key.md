---
Title: Reviving Praderas (Day 21) — `export_cover.py` and the `Translation_Key` flag
Description: New `--translation-key` flag patches `Image:` on the ES/EN pair without listing two paths; mutually exclusive with `--patch-markdown`; dedicated Comfy cover (seed 21052026).
Date: 2026-05-14 05:30PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 21
Lang: en
Translation_Key: praderas-day-21-export-cover-translation-key-flag
Image: /assets/images/day21-comfyui-sdxl-translation-key-patch.webp

---

# Reviving Praderas (Day 21) — from two paths to one key

**Day 19** shipped **`--patch-markdown`**: after exporting the cover, the script **inserts or replaces** the **`Image:`** line in one or more Markdown files. In practice, ES/EN pairs meant **passing two paths** every time. Today we narrow checklist **row 7** with **`--translation-key`**: the script **walks** `content/blog/*.md` and `content/blog/en/*.md`, reads front matter, and finds **exactly two** posts that share the same **`Translation_Key`**, then applies the same **`Image:`** we already derive from **`--output`** (or **`--image-value`**).

## Wall clock

- **Python change + docs + ship log:** on the order of **tens of minutes** of agent time plus light review (not second-timed here).  
- **ComfyUI + `cwebp` + key-based patch:** similar magnitude to **Day 19** when the GPU is idle.

## What changed

1. **`export_cover.py`** — **`--translation-key <KEY>`** (mutually exclusive with **`--patch-markdown`**). Deterministic order: Spanish under **`content/blog/`** first, then English under **`content/blog/en/`**. If **0**, **1**, or **more than 2** matches, the command **fails** and prints the hits (avoids ambiguous patches).
2. **Today’s cover** — dedicated raster **`day21-comfyui-sdxl-translation-key-patch.webp`** (same SDXL ubersimple graph; **seed `21052026`**; CLIP prompt below; ~**91 KiB** on this export). Same rule since **Day 20**: **one file per `Translation_Key`**, no borrowing another day’s art.
3. **`.agents/comfyui-cover-images.md`** — checklist **row 7**: **key-based patch** documented; migration + backlog notes aligned.

## Positive CLIP prompt (Day 21 cover)

> Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract twin document panels suggesting paired Spanish and English markdown files linked by a shared key motif, subtle mirrored shapes and soft link lines without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading

## How to reproduce (export + WebP + patch by key)

With ComfyUI running and both posts already on disk sharing the same **`Translation_Key`**:

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day21-comfyui-sdxl-translation-key-patch.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract twin document panels suggesting paired Spanish and English markdown files linked by a shared key motif, subtle mirrored shapes and soft link lines without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 21052026 \
  --prefix praderas_day21_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-21-export-cover-translation-key-flag
```

**Patch only** (no Comfy) when the **WebP** already exists:

```bash
python3 scripts/comfyui/export_cover.py --skip-comfy \
  --output assets/images/day21-comfyui-sdxl-translation-key-patch.webp \
  --translation-key praderas-day-21-export-cover-translation-key-flag
```

## Next focus

- Remaining **row 9** (`ffmpeg` or other encoders) if needed; **CI** (row 8) when generation leaves the laptop.  
- **Retrofit** covers for the archive (`comfyui-cover-images.md` § *Retrofit plan*) — key-based patching lowers friction across many pairs.
