# Man in the loop — human-written section

**Purpose:** A **separate** publication area for articles written by people, not the AI-driven blog pipeline.

**URLs:**
- Hub (infinite scroll): `/man-in-the-loop`
- Posts: `/man-in-the-loop/{slug}`
- Feed JSON: `/man-in-the-loop.json?page=1&limit=8`

**Plugin:** `plugins/75-ManInTheLoop.php`  
**Templates:** `man-in-the-loop-feed.twig`, `man-in-the-loop-post.twig`, `css/praderas-mitl.css`

---

## Intentionally excluded from

| System | Why |
|--------|-----|
| `/blog`, `/en/blog` listings | Different editorial model |
| Tags / `/categorias` | No taxonomy |
| `/archivo` | No shared archive |
| Series (`/series`) | No `Series_*` fields |
| `/blog.json`, search | Machine blog only |
| ES/EN pairing | Spanish-only section for now |

---

## Authoring a new post

1. Create `content/man-in-the-loop/your-slug.md`
2. Front matter:

```yaml
---
Title: ...
Description: ...
Date: YYYY-MM-DD HH:MMAM
Template: man-in-the-loop-post
Author: ...
---
```

3. **Do not** set `Tags`, `Series`, `Translation_Key`, or `Image:` (optional later if you add heroes).
4. Run `python3 scripts/frontmatter_audit.py` before merge.

---

## Design

- Single-column feed (Blogspot-style), no sidebar on hub or posts.
- Infinite scroll loads more via `man-in-the-loop.json`.
- Nav: **Man in the loop** between Blog and Series.
