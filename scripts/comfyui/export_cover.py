#!/usr/bin/env python3
"""
Submit the in-repo SDXL ubersimple ComfyUI workflow, wait for completion,
and save the first SaveImage (node 6) PNG to a path inside this repo.

Requires a running ComfyUI instance (default http://127.0.0.1:8188).
Override with COMFYUI_URL.

After a successful export (or with --skip-comfy when the PNG already exists),
optional --patch-markdown updates YAML front matter: set or replace Image:
with a site-relative path (default derived from --output under repo root).

With --webp, runs cwebp to write a sibling .webp (use --webp-delete-png to
remove the PNG after encoding). Requires: brew install webp

Examples:
  python3 scripts/comfyui/export_cover.py \\
    --output assets/images/day18-comfyui-sdxl-cover-responsive.png \\
    --positive "Wide cinematic ..." \\
    --seed 18052026 \\
    --prefix praderas_day18_export

  # Export then patch paired posts (same Image: path in both files):
  python3 scripts/comfyui/export_cover.py \\
    --output assets/images/day19-comfyui-sdxl-export-frontmatter.png \\
    --positive "..." --seed 19052026 --prefix praderas_day19_export \\
    --patch-markdown content/blog/reviviendo-praderas-dia-19-....md \\
      content/blog/en/reviving-praderas-day-19-....md

  # Export + WebP + patch (brew install webp):
  python3 scripts/comfyui/export_cover.py \\
    --output assets/images/foo.png --positive "..." --seed 1 --prefix p \\
    --webp --webp-delete-png --patch-markdown content/blog/foo.md
"""

from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
import uuid
from pathlib import Path

DEFAULT_WORKFLOW = Path(__file__).resolve().parent / "sdxl_ubersimple.api.json"
POLL_INTERVAL_S = 1.0
POLL_MAX_S = 600.0

FRONT_MATTER = re.compile(r"\A(---\s*\n)(.*?)(\n---\s*\n)(.*)\Z", re.DOTALL)
IMAGE_LINE = re.compile(r"(?m)^Image:\s*.+$")


def _post_json(url: str, payload: dict, timeout: int = 30) -> dict:
    data = json.dumps(payload).encode("utf-8")
    req = urllib.request.Request(
        url, data=data, headers={"Content-Type": "application/json"}, method="POST"
    )
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        return json.load(resp)


def _get_json(url: str, timeout: int = 60) -> dict:
    with urllib.request.urlopen(url, timeout=timeout) as resp:
        return json.load(resp)


def _download_file(url: str, dest: Path, timeout: int = 120) -> None:
    dest.parent.mkdir(parents=True, exist_ok=True)
    with urllib.request.urlopen(url, timeout=timeout) as src, dest.open("wb") as out:
        out.write(src.read())


def find_repo_root(start: Path) -> Path:
    """Locate repo root (directory containing config/config.yml and content/)."""
    cur = start.resolve()
    for d in [cur, *cur.parents]:
        if (d / "config" / "config.yml").is_file() and (d / "content").is_dir():
            return d
    return cur


def default_image_site_path(repo_root: Path, output: Path) -> str:
    """Build /assets/... style path from output file under repo (POSIX)."""
    out_abs = output.resolve()
    root = repo_root.resolve()
    try:
        rel = out_abs.relative_to(root).as_posix()
    except ValueError:
        print(
            f"error: --output {out_abs} is not under repo root {root}; "
            "pass --image-value explicitly",
            file=sys.stderr,
        )
        raise SystemExit(1)
    if not rel.startswith("assets/"):
        print(
            f"warning: image path {rel!r} is not under assets/ — continuing",
            file=sys.stderr,
        )
    return "/" + rel.lstrip("/")


