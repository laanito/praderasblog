# Generated JSON snapshots

Static listing files produced by `scripts/pregenerate_blog_json.py` for deploy mirrors, CI artifacts, or offline checks.

- **Not served by Pico by default** — production consumers should use live `/blog.json` and `/blog/en.json` (or filter with `?tag=…`).
- Regenerate after bulk content changes: `python3 scripts/pregenerate_blog_json.py`
