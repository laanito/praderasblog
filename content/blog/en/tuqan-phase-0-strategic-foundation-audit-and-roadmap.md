---
Title: Tuqan Phase 0 — Strategic foundation, audit, and initial roadmap
Description: Kicking off the agentic modernization of Tuqan with living documentation, an audit plan, and a prioritized roadmap. First post in the Tuqan series.
Date: 2026-05-06 10:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas
Lang: en
Translation_Key: tuqan-phase-0-strategic-foundation
Series: Tuqan Modernization
Series_Slug: tuqan-modernization
Series_Order: 1
Image: /assets/images/tuqan-phase-0-strategic-foundation-hero.webp

---

# Tuqan Phase 0 — strategic foundation, audit, and initial roadmap

**"There are projects that don't need to be born from scratch—they need a second life."**

This marks the start of **Phase 0** of the Tuqan modernization, as a second major workstream in the Praderas ecosystem.

## Project context

Tuqan is a legacy ISO 9001 / ISO 14001 management application (circa 2005): PHP 5.1 and PEAR alongside partial modernizations (Composer, PSR-4, Phroute, Bootstrap 5, PDO, etc.). In its current state the application is **not functional**.

The goal is not a full rewrite, but to **evolve it while preserving business logic** and applying modern standards.

## Agentic approach and "documentation first"

- Agent-oriented documentation under `.agents/`, following the same pattern we already describe on this blog (for example `repo-context.md`, `proposed-improvements.md`, `phase-5-6-plan.md`).
- Each session on a new branch; changes reviewed via PR.
- No application code until Phase 0 scope and roadmap are settled.

## This blog vs. the Tuqan repository

Tuqan-specific audit deliverables (reports, checklists, and detailed technical roadmap) live in the **application repository**. Here we publish **milestones** and decisions as articles, consistent with the project's transparency policy.

## Tooling friction (GitHub / agents)

During preparation we hit typical integration limits (partial JSON responses, SHA mismatches when updating files, sequential pushes). That's an honest part of the story: agentic workflows still have operational friction.

## Audit plan (Phase 0)

- Legacy file and folder layout.
- Composer dependencies and obsolete packages.
- Separation (or mixing) of business logic and presentation.
- Security surface (PEAR remnants, dated PHP practices).
- Actual functionality status.

## High-level roadmap

- **Phase 0:** Strategic foundation and audit (current).
- **Phase 1:** Dependency cleanup and PSR alignment.
- **Phase 2:** Architecture modernization (routing, DI, templates).
- **Phase 3:** Business logic extraction and testing.
- **Phase 4:** UI/UX refresh and mobile.
- **Phase 5:** Deployment and monitoring.

## Next steps

Land this post on the blog's canonical URL, run the audit in the Tuqan repo, and follow up with findings in the next article.