def patch_markdown_image(md_path: Path, image_site_path: str, dry_run: bool) -> None:
    """Insert or replace `Image:` inside the first YAML front matter block."""
    text = md_path.read_text(encoding="utf-8")
    m = FRONT_MATTER.match(text)
    if not m:
        print(f"error: no YAML front matter in {md_path}", file=sys.stderr)
        raise SystemExit(1)
    prefix, inner, sep, body = m.groups()
    new_line = f"Image: {image_site_path.strip()}"
    if IMAGE_LINE.search(inner):
        inner_new = IMAGE_LINE.sub(new_line, inner, count=1)
    else:
        inner_stripped = inner.rstrip()
        inner_new = inner_stripped + ("\n" if inner_stripped else "") + new_line + "\n"
    new_text = prefix + inner_new + sep + body
    if dry_run:
        print(f"dry-run: would patch {md_path} -> {new_line}")
        return
    md_path.write_text(new_text, encoding="utf-8")
    print(f"patched {md_path} ({new_line})")


def run_comfy_export(
    base: str,
    wf_path: Path,
    positive: str,
    seed: int,
    prefix: str,
    out_path: Path,
) -> None:
    wf = json.loads(wf_path.read_text(encoding="utf-8"))
    if "3" not in wf or wf["3"].get("class_type") != "CLIPTextEncode":
        print("error: workflow missing node 3 CLIPTextEncode", file=sys.stderr)
        raise SystemExit(1)
    wf["3"]["inputs"]["text"] = positive
    wf["7"]["inputs"]["seed"] = int(seed)
    wf["6"]["inputs"]["filename_prefix"] = prefix

    try:
        pr = _post_json(
            f"{base}/prompt",
            {"prompt": wf, "client_id": str(uuid.uuid4())},
            timeout=120,
        )
    except urllib.error.URLError as e:
        print(f"error: cannot reach ComfyUI at {base}: {e}", file=sys.stderr)
        raise SystemExit(1)

    if pr.get("node_errors"):
        print(f"error: node_errors: {pr['node_errors']}", file=sys.stderr)
        raise SystemExit(1)

    prompt_id = pr.get("prompt_id")
    if not prompt_id:
        print(f"error: unexpected prompt response: {pr}", file=sys.stderr)
        raise SystemExit(1)

    deadline = time.monotonic() + POLL_MAX_S
    hist_entry = None
    while time.monotonic() < deadline:
        try:
            hist = _get_json(f"{base}/history/{prompt_id}", timeout=60)
        except urllib.error.HTTPError:
            time.sleep(POLL_INTERVAL_S)
            continue
        hist_entry = hist.get(prompt_id)
        if hist_entry and hist_entry.get("outputs"):
            break
        time.sleep(POLL_INTERVAL_S)
    else:
        print("error: timed out waiting for ComfyUI outputs", file=sys.stderr)
        raise SystemExit(1)

    assert hist_entry is not None
    outs = hist_entry.get("outputs") or {}
    node6 = outs.get("6") or {}
    images = node6.get("images") or []
    if not images:
        print(f"error: no images in outputs: {outs}", file=sys.stderr)
        raise SystemExit(1)

    img0 = images[0]
    fn = img0["filename"]
    subfolder = img0.get("subfolder") or ""
    typ = img0.get("type") or "output"
    q = urllib.parse.urlencode({"filename": fn, "type": typ, "subfolder": subfolder})
    view_url = f"{base}/view?{q}"
    try:
        _download_file(view_url, out_path)
    except urllib.error.URLError as e:
        print(f"error: download failed: {e}", file=sys.stderr)
        raise SystemExit(1)

    print(f"wrote {out_path.resolve()} ({out_path.stat().st_size} bytes)")


def encode_png_to_webp(png_path: Path, delete_png: bool) -> Path:
    """Write sibling .webp using cwebp (Homebrew: brew install webp)."""
    webp_path = png_path.with_suffix(".webp")
    cmd = [
        "cwebp",
        "-q",
        "82",
        "-m",
        "6",
        "-af",
        "-f",
        "0",
        "-sharp_yuv",
        str(png_path),
        "-o",
        str(webp_path),
    ]
    try:
        subprocess.run(cmd, check=True, capture_output=True)
    except FileNotFoundError as e:
        print(
            "error: cwebp not on PATH (install: brew install webp)",
            file=sys.stderr,
        )
        raise SystemExit(1) from e
    except subprocess.CalledProcessError as e:
        print(e.stderr.decode("utf-8", errors="replace"), file=sys.stderr)
        raise SystemExit(1) from e
    if delete_png and png_path.is_file():
        png_path.unlink()
    print(f"wrote {webp_path.resolve()} ({webp_path.stat().st_size} bytes)")
    return webp_path


