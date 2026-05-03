# Multilingual UI backlog (ES default, EN subtree)

**Purpose:** Track **non-post** surfaces that still mix languages or lack an EN route, so future agents do not rediscover the same gaps. **Canonical translation ledger** for posts remains `translation-migration-tracker.md`.

**Last reviewed:** 2026-05-03 (Batch 4 + archive/blog pairing).

---

## Shipped (reference)

### Day 11 slice

- English **display labels** for canonical Spanish tag names (`tag_label_en` in `65-Multilingual.php`); URLs keep `?tag=<canonical Spanish key>`.
- **`/en/tags`** paired with Spanish `tags` (`Translation_Key: praderas-nav-tags`); `tags.twig` filters post cards by language.
- **`/en/about-picocms`** paired with `acerca-de-picocms` (`Translation_Key: praderas-nav-about-picocms`); EN nav **About** targets the EN page.
- **`sidebar.twig`**, **`post.twig`**, **`categories.twig`**, **`breadcrumbs.twig`**: tag hubs, pills, archive CTA copy, breadcrumb `aria-label`, EN footer on `post.twig`.

### Batch 4 follow-up (blog + archive)

- **`/en/blog` language switcher:** `content/blog.md` ↔ `content/en/blog.md` share `Translation_Key: praderas-nav-blog-listing` so `lang-switcher.twig` can surface **Español** from the English listing.
- **`/en/archivo`:** paired with `content/archivo.md` via `Translation_Key: praderas-nav-archive`; **`archive.twig`** branches month names, breadcrumbs, and post filter (`blog/en/` vs Spanish `blog/*`).

---

## Pending (prioritized)

| Item | Route / surface | Notes |
|------|-----------------|-------|
| **Search UI** | `/search`, sidebar search | No `content/en/search.md` + template split; strings and results language rules TBD (search plugin may need `inferLang` filtering like tag counts). |
| **`index.twig` footer** | Non-`post.twig` layouts (home, tags hub, etc.) | Footer credit block is still Spanish-only; align with `blog.twig` / `post.twig` split when touched. |
| **Robots / sitemap per language** | `PicoRobots` + sitemap templates | Phase 5 “still open” items from `proposed-improvements.md`; verify alternate URLs when implemented. |
| **Optional: EN tag vocabulary** | Front matter `Tags` | Today we intentionally keep **one** YAML tag set; migrating to bilingual keys would be a **large** content + tooling change—only if product explicitly wants distinct taxonomies. |
| **Batch 4 extension** | `tendencias-tecnologicas-futuras-*.md`, `tendencias-economicas-emergentes.md`, … | Still Spanish-only; schedule as batch 4b or merge into batch 5 by theme. |

---

## How to pick up work

1. Read `translation-batches.md` + `translation-migration-tracker.md` for post batches.
2. For UI-only changes, prefer **`content_lang`** branching in Twig or small **`65-Multilingual.php`** variables over duplicating content unless an EN **route** is required.
3. When adding a paired top page under `content/en/`, set **`Translation_Key`** on **both** languages and update **`nav.twig`** if the page belongs in primary navigation.
