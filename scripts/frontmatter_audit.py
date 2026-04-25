#!/usr/bin/env python3
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

def main() -> int:
    root = pathlib.Path("content/blog")
    posts = sorted(root.glob("*.md"))
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
    if errors:
        print("Frontmatter audit FAILED:")
        for e in errors:
            print("-", e)
        return 1
    print(f"Frontmatter audit OK. Checked {len(posts)} posts.")
    return 0

if __name__ == "__main__":
    sys.exit(main())