def main() -> int:
    p = argparse.ArgumentParser(
        description="Export a cover PNG via local ComfyUI API; optionally patch Image: in Markdown."
    )
    p.add_argument(
        "--comfy-url",
        default=None,
        help="ComfyUI base URL (default: env COMFYUI_URL or http://127.0.0.1:8188)",
    )
    p.add_argument(
        "--workflow",
        type=Path,
        default=DEFAULT_WORKFLOW,
        help="API-format workflow JSON",
    )
    p.add_argument("--output", type=Path, required=True, help="Destination PNG path")
    p.add_argument(
        "--positive",
        default="",
        help="CLIP positive prompt (required unless --skip-comfy)",
    )
    p.add_argument("--seed", type=int, default=0, help="KSampler seed")
    p.add_argument(
        "--prefix",
        default="praderas_cover_export",
        help="SaveImage filename_prefix",
    )
    p.add_argument(
        "--skip-comfy",
        action="store_true",
        help="Do not call ComfyUI; require existing --output file (patch-only mode)",
    )
    p.add_argument(
        "--patch-markdown",
        type=Path,
        nargs="*",
        default=(),
        metavar="PATH",
        help="After export (or with --skip-comfy), set or replace Image: in these files",
    )
    p.add_argument(
        "--image-value",
        default="",
        help="Site path for Image: (default: derived from --output under repo root, e.g. /assets/images/foo.webp)",
    )
    p.add_argument(
        "--dry-run-patch",
        action="store_true",
        help="Print patch actions without modifying Markdown",
    )
    p.add_argument(
        "--webp",
        action="store_true",
        help="After PNG export (or with --skip-comfy on a .png), run cwebp to a sibling .webp (brew install webp)",
    )
    p.add_argument(
        "--webp-delete-png",
        action="store_true",
        help="With --webp, delete the PNG after WebP is written",
    )
    args = p.parse_args()

    import os

    out_path: Path = args.output
    patch_paths = list(args.patch_markdown)

    if args.skip_comfy:
        if not out_path.is_file():
            print(f"error: --skip-comfy requires existing file {out_path}", file=sys.stderr)
            return 1
        if not patch_paths:
            print("error: --skip-comfy requires --patch-markdown", file=sys.stderr)
            return 1
    else:
        if not args.positive.strip():
            print("error: --positive is required unless --skip-comfy", file=sys.stderr)
            return 1

    wf_path: Path = args.workflow
    if not args.skip_comfy:
        if not wf_path.is_file():
            print(f"error: workflow not found: {wf_path}", file=sys.stderr)
            return 1
        base = (args.comfy_url or os.environ.get("COMFYUI_URL") or "http://127.0.0.1:8188").rstrip(
            "/"
        )
        run_comfy_export(base, wf_path, args.positive, args.seed, args.prefix, out_path)

    canonical_out = out_path
    if args.webp:
        if canonical_out.suffix.lower() != ".png":
            print("error: --webp requires --output to end in .png", file=sys.stderr)
            return 1
        if not canonical_out.is_file():
            print(f"error: --webp requires existing PNG {canonical_out}", file=sys.stderr)
            return 1
        canonical_out = encode_png_to_webp(canonical_out, args.webp_delete_png)

    repo_root = find_repo_root(canonical_out.parent)
    image_site = (args.image_value or "").strip()
    if not image_site:
        image_site = default_image_site_path(repo_root, canonical_out)

    for md in patch_paths:
        if not md.is_file():
            print(f"error: markdown file not found: {md}", file=sys.stderr)
            return 1
        try:
            md.resolve().relative_to(repo_root.resolve())
        except ValueError:
            print(f"error: {md} must live under repo root {repo_root}", file=sys.stderr)
            return 1
        patch_markdown_image(md, image_site, args.dry_run_patch)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
