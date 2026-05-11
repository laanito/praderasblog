#!/usr/bin/env python3
"""
Submit the in-repo SDXL ubersimple ComfyUI workflow, wait for completion,
and save the first SaveImage (node 6) PNG to a path inside this repo.

Requires a running ComfyUI instance (default http://127.0.0.1:8188).
Override with COMFYUI_URL.

Example:
  python3 scripts/comfyui/export_cover.py \\
    --output assets/images/day18-comfyui-sdxl-cover-responsive.png \\
    --positive "Wide cinematic ..." \\
    --seed 18052026 \\
    --prefix praderas_day18_export
"""

from __future__ import annotations

import argparse
import json
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


def main() -> int:
    p = argparse.ArgumentParser(description="Export a cover PNG via local ComfyUI API.")
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
    p.add_argument("--positive", required=True, help="CLIP positive prompt text")
    p.add_argument("--seed", type=int, default=0, help="KSampler seed")
    p.add_argument(
        "--prefix",
        default="praderas_cover_export",
        help="SaveImage filename_prefix",
    )
    args = p.parse_args()

    import os

    base = (args.comfy_url or os.environ.get("COMFYUI_URL") or "http://127.0.0.1:8188").rstrip(
        "/"
    )

    wf_path: Path = args.workflow
    if not wf_path.is_file():
        print(f"error: workflow not found: {wf_path}", file=sys.stderr)
        return 1

    wf = json.loads(wf_path.read_text(encoding="utf-8"))
    if "3" not in wf or wf["3"].get("class_type") != "CLIPTextEncode":
        print("error: workflow missing node 3 CLIPTextEncode", file=sys.stderr)
        return 1
    wf["3"]["inputs"]["text"] = args.positive
    wf["7"]["inputs"]["seed"] = int(args.seed)
    wf["6"]["inputs"]["filename_prefix"] = args.prefix

    try:
        pr = _post_json(
            f"{base}/prompt",
            {"prompt": wf, "client_id": str(uuid.uuid4())},
            timeout=120,
        )
    except urllib.error.URLError as e:
        print(f"error: cannot reach ComfyUI at {base}: {e}", file=sys.stderr)
        return 1

    if pr.get("node_errors"):
        print(f"error: node_errors: {pr['node_errors']}", file=sys.stderr)
        return 1

    prompt_id = pr.get("prompt_id")
    if not prompt_id:
        print(f"error: unexpected prompt response: {pr}", file=sys.stderr)
        return 1

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
        return 1

    assert hist_entry is not None
    outs = hist_entry.get("outputs") or {}
    node6 = outs.get("6") or {}
    images = node6.get("images") or []
    if not images:
        print(f"error: no images in outputs: {outs}", file=sys.stderr)
        return 1

    img0 = images[0]
    fn = img0["filename"]
    subfolder = img0.get("subfolder") or ""
    typ = img0.get("type") or "output"
    q = urllib.parse.urlencode(
        {"filename": fn, "type": typ, "subfolder": subfolder}
    )
    view_url = f"{base}/view?{q}"
    out_path: Path = args.output
    try:
        _download_file(view_url, out_path)
    except urllib.error.URLError as e:
        print(f"error: download failed: {e}", file=sys.stderr)
        return 1

    print(f"wrote {out_path.resolve()} ({out_path.stat().st_size} bytes)")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
