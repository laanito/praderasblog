# ComfyUI cover images — agent reference (local / optional automation)

**Purpose:** Give future sessions a **single starting point** for how we validated ComfyUI from this repo, which graph worked, and what remains to **productize** (automation script, CI). This is **not** required to build or run the blog.

**Status (2026-05-28):** Generation stays **on the laptop** (ComfyUI local). **No CI** for covers or Comfy. Before PR: run **`python3 scripts/frontmatter_audit.py`** locally. Still **Open:** optional **`ffmpeg`** (row 9), **VAE** experiment. **Prompts:** `.agents/image-prompt-guidelines.md`.

## Next steps (recommended order)

1. **Optional row 9 remainder** — **`ffmpeg`** or host-specific encode if we ever need formats beyond **`cwebp`** WebP.
2. **Checklist row 8** — **Closed:** audit runs **locally** only; generation does not move to CI.
3. **Archive retrofit (editorial)** — when capacity allows, add **`Image:`** to older pairs in **tier order** (§ *Retrofit plan*); **`--translation-key`** lowers friction per batch.

---

## Goals (product)

- Replace **random / placeholder** hero imagery with something **deterministic or controllable**.
- Keep the **Markdown-first** author flow: ideally the human/agent edits **front matter + body**; image generation is an **optional step** (local script, post-save hook, or CI), not a hard dependency of `index.php`.

Two complementary tracks (see also `proposed-improvements.md` § Cover retrofit):

1. **Closed vocabulary** — `Cover: productivity` style keys mapped to static assets in-repo (zero GPU).
2. **ComfyUI** — tailored raster from `Title` / `Description` / house style prompt, then commit **`Image:`** pointing at a file under `assets/` (or CDN URL). **Prompt discipline:** `.agents/image-prompt-guidelines.md` (tone + article linkage).

This document focuses on **(2)**.

---

## Preconditions

- **ComfyUI** running with the HTTP API (default **`http://127.0.0.1:8188`** when developing on the same machine as Cursor).
- **Checkpoint** used in the reference workflow: `SDXL/sd_xl_base_1.0.safetensors` (path relative to ComfyUI `models/checkpoints/`).
- **Cursor / agent shell** must be able to reach that host (same laptop → `127.0.0.1` works; cloud agents → need a tunnel + secrets; never expose an unauthenticated GPU API to the public internet).

---

## API flow (minimal)

1. **`POST /prompt`**  
   Body: `{"prompt": { ...workflow API dict... }, "client_id": "<uuid>"}`  
   Response includes `prompt_id` (and may include `node_errors` if the graph is invalid).

2. **Poll `GET /history/{prompt_id}`**  
   Wait until the entry exists and **`outputs`** contain an image-producing node (`SaveImage` or `PreviewImage`).

3. **`GET /view?filename=...&type=output&subfolder=...`**  
   Download the PNG (or decode websocket output if you prefer).

**Discovery:** `GET /system_stats` (sanity check), `GET /object_info` / `GET /object_info/CheckpointLoaderSimple` (list checkpoints, node schemas).

---

## Reference workflow: SDXL “ubersimple”

Canonical JSON for automation lives in-repo:

- **`scripts/comfyui/sdxl_ubersimple.api.json`**

Graph summary:

| Node | Type | Role |
|------|------|------|
| 1 | `CheckpointLoaderSimple` | Load `SDXL/sd_xl_base_1.0.safetensors` → model `["1",0]`, clip `["1",1]`, vae `["1",2]` |
| 2 | `EmptyLatentImage` | **1024×768** (batch 1) |
| 3 | `CLIPTextEncode` | **Positive** prompt (replace `inputs.text` per article) |
| 11 | `CLIPTextEncode` | **Negative** prompt (long quality / artifact string; keep stable) |
| 7 | `KSampler` | Typical: `euler`, `sgm_uniform`, ~30 steps, cfg ~5, `denoise` 1 |
| 5 | `VAEDecode` | `samples` ← `["7",0]`, **`vae` ← `["1",2]`** (checkpoint VAE) |
| 6 | `SaveImage` | **`images` ← `["5",0]`**, `filename_prefix` for predictable filenames (UI variant may use `PreviewImage` instead) |

**Why this looked better than SD1.5 smoke:** higher resolution, SDXL base, tuned sampler/scheduler, strong negative. If colors stay soft later, try a **standalone SDXL VAE** feeding node 5 instead of `["1",2]` (common community tweak — not in the baseline JSON until we standardize a filename).

---

