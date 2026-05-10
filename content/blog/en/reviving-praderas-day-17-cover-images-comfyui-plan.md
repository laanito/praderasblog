---
Title: Reviving Praderas (Day 17) — cover images: ComfyUI plan + SDXL template
Description: Planning session: alternatives to random placeholders, local ComfyUI validation (`/prompt`, SDXL ubersimple), JSON template under `scripts/comfyui/`, and a checklist to wire hero images into Pico without abandoning Markdown-first authoring.
Date: 2026-05-10 07:45PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 17
Lang: en
Translation_Key: praderas-day-17-comfyui-cover-images-plan
---

# Reviving Praderas (Day 17) — cover images without roulette (plan + ComfyUI)

After the per-language sitemap work (Day 16), we picked up **Priority 2** in `proposed-improvements.md`: replace **random placeholder imagery** with something **predictable** or **controlled**, without forcing a different editorial flow than we already use (Markdown + front matter).

## What we decided in this session

1. **Two complementary tracks**
   - **Deterministic in-repo:** keys like `Cover:` mapped to static assets (no GPU; great when agents only emit YAML).
   - **ComfyUI (local):** generate a raster from `Title` / `Description` + a fixed house-style block, then **commit** the image and reference it (e.g. `Image:`) when publishing.

2. **Real technical validation**  
   Against `http://127.0.0.1:8188` we exercised the usual flow: `POST /prompt` → `GET /history/{prompt_id}` → `GET /view?...`. A minimal **SD 1.5 @ 512²** graph proved wiring; an **SDXL “ubersimple”** graph (`SDXL/sd_xl_base_1.0.safetensors`, **1024×768**, `euler` + `sgm_uniform`, long negative, `VAEDecode` with the **checkpoint VAE** `["1",2]`) produced a **clearly better** image — consistent with the intuition that quality was about the **full stack**, not “VAE alone.”

## Example output (same session, local smoke test)

Committed file: `assets/images/day17-comfyui-sdxl-example.png` (**1024×768**). Generic positive prompt (editorial desk / terminal mood, no readable text); meant only to **show the quality bar** of the SDXL graph above, not as the final cover for a specific post.

![Example output: ComfyUI SDXL ubersimple, local 1024×768 smoke test](/assets/images/day17-comfyui-sdxl-example.png)

### What we did not ship yet  
   No Pico plugin and no `post.twig` / `page-meta.twig` wiring in this branch: the goal was to **freeze a starting point** for future sessions, not couple the static site to a GPU service.

## Where agents should look

- **`.agents/comfyui-cover-images.md`** — prerequisites, API flow, node table, security notes (localhost vs tunnel), integration checklist (assets, front matter, Twig, script, CI, lint).
- **`scripts/comfyui/sdxl_ubersimple.api.json`** — template JSON: replace node **3** `inputs.text`, tune **seed** / `SaveImage` prefix.

We also linked from **`repo-context.md`**, **`post-template.md`** (future `Image:` field), and **`proposed-improvements.md`** so discovery does not depend on chat history.

## Next steps (when prioritized)

1. Pick a single convention (`Image:` or another) and extend `frontmatter_audit.py` if needed.  
2. Add hero + `og:image` in Twig with resolved paths and a clean fallback.  
3. Small script (Python) that reads a `.md`, fills the JSON, saves PNG under `assets/` (or a Git LFS policy).  
4. Decide whether generation stays **local-only** or runs in CI against a reachable instance with secrets.

## Wall clock (order of magnitude)

Docs + template + this log + PR: **~25–40 minutes** in one pass; the earlier ComfyUI smoke tests were a separate session.
