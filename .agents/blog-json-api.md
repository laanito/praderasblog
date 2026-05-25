# Blog JSON API (Phase 6)

**Purpose:** Machine-consumable blog data for agents, RAG pipelines, and tooling — without scraping HTML nav, sidebars, or Twig chrome.

**Implementation:** `plugins/70-BlogJson.php`  
**Status:** **v1.1** shipped 2026-05-24 (Day 24): `search.json`, agent fields on listings, schema **1.1**. v1 listing + per-post shipped 2026-05-20 (Day 23).  
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
| GET | `/search.json?q=…` | `es` | Blog search (requires `q`; reuses `PicoSearch` ranking) |
| GET | `/en/search.json?q=…` | `en` | English blog search |

**Discovery (v1.2):** [`/for-ai-agents`](https://blog.praderas.org/for-ai-agents) (ES) · [`/en/for-ai-agents`](https://blog.praderas.org/en/for-ai-agents) (EN) — `Translation_Key: praderas-for-ai-agents`.

**Not yet:** tag query params on listings, static pre-generation, sitemap entries for JSON URLs.

---

## Response headers

- `Content-Type: application/json; charset=utf-8`
- `Cache-Control: public, max-age=3600` (content is flat-file; tune at deploy if needed)

---

## Schema version 1.1

### Listing (`/blog.json`, `/blog/en.json`)

```json
{
  "meta": {
    "schema_version": "1.1",
    "generated_at": "2026-05-24T14:00:00+00:00",
    "language": "es",
    "count": 57
  },
  "posts": [ /* listing items */ ]
}
```

### Listing item (`posts[]` and `results[]`)

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
| `word_count` | int \| null | Body word count (v1.1) |
| `estimated_tokens` | int \| null | Rough `ceil(strlen(body) / 4)` for context budgeting (v1.1) |
| `modified_at` | string \| null | ISO 8601 from file mtime (v1.1 on listings too) |
| `search_rank` | float | **Search responses only** — relevance score from `PicoSearch` |

### Search (`/search.json`, `/en/search.json`)

Requires query parameter **`q`**. Missing `q` → **400** `{"error":"Missing required query parameter: q","status":400}`.

```json
{
  "meta": {
    "schema_version": "1.1",
    "generated_at": "2026-05-24T14:00:00+00:00",
    "language": "es",
    "query": "multilingue",
    "count": 2
  },
  "results": [ /* listing items + search_rank */ ]
}
```

### Single post (`/blog/...json`, `/blog/en/...json`)

Wraps one object under `post` with **all listing fields** plus:

| Field | Type | Notes |
|-------|------|-------|
| `content` | string | Markdown body (no YAML front matter) |
| `content_format` | string | Always `"markdown"` |
| `series` | string \| null | `Series` when set |
| `series_slug` | string \| null | `Series_Slug` |
| `series_order` | int \| null | `Series_Order` |

`meta` on single-post responses includes `schema_version`, `generated_at`, and `language` (no `count`).

### Errors

| Status | Body |
|--------|------|
| 400 | `{"error":"Missing required query parameter: q","status":400}` (search only) |
| 404 | `{"error":"Post not found","status":404}` |
| 503 | `{"error":"Search plugin unavailable","status":503}` |

---

## Language and tags

- **Listings are language-pure:** Spanish feed never includes `blog/en/*`; English feed only `blog/en/*`.
- **Search is language-pure** and scoped to blog posts (not hub pages).
- **`tags` stay Spanish** in JSON even on EN posts — matches YAML and `/tags/?tag=...` URLs. For EN display labels use `scripts/tag_vocabulary.json` separately.

---

## Examples

```bash
curl -sS 'https://blog.praderas.org/blog.json' | head
curl -sS 'https://blog.praderas.org/blog/en.json' | jq '.meta.count'
curl -sS 'https://blog.praderas.org/search.json?q=multilingue' | jq '.results[0].title'
curl -sS 'https://blog.praderas.org/blog/reviviendo-praderas-dia-4-fase-3-metadatos-taxonomy-y-lint-de-front-matter.json' | jq '.post.title'
```

Local stack (PHP + Composer):

```bash
curl -sS 'http://localhost:8080/search.json?q=traduccion'
curl -sS 'http://localhost:8080/blog.json' | jq '.posts[0] | {word_count, estimated_tokens}'
```

---

## Versioning policy

- Bump **`schema_version`** in `meta` when removing or renaming fields.
- Prefer **additive** fields in minor revisions (document in this file).
- HTML routes and slugs remain canonical for humans and SEO; JSON is an alternate representation.

---

## Changelog

- **2026-05-25:** v1.2 — public `/for-ai-agents` + `/en/for-ai-agents` discovery pages (Day 25).
- **2026-05-24:** v1.1 — `/search.json`, `/en/search.json`, `word_count`, `estimated_tokens`, `modified_at` on listings; `PicoSearch::searchBlogPosts()`; schema **1.1** (Day 24).
- **2026-05-20 (follow-up):** Planned v1.2 (`/for-ai-agents`).
- **2026-05-20:** v1 — `70-BlogJson.php`, four endpoints, schema 1.0 documented (Day 23).
