---
Title: Reviving Praderas (Day 18) — `Image:` hero, Open Graph, responsive layout (Picsum fallback)
Description: Optional cover from front matter, social meta, listing cards with `Image:` thumbnail or stable Picsum, CSS for in-body images and hero; ComfyUI production-ready for generation; wall-clock log.
Date: 2026-05-11 10:05AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 18
Lang: en
Translation_Key: praderas-day-18-cover-image-hero-social-responsive
Image: /assets/images/day18-comfyui-sdxl-cover-responsive.webp
---

# Reviving Praderas (Day 18) — serious cover handling on the site

This closes the next slice from **Day 17 / `.agents/comfyui-cover-images.md`**: the site now **understands `Image:`**, renders a **stable hero** at narrow widths, emits **`og:image`** (and Twitter **`summary_large_image`** when applicable), and on **listings, tags, and search** uses the **`Image:`** thumbnail when set; otherwise **Lorem Picsum** with a **stable seed** (from `page.id` / URL) so cards are not visually “broken”. **ComfyUI** is documented as **production-ready** for **asset generation**; only automation script / CI remain open.

## Wall clock (implementation + article + docs)

- **Start:** `2026-05-11 09:56:08 CEST`  
- **End:** `2026-05-11 09:59:35 CEST`  

Measured window: **~3m30s** of calendar time on this session (branch from `main`, Twig/CSS/PHP changes, front matter audit update, `.agents` refresh, this log, commit and push). Excludes human design polish and production deploy verification.

## What shipped

1. **`Image:` front matter** — registered in `65-Multilingual.php` (`onMetaHeaders`) so Pico exposes `meta.image` in Twig.
2. **`post.twig`** — optional hero (`pradera-hero-figure` / `pradera-hero-img`): from **`Image:`** when set; otherwise **Picsum** 1200×630 for `blog/…` articles with the same stable seed as cards (`praderas-macros.twig`).
3. **`page-meta.twig`** — absolute URL for `og:image` and `twitter:image`; `twitter:card` becomes `summary_large_image` when an image exists.
4. **Listings** — `list-card-thumb.twig` included from `blog.twig`, `blog-en.twig`, `tags.twig`, `search.twig`: thumbnail from **`Image:`** when set; otherwise **Picsum** at `https://picsum.photos/seed/…/400/200` with a stable seed (after merge, a neutral-only placeholder proved worse for undecorated posts and was reverted).
5. **`praderas-theme.css`** — `max-width: 100%`, `object-fit: contain`, `max-height` with `vh` on the hero; rules for `.post-body img` / `figure` so Markdown images do not blow the column on small screens.
6. **`scripts/frontmatter_audit.py`** — also walks `content/blog/en/*.md` and verifies on-disk paths for non-HTTP `Image:` values.
7. **Day 18** — a **dedicated WebP** cover from ComfyUI (see below); **Day 17** still uses the smoke-test file `day17-comfyui-sdxl-example.webp` as a historical reference in that log pair.

## Cover image for this entry (ComfyUI / SDXL)

This log ships **`Image: /assets/images/day18-comfyui-sdxl-cover-responsive.webp`**: hero, social preview, and listing thumbnail all share that path (no duplicate `![](...)` in the body).

- **Graph:** `scripts/comfyui/sdxl_ubersimple.api.json` (same baseline as Day 17).
- **Reproducible export:** `python3 scripts/comfyui/export_cover.py` with `--output` targeting that path, **`--seed 18052026`**, and a unique SaveImage `--prefix` per run (e.g. `praderas_day18_export`).
- **Positive CLIP prompt:**

> Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft green meadow at golden hour, subtle abstract UI wireframes and gentle grid lines suggesting responsive web layout and Open Graph cards, calm modern typography shapes, no readable text, no logos, peaceful professional atmosphere, high detail, tasteful color grading

The in-repo **image migration plan** (naming under `assets/images/`, paired ES/EN sharing one `Image:`, avoid silently reusing another day’s asset) lives in **`.agents/comfyui-cover-images.md`**.

## ComfyUI “production ready”

In agent docs: the **instance + SDXL graph** are ready to **produce** images locally; the blog **never** calls Comfy at runtime — it only serves committed files or absolute URLs your pipeline writes.

## Still open (next PR when prioritized)

- Finish the checklist gap: automatic front-matter patch (`Image:`) after export; **`scripts/comfyui/export_cover.py`** now also supports **`--patch-markdown`** and **`--webp`**.  
- Optional CI and LFS policy if binary weight grows.
