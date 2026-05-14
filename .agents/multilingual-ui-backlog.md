# Multilingual UI backlog (ES default, EN subtree)

**Purpose:** Track **non-post** surfaces that still mix languages or lack an EN route, so future agents do not rediscover the same gaps. **Canonical translation ledger** for posts remains `translation-migration-tracker.md`.

**Last reviewed:** 2026-05-15 (retrofit **queue + cadence** in `retrofit-cover-queue.md`; Day 21 **`export_cover.py --translation-key`**; Day 20 **WebP** + **`README.md`** hub; Day 19 `--patch-markdown`; Day 18 hero/Picsum + `image-prompt-guidelines.md`).

---

## Shipped (reference)

### Day 11 slice

- English **display labels** for canonical Spanish tag names (`tag_label_en` in `65-Multilingual.php`); URLs keep `?tag=<canonical Spanish key>`.
- **`/en/tags`** paired with Spanish `tags` (`Translation_Key: praderas-nav-tags`); `tags.twig` filters post cards by language.
- **`/en/about-picocms`** paired with `acerca-de-picocms` (`Translation_Key: praderas-nav-about-picocms`); EN nav **About** targets the EN page.
- **`sidebar.twig`**, **`post.twig`**, **`categories.twig`**, **`breadcrumbs.twig`**: tag hubs, pills, archive CTA copy, breadcrumb `aria-label`, EN footer on `post.twig`.

### Batch 5 slice (productivity guides)

- Six ES/EN post pairs (`praderas-b5-*`) for remote work, Etherpad, Redmine, Taskwarrior, Focalboard, and Nextcloud+Deck — see `translation-migration-tracker.md` backlog rows.

### Batch 4 follow-up (blog + archive)

- **`/en/blog` language switcher:** `content/blog.md` ↔ `content/en/blog.md` share `Translation_Key: praderas-nav-blog-listing` so `lang-switcher.twig` can surface **Español** from the English listing.
- **`/en/archivo`:** paired with `content/archivo.md` via `Translation_Key: praderas-nav-archive`; **`archive.twig`** branches month names, breadcrumbs, and post filter (`blog/en/` vs Spanish `blog/*`).

### Day 15 slice

- **Search UI EN route:** paired `content/search.md` ↔ `content/en/search.md` (`Translation_Key: praderas-nav-search`) so `/en/search/<terms>` resolves with EN metadata.
- **Language-safe search results:** `PicoSearch` now keeps result language aligned with the current page (`Multilingual::inferLang`), while preserving scoped folder search behavior.
- **Search copy split:** `search.twig` and `search-behavior.twig` now branch labels, breadcrumbs, CTA text, and redirect base (`/search` vs `/en/search`) by `content_lang`.
- **Non-post footer i18n:** `index.twig` footer credit block now mirrors ES/EN split already used in post/blog layouts.

### Day 16 slice

- **`/sitemap.xml` as sitemap index:** points to `/sitemap-es.xml` and `/sitemap-en.xml` (standard `<sitemapindex>`).
- **Language-filtered URL sets:** `PicoRobots` builds each child sitemap using `Multilingual::inferLang` so URLs align with ES vs EN trees (`blog/en/`, `content/en/`, `Lang`).
- **Theme templates:** `themes/bootstrap-blog/sitemap-index.twig` + `sitemap.twig`; `robots.txt` unchanged pattern (`Sitemap:` → index).

### Day 18 slice

- **Optional `Image:`** on posts (`65-Multilingual.php` meta header): hero in `post.twig` (else **Picsum** on `blog/…` articles), **`og:image`** + Twitter **`summary_large_image`** in `page-meta.twig` from resolved cover URL.
- **Listings / search / tags:** `list-card-thumb.twig` — **`Image:`** when present, else **Picsum** with stable seed (`blog.twig`, `blog-en.twig`, `tags.twig`, `search.twig`).
- **Responsive formatting:** `praderas-theme.css` rules for `.pradera-hero-*` and `.post-body img` / `figure` so layouts do not break on narrow viewports.

---

## Pending (prioritized)

| Item | Route / surface | Notes |
|------|-----------------|-------|
| **Optional: EN tag vocabulary** | Front matter `Tags` | Today we intentionally keep **one** YAML tag set; migrating to bilingual keys would be a **large** content + tooling change—only if product explicitly wants distinct taxonomies. |

---

## How to pick up work

1. Read `translation-batches.md` + `translation-migration-tracker.md` for post batches.
2. For UI-only changes, prefer **`content_lang`** branching in Twig or small **`65-Multilingual.php`** variables over duplicating content unless an EN **route** is required.
3. When adding a paired top page under `content/en/`, set **`Translation_Key`** on **both** languages and update **`nav.twig`** if the page belongs in primary navigation.
4. For **hero cover retrofit** on older *Reviviendo Praderas* pairs, use **`.agents/retrofit-cover-queue.md`** (daily cadence) + **`comfyui-cover-images.md`** § *Retrofit plan*.
