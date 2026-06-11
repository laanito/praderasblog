---
Title: Tuqan — 9.2: Qwen + opencode Delivered the Stage (The TODOs Helped) but Failed to Hold the Standards
Description: Even with a fine-grained, prioritized backlog (MIGRATION-TODOS.md from stage 9.0) and a detailed stage plan, the local model + opencode managed to deliver functional Proveedores code, but failed to maintain the project's conventions: naming, use of catalog bases, .agents/ updates, legacy routes, and the verification ritual. Lessons on the value of structured TODOs versus accumulated standards discipline.
Date: 2026-06-10
Template: post
Author: Luis Amigo (with fixes by Grok 4.3)
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-9-2-qwen-standards
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 15
Image: /assets/images/tuqan-9-2-qwen-delivered-stage-but-missed-standards-hero.webp
---

### The Context

After the 9.0 leg (making the migration plan a usable daily todo list) and the small 9.1 hygiene win, the `MIGRATION-TODOS.md` was explicit about the next item:

> One real Aplicacion vertical (medium): Proveedores (listado + nuevo/editar ...). Follow 8.6-8.8 pattern (table in patch if new, Pages/ + templates/, full routes modern+legacy, POST Procesar, flashes, verify extension, playbook, update this TODOS + checklists).

The `stage-9.2-proveedores-plan.md` was written first on the branch (discipline followed). The catalog bases, naming conventions (Formulario.php everywhere), .agents/ updates, data patch format, verify script extensions, and full playbook sections in STAGE-CHECKLISTS.md were all well documented and recent.

A "new developer" (in practice: opencode + Qwen) was tasked with the leg.

### What the TODOs Enabled (Bright Side)

Qwen + opencode **delivered a functional stage**.

- They produced Listado + Form for Proveedores (including the extra 'telefono' column).
- Templates were created.
- A plan document was present.
- The branch naming was correct (stage-9.2-...).
- The structured, fine-grained, prioritized TODO list from 9.0 allowed the model to identify the scope and produce working code that passed basic functionality checks.

This is meaningful progress compared to the earlier "opencode_mess" (detached HEAD, untracked docker/ and scripts/, local PHP attempts, git chaos). The clear backlog + plan raised the floor. The TODOs literally "proved" that the model could now deliver the stage.

### What It Missed (Standards Failure)

Despite the improved scaffolding, the initial delivery violated core, long-standing project invariants:

- **Naming**: `Pages/Proveedores/Form.php` + `class Form` instead of the consistent `Formulario.php` / `class Formulario` used by every single modern module since the beginning of Stage 8.
- **Architecture regression**: The Form side completely ignored `CatalogFormulario` (the main achievement of 8.9) and re-wrote the full boilerplate (Twig setup, MainPage sidebar, Manejador_Base_Datos construction, flash handling, etc.). The Listado was correctly tiny; the Form was a full revert to pre-base patterns.
- **Process & ritual incomplete**:
  - No data patch for the `proveedores` table (the plan itself called for 0020-style patch).
  - Only modern `/admin/proveedores` routes; legacy `/administracion/proveedores/...` mappings missing.
  - `MIGRATION-TODOS.md` not updated (the checkbox remained unchecked).
  - No 9.2 section in `STAGE-CHECKLISTS.md` with the required playbook, validation commands, browser flows, and DB asserts.
  - `verify-8.6.sh` barely extended for the new table/patch.
- **Template and variable drift**: Custom flash handling and forward-looking notes that didn't perfectly match the established catalog template patterns.

The result: working functionality on the surface, but not something that belonged in this codebase without significant senior cleanup.

### The Real Learning

Clearer guidelines + fine-grained daily TODOs (exactly what 9.0 was built for) are powerful. They let even a local model "meet the bar" on delivering the stage where previous unstructured attempts had collapsed.

However, the accumulated standards — naming discipline, mandatory use of extracted bases, the full .agents/ + verification ritual, avoiding regression to old patterns — were still not held.

This is the persistent gap with current local models in complex, convention-heavy, long-running projects: they can follow explicit "what" instructions and produce happy-path code, but they default to plausible old patterns when the "how we do things here" knowledge is distributed across plans, checklists, past PRs, base classes, and historical decisions.

The structured backlog made the gaps extremely fast and cheap for a human (who already knew the standards) to diagnose and fix. That is the real value delivered by the 9.0 leg.

### Implications

- The TODO list + "plan first" + "update the living documents" ritual is necessary, but still requires strong review against the full body of conventions.
- This kind of exercise (give the model-assisted "new developer" a real item from the living MIGRATION-TODOS with the plan template) is an excellent filter for whether someone (or a model + human) can ship mergeable work without creating technical or process debt.
- We are at a point where local models + good scaffolding can get us most of a leg. The last 20-30% (the taste and discipline part) is where the leverage remains.

The TODOs did their job. The standards still need (human) enforcement for now.
