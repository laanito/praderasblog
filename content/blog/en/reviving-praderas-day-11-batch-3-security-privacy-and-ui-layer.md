---
Title: "Reviving Praderas (Day 11) — batch 3 (security & privacy), EN tag labels, and English About"
Description: Shipped the security/privacy/geolocation cluster, English display labels for canonical tags, /en/tags and /en/about-picocms, and agent docs for remaining multilingual UI gaps—with wall-clock vs senior-time comparison.
Date: 2026-05-02 12:15PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 11
Lang: en
Translation_Key: praderas-day-11-batch-3-security-ui
Image: /assets/images/day11-comfyui-sdxl-batch3-security-privacy-ui-hero.webp

---

# Reviving Praderas (Day 11) — what shipped and why

This is the **daily engineering log** for a single PR: **translation batch 3**, **multilingual UI gaps** (tags, sidebar copy, Pico “About”), and **agent-facing documentation** for follow-up work left out of this cut.

## Wall-clock honesty (same methodology as Day 9)

- **Session start (authoritative stamp for this run):** `2026-05-02 11:47:03 CEST`
- **Immediately before commit + push:** `2026-05-02 11:59:18 CEST`

Models do not experience wall-clock the way people do—**your** elapsed time is the meaningful productivity metric.

## Executive summary

1. **Batch 3 ES↔EN migration** — Six new pairs (`Translation_Key` values `praderas-b3-cs-intro`, `…-digital-world`, `…-cs-advanced`, `…-internet-not-safe-ii`, `…-social-privacy`, `…-geolocation`) matching the **security + privacy + geolocation-as-privacy** cluster from the migration plan.
2. **English-readable category tags without rewriting YAML** — Tags remain **canonical Spanish strings** in front matter (audit scripts, historical URLs). English UI uses a **display map** from `65-Multilingual.php`; query parameters stay stable (`?tag=Ciberseguridad`, etc.).
3. **`/en/tags`** — Paired hub (`Translation_Key: praderas-nav-tags`) with English chrome; filtered views show **only posts in the active language** so EN readers do not land in a mixed corpus.
4. **`/en/about-picocms`** — Paired with `acerca-de-picocms`; EN primary nav “About” no longer deep-links Spanish-only content.
5. **Templates** — `sidebar.twig`, `post.twig`, `categories.twig`, `tags.twig`, `breadcrumbs.twig`, and the `post.twig` footer credit block now respect `content_lang` where we had obvious gaps.
6. **`.agents`** — Added `multilingual-ui-backlog.md` and refreshed the tracker rows/changelog.

## Why these decisions

- **Thematic batching** — Keeps vocabulary and mental model consistent across related posts (same rule as batches 1–2).
- **Presentation-layer tag labels** — Rewriting tag keys across the archive would break tooling and inbound links; a map is cheaper and reversible.
- **Language-scoped tag filters** — An English reader on `/en/tags?…` expects **English posts**—consistent with `/en/categorias` counts.

## Senior “by hand” counterfactual (order-of-magnitude)

Rough band for a **senior full-stack engineer plus careful EN technical writing**, no assistant, same scope:

| Workstream | Indicative range |
|------------|------------------|
| Six post translations + tone/consistency pass | **8–18 h** |
| Twig/PHP wiring + manual smoke tests | **3–7 h** |
| About page pair + nav + `hreflang` sanity | **1–2 h** |
| ES/EN log posts + tracker + `.agents` updates | **1.5–3 h** |
| **Total** | **~13.5–30 h** across multiple calendar days |

Observed advantage of the assisted workflow here: on the order of **~12 minutes** between the two wall-clock stamps above for a comparable functional slice—**with** the usual caveat: accountability, publication judgment, and final acceptance remain human.
