# Post Template (Phase 3)

**Before writing:** `.agents/article-authoring-guide.md` (full workflow for agents).  
**Tone:** `.agents/editorial-guidelines.md`.

Use this as starter front matter for new posts under `content/blog/`.

```yaml
---
Title: <Título del artículo>
Description: <Resumen corto y útil para SEO>
Date: YYYY-MM-DD HH:MMAM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad
# Optional (Phase 5 — bilingual):
# Lang: es
# Translation_Key: my-post-slug-pair   # same string on the EN file under content/blog/en/
---
```

## Required fields

- `Title`
- `Description`
- `Date` (preferred format: `YYYY-MM-DD HH:MMAM`)
- `Template: post`
- `Author`
- `Tags` (comma-separated)

## Canonical tags

- `Aplicaciones Moviles`
- `Ciberseguridad`
- `Crypto`
- `Desarrollo Web`
- `Economia`
- `Inteligencia Artificial`
- `Privacidad`
- `Productividad`
- `Sistemas`
- `Sociedad`

Run `python3 scripts/frontmatter_audit.py` before PR to catch missing fields, date drift, unknown tags, broken **`Image:`** paths, and invalid **`Translation_Key`** maps (more than two posts per key, or two files not split as one `content/blog/` + one `content/blog/en/`).

## Optional (Phase 5)

- **`Lang`:** `es` or `en` (can be omitted when language is implied by path, e.g. `blog/en/...`).
- **`Translation_Key`:** shared identifier between the Spanish markdown file and its English twin so the theme can render the language switcher and `hreflang` alternates.

## Optional — hero / social image (`Image:`)

- **`Image:`** — optional site-relative path (e.g. **`/assets/images/mi-portada.webp`** or `.png`) or absolute `https://...`. On **`Template: post`** under **`blog/…`**, `post.twig` shows a **responsive hero** from **`Image:`** when set, otherwise the same **Picsum** URL family as listing cards (stable seed; larger size for hero). `page-meta.twig` emits **`og:image`** and **`summary_large_image`** for that resolved URL. Paths under the site root are checked by `scripts/frontmatter_audit.py` when not HTTP(S). For **ComfyUI** covers, prefer **WebP** after **`cwebp`** (see `.agents/comfyui-cover-images.md`), `.agents/image-prompt-guidelines.md`, `scripts/comfyui/sdxl_ubersimple.api.json`, and **`scripts/comfyui/export_cover.py`** (PNG + optional **`--webp`** / **`--patch-markdown`** / **`--translation-key`**).
- **Dedicated file per post** — If you set **`Image:`**, it must target a **new** raster for that article (paired ES/EN use the **same** path). **Do not** point **`Image:`** at another post’s `assets/images/...` file to save time; the exception is when the **body text** explicitly explains reuse (see **“Avoid silent reuse”** in `comfyui-cover-images.md`).

For **migration work** (many posts, series, PR sizing), read `.agents/translation-batches.md` first: batches, glossary, and plain-language “context” rules for agents and for meta posts.

## Writing for humans (required for new posts)

1. **`.agents/article-authoring-guide.md`** — structure, checklist, anti-patterns.  
2. **`.agents/editorial-guidelines.md`** — what / why / how / benefits / scope in prose.

JSON feeds (`/blog.json`, etc.) are for machines; they do **not** replace narrative. Avoid ship logs that are only command dumps — commands belong in a short subsection **after** the explanation.

After merge, if the post uses **tables or code blocks**, note layout checks in **`.agents/visual-qa-backlog.md`** (sample URLs + viewports).
