#!/usr/bin/env bash
# Tier A retrofit rows 10–16 — one WebP per Translation_Key (ES+EN patched).
set -euo pipefail
cd "$(dirname "$0")/../.."

BASE='Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, '
TAIL=' professional quiet atmosphere, no logos, no watermarks, no readable text, high detail, tasteful color grading'

run() {
  local num=$1 key=$2 slug=$3 prompt=$4 seed=$5
  echo "=== Row ${num}: ${key} ==="
  python3 scripts/comfyui/export_cover.py \
    --output "assets/images/day${num}-comfyui-sdxl-${slug}.png" \
    --positive "${BASE}${prompt}${TAIL}" \
    --seed "${seed}" \
    --prefix "praderas_day${num}_export" \
    --webp --webp-delete-png \
    --translation-key "${key}"
}

run 10 praderas-day-10-batch-2-multilingual-hubs batch2-multilingual-hubs-hero \
  'abstract paired hub portals and gentle bilingual route arcs suggesting EN series and categories hubs, ' \
  10052026

run 11 praderas-day-11-batch-3-security-ui batch3-security-privacy-ui-hero \
  'layered soft shields and privacy veil motifs with subtle UI panel silhouettes suggesting security cluster and tag labels, cool teal accents on meadow greens, ' \
  11052026

run 12 praderas-day-12-batch-4-archive-blog-log batch4-ai-archive-blog-hero \
  'abstract archive timeline and gentle AI node glow with blog card silhouettes suggesting AI cluster and date archive, ' \
  12052026

run 13 praderas-day-13-batch-5-productivity-log batch5-productivity-tools-hero \
  'calm desk still-life silhouettes and soft kanban lane shapes suggesting productivity guides and task tools, ' \
  13052026

run 14 praderas-day-14-batch-6-7-8-translation-finale-log batch678-translation-finale-hero \
  'converging document waves from three gentle lanes merging into one horizon suggesting batches six seven eight closure, ' \
  14052026

run 15 praderas-day-15-ui-search-footer-log day15-search-footer-ui-hero \
  'soft search magnifier and footer band silhouettes with bilingual UI hints suggesting search route and footer i18n, ' \
  15052026

run 16 praderas-day-16-sitemap-robots-lang-log day16-sitemap-robots-lang-hero \
  'abstract sitemap tree forked into two calm language branches with subtle robot.txt scroll shape suggesting per-language discovery, ' \
  16052026

echo 'Done: Tier A rows 10–16'
