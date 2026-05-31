# Man in the loop — human-written section

**Purpose:** A **separate** publication area for articles written by people, not the AI-driven blog pipeline.

**URLs (bilingual):**

| Lang | Hub | Post | Feed JSON |
|------|-----|------|-----------|
| ES | `/man-in-the-loop` | `/man-in-the-loop/{slug}` | `/man-in-the-loop.json` |
| EN | `/en/man-in-the-loop` | `/man-in-the-loop/en/{slug}` | `/en/man-in-the-loop.json` |

**Plugin:** `plugins/75-ManInTheLoop.php`  
**Templates:** `man-in-the-loop-feed.twig`, `man-in-the-loop-post.twig`, `mitl-sidebar.twig`, `css/praderas-mitl.css`

---

## Intentionally excluded from

| System | Why |
|--------|-----|
| `/blog`, `/en/blog` | Different editorial model |
| Tags / `/categorias` | No taxonomy |
| `/archivo` | No shared archive |
| Series (`/series`) | No `Series_*` fields |
| `/blog.json`, site search | Machine blog only |

---

## Authoring a new post (ES + EN pair)

1. `content/man-in-the-loop/your-slug.md` (`Lang: es`)
2. `content/man-in-the-loop/en/your-slug.md` (`Lang: en`)
3. Same `Translation_Key` on both; **no** `Tags` or `Series_*`

```yaml
---
Title: ...
Description: ...
Date: YYYY-MM-DD HH:MMAM
Template: man-in-the-loop-post
Author: ...
Lang: es   # or en
Translation_Key: mitl-your-stable-key
Image: /assets/images/mitl-your-stable-key-hero.webp
Image_Alt: Short accessible description of the cover (same on ES + EN)
---
```

**Hero images:** Same rules as the AI blog — one **WebP** per `Translation_Key`, shared `Image:` on both ES and EN posts. Generate with `scripts/comfyui/export_cover.py` (`--translation-key mitl-…` scans `content/man-in-the-loop/`). Post template renders the hero above the body; OG/Twitter use `page-meta.twig` article covers.

4. Run `python3 scripts/frontmatter_audit.py` before merge.

Hub pages: `content/man-in-the-loop.md` + `content/en/man-in-the-loop.md` with `Translation_Key: mitl-nav-hub`.

---

## UX

- **Feed:** infinite scroll + **sidebar** (anchor links on hub; jump between articles on post pages).
- **Language switcher** via `65-Multilingual.php` + `Translation_Key`.
- Nav: **Man in the loop** (ES and EN).
