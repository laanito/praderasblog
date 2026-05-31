#!/usr/bin/env python3
"""List blog posts missing Image: (Tier B+ retrofit planning)."""

from __future__ import annotations

import pathlib
import re
import sys

REPO = pathlib.Path(__file__).resolve().parents[1]


def has_image(text: str) -> bool:
    return bool(re.search(r"^Image:\s*.+$", text, re.M))


def main() -> int:
    rows = []
    for root in (REPO / "content/blog", REPO / "content/blog/en"):
        if not root.is_dir():
            continue
        for path in sorted(root.rglob("*.md")):
            if path.name == "blog.md":
                continue
            text = path.read_text(encoding="utf-8")
            if "Template: post" not in text:
                continue
            if has_image(text):
                continue
            rel = path.relative_to(REPO / "content/blog")
            key = ""
            m = re.search(r"^Translation_Key:\s*(.+)$", text, re.M)
            if m:
                key = m.group(1).strip()
            order = ""
            m2 = re.search(r"^Series_Order:\s*(\d+)", text, re.M)
            if m2:
                order = m2.group(1)
            rows.append((order, str(rel), key))

    rows.sort(key=lambda r: (r[0] == "", r[0], r[1]))
    print(f"Posts without Image: {len(rows)}\n")
    for order, rel, key in rows[:40]:
        print(f"  order={order or '-':>3}  {rel}  {key}")
    if len(rows) > 40:
        print(f"  ... and {len(rows) - 40} more")
    return 0


if __name__ == "__main__":
    sys.exit(main())
