---
Title: Tuqan — Post-9.40 plateau: user QA checklist, three P0s, and why the verify script is not enough
Description: After Proveedores homologación (9.40), we ran a human checklist over modern /admin/*: smoke, journeys, and shells. The non-interactive verify script passed with patches 0001–0050; the browser found three P0 blockers (Mejora, ejecución list, areas table). How we structured QA, what we fixed, and what stayed out of the merge.
Date: 2026-08-20 07:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-post-9-40-user-qa-plateau
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 17
Image: /assets/images/tuqan-post-9-40-user-qa-plateau-hero.webp
---

Tuqan is a legacy ISO 9001/14001 management app we are modernizing with a **strangler fig**: new routes and pages under `/admin/*`, Postgres via Docker, and a numbered **data-patches** path instead of a big-bang rewrite. After merging **Proveedores homologación (Stage 9.40 / PR #112)** and the **post-9.40 user checklist (PR #113)**, the plan itself asked for something before heavy GenPDF, WYSIWYG, or deep verticals: a **human QA pass** on the current plateau.

This post is that pass: how we designed it, what the browser found that `verify-8.6.sh` cannot see, the three P0s fixed in [PR #114](https://github.com/laanito/tuqan/pull/114), and what we deliberately left as notes. It complements living docs in the Tuqan repo: `reference/USER-QA-CHECKLIST-post-9.40.md`, `.agents/STAGE-CHECKLISTS.md`, and `.agents/BLOG-POSTING.md`.

### Context: what the 9.40 “plateau” means

Through the 9.x stages, day-to-day work has focused on modules that are **walkable end-to-end** in modern UI: Documentación (workflow + text + attachment), Auditorías (execution, findings, schedule, HTML report), Mejora, Formación (plans ↔ courses ↔ enrollments), Equipos (revisions, calendar, preventive plan), and Proveedores (homologation, products, criteria). Other modules (Aspectos, Indicadores, Procesos) are **shells**: list and basic edit only.

That is intentional. If the plateau cannot survive a 45–90 minute user pass, GenPDF or a WYSIWYG editor only multiplies debt on routes you still cannot trust.

### Why a user checklist on top of the verify script

Earlier deliveries formalized a Docker **Test + Fix Loop**: `php -l`, `init-db` + **data-patches**, table/row asserts, and CI running verify. That is necessary and non-negotiable. It is **not enough** for a business CMS:

- The script does not log in or POST workflow actions.
- It does not catch misnamed Twig variables (empty list while the DB has rows).
- It does not catch a wrong PHP `use` that only blows up on a POST action.
- It does not catch a full-legacy schema table the **minimal + patches** path never created.

So the post-9.40 checklist splits **P0** (must pass or you have a blocker), **P1** (log and prioritize), and shells that may **soft-pass** if they open and save the minimum. Plateau criterion: **all P0s Pass**; open P1s do not block the next feature if small fix PRs are planned.

### How we ran the pass (agent + browser + authenticated fetch)

1. **Environment prep** — containers up, app on `localhost:8080`, green `verify-8.6.sh`, demo login (`demo` / `admin` then user `admin` / `admin`).
2. **Smoke** — GET every modern list (`/admin/documentacion`, ejecución, formación, equipos, proveedores, shells…). Fail only on white screen, 500, or route-not-found.
3. **Journeys A–E** — Documentación, Auditorías↔Mejora↔Informe, Formación, Equipos, Proveedores: filters, `?fk=` prefills, create rows, flashes, downloads, state transitions.
4. **Shells + cross-cut** — edit one aspecto/indicador, empty-name validation, friendly empty filter.
5. **Classify and fix P0s** — not report-only: three root causes in code + one data patch, browser re-check, PR.

Operational detail: the sidebar still points at **legacy routes** in many places; plateau QA is done via `/admin/*` URLs on purpose. That is product P1 (menu/authz), not a smoke failure if the module is reachable by URL.

### Migrations finding: 50/50 applied and a schema gap

Before touching the Documentación P0, we compared **disk** (`docker/db-init/data-patches/*.sql`) with Postgres **`data_patches`**. Result: **0001–0050 fully applied**. No stuck migration queue.

The **`areas`** table *does* exist in the full schema dump (`00-schema.sql`) and old production legacy, but the modern path (minimal schema + patches) **never created it**. Demo documents already had `area = 1` and `2`. The modern form called `getRelatedOptions('areas')` with no safety net → SQL exception on edit. Re-running 0050 does not fix that; you need to **add the catalog on the patches path** (and optionally soft-fail if the table is still missing mid-migration).

### The three P0s (root cause → fix)

| Symptom | Root cause | Fix (PR #114) |
|---------|------------|----------------|
| Mejora **Verificar** / **Cerrar** → PHP fatal | `Config::initialize()` under namespace `Tuqan\Pages\Mejora` without `use Tuqan\Classes\Config` | Correct import |
| `/admin/auditorias/ejecucion` always empty | Nested `templateDir` → Twig key `auditorias/ejecucion`; template expects `auditoria` | Alias in `buildListVariables` (same pattern as Hallazgos/Cursos) |
| Edit document → `relation "areas" does not exist` | Legacy catalog missing on minimal path | Patch **0051** + soft-fail options/labels |

Post-fix re-check (authenticated session): ejecución list with three rows and counts; Documentación form with content and attachment; save text and reopen; Verificar/Cerrar with success flashes. `verify-8.6.sh` extended to assert `areas` and patch 0051.

### What passed (and what is not a “product bug” yet)

- **Formación, Equipos, Proveedores homologación**, and most cross-cuts (flash, validation, prefills) behaved once **Twig cache** (`templates/cache/`) was cleared. Stale compiled templates can hide already-merged UI: an ops note, not a feature gap.
- Documentación **list-side** workflow (send to review → review → approve) and binary download worked **before** the edit form was fixed: the P0 was the editor, not the state machine.
- Auditoría informe (edit + HTML ficha) usable; print CSS was not fully certified (optional P1).
- **Deshomologar** without a confirm dialog: UX, not a crash.
- Legacy sidebar: conscious P1.

### Why this matters for agentic work

Earlier posts in this series covered agentic loops (checklist → implement → verify → PR) and lessons when an agent “passes” without standards. This pass closes the loop from the other side: a **human (or an agent in QA role) must walk the plateau as a user** before the next vertical.

An agent that only runs `verify-8.6.sh` would have called the plateau green with three live P0s. An agent that also runs the checklist (browser or session-cookie fetch) finds what a real user would find on Monday morning. That is part of the “second Q&A agent” we already talked about: not only diff review, but **product smoke**.

### Scope and next work

**Inside hotfix #114:** the three P0s, patch 0051, verify asserts.  
**Outside (documented in repo QA notes, not plateau blockers once P0s are green):** menu → `/admin/*`, confirm on deshomologar, Twig cache hygiene on deploy, GenPDF / contactos / WYSIWYG depth.

Plateau decision after the fix: **Go with fixes** (now merged), then pick **one** roadmap leg—not three at once.

### Reproduction (operators)

After pulling #114 on an already-initialized environment:

```bash
# Apply 0051 if data_patches does not list it yet
docker compose --env-file .env.docker exec -T db psql -U qnova -d qnova \
  -v ON_ERROR_STOP=1 -f - < docker/db-init/data-patches/0051-areas-table-and-seed.sql

docker compose --env-file .env.docker exec app ./scripts/verify-8.6.sh
# Optional after template deploys:
# docker compose exec app sh -c 'rm -rf /var/www/html/templates/cache/*'
```

Human checklist: `reference/USER-QA-CHECKLIST-post-9.40.md` in the Tuqan repo. Full clean-room: `down -v`, `up`, `./scripts/init-db.sh` (applies 0001…0051 in order).

### Closing

The post-9.40 plateau is not defined only by “there is modern code.” It is defined by **walking Documentación, Auditorías, Mejora, Formación, Equipos, and Proveedores without white screens or fatals**, with a green DB script **and** a user checklist. Verify told us the patches were applied; user QA told us where the product still lied. Fixing that in a small PR before the next feature is exactly the discipline this modernization needs if progress is going to stay real.
