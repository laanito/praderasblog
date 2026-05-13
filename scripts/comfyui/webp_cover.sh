#!/usr/bin/env bash
# Encode a Comfy-exported PNG cover to WebP for smaller hero/listing/OG payloads.
# Requires: cwebp (Homebrew: brew install webp)
set -euo pipefail
if ! command -v cwebp >/dev/null 2>&1; then
  echo "error: cwebp not found. Install: brew install webp" >&2
  exit 1
fi
if [[ $# -lt 1 ]]; then
  echo "usage: $0 <path-to.png> [more.png...]" >&2
  exit 1
fi
for src in "$@"; do
  dst="${src%.png}.webp"
  cwebp -q 82 -m 6 -af -f 0 -sharp_yuv "$src" -o "$dst"
  echo "wrote $dst ($(wc -c < "$dst" | tr -d ' ') bytes)"
done
