#!/usr/bin/env python3
"""Write static JSON listing snapshots for deploy mirrors (Phase 6 extras).

Generates generated/json/blog.json and generated/json/blog-en.json from Markdown
front matter. For full parity (search, per-post bodies) use live Pico endpoints.

Usage:
  python3 scripts/pregenerate_blog_json.py
  python3 scripts/pregenerate_blog_json.py --tag "Desarrollo Web"
"""

from __future__ import annotations

import argparse
import json
import math
import pathlib
import re
import sys
from datetime import datetime, timezone

REPO = pathlib.Path(__file__).resolve().parents[1]
BLOG_ES = REPO / "content" / "blog"
BLOG_EN = BLOG_ES / "en"
OUT_DIR = REPO / "generated" / "json"
SCHEMA = "1.2"


def parse_frontmatter(text: str) -> dict[str, str]:
    if not text.startswith("---"):
        return {}
    block = text.split("---", 2)[1]
    data: dict[str, str] = {}
    for line in block.splitlines():
        if ":" in line:
            key, val = line.split(":", 1)
            data[key.strip()] = val.strip()
    return data


def parse_tags(raw: str) -> list[str]:
    if not raw:
        return []
    return [t.strip() for t in raw.split(",") if t.strip()]


def slug_from_path(path: pathlib.Path, lang: str) -> str:
    rel = path.relative_to(BLOG_ES)
    parts = rel.parts
    if lang == "en" and parts[0] == "en":
        return "/".join(parts[1:]).replace(".md", "")
    return str(rel).replace(".md", "")


def page_id(path: pathlib.Path, lang: str) -> str:
    slug = slug_from_path(path, lang)
    return f"blog/en/{slug}" if lang == "en" else f"blog/{slug}"


def body_text(path: pathlib.Path) -> str:
    text = path.read_text(encoding="utf-8")
    if text.startswith("---"):
        return text.split("---", 2)[2].strip()
    return text.strip()


def word_count(text: str) -> int | None:
    words = len(re.findall(r"\b\w+\b", text, flags=re.UNICODE))
    return words if words > 0 else None


def modified_at(path: pathlib.Path) -> str:
    return datetime.fromtimestamp(path.stat().st_mtime, tz=timezone.utc).strftime(
        "%Y-%m-%dT%H:%M:%S+00:00"
    )


def serialize_item(path: pathlib.Path, lang: str, base_url: str) -> dict:
    fm = parse_frontmatter(path.read_text(encoding="utf-8"))
    slug = slug_from_path(path, lang)
    body = body_text(path)
    wc = word_count(body)
    url_path = f"/blog/en/{slug}" if lang == "en" else f"/blog/{slug}"
    image = fm.get("Image", "").strip() or None
    return {
        "slug": slug,
        "id": page_id(path, lang),
        "title": fm.get("Title", ""),
        "description": fm.get("Description", ""),
        "date": fm.get("Date", ""),
        "author": fm.get("Author", ""),
        "tags": parse_tags(fm.get("Tags", "")),
        "lang": lang,
        "translation_key": fm.get("Translation_Key") or None,
        "url": f"{base_url}{url_path}",
        "alternate_url": None,
        "image": image,
        "reading_time_minutes": max(1, round(wc / 200)) if wc else None,
        "word_count": wc,
        "estimated_tokens": max(1, math.ceil(len(body) / 4)) if body else None,
        "modified_at": modified_at(path),
    }


def iter_blog_posts(lang: str):
    if lang == "en":
        yield from sorted(BLOG_EN.rglob("*.md"))
        return
    for path in sorted(BLOG_ES.rglob("*.md")):
        rel = path.relative_to(BLOG_ES)
        if rel.parts and rel.parts[0] == "en":
            continue
        yield path


def collect_posts(lang: str, tag_filter: str) -> list[dict]:
    base_url = "https://blog.praderas.org"
    items: list[dict] = []
    for path in iter_blog_posts(lang):
        if path.name in ("blog.md",):
            continue
        fm = parse_frontmatter(path.read_text(encoding="utf-8"))
        if fm.get("Template", "") != "post":
            continue
        tags = parse_tags(fm.get("Tags", ""))
        if tag_filter and tag_filter not in tags:
            continue
        items.append(serialize_item(path, lang, base_url))
    items.sort(key=lambda x: x.get("date", ""), reverse=True)
    return items


def write_listing(lang: str, tag_filter: str) -> pathlib.Path:
    posts = collect_posts(lang, tag_filter)
    meta = {
        "schema_version": SCHEMA,
        "generated_at": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%S+00:00"),
        "language": lang,
        "count": len(posts),
        "source": "scripts/pregenerate_blog_json.py",
    }
    if tag_filter:
        meta["tag_filter"] = tag_filter
    payload = {"meta": meta, "posts": posts}
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    suffix = ""
    if tag_filter:
        safe = re.sub(r"[^a-zA-Z0-9]+", "-", tag_filter).strip("-").lower()
        suffix = f"-tag-{safe}"
    out = OUT_DIR / f"blog{'-en' if lang == 'en' else ''}{suffix}.json"
    out.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    return out


def main() -> int:
    parser = argparse.ArgumentParser(description="Pregenerate blog listing JSON snapshots.")
    parser.add_argument("--tag", default="", help="Canonical Spanish tag filter (optional)")
    args = parser.parse_args()
    tag = args.tag.strip()
    paths = [write_listing("es", tag), write_listing("en", tag)]
    for p in paths:
        print(f"wrote {p} ({p.stat().st_size} bytes)")
    return 0


if __name__ == "__main__":
    sys.exit(main())
