# Cover image prompts — Praderas house style (ComfyUI / SDXL)

**Purpose:** Keep **generated** covers visually coherent with the blog and **semantically tied** to each article, without pasting literal titles into the picture. Use with `scripts/comfyui/sdxl_ubersimple.api.json` and **`scripts/comfyui/export_cover.py`** (see `.agents/comfyui-cover-images.md`).

**Language:** Write the **positive** CLIP string in **English** (model convention). The article itself may be Spanish or English.

---

## 1. House tone (carry into every prompt)

Start from this **baseline mood** (adapt wording; do not drop the spirit):

- **Wide cinematic editorial illustration** for a Spanish-language tech blog named **Praderas** (meadow / calm productivity subtext).
- **Soft natural light** — often golden-hour warmth on greens and neutrals; avoid neon cyberpunk unless the article is explicitly about that aesthetic.
- **Abstract or symbolic** cues only: gentle grids, wireframe hints, soft shapes suggesting interfaces, data, or craft — **no readable text**, **no logos**, **no watermarks**, **no real brand marks**.
- **Professional, quiet, high-detail** finish; tasteful color grading, not posterized or oversaturated.

This aligns with the first production-quality Praderas covers (e.g. Day 18) and keeps the series recognizable in listings and social previews.

---

## 2. Tie the image to the **article** (required)

Before writing the prompt, skim:

1. **`Title`** and **`Description`** (required inputs).
2. **`Tags`** and, if present, **`Series`** / topic (e.g. security vs productivity vs AI).

Then add **one or two short clauses** that translate the **topic** into **visual metaphor**, for example:

| Article angle | Example visual hints (abstract) |
|---------------|----------------------------------|
| Security / privacy | layered shields, soft depth fields, cool teal accents, obfuscated light trails |
| Productivity / workflows | calm desk still-life silhouettes, gentle kanban or timeline shapes (no legible labels) |
| AI / ML | abstract neural motifs, diffused nodes and links, restrained glow (not sci-fi cliché overload) |
| Infra / systems | modular blocks, subtle network topology, horizon line suggesting scale |
| Web / front-end | responsive column hints, browser-chrome **shapes** without UI text, Open Graph–style card silhouettes (as in Day 18) |

**Rules of thumb**

- Prefer **one clear metaphor** over a laundry list; CLIP saturates quickly.
- If the post is a **ship log** or meta entry about the blog itself, lean on **Praderas + meadow + craft** imagery more heavily.
- If the post is **newsy or political**, stay **abstract** — no identifiable public figures; no flags unless the article truly requires symbolism and you accept editorial risk.

---

## 3. Structure of the positive prompt (template)

Assemble in this order (single paragraph or two short sentences):

1. **House opening** — wide editorial illustration, Praderas, cinematic light, meadow or calm landscape undertone as appropriate.
2. **Article hook** — 1–2 phrases from §2 grounded in this post’s tags/topic.
3. **Constraints** — *no readable text, no logos, no watermarks*; optional *high detail, tasteful color grading*.

**Negative prompt:** keep the **existing long negative** in `sdxl_ubersimple.api.json` unless you have a targeted reason to extend it (e.g. add *“political symbols”* for sensitive topics).

---

## 4. Workflow before `export_cover.py`

1. Read Title + Description + Tags (and Series if useful).
2. Draft the **positive** string following §§1–3; read it aloud — if it sounds like stock photo SEO, simplify.
3. Pick a **numeric seed**; document it in the ship log or commit message when the asset ships.
4. Run `export_cover.py` with `--positive "..."` and, in one step, **`--patch-markdown`** on the paired `.md` files (or commit the raster and patch later with **`--skip-comfy`**). Use the same **`Image:`** path on ES and EN.
5. **Optional (planned):** run an **encode / resize** pass to cap file weight — see **checklist row 9** in `.agents/comfyui-cover-images.md` (**`ffmpeg`**, WebP vs PNG, `oxipng`, etc.) before `git add` if the PNG is large.

---

## 5. What “good” looks like

- A viewer recognizes **Praderas** tone even without reading the title.
- Someone who **has read** the article sees **why** the metaphors fit (topic, not random decoration).
- The image still works as a **small listing thumbnail** (clear focal mass, not ultra-fine textural noise only).

---

## Changelog (in-repo)

- **2026-05-12:** Workflow step 4 — **`--patch-markdown`** / **`--skip-comfy`**; step 5 — asset weight / **`ffmpeg`** (checklist row 9 in `comfyui-cover-images.md`).
- **2026-05-12:** Initial guidelines (house tone + article linkage + prompt structure + workflow).