## Integration checklist (blog repo)

| Step | Status |
|------|--------|
| 1. **Assets tree** — e.g. `assets/images/...`; Git LFS if binaries grow; **WebP** for Comfy covers (row 9) | **Policy:** **`.webp`** for committed SDXL covers from **Day 20**; PNG remains Comfy’s wire format until conversion |
| 2. **Front matter** — optional **`Image:`** (site-relative `/assets/...` or absolute `https://...`) | **Shipped** (`post-template.md`, `65-Multilingual.php` meta header) |
| 3. **Twig** — `post.twig` hero; `page-meta.twig` `og:image` + Twitter `summary_large_image`; **`praderas-macros.twig`** resolves **`Image:`** or **Picsum** (blog posts only for hero/social when unset) | **Shipped** |
| 4. **Listing cards** — `Image:` when set; else **Picsum** (`/seed/{page.id or url}/400/200`) | **Shipped** (`list-card-thumb.twig`, `blog.twig`, `blog-en.twig`, `tags.twig`, `search.twig`) |
| 5. **CSS** — responsive hero + `.post-body img` / `figure` | **Shipped** (`praderas-theme.css`) |
| 6. **Lint** — `Image:` path exists when set; **`Translation_Key`** maps to ≤2 posts and exactly one ES + one EN when duplicated | **Shipped** (`frontmatter_audit.py`: blog + **`content/man-in-the-loop/`** pairs) |
| 7. **Script** — POST `/prompt` + write PNG + patch front matter | **Shipped** — `export_cover.py`: PNG + **`--patch-markdown`** / **`--translation-key`** (blog + **MITL** trees) / **`--skip-comfy`** / **`--image-value`** / **`--dry-run-patch`** + **`--webp`**; **Open** optional duplicate-key CI guard |
| 8. **CI / secrets** — optional | **N/A** (local generation; audit before PR on laptop) |
| 9. **Asset weight** — WebP (`cwebp`) + optional **`ffmpeg`** / PNG optimizers | **Partial** — **`cwebp`** via **`export_cover.py --webp`** + **`webp_cover.sh`**; Days **17–21** **`.webp`** in repo; **Open** further **`ffmpeg`** tuning if needed |

---

## Image migration plan (in-repo covers)

When a ship log (or any post) should ship a **real** cover instead of a placeholder:

> **Note:** If **`Image:`** is omitted, cards and **blog article** pages still use **Lorem Picsum** with a stable seed (`list-card-thumb.twig`, `post.twig`, `page-meta.twig` via `praderas-macros.twig`); the steps below are for **opting in** to a committed or absolute hero/thumbnail.

1. **Naming** — Prefer `assets/images/day{NN}-<role>-<short-slug>.webp` aligned with **`Series_Order`** (export **`*.png`** from Comfy, then **`cwebp`** / **`--webp`** before merge). **Spanish and English** paired posts use the **same** `Image:` path.
2. **Front matter** — Set `Image: /assets/images/...` in **both** `content/blog/...` and `content/blog/en/...` before merge; hero, Open Graph / Twitter, and listing thumbnails all read that single value.
3. **Generation** — With ComfyUI running locally, run:

   `python3 scripts/comfyui/export_cover.py --output assets/images/<name>.png --positive "<CLIP positive>" --seed <int> --prefix <SaveImage prefix> [--webp --webp-delete-png] ([--patch-markdown a.md b.md] | [--translation-key <KEY>])`

   Use **`--translation-key`** when the paired ES/EN files already exist and share the same key; use **`--patch-markdown`** for explicit paths or non-blog Markdown. Do not pass both.

   Or encode an existing PNG: `bash scripts/comfyui/webp_cover.sh assets/images/<name>.png` then set **`Image:`** to **`/<same-basename>.webp`** (and remove the PNG from git when satisfied).

   Commit the **WebP** (and drop the multi‑MiB PNG from the repo once converted). When using **`--patch-markdown`** or **`--translation-key`**, review the diff before merge (same **`Image:`** path on paired ES/EN files).
