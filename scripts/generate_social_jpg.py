#!/usr/bin/env python3
"""
Build 1200×630 JPEG social previews from committed WebP (or PNG) heroes.

Twitter/X and some crawlers handle JPEG at 2:1 better than 4:3 WebP. Convention:
  /assets/images/foo-hero.webp  →  /assets/images/foo-hero-social.jpg

Used by export_cover.py (--webp) and for batch backfill of existing covers.
"""

from __future__ import annotations

import argparse
import subprocess
import sys
from pathlib import Path

SOCIAL_WIDTH = 1200
SOCIAL_HEIGHT = 630


def social_jpg_path_for_site_path(site_path: str) -> str:
    """Map a site-relative hero path to its social JPEG sibling."""
    path = site_path.strip()
    if path.endswith(".webp"):
        return path[:-5] + "-social.jpg"
    if path.endswith(".png"):
        return path[:-4] + "-social.jpg"
    if path.endswith((".jpg", ".jpeg")):
        stem = path.rsplit(".", 1)[0]
        return f"{stem}-social.jpg"
    return path


def social_jpg_path_for_file(image_file: Path) -> Path:
    return image_file.with_name(image_file.stem + "-social.jpg")


def write_social_jpg(source: Path, dest: Path) -> None:
    """Center-crop to 1200×630 JPEG (quality ~85). Requires ffmpeg on PATH."""
    if not source.is_file():
        raise FileNotFoundError(source)
    dest.parent.mkdir(parents=True, exist_ok=True)
    vf = (
        f"scale={SOCIAL_WIDTH}:{SOCIAL_HEIGHT}:force_original_aspect_ratio=increase,"
        f"crop={SOCIAL_WIDTH}:{SOCIAL_HEIGHT}"
    )
    cmd = [
        "ffmpeg",
        "-y",
        "-loglevel",
        "error",
        "-i",
        str(source),
        "-vf",
        vf,
        "-q:v",
        "3",
        str(dest),
    ]
    try:
        subprocess.run(cmd, check=True, capture_output=True)
    except FileNotFoundError as exc:
        print("error: ffmpeg not on PATH (brew install ffmpeg)", file=sys.stderr)
        raise SystemExit(1) from exc
    except subprocess.CalledProcessError as exc:
        print(exc.stderr.decode("utf-8", errors="replace"), file=sys.stderr)
        raise SystemExit(1) from exc


def iter_hero_webps(images_dir: Path) -> list[Path]:
    return sorted(images_dir.glob("*.webp"))


def main() -> int:
    p = argparse.ArgumentParser(description="Generate *-social.jpg previews from WebP heroes.")
    p.add_argument(
        "paths",
        nargs="*",
        type=Path,
        help="WebP/PNG files (default: all *.webp under assets/images/)",
    )
    p.add_argument(
        "--images-dir",
        type=Path,
        default=Path("assets/images"),
        help="Directory to scan when no paths given",
    )
    p.add_argument(
        "--force",
        action="store_true",
        help="Regenerate even when the JPEG already exists",
    )
    args = p.parse_args()

    repo_root = Path(__file__).resolve().parents[1]
    sources: list[Path] = []
    if args.paths:
        for raw in args.paths:
            path = raw if raw.is_absolute() else repo_root / raw
            sources.append(path)
    else:
        images_dir = args.images_dir if args.images_dir.is_absolute() else repo_root / args.images_dir
        sources = iter_hero_webps(images_dir)

    if not sources:
        print("No source images found.", file=sys.stderr)
        return 1

    written = 0
    skipped = 0
    for src in sources:
        if not src.is_file():
            print(f"skip missing {src}", file=sys.stderr)
            continue
        dest = social_jpg_path_for_file(src)
        if dest.is_file() and not args.force:
            skipped += 1
            continue
        write_social_jpg(src, dest)
        print(f"wrote {dest} ({dest.stat().st_size} bytes)")
        written += 1

    print(f"Done. wrote={written} skipped={skipped}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
