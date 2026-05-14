#!/usr/bin/env python3
"""Validate blog post front matter (required fields, tags, Image: paths).

Also ensures each non-empty Translation_Key maps to at most one ES and one EN
file under content/blog/ — required for export_cover.py --translation-key.
"""

from __future__ import annotations

import pathlib
import re
import sys

REQUIRED = ["Title", "Description", "Date", "Author", "Template", "Tags"]
CANONICAL_TAGS = {
    "Aplicaciones Moviles",
    "Ciberseguridad",
    "Crypto",
    "Desarrollo Web",
    "Economia",
    "Inteligencia Artificial",
    "Privacidad",
    "Productividad",
    "Sistemas",
    "Sociedad",
}
DATE_RE = re.compile(r"^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}(AM|PM)?)?$")


def parse_frontmatter(path: pathlib.Path):
    text = path.read_text(encoding="utf-8")
    if not text.startswith("---"):
        return {}
    fm = text.split("---", 2)[1]
    data = {}
    for line in fm.splitlines():
        if ":" in line:
            k, v = line.split(":", 1)
            data[k.strip()] = v.strip()
    return data


def check_image_field(repo_root: pathlib.Path, post: pathlib.Path, fm: dict, errors: list) -> None:
    raw = fm.get("Image", "").strip().strip('"').strip("'")
    if not raw:
        return
    if raw.startswith("http://") or raw.startswith("https://"):
        return
    rel = raw.lstrip("/")
    target = repo_root / rel
    if not target.is_file():
        errors.append(f"{post}: Image path not found on disk: {raw} (resolved {target})")


def _is_en_blog_post(repo_root: pathlib.Path, post: pathlib.Path) -> bool:
    """True if path is content/blog/en/<file>.md (EN subtree)."""
    rel = post.resolve().relative_to(repo_root.resolve())
    parts = rel.parts
    return (
        len(parts) >= 4
        and parts[0] == "content"
        and parts[1] == "blog"
        and parts[2] == "en"
    )


def check_translation_key_pairs(
    repo_root: pathlib.Path, posts: list[pathlib.Path], errors: list
) -> None:
    """Each Translation_Key may appear at most once in ES blog and once in EN blog/en."""
    by_key: dict[str, list[pathlib.Path]] = {}
    for post in posts:
        fm = parse_frontmatter(post)
        if not fm:
            continue
        raw = fm.get("Translation_Key", "").strip().strip('"').strip("'")
        if not raw:
            continue
        by_key.setdefault(raw, []).append(post)

    for key in sorted(by_key):
        paths = sorted(by_key[key], key=lambda p: str(p))
        n = len(paths)
        if n > 2:
            errors.append(
                f"Translation_Key {key!r} appears on {n} files (max 2 for ES+EN pair):"
            )
            for p in paths:
                errors.append(f"  - {p}")
        elif n == 2:
            a, b = paths[0], paths[1]
            en_a = _is_en_blog_post(repo_root, a)
            en_b = _is_en_blog_post(repo_root, b)
            if en_a == en_b:
                errors.append(
                    f"Translation_Key {key!r}: expected exactly one file under content/blog/ "
                    f"and one under content/blog/en/; got: {a} | {b}"
                )


def main() -> int:
    repo_root = pathlib.Path(__file__).resolve().parents[1]
    roots = [repo_root / "content/blog", repo_root / "content/blog/en"]
    posts = []
    for root in roots:
        if root.is_dir():
            posts.extend(sorted(root.glob("*.md")))
    errors = []
    for post in posts:
        fm = parse_frontmatter(post)
        if not fm:
            errors.append(f"{post}: missing YAML frontmatter")
            continue
        for key in REQUIRED:
            if not fm.get(key):
                errors.append(f"{post}: missing required field '{key}'")
        date = fm.get("Date", "")
        if date and not DATE_RE.match(date):
            errors.append(f"{post}: non-standard Date format '{date}'")
        tags = [t.strip() for t in fm.get("Tags", "").split(",") if t.strip()]
        for tag in tags:
            if tag not in CANONICAL_TAGS:
                errors.append(f"{post}: unknown tag '{tag}'")
        check_image_field(repo_root, post, fm, errors)

    check_translation_key_pairs(repo_root, posts, errors)

    if errors:
        print("Frontmatter audit FAILED:")
        for e in errors:
            print("-", e)
        return 1
    print(f"Frontmatter audit OK. Checked {len(posts)} posts.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
