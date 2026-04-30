---
Title: Reviving Praderas (Day 9) — translation migration: batch plan, completed batch 1, and time estimates
Description: We kicked off the ES→EN migration with an 8-batch plan, completed batch 1 for the full Reviving Praderas series, and documented transparent time estimates.
Date: 2026-04-30 11:55AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 9
Lang: en
Translation_Key: praderas-day-9-translation-migration-batch-1
---

# Reviving Praderas (Day 9) — translation with method: plan, batch 1, and numbers

Today we formally started English content migration with one principle: **coherence before raw speed**.

## What we shipped today

1. **Batch migration plan** in `.agents/translation-migration-tracker.md`, split into 8 batches to avoid context overflow.
2. **Batch 1 completed**: full **Reviving Praderas** series paired in EN (Day 1 to Day 8), with no half-translated chapter sequence.
3. **Glossary updates** for recurring naming choices to keep navigation and tone consistent.
4. **Translation metadata hardening** (`Lang` + `Translation_Key`) for Day 1 to Day 7 so language switcher and `hreflang` behave on complete pairs.

## Why this batching model

Page-by-page partial translation often breaks reader flow: mixed-series chapters, inconsistent naming, and unstable tone.

With thematic batches and a living glossary:

- readers do not hit half-translated chapter paths,
- repeated terms stay stable,
- review cost drops as each batch reuses prior decisions.

## Time estimate (AI-assisted PR flow vs traditional localization)

Reasonable estimate for **today's scope** (plan + batch 1 + wrap-up article + PR):

- **AI-assisted workflow (this work):** ~**1.5 to 2.5 hours** elapsed.
- **Localization specialist plus classic editorial pipeline:** ~**8 to 14 hours** for equivalent coverage, terminology consistency, and metadata checks.

This is not a universal benchmark. It depends on context, tooling, and review depth. In this repository, with strong in-repo context, the timeline compression is substantial.

## What comes next

The next logical block is **Batch 2**: the full **Control de Tiempo Desacoplado** series (13 chapters), following the same rule of not splitting a series in half.
