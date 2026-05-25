---
Title: Tuqan — PHP 8 migration plan, Docker-only dev, and test harness
Description: After merging the executable plan in the Tuqan repo (PR #44): 100% Docker development, PHP 8.3, PHPUnit in containers, and an eight-stage roadmap with agent checklists.
Date: 2026-05-25 11:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad
Lang: en
Translation_Key: tuqan-php8-docker-migration-plan
Series: Tuqan Modernization
Series_Slug: tuqan-modernization
Series_Order: 2
Image: /assets/images/tuqan-comfyui-sdxl-php8-docker-migration-plan-hero.webp

---

# Tuqan — from audit to an executable plan

In [Phase 0](https://blog.praderas.org/blog/en/tuqan-phase-0-strategic-foundation-audit-and-roadmap) we set context: Tuqan is a legacy ISO 9001/14001 app that **does not run today** and must evolve without discarding business logic. This post records the next milestone in the **application repository**, not a substitute for the code.

## What landed in Tuqan (PR #44)

The team merged an **actionable** documentation pack for humans and agents:

| Document | Role |
|----------|------|
| `AGENTS.md` | Strict operating rules (Docker only, doc-first, checklists) |
| `MIGRATION-PLAN.md` | Current-state audit + sections 5.1/5.2/5.3 and an **8-stage** roadmap |
| `DOCKER-ENV.md` | Dockerfile, compose, nginx, ready-to-copy workflows |
| `TESTING-HARNESS.md` | PHPUnit (and PHPStan) **inside** Docker |
| `STAGE-CHECKLISTS.md` | Per-stage lists with validation commands |

Reference: [github.com/laanito/tuqan/pull/44](https://github.com/laanito/tuqan/pull/44) — *docs(agents): PHP 8 + Docker-only dev environment + testing migration plan*.

## Why Docker-only and PHP 8.3

Tuqan started on PHP 5.1 and PEAR; later came partial modernisation (Composer, PSR-4, Phroute, PDO…). Mixing the host PHP with that history **hides production failures** and makes audits non-repeatable.

**Decision:** development and automated tests run **only in containers** (PHP **8.3**, nginx, PostgreSQL). No ad-hoc local services: `docker compose up` is the front door for every agent and developer.

## Why tests before rewriting screens

The migration does not start by restyling Bootstrap. It starts by **learning what breaks** when the runtime upgrades and dependencies are rationalised. PHPUnit in Docker is the safety belt for stages touching auth, ISO reporting, and Phroute.

## How this blog relates to the repo

- **Here:** milestones, decisions, and links (Praderas transparency).
- **On github.com/laanito/tuqan:** reports, checklists, and the plan agents must follow literally.

## Next step in Tuqan

**Stage 1 — Docker foundation** on a feature branch: bring up the stack from `DOCKER-ENV.md`, confirm the PHP container responds, and record the baseline in `STAGE-CHECKLISTS.md`. We will post again when that stage closes with evidence (commands and lessons, not ticks alone).
