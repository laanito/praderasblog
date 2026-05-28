---
Title: For AI agents and developers (JSON API)
Description: Public entry point to consume the blog without HTML — JSON endpoints, schema version, languages, and canonical tags.
Lang: en
Translation_Key: praderas-for-ai-agents
Robots: index, follow
---

# For AI agents and developers

This page explains how to **read Blog Praderas in a machine-friendly way** without parsing Twig chrome or sidebars. HTML remains canonical for humans and SEO; JSON is a **parallel representation** from the same Pico instance on dedicated routes (`plugins/70-BlogJson.php`).

Full contract in the repo: [`.agents/blog-json-api.md`](https://github.com/laanito/praderasblog/blob/main/.agents/blog-json-api.md) when you work from git. In production, use host `https://blog.praderas.org`.

## Current schema

- **`schema_version`:** `1.2` (listings support optional `?tag=` filter)
- **Languages:** listings and search are **language-pure** (`es` vs `en`); JSON `tags` stay **canonical Spanish** (same as YAML `Tags`).

## Endpoints

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/blog.json` | Spanish post listing |
| GET | `/blog.json?tag=…` | Spanish listing filtered by canonical tag (Spanish name) |
| GET | `/blog/en.json` | English post listing |
| GET | `/blog/en.json?tag=…` | English listing filtered by the same canonical tag |
| GET | `/blog/{slug}.json` | Spanish article + markdown body |
| GET | `/blog/en/{slug}.json` | English article + markdown body |
| GET | `/search.json?q=…` | Spanish search (same ranking as `/search/…`) |
| GET | `/en/search.json?q=…` | English search |

Typical headers: `Content-Type: application/json; charset=utf-8`, `Cache-Control: public, max-age=3600`.

## RAG-oriented fields

On listings and search hits: `word_count`, `estimated_tokens` (rough `strlen/4`), `modified_at`, `translation_key`, `alternate_url`, `reading_time_minutes`.

Search JSON items may include `search_rank`.

## Examples

```bash
curl -sS 'https://blog.praderas.org/blog.json' | head
curl -sS 'https://blog.praderas.org/search.json?q=translation' | jq '.meta'
curl -sS 'https://blog.praderas.org/blog/en/reviving-praderas-day-24-tier-a-days-8-9-and-search-json.json' | jq '.post.title'
```

## Content rules

1. **Do not mix languages** in one JSON feed; use `/blog.json` or `/blog/en.json`.
2. **Tags:** Spanish canonical names in JSON; English display labels are HTML-only via `scripts/tag_vocabulary.json`.
3. **Translation pairs:** shared `translation_key`; `alternate_url` when a sibling exists.

## Editorial context

The *Reviving Praderas* series (Days 23–25) documents API rollout and cover retrofit. For human-first writing policy vs JSON, see [`.agents/` in the repo](https://github.com/laanito/praderasblog/tree/main/.agents).
