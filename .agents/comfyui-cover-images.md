# ComfyUI cover images — agent reference (local / optional automation)

**Purpose:** Give future sessions a **single starting point** for how we validated ComfyUI from this repo, which graph worked, and what remains to **productize** (Pico theme, front matter, git policy). This is **not** required to build or run the blog.

**Status (2026-05-10):** Smoke-tested against `http://127.0.0.1:8188` on the maintainer machine. **SDXL “ubersimple”** graph produced clearly better results than a minimal **SD 1.5 @ 512²** chain. No cover pipeline is wired into Pico yet.

---

## Goals (product)

- Replace **random / placeholder** hero imagery with something **deterministic or controllable**.
- Keep the **Markdown-first** author flow: ideally the human/agent edits **front matter + body**; image generation is an **optional step** (local script, post-save hook, or CI), not a hard dependency of `index.php`.

Two complementary tracks (see also `proposed-improvements.md` Priority 2):

1. **Closed vocabulary** — `Cover: productivity` style keys mapped to static assets in-repo (zero GPU).
2. **ComfyUI** — tailored raster from `Title` / `Description` / house style prompt, then commit **`Image:`** (or theme convention path) pointing at a generated file under `assets/` or the theme.

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

## Integration checklist (blog repo — future PRs)

1. **Assets** — Decide tree, e.g. `assets/images/covers/<slug>.webp` or theme-relative paths; avoid huge binaries in PRs without **Git LFS** or a deliberate policy.
2. **Front matter** — Add optional `Image:` / `Cover:` / `Og_Image:` (pick one convention; document in `post-template.md`).
3. **Twig** — `post.twig` + `page-meta.twig`: hero `<img>` and `og:image` only when the resolved path exists.
4. **Script** — Python or Node: read Markdown front matter → build prompt string → load JSON template → substitute node `3` text → POST `/prompt` → poll → write image → optionally patch front matter (or print instructions).
5. **Secrets & CI** — If generation runs in GitHub Actions, store **API URL + tokens** in secrets; ComfyUI must be reachable from the runner (unusual) **or** generation stays **local-only** with human-uploaded assets.
6. **Lint** — Extend `scripts/frontmatter_audit.py` (or a sibling script) to assert `Image:` paths exist on disk when set.

---

## Security notes

- Default ComfyUI API is often **unauthenticated** on the LAN. Prefer **localhost only**, VPN, or tunnel with access control.
- Prompt injection: if article body is concatenated raw into CLIP text, treat it like **untrusted input** — prefer **Title + Description + fixed style block** from a template.

---

## Changelog (in-repo)

- **2026-05-10:** Initial doc + `scripts/comfyui/sdxl_ubersimple.api.json` + Day 17 meta posts documenting validation and integration checklist.