4. **One asset per article (hard rule)** — Each post that sets **`Image:`** must point at its **own** file under **`assets/images/`** (one basename per **`Translation_Key`**; Spanish and English twins **share** that single file). **Do not** reuse another article’s committed cover as a placeholder shortcut (e.g. Day 20 must not borrow Day 18’s raster). The **only** exception is when the **prose** explicitly documents reuse (e.g. Day 17 smoke-test reference). Otherwise run **`export_cover.py`** again with a new **`--output`** / **`--prefix`** / seed.
5. **Weight / dimensions** — Baseline workflow is **1024×768**; raw Comfy **PNG** is often **~0.9–1.0 MiB** — too heavy for default `<img>` alongside HTML on slow links. **Shipped (Day 20):** **`cwebp`** at **quality ~82** yields **~45–65 KiB** WebP for our reference covers with acceptable PSNR. **`export_cover.py --webp [--webp-delete-png]`** or **`scripts/comfyui/webp_cover.sh`** encodes after export. Optional later: **`ffmpeg`** / **`oxipng`** if we standardize other formats or squeeze further. Revisit **Git LFS** (row 1) if the tree grows anyway.

---

## Retrofit plan: archive posts and body assets (pre–Day 17 heroes)

**Context:** Most of the archive **does not** set **`Image:`**; templates fall back to **Lorem Picsum** with a stable seed (`praderas-macros.twig`, `list-card-thumb.twig`, `post.twig`, `page-meta.twig`). That keeps weight low but looks **random** per post. **Retrofit** means deliberately adding or replacing **in-repo** rasters for **older** paired (or ES-only) posts **without** regressing page weight.

### What counts as retrofit

1. **Hero / card / social cover** — Add **`Image: /assets/images/...webp`** to posts that currently rely on Picsum (e.g. *Reviving Praderas* **Days 1–16**, tutorial series, evergreen guides). Spanish and English siblings **must share the same path** (single asset, two front matters).
2. **Body images** — Today the tree has almost no inline `![](/assets/...)` in old posts; if we add diagrams or screenshots later, they live under **`assets/`** and follow the same rule: **WebP (or SVG) in git**, not multi‑MiB raw PNGs from tools unless converted before commit.
3. **Regeneration** — If we only have an old **PNG** in history or locally, run **`webp_cover.sh`** (or **`--webp --webp-delete-png`**) and **drop the PNG from git** once the WebP is referenced everywhere.

### Prioritization (recommended order)

| Tier | Audience | Rationale |
|------|-----------|-----------|
| **A** | *Reviviendo Praderas* **Days 1–16** (paired ES/EN) | Same narrative spine as Days 17–20; visual continuity on `/blog` and social previews when we share those logs. |
| **B** | **First or flagship** post per long series (e.g. CTD-01, batch openers) | Readers land from series indexes; a deterministic cover reads more trustworthy than Picsum. |
| **C** | High‑effort **technical** posts that will gain **inline** figures | Plan assets up front so the first commit is already WebP. |
| **D** | Long tail | Opportunistic: when touching a post for translation or factual edits, optionally add **`Image:`** if we have art ready. |

Adjust tiers when analytics or search-console priorities exist; until then **A → B** is the clearest editorial win.

### Daily cadence (steady backfill)

Retrofit is easy to **defer indefinitely** unless it has a rhythm. Suggested operating mode:

- **Target:** **two** ES/EN pairs per day (**two** new WebP heroes, **four** posts) when Comfy + review time allow.
- **Floor:** **at least one** pair on any day you touch covers — keeps the habit alive.
- **Queue:** Track Tier **A** progress in **`.agents/retrofit-cover-queue.md`** (tick **`done`** after each merge).

Large ad-hoc batches are still fine; the cadence exists so **small daily progress** is always an acceptable outcome.

### Procedure (per batch)

1. **Pick the next rows** from **`.agents/retrofit-cover-queue.md`** (or another tier slice) — e.g. **1–2** pairs for a daily PR, or **3–5** pairs when batching.
2. **Produce art** — Comfy path: **`export_cover.py`** + **`--webp`** + house prompts (`.agents/image-prompt-guidelines.md`). Static path: design/export once, still **`cwebp`** before commit.
3. **Naming** — Keep **`assets/images/<slug-or-dayNN>-<role>.webp`** predictable and **unique per `Translation_Key`** (never reuse another post’s basename as a shortcut); avoid reusing another day’s file unless the article explicitly discusses reuse.
4. **Edit front matter** — Set identical **`Image:`** on **both** `content/blog/...` and `content/blog/en/...`; run **`python3 scripts/frontmatter_audit.py`**.
5. **Ship** — One PR per batch (or folded into a daily log PR if that is the house rhythm); mention the batch in the tracker changelog or the day’s ship log so we do not double‑book the same pair.

### Out of scope for now (backlog ideas)

