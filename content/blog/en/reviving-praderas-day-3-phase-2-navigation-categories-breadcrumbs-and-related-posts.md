---
Title: Reviving Praderas (Day 3) — Phase 2: navigation, categories, breadcrumbs, and related posts
Description: Phase 2 moved the site from a flat page list to intentional navigation with category mapping, breadcrumbs, related posts by tag, and chronological previous/next links.
Date: 2026-04-24 09:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 3
Lang: en
Translation_Key: praderas-day-3-phase-2-navigation-categories-breadcrumbs-related
---

# Reviving Praderas (Day 3) — Phase 2: making the site tell you where you are

Phase 1 stabilized core behavior: listing, search, pagination, and a useful sidebar. **Phase 2** moved to discovery: finding content without menu overload, and understanding where to go next from each post.

This is product work, not only CSS. The workflow stayed the same: **AI agent proposes and implements, human validates and prioritizes**.

## What Phase 2 meant in practice

1. **Four-item main menu** — Home, Blog, Categories, About. No automatic "every `.md` goes to navbar" behavior.
2. **Categories page** — each blog tag with a short Spanish description, approximate post count, and filtered-list link (`/tags/?tag=...`).
3. **Breadcrumbs** across blog, search, tags, and posts to avoid navigation guesswork.
4. **Discovery at post end** — related entries by shared tag, plus previous/next navigation by publication time.

The last point required server-side logic for maintainability.

## Technical decision for maintainability

We added a focused plugin, `plugins/50-BlogNeighbors.php`, that calculates:

- previous and next post neighbors,
- related posts,
- category counts for category-index rendering.

Small plugin, no database, easy to evolve.

## Scope honesty

Today focused on structure and comprehension, not every Priority 2 visual/polish task. Language unification and media upgrades stay in backlog and are explicitly documented in `proposed-improvements.md`.

## Day 3 closing

The blog feels less like a folder of files and more like a **place**: you know where you are, what topic you are reading, and what you may want to open next.

### Update (same day)

We also revised the homepage (`content/index.md`) to remove old messaging centered on ChatGPT as a direct reader "partner", because it no longer matched the project narrative. The current focus is the blog and repository workflow, with agents and tooling where appropriate and a human guiding direction.
