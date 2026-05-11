# ComfyUI cover images — agent reference (local / optional automation)

**Purpose:** Give future sessions a **single starting point** for how we validated ComfyUI from this repo, which graph worked, and what remains to **productize** (automation script, CI). This is **not** required to build or run the blog.

**Status (2026-05-12):** **ComfyUI** stack (SDXL ubersimple, local API) is treated as **production-ready** on the maintainer side for **generating** assets. The **blog** consumes optional **`Image:`** front matter (hero + `og:image` / Twitter large card). **Listings / tags / search** use **`Image:`** when set, otherwise **Lorem Picsum** with a **stable seed** derived from `page.id` (fallback `page.url`) (`list-card-thumb.twig`). **`scripts/comfyui/export_cover.py`** downloads the rendered PNG into `assets/images/` for you to **git commit**; optional work remains: **auto `Image:` patch**, **CI**, optional **standalone SDXL VAE** experiment.

---

## Goals (product)

- Replace **random / placeholder** hero imagery with something **deterministic or controllable**.
- Keep the **Markdown-first** author flow: ideally the human/agent edits **front matter + body**; image generation is an **optional step** (local script, post-save hook, or CI), not a hard dependency of `index.php`.

Two complementary tracks (see also `proposed-improvements.md` Priority 2):

1. **Closed vocabulary** — `Cover: productivity` style keys mapped to static assets in-repo (zero GPU).
2. **ComfyUI** — tailored raster from `Title` / `Description` / house style prompt, then commit **`Image:`** pointing at a file under `assets/` (or CDN URL).

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
| 1. **Assets tree** — e.g. `assets/images/...`; Git LFS policy if binaries grow | **Policy:** small PNGs in git OK for now |
| 2. **Front matter** — optional **`Image:`** (site-relative `/assets/...` or absolute `https://...`) | **Shipped** (`post-template.md`, `65-Multilingual.php` meta header) |
| 3. **Twig** — `post.twig` hero; `page-meta.twig` `og:image` + Twitter `summary_large_image` | **Shipped** |
| 4. **Listing cards** — `Image:` when set; else **Picsum** (`/seed/{page.id or url}/400/200`) | **Shipped** (`list-card-thumb.twig`, `blog.twig`, `blog-en.twig`, `tags.twig`, `search.twig`) |
| 5. **CSS** — responsive hero + `.post-body img` / `figure` | **Shipped** (`praderas-theme.css`) |
| 6. **Lint** — `Image:` path exists when set | **Shipped** (`scripts/frontmatter_audit.py` scans `content/blog` + `content/blog/en`) |
| 7. **Script** — POST `/prompt` + write PNG + patch front matter | **Partial** — `scripts/comfyui/export_cover.py` saves PNG from the in-repo workflow; **auto `Image:` patch** still **Open** |
| 8. **CI / secrets** — optional | **Open** |

---

## Image migration plan (in-repo PNGs)

When a ship log (or any post) should ship a **real** cover instead of a placeholder:

> **Note:** If **`Image:`** is omitted, cards still use **Lorem Picsum** with a stable seed (`list-card-thumb.twig`); the steps below are for **opting in** to a committed or absolute hero/thumbnail.

1. **Naming** — Prefer `assets/images/day{NN}-<role>-<short-slug>.png` aligned with **`Series_Order`** (example: `day18-comfyui-sdxl-cover-responsive.png`). **Spanish and English** paired posts use the **same** `Image:` path.
2. **Front matter** — Set `Image: /assets/images/...` in **both** `content/blog/...` and `content/blog/en/...` before merge; hero, Open Graph / Twitter, and listing thumbnails all read that single value.
3. **Generation** — With ComfyUI running locally, run:

   `python3 scripts/comfyui/export_cover.py --output assets/images/<name>.png --positive "<CLIP positive>" --seed <int> --prefix <SaveImage prefix>`

   Commit the PNG with the article. In the series log, note **seed** and a one-line **intent** for the prompt (CLIP is fine in English) so a future run can reproduce or iterate.
4. **Avoid silent reuse** — Do not point a new day’s `Image:` at an older entry’s PNG unless the article explicitly discusses that reuse (e.g. “same smoke test asset as Day 17”). Otherwise export a **dedicated** file for that day.
5. **Weight** — Baseline workflow is **1024×768**; keep binaries reasonable; if the tree grows, revisit **Git LFS** (see checklist row 1).

---

## Security notes

- Default ComfyUI API is often **unauthenticated** on the LAN. Prefer **localhost only**, VPN, or tunnel with access control.
- Prompt injection: if article body is concatenated raw into CLIP text, treat it like **untrusted input** — prefer **Title + Description + fixed style block** from a template.

---

## Committed examples (reference PNGs)

- **`assets/images/day17-comfyui-sdxl-example.png`** — **1024×768** PNG from the validated SDXL ubersimple graph (local smoke test). Day 17 posts use **`Image:`** here to demonstrate the pipeline on an earlier log entry.
- **`assets/images/day18-comfyui-sdxl-cover-responsive.png`** — **1024×768** PNG generated for **Day 18** (paired ES/EN ship log): same graph, **seed `18052026`**, positive prompt documented in those posts; exported with `scripts/comfyui/export_cover.py` (or equivalent `POST /prompt` + `/view` flow).

## Changelog (in-repo)

- **2026-05-11 (follow-up):** Day 18 gains a **dedicated** committed cover PNG + `export_cover.py`; `.agents` image **migration plan**; checklist row 7 marked **Partial**.
- **2026-05-12:** Listings/tags/search — **Picsum fallback** restored when `Image:` is unset (stable seed from `page.id`); neutral placeholder removed from default path.
- **2026-05-11:** Day 18 — `Image:` hero + social meta + responsive CSS; `frontmatter_audit` validates `Image:` paths; ComfyUI marked production-ready for **generation**; checklist updated.
- **2026-05-10:** Initial doc + `scripts/comfyui/sdxl_ubersimple.api.json` + Day 17 meta posts documenting validation and integration checklist.
- **2026-05-10 (follow-up):** Added committed example PNG `assets/images/day17-comfyui-sdxl-example.png` and embedded it in Day 17 ES/EN posts.
