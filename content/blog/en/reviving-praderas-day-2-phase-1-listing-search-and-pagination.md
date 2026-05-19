---
Title: Reviving Praderas (Day 2) — Phase 1: fixing listing, search, and pagination (with an AI agent)
Description: Phase 1 delivered a clean listing template, unified search behavior, restored pagination, and a meaningful sidebar recent-posts block.
Date: 2026-04-23 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 2
Lang: en
Translation_Key: praderas-day-2-phase-1-listing-search-pagination
Image: /assets/images/day02-comfyui-sdxl-phase1-listing-search-pagination-hero.webp

---

# Reviving Praderas (Day 2) — Phase 1: listing, search, pagination (with AI as copilot)

Yesterday we prepared the map. Today we executed **Phase 1** from `.agents`: fix the listing template, unify search behavior, bring pagination back, and replace the old sidebar placeholder.

The thesis remains the same: **an AI agent can execute effectively** when the repository has clear context and priorities, then a human reviews and deploys.

## What was broken (or weak)

In the blog listing, HTML leaked at the end of the document: script leftovers and even a second `<!DOCTYPE` in the rendered page source. That was not only ugly; it signaled a **corrupted or truncated** template.

At the same time, listing-page search was wired differently from other pages, and the old sidebar widget still carried placeholder tutorial copy.

## What we shipped in Phase 1

1. **Rebuilt** `blog.twig` with a clean document close.
2. **Unified** search wiring and IDs (`search_input` / `search_submit`) through a shared partial so click and Enter both route to canonical search.
3. **Restored** pagination on `/blog`, including Spanish labels and current-page status.
4. **Replaced** the sidebar placeholder with a **recent posts** block (five newest entries under `blog/`).
5. **Centralized** sidebar rendering in reusable `sidebar.twig` so base layout, posts, and listing do not silently diverge.

We also left pagination copy in `config/config.yml` documented for future direct use.

## Recent-posts block and human feedback

This part matters because it showed a common pattern: an agent can close technical tasks and still miss subtle UX quality. The first version worked, but links looked too plain.

Human feedback triggered a second PR that refined markup and CSS so hierarchy, hover, and focus states felt intentional. The current status is honest: **much better, not final-perfect, and good enough to keep momentum**.

## Why process documentation matters

Template and plugin changes in a flat-file site are easy to break in the next redesign if there is no trace for the next contributor (human or agent). That is why we updated `.agents` context with all Phase 1 decisions.

## What is next

Phase 2 focuses on navigation and discovery: categories, related posts, breadcrumbs. Today set that stage: stable listing, reliable search, visible pagination, and a useful sidebar.
