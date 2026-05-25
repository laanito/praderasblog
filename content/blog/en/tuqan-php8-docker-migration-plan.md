---
Title: Tuqan — PHP 8 migration plan, Docker-only dev, and test harness
Description: Why upgrading a PHP 5.1 app with PEAR and later patches is not “bump the version and ship”, and what we decided after merging the executable plan in Tuqan (PR #44): Docker-only, PHP 8.3, and tests in containers.
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

# Tuqan — from audit to a plan you can actually run

**“Some projects do not need to be born again — they need a second life.”** We said that in [Phase 0](https://blog.praderas.org/blog/en/tuqan-phase-0-strategic-foundation-audit-and-roadmap). This post goes one step further: not only *what* Tuqan is, but **why** the decisions in the plan just merged into the application repository make sense if you did not live through PHP’s 2005–2015 era.

This article **does not replace** the code or checklists in [github.com/laanito/tuqan](https://github.com/laanito/tuqan). Here we explain the reasoning; there lives what you execute.

---

## What Tuqan is (without assuming you read Phase 0)

**Tuqan** is a web application built to support quality and environmental management aligned with standards such as **ISO 9001** (quality) and **ISO 14001** (environment). In practice: records, reports, workflows, and data an organisation must keep auditable for years — not a weekend side project.

It was started around **2005** on **PHP 5.1**, when many enterprise PHP apps looked like this: a fixed interpreter on the server, libraries installed by hand or via **PEAR**, and code that mixed business logic, database access, and HTML in the same files.

In 2026 that stack **does not start reliably**. The modernisation programme is not “throw away twenty years of business rules and build something else”, but **evolve the app** with current tools and practices — including agent-assisted work documented in the open, like the rest of the Praderas ecosystem.

---

## A map of the “archaeological” stack (so the decisions make sense)

If you were not close to mid-2000s PHP, this short map covers the pieces that show up in Tuqan’s audit and in [PR #44](https://github.com/laanito/tuqan/pull/44):

| Piece | Plain-language meaning | Why it matters for Tuqan |
|-------|------------------------|---------------------------|
| **PHP 5.1** | Language version from ~2005. Lacks many protections and conventions we take for granted today. | Much of the original code assumes that era (references, notices that are now fatal errors, APIs removed later). |
| **PEAR** | Old PHP “package manager” (a predecessor to today’s Composer). Installed libraries into global server paths. | Remaining PEAR dependencies do not **travel cleanly** across machines or PHP versions. |
| **Composer** | Modern dependency manager (~2012 onward). Declares libraries in `composer.json` and installs them under `vendor/`. | Tuqan has **partial** Composer modernisation, but it coexists with code never written for that model. |
| **PSR-4** | Naming convention: “this class lives in this file under this namespace”. Enables orderly autoloading. | Mixing 2005-style files with PSR-4 classes without a careful pass yields **classes that never load** or duplicate paths. |
| **PDO** | Standard layer for talking to databases (PostgreSQL, MySQL, etc.), replacing old extensions (`mysql_*`, etc.). | Migrating tightly coupled queries is delicate; mistakes here break entire ISO reports. |
| **Phroute** (or similar) | Library that maps URLs to controllers/functions instead of “one `.php` file per screen”. | Adding modern routing on top of legacy folders requires knowing **which URLs still matter** and what code actually runs. |

You do not need to memorise the table. It explains **why “install PHP 8 on my laptop” is not a plan**.

---

## Why it is not “PHP 5 → 8” in one jump

Marketing likes “upgrade to PHP 8”. Engineering for Tuqan crosses **several cliffs**; skipping them is what fails most often.

### 1. PHP 5 → PHP 7: the break many people forget

**PHP 7** (2015) was not “5.7”: it was a new line with **backward-incompatible** changes versus PHP 5.6. Legacy codebases repeatedly hit:

- Code that relied on permissive behaviour (undefined variables, weak comparisons in critical paths).
- Extensions and functions **removed** or moved.
- Issues that were warnings and now **stop** execution.

If the code never ran cleanly on PHP 7, jumping straight to 8 only **bundles** failures and makes root cause opaque.

### 2. PHP 7 → PHP 8: another step with sharp edges

**PHP 8** added more deliberate breaks (types, comparisons, string/number behaviour, deprecations that become errors). For an app with reports, PDFs, sessions, and routes accumulated over years, the risk is not “a line in the log”: it is **blank screens** or wrong data in a compliance context.

### 3. The strategy we chose: stages, fixed environment, tests

So the Tuqan repo plan does not say “upgrade PHP and merge”. It says something duller and safer:

1. **Freeze the environment** (Docker) so everyone — humans and agents — sees the same thing.
2. **Measure what exists** (audit started in Phase 0).
3. **Upgrade and fix in phases**, with **automated tests** before touching the whole UI again.

That is less flashy than a rewrite, but it is what preserves ISO-related logic that is not fully documented outside the code.

---

## What [PR #44](https://github.com/laanito/tuqan/pull/44) added to the Tuqan repo

Until Phase 0 we had intent and a pending audit. With the PR merged to `master`, the application repository gains **actionable documentation** (not slides only):

| Document | Purpose |
|----------|---------|
| `AGENTS.md` | Rules for anyone touching the repo: **Docker only**, update docs before stray code changes, use checklists. Stops an agent or developer from “fixing” something on a local PHP and creating a false positive. |
| `MIGRATION-PLAN.md` | Real code state + plan sections 5.1 / 5.2 / 5.3 + an **eight-stage roadmap** with exit criteria. |
| `DOCKER-ENV.md` | Dockerfile, `docker compose`, nginx, PHP **8.3**, PostgreSQL — copy-paste ready, not aspirational. |
| `TESTING-HARNESS.md` | How to run **PHPUnit** (and PHPStan) **inside** the container, not on the host. |
| `STAGE-CHECKLISTS.md` | Per-stage lists with validation commands you can paste into a PR description. |

PR title: *docs(agents): PHP 8 + Docker-only dev environment + testing migration plan*.

**Explicit decision:** implementation Stage 1 does not start by rewriting Bootstrap screens; it starts when `docker compose up` and the first smoke test are green and recorded.

---

## Why **Docker-only** development (with PHP 8.3 inside)

On greenfield apps, teams often mix “whatever PHP my Mac has” with Docker in production. For Tuqan that is especially risky:

- History includes **PEAR**, absolute paths, extensions, and versions that **do not match** Homebrew, apt, or old hosting PHP.
- Two people on “PHP 8” with different extensions and `php.ini` get different results from the same `git clone`.
- AI agents do not share a standard laptop; they need a **reproducible contract**: same image, same volumes, same database.

**Hence the Docker-only rule:** nginx + PHP 8.3 + PostgreSQL via Compose. The front door for humans and agents is `docker compose up`, not “install PHP locally and try”.

What we want when something fails: debate **code and plan**, not whether someone had `mbstring` enabled on the host.

---

## Why tests (PHPUnit) before “making it pretty again”

Bootstrap 5 and cleaner templates already appeared in earlier patches; end users can feel an “modern app” while the **core still breaks** on a PHP version change.

**PHPUnit in Docker** is not academic luxury here:

- It is the safety net when we touch authentication, report generation, and Phroute routes.
- It lets a PR say “harness passed” with the same meaning for everyone.
- It aligns with ISO in spirit: **evidence** that a change did not break critical paths, not only “worked on my machine”.

Rewriting UI without that net often produces the worst outcome: something that **looks** fine in a demo and fails on the first real report.

---

## How this blog fits the Tuqan repository

| Where | What we publish |
|-------|-----------------|
| **blog.praderas.org** (here) | Milestones, decisions in prose, links to PRs — transparency for the Praderas programme. |
| **Tuqan repo** | Audit reports, Dockerfiles, checklists, issues — what an implementer or agent must execute literally. |

If you need copy-paste commands for Stage 1, they live in `DOCKER-ENV.md` and `STAGE-CHECKLISTS.md` in the repo, not duplicated here.

---

## What happens next (Stage 1 — Docker foundation)

On a feature branch in the Tuqan repo:

1. Bring up the stack from `DOCKER-ENV.md`.
2. Confirm the PHP container responds (even if the app still fails on legacy code).
3. Record the **baseline** in `STAGE-CHECKLISTS.md`.

When that stage closes with evidence (what worked, which readable error appeared first), we will post again in this series — same criterion: **context for humans**, technical detail in the repository.

---

## Related reading

- [Tuqan Phase 0 — foundation and audit](https://blog.praderas.org/blog/en/tuqan-phase-0-strategic-foundation-audit-and-roadmap) — why the series started.
- [PR #44 on GitHub](https://github.com/laanito/tuqan/pull/44) — diff and discussion for the merged plan.
- [Reviving Praderas (Day 25)](https://blog.praderas.org/blog/en/reviving-praderas-day-25-tier-a-complete-tuqan-for-ai-agents) — same-day blog log (Tier A, `/for-ai-agents`).
