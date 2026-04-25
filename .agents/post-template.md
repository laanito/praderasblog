# Post Template (Phase 3)

Use this as starter front matter for new posts under `content/blog/`.

```yaml
---
Title: <Título del artículo>
Description: <Resumen corto y útil para SEO>
Date: YYYY-MM-DD HH:MMAM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad
---
```

## Required fields

- `Title`
- `Description`
- `Date` (preferred format: `YYYY-MM-DD HH:MMAM`)
- `Template: post`
- `Author`
- `Tags` (comma-separated)

## Canonical tags

- `Aplicaciones Moviles`
- `Ciberseguridad`
- `Crypto`
- `Desarrollo Web`
- `Economia`
- `Inteligencia Artificial`
- `Privacidad`
- `Productividad`
- `Sistemas`
- `Sociedad`

Run `python3 scripts/frontmatter_audit.py` before PR to catch missing fields, date drift, and unknown tags.