- **Inventory script** — e.g. list Markdown files under `content/blog` missing **`Image:`** or still pointing at **`.png`**; add when batch size grows.
- **Inventory** — **`scripts/list_missing_hero_images.py`** lists posts still on Picsum (Tier B+ planning).

---

## Security notes

- Default ComfyUI API is often **unauthenticated** on the LAN. Prefer **localhost only**, VPN, or tunnel with access control.
- Prompt injection: if article body is concatenated raw into CLIP text, treat it like **untrusted input** — prefer **Title + Description + fixed style block** from a template.

---

## Committed examples (reference covers)

- **`assets/images/day17-comfyui-sdxl-example.webp`** — **1024×768** WebP (~**54 KiB**), SDXL ubersimple smoke asset; Day 17 **`Image:`** demo.
- **`assets/images/day18-comfyui-sdxl-cover-responsive.webp`** — **1024×768** WebP (~**47 KiB**); Day 18 paired log; **seed `18052026`** in series text.
- **`assets/images/day19-comfyui-sdxl-export-frontmatter.webp`** — **1024×768** WebP (~**64 KiB**); Day 19 **`--patch-markdown`** demo; **seed `19052026`**.
- **`assets/images/day20-comfyui-sdxl-webp-agents-index.webp`** — **1024×768** WebP (~**76 KiB**); Day 20 ship log (WebP weight + `.agents` hub); **seed `20052026`**.
- **`assets/images/day21-comfyui-sdxl-translation-key-patch.webp`** — **1024×768** WebP (~**91 KiB**); Day 21 **`--translation-key`** demo; **seed `21052026`**.
- **`assets/images/day01-comfyui-sdxl-technical-audit-hero.webp`** — **1024×768** WebP (~**106 KiB**); **Tier A retrofit** Day 1 pair; **seed `01052026`**.

## Changelog (in-repo)

- **2026-05-15 (Tier A retrofit):** **`day01-comfyui-sdxl-technical-audit-hero.webp`** — Day 1 ES/EN **`Image:`** via **`--translation-key`**; **`retrofit-cover-queue.md`** row 1 → **done**.
- **2026-05-15:** **Retrofit cadence** — § *Daily cadence* + link to **`.agents/retrofit-cover-queue.md`** (Tier A tick table; target **2 pairs/day**, floor **1**).
- **2026-05-14 (follow-up):** **`scripts/frontmatter_audit.py`** — **`Translation_Key`** duplicate / ES+EN pairing guard (supports **`export_cover.py --translation-key`** safely).
- **2026-05-14:** Day 21 — **`export_cover.py --translation-key`** (resolve ES+EN by **`Translation_Key`**); **`day21-comfyui-sdxl-translation-key-patch.webp`**; checklist **row 7 → Shipped**.
- **2026-05-13 (follow-up):** **Retrofit plan** § for archive heroes + future body assets (priority tiers A–D, batch procedure).
- **2026-05-13:** Day 20 — committed covers **WebP** (remove multi‑MiB PNGs); **`export_cover.py --webp`**, **`webp_cover.sh`**; checklist **row 9 → Partial**; **`.agents/README.md`** hub.
- **2026-05-12:** Day 19 — `export_cover.py` gains **`--patch-markdown`**, **`--skip-comfy`**, **`--image-value`**, **`--dry-run-patch`**; checklist row 7 updated; Day 19 PNG then **WebP** on 2026-05-13 + paired ES/EN log.
- **2026-05-12 (plan):** **Next steps** section; checklist **row 9** (asset weight / **`ffmpeg`** or PNG optimizers); migration §5 expanded; row 1 notes encode pipeline.
- **2026-05-12 (follow-up):** Blog **post** view + **og:image** use **Picsum** when `Image:` unset (`post.twig`, `page-meta.twig`, `praderas-macros.twig`); initial **`.agents/image-prompt-guidelines.md`** for Comfy positives tied to article metadata.
- **2026-05-12:** Listings/tags/search — **Picsum fallback** restored when `Image:` is unset (stable seed from `page.id`); neutral placeholder removed from default path.
- **2026-05-11:** Day 18 — `Image:` hero + social meta + responsive CSS; `frontmatter_audit` validates `Image:` paths; ComfyUI marked production-ready for **generation**; checklist updated.
- **2026-05-10:** Initial doc + `scripts/comfyui/sdxl_ubersimple.api.json` + Day 17 meta posts documenting validation and integration checklist.
- **2026-05-10 (follow-up):** Added committed example raster `assets/images/day17-comfyui-sdxl-example.png` (later **WebP** in Day 20) in Day 17 ES/EN posts.
