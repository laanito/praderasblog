# Multilingual UI — closure note

**Status:** **Closed** (2026-05-19). Phase 5 **non-post** EN surfaces are shipped for the current model.

**Post translation ledger:** `translation-migration-tracker.md` (all batches **done**).

---

## Model (do not change casually)

- **`Tags` in YAML:** canonical **Spanish** names (URLs, audit, JSON).
- **EN display:** `scripts/tag_vocabulary.json` → `65-Multilingual.php` → Twig (`tag_label_en`, blurbs).
- **Routes:** Spanish at `/blog`, `/archivo`, `/search`, …; English at `/en/...` and `/blog/en/...`; paired hubs use shared **`Translation_Key`**.

---

## Shipped surfaces (summary)

EN hubs and UI branching for: tags, about, blog listing + pagination, archive, search (copy + results scoped by language), sitemap index (`sitemap-es.xml` / `sitemap-en.xml`), footer on index, tag vocabulary on categories, language switcher via `Translation_Key`.

Day-by-day detail is in *Reviviendo Praderas* Días 8–16 posts — not duplicated here.

---

## Deferred

| Item | Notes |
|------|--------|
| **Bilingual YAML `Tags`** | Large migration; revisit only if product wants separate taxonomies per language. |

---

## If you change UI copy or add `/en/...` pages

1. Pair with Spanish via **`Translation_Key`**; update **`nav.twig`** if primary nav.
2. Prefer **`content_lang`** in Twig or extend **`tag_vocabulary.json`**.
3. New canonical tag → `tag_vocabulary.json` + `frontmatter_audit.py` **`CANONICAL_TAGS`**.

---

## Changelog

- **2026-05-26:** Compressed — removed long “shipped slices” list (redundant with tracker + ship logs).
- **2026-05-19:** Phase 5 UI closure documented.
