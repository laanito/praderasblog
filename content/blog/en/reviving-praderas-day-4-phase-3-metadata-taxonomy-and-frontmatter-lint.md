---
Title: Reviving Praderas (Day 4) — Phase 3: metadata, taxonomy, and front matter lint
Description: Phase 3 focused on editorial consistency by completing missing tags, normalizing date formats, and adding a front matter audit script for future safety.
Date: 2026-04-25 10:40AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 4
Lang: en
Translation_Key: praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint
Image: /assets/images/day04-comfyui-sdxl-phase3-metadata-taxonomy-frontmatter-hero.webp

---

# Reviving Praderas (Day 4) — Phase 3: cleaning metadata without drama

Some days are feature launches, others are kitchen cleanup. Phase 3 was the second one: editorial consistency over shiny UI.

## What we found during the audit

Before changing content, we audited front matter in `content/blog` and found:

- 13 posts missing `Tags` (or using lowercase `tags`),
- one outlier date format (`2023-08-14 9:00`),
- canonical taxonomy defined but not automatically enforced.

The content was valuable, but hidden metadata debt would eventually hurt filters, SEO, and category navigation.

## What we implemented

1. **Completed missing tags** on the 13 pending posts using existing taxonomy.
2. **Normalized key casing** (`tags` -> `Tags`) where needed.
3. **Standardized date formatting** for the outlier case.
4. **Added audit script** `scripts/frontmatter_audit.py` to validate:
   - required fields,
   - date format,
   - tags outside canonical set.
5. **Documented editorial template** in `.agents/post-template.md`.

## Why this matters

Inconsistent metadata often "works" until it quietly fails: partial filters, weak related-post blocks, and blind editorial choices.

This pass improved baseline reliability: **fewer surprises, more predictable behavior**.

## Day 4 closing

Phase 3 is not flashy, but it prevents compound pain. No new screen today; we shipped **content discipline**.
