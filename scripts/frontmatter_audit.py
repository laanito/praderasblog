#!/usr/bin/env python3
"""Validate blog post front matter (required fields, tags, Image: paths).

Also ensures each non-empty Translation_Key maps to at most one ES and one EN
file under content/blog/ — required for export_cover.py --translation-key.

Tag display vocabulary (labels + blurbs) lives in scripts/tag_vocabulary.json and
is loaded by plugins/65-Multilingual.php; this audit keeps that file aligned with
CANONICAL_TAGS.
"""

from __future__ import annotations

import json
import pathlib
import re
import sys

REQUIRED = ["Title", "Description", "Date", "Author", "Template", "Tags"]
REQUIRED_MITL_POST = ["Title", "Description", "Date", "Author", "Template", "Lang", "Translation_Key"]
MITL_POST_TEMPLATE = "man-in-the-loop-post"
MITL_HUB_TEMPLATE = "man-in-the-loop-feed"
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
    if raw.endswith(".webp"):
        social_rel = raw[:-5] + "-social.jpg"
        social_target = repo_root / social_rel.lstrip("/")
        if not social_target.is_file():
            errors.append(
                f"{post}: social JPEG missing for WebP hero: {social_rel} "
                f"(run python3 scripts/generate_social_jpg.py)"
            )


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


def check_tag_vocabulary_json(repo_root: pathlib.Path, errors: list) -> None:
    """scripts/tag_vocabulary.json must cover every canonical tag with label + blurbs."""
    path = repo_root / "scripts" / "tag_vocabulary.json"
    if not path.is_file():
        errors.append(f"Missing tag vocabulary file: {path}")
        return
    try:
        raw = json.loads(path.read_text(encoding="utf-8"))
    except json.JSONDecodeError as exc:
        errors.append(f"{path}: invalid JSON ({exc})")
        return
    tags = raw.get("tags")
    if not isinstance(tags, dict):
        errors.append(f"{path}: expected top-level 'tags' object")
        return
    vocab_keys = set(tags.keys())
    missing = CANONICAL_TAGS - vocab_keys
    extra = vocab_keys - CANONICAL_TAGS
    if missing:
        errors.append(
            f"{path}: missing vocabulary rows for canonical tags: {sorted(missing)}"
        )
    if extra:
        errors.append(
            f"{path}: vocabulary rows not in CANONICAL_TAGS: {sorted(extra)}"
        )
    for tag in CANONICAL_TAGS:
        row = tags.get(tag)
        if not isinstance(row, dict):
            errors.append(f"{path}: tag {tag!r} must be an object")
            continue
        for field in ("label_en", "blurb_es", "blurb_en"):
            val = row.get(field, "")
            if not isinstance(val, str) or not val.strip():
                errors.append(f"{path}: tag {tag!r} missing non-empty {field}")


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


def _is_en_mitl_post(repo_root: pathlib.Path, post: pathlib.Path) -> bool:
    rel = post.resolve().relative_to(repo_root.resolve())
    return len(rel.parts) >= 3 and rel.parts[0:3] == ("content", "man-in-the-loop", "en")


def check_mitl_translation_pairs(repo_root: pathlib.Path, mitl_posts: list[pathlib.Path], errors: list) -> None:
    by_key: dict[str, list[pathlib.Path]] = {}
    for post in mitl_posts:
        fm = parse_frontmatter(post)
        if not fm:
            continue
        raw = fm.get("Translation_Key", "").strip().strip('"').strip("'")
        if not raw:
            errors.append(f"{post}: Man in the loop posts require Translation_Key")
            continue
        by_key.setdefault(raw, []).append(post)

    for key in sorted(by_key):
        paths = sorted(by_key[key], key=lambda p: str(p))
        if len(paths) != 2:
            errors.append(
                f"Translation_Key {key!r}: expected exactly 2 MITL files (ES+EN), got {len(paths)}"
            )
            for p in paths:
                errors.append(f"  - {p}")
            continue
        a, b = paths[0], paths[1]
        if _is_en_mitl_post(repo_root, a) == _is_en_mitl_post(repo_root, b):
            errors.append(
                f"Translation_Key {key!r}: expected one ES + one EN MITL file; got: {a} | {b}"
            )


def check_man_in_the_loop(repo_root: pathlib.Path, errors: list) -> tuple[int, list[pathlib.Path]]:
    """Human section: bilingual pairs; no Tags/Series."""
    count = 0
    mitl_posts: list[pathlib.Path] = []
    for hub in (repo_root / "content/man-in-the-loop.md", repo_root / "content/en/man-in-the-loop.md"):
        if hub.is_file():
            fm = parse_frontmatter(hub)
            if fm.get("Template", "") != MITL_HUB_TEMPLATE:
                errors.append(f"{hub}: Template must be {MITL_HUB_TEMPLATE!r}")
    mitl_dir = repo_root / "content/man-in-the-loop"
    if mitl_dir.is_dir():
        for post in sorted(mitl_dir.glob("*.md")):
            count += 1
            mitl_posts.append(post)
            _audit_mitl_post(repo_root, post, errors)
        en_dir = mitl_dir / "en"
        if en_dir.is_dir():
            for post in sorted(en_dir.glob("*.md")):
                count += 1
                mitl_posts.append(post)
                _audit_mitl_post(repo_root, post, errors)
    check_mitl_translation_pairs(repo_root, mitl_posts, errors)
    return count, mitl_posts


def _audit_mitl_post(repo_root: pathlib.Path, post: pathlib.Path, errors: list) -> None:
    fm = parse_frontmatter(post)
    if not fm:
        errors.append(f"{post}: missing YAML frontmatter")
        return
    for key in REQUIRED_MITL_POST:
        if not fm.get(key):
            errors.append(f"{post}: missing required field '{key}'")
    if fm.get("Template", "") != MITL_POST_TEMPLATE:
        errors.append(f"{post}: Template must be {MITL_POST_TEMPLATE!r}")
    if fm.get("Tags", "").strip():
        errors.append(f"{post}: Man in the loop posts must not use Tags")
    for key in ("Series", "Series_Slug", "Series_Order"):
        if fm.get(key, "").strip():
            errors.append(f"{post}: must not set {key}")
    date = fm.get("Date", "")
    if date and not DATE_RE.match(date):
        errors.append(f"{post}: non-standard Date format '{date}'")
    check_image_field(repo_root, post, fm, errors)


def main() -> int:
    repo_root = pathlib.Path(__file__).resolve().parents[1]
    roots = [repo_root / "content/blog", repo_root / "content/blog/en"]
    posts = []
    for root in roots:
        if root.is_dir():
            posts.extend(sorted(root.glob("*.md")))
    errors = []
    mitl_count, _mitl_posts = check_man_in_the_loop(repo_root, errors)
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
    check_tag_vocabulary_json(repo_root, errors)

    if errors:
        print("Frontmatter audit FAILED:")
        for e in errors:
            print("-", e)
        return 1
    total = len(posts) + mitl_count
    print(f"Frontmatter audit OK. Checked {len(posts)} blog posts + {mitl_count} man-in-the-loop posts.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
