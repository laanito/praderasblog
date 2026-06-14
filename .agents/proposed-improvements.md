# Product backlog (Praderas blog)

**Purpose:** What is **open** vs **already shipped**. Detail for phases 1–6 lives in *Reviviendo Praderas* ship logs (`content/blog/reviviendo-praderas-dia-*`); this file avoids repeating those narratives.

**Hub:** `.agents/README.md` — which doc to read for each task.

---

## Goals (unchanged)

- Browse and search in ≤2 clicks where possible.
- Trustworthy taxonomy, consistent bilingual model, readable long-form.
- Flat-file Pico workflow; preserve Spanish post URLs.

---

## Shipped (do not re-open without reason)

| Area | Status | Where documented |
|------|--------|------------------|
| P0–P1 UX (blog template, search, pager, nav, categories, crumbs, related, series) | Done | Días 2–6 posts; `repo-context.md` (architecture only) |
| P3 metadata + `frontmatter_audit.py` | Done | Día 4; `post-template.md` |
| P4 SEO, archive, sitemaps | Done | Día 7, 16 |
| P5 multilingual (posts + EN UI + vocabulary JSON) | Done | Día 8–16; `multilingual-ui-backlog.md` (closure) |
| P6 JSON v1–v1.2 (`/blog.json`, `search.json`, `/for-ai-agents`) | Done | Días 23–25; `blog-json-api.md` |
| Day 5 visual + series | Done | `praderas-theme.css`; Día 5–6 (consultant notes: `day5-consultant-feedback.md`) |
| Cover pipeline + Tier A heroes (Days 1–16) | Done | `comfyui-cover-images.md`, `retrofit-cover-queue.md` |
| Agent authoring guide | Done | `article-authoring-guide.md` (2026-05-26) |

---

## Open backlog (prioritized)

### 1. Man in the loop (human section) — **shipped**

**Owner doc:** `.agents/man-in-the-loop.md`. Hub `/man-in-the-loop`, bilingual ES/EN pairs, infinite scroll + sidebar, excluded from blog taxonomy/tags/series/archive.

### 2. Twitter / OG social previews — **shipped (this PR)**

**Fix:** `page-meta.twig` serves **`*-social.jpg`** (1200×630) for `og:image` / `twitter:image` instead of WebP heroes; batch backfill via `scripts/generate_social_jpg.py`; audit enforces JPEG sibling when `Image:` ends in `.webp`.

### 3. Article body visuals — **follow-up QA**

**Owner doc:** `.agents/visual-qa-backlog.md`. **S1–S3 shipped** (prose, tables, code).

- Spot-check live sample URLs after deploy.

### 4. Cover retrofit Tier B+ — **medium (editorial habit)**

**Owner docs:** `retrofit-cover-queue.md`, `scripts/list_missing_hero_images.py`.

- Tier B: CTD-01–05 + Tuqan Phase 0 done.
- Continue ~2 pairs/day when capacity allows (CTD-06+ next).

### 5. Comfy hygiene — **low**

- **`ffmpeg`** row 9 — now used for `*-social.jpg` generation (`generate_social_jpg.py`).
- **Covers stay on laptop** — no Comfy in CI; run `frontmatter_audit.py` locally before PR.

### 6. Phase 6 extras — **done**

- Tag filter, static JSON script, **sitemap policy** (JSON URLs omitted from sitemap).

### 7. Accessibility — **partial**

- **Done:** `:focus-visible` outlines; darker muted text; hero `alt` uses `Image_Alt` or title.
- **Open:** systematic keyboard audit; hero alt policy doc.

### 8. Deferred product bets — **only if requested**

- Bilingual YAML `Tags` (separate taxonomies) — see `multilingual-ui-backlog.md`.
- Search highlighted snippets in HTML results.

---

## Success metrics (still valid)

- ≤2 clicks to a post from home; search works on all templates.
- 100% posts pass `frontmatter_audit.py` for required fields and tags.
- No malformed HTML on core routes after template changes.

---

## Changelog

- **2026-06-01:** Tier B retrofit CTD-04 + CTD-05 (JWT + React intro heroes).
- **2026-05-31:** Twitter/OG fix (`*-social.jpg`); Tier B retrofit CTD-02 + CTD-03; `generate_social_jpg.py` backfill.
- **2026-05-28:** Visual S1–S3; Tier B covers (CTD-01, Tuqan Phase 0); Phase 6 tag filter + static JSON script + CI audit workflow.
- **2026-05-26:** Consolidated — removed duplicate P0–P4 and phase-1–6 essay; shipped table + open backlog only.
- **2026-05-26:** Added agent authoring + visual QA pointers (prior long form).
- **2026-04-24:** Initial phased backlog.
