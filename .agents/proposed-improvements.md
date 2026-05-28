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

### 1. Article body visuals — **follow-up QA**

**Owner doc:** `.agents/visual-qa-backlog.md`. **S1–S3 shipped 2026-05-28** (prose ~50–52rem, tables, code colors, table scroll wrap).

- Spot-check live sample URLs after deploy; tune if measure still feels tight on xl screens.

### 2. Cover retrofit Tier B+ — **medium (editorial habit)**

**Owner docs:** `retrofit-cover-queue.md` (Tier A **complete**; Tier B started), `comfyui-cover-images.md` § *Retrofit plan*.

- **Done 2026-05-28:** CTD-01 + Tuqan Phase 0 openers (2 pairs).
- Continue long-tail / remaining series openers when capacity allows (~2 pairs/day).

### 3. Comfy / CI hygiene — **low**

- `ffmpeg` / asset pipeline row 9 (`comfyui-cover-images.md`).
- **Done 2026-05-28:** GitHub Action runs `frontmatter_audit.py` + `pregenerate_blog_json.py` on PR/push.

### 4. Phase 6 extras — **mostly done**

- **Done 2026-05-28:** Listing `?tag=` filter (schema **1.2**); `scripts/pregenerate_blog_json.py` → `generated/json/`.
- **Open:** sitemap policy for JSON URLs (`phase-5-6-plan.md` § Extras).

### 5. Accessibility & polish — **low**

- Alt text policy for heroes; keyboard focus audit; muted text / badge contrast.

### 6. Deferred product bets — **only if requested**

- Bilingual YAML `Tags` (separate taxonomies) — see `multilingual-ui-backlog.md`.
- Search highlighted snippets in HTML results.

---

## Success metrics (still valid)

- ≤2 clicks to a post from home; search works on all templates.
- 100% posts pass `frontmatter_audit.py` for required fields and tags.
- No malformed HTML on core routes after template changes.

---

## Changelog

- **2026-05-28:** Visual S1–S3; Tier B covers (CTD-01, Tuqan Phase 0); Phase 6 tag filter + static JSON script + CI audit workflow.
- **2026-05-26:** Consolidated — removed duplicate P0–P4 and phase-1–6 essay; shipped table + open backlog only.
- **2026-05-26:** Added agent authoring + visual QA pointers (prior long form).
- **2026-04-24:** Initial phased backlog.
