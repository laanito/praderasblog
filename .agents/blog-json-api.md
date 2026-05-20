# Blog JSON API (Phase 6 v1)

**Purpose:** Machine-consumable blog data for agents, RAG pipelines, and tooling — without scraping HTML nav, sidebars, or Twig chrome.

**Implementation:** `plugins/70-BlogJson.php`  
**Status:** **v1 listing + per-post** shipped 2026-05-20 (Day 23 slice).  
**Canonical roadmap:** `phase-5-6-plan.md` § Phase 6

---

## Base URL

All paths are relative to the site origin in `config/config.yml`:

- **`https://blog.praderas.org`**

With `rewrite_url: true`, use the paths below as-is (no `.md` suffix).

---

## Endpoints

| Method | Path | Language | Description |
|--------|------|----------|-------------|
| GET | `/blog.json` | `es` | All Spanish posts under `content/blog/*.md` (excludes `blog/en/*`) |
| GET | `/blog/en.json` | `en` | All English posts under `content/blog/en/*.md` |
| GET | `/blog/{slug}.json` | `es` | Single Spanish article (`slug` = filename without extension) |
| GET | `/blog/en/{slug}.json` | `en` | Single English article |

**Not in v1:** `search.json`, tag query params, static pre-generation, sitemap entries for JSON URLs.

---

## Response headers

- `Content-Type: application/json; charset=utf-8`
- `Cache-Control: public, max-age=3600` (content is flat-file; tune at deploy if needed)

---

## Schema version 1.0

### Listing (`/blog.json`, `/blog/en.json`)

```json
{
  "meta": {
    "schema_version": "1.0",
    "generated_at": "2026-05-20T14:00:00+00:00",
    "language": "es",
    "count": 56
  },
  "posts": [ /* listing items */ ]
}
```

### Listing item (`posts[]`)

| Field | Type | Notes |
|-------|------|-------|
| `slug` | string | e.g. `reviviendo-praderas-dia-4-...` |
| `id` | string | Pico id, e.g. `blog/...` or `blog/en/...` |
| `title` | string | From `Title` |
| `description` | string | From `Description` |
| `date` | string | As stored in front matter |
| `author` | string | From `Author` |
| `tags` | string[] | **Canonical Spanish** tag names (same as YAML `Tags`) |
| `lang` | string | `es` or `en` (`Lang` + path inference via `Multilingual`) |
| `translation_key` | string \| null | Shared `Translation_Key` when paired |
| `url` | string | Absolute article URL |
| `alternate_url` | string \| null | Paired translation URL when `translation_key` resolves |
| `image` | string \| null | Site-relative `Image:` when set |
| `reading_time_minutes` | int \| null | ~200 wpm from markdown body |

### Single post (`/blog/...json`, `/blog/en/...json`)

Wraps one object under `post` with **all listing fields** plus:

| Field | Type | Notes |
|-------|------|-------|
| `content` | string | Markdown body (no YAML front matter) |
| `content_format` | string | Always `"markdown"` in v1 |
| `series` | string \| null | `Series` when set |
| `series_slug` | string \| null | `Series_Slug` |
| `series_order` | int \| null | `Series_Order` |
| `modified_at` | string \| null | ISO 8601 from file mtime |

`meta` on single-post responses includes `schema_version`, `generated_at`, and `language` (no `count`).

### Errors

| Status | Body |
|--------|------|
| 404 | `{"error":"Post not found","status":404}` |

---

## Language and tags

- **Listings are language-pure:** Spanish feed never includes `blog/en/*`; English feed only `blog/en/*`.
- **`tags` stay Spanish** in JSON even on EN posts — matches YAML and `/tags/?tag=...` URLs. For EN display labels use `scripts/tag_vocabulary.json` separately.

---

## Examples

```bash
curl -sS 'https://blog.praderas.org/blog.json' | head
curl -sS 'https://blog.praderas.org/blog/en.json' | jq '.meta.count'
curl -sS 'https://blog.praderas.org/blog/reviviendo-praderas-dia-4-fase-3-metadatos-taxonomy-y-lint-de-front-matter.json' | jq '.post.title'
```

Local stack (PHP + Composer):

```bash
curl -sS 'http://localhost:8080/blog.json'
```

---

## Versioning policy

- Bump **`schema_version`** in `meta` when removing or renaming fields.
- Prefer **additive** fields in minor revisions (document in this file).
- HTML routes and slugs remain canonical for humans and SEO; JSON is an alternate representation.

---

## Changelog

- **2026-05-20:** v1 — `70-BlogJson.php`, four endpoints, schema documented (Day 23).
