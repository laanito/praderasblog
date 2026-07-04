---
Title: Tuqan — Agentic Lessons (16): The Base Consolidates and Human Verification Comes Back Clean
Description: After several autonomous stages since 9.3, the first full human verification pass on the accumulated Tuqan work found no breakages and no oddities. Summary of modernized modules and the cross-cuts that strengthened the shared infrastructure.
Date: 2026-07-02 09:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Inteligencia Artificial, Productividad, Sistemas
Lang: en
Translation_Key: tuqan-agentic-lessons-16-base-consolidates-clean-verification
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 16
Image: /assets/images/tuqan-agentic-lessons-16-base-consolidates-clean-verification-hero.webp

---

After several stages of autonomous work since stage 9.3, the user paused the cycle to perform the first complete human verification of the accumulated work. The result: everything looks great, with no breakages and no oddities.

### Context

Since Equipos landed in 9.3, the project has followed the strict ritual documented in `.agents/MIGRATION-TODOS.md`, `MIGRATION-PLAN.md`, and `STAGE-CHECKLISTS.md`: read the TODOs first, detailed plan committed first, Docker-only, update living docs, clean verification, and PR.

The autonomous mode (the agent runs complete legs, the human only merges) has continued with minimal intervention, exactly as explored in previous posts in the series.

### What was built since 9.3

**Modernized verticals (Pages + templates + modern /admin/* routes):**

- 9.4: Mejora (Acciones de Mejora) — basic CRUD with custom fields (description, date, closed status, cost, analysis/treatment).
- 9.5: Formación (Planes) + initial Documentación shell.
- 9.6: Auditorías (annual audit program) — core fields name/vigente/revision/activo.
- 9.7: Aspectos Ambientales — basic CRUD with scores (magnitud, gravedad, frecuencia) + area and type.
- 9.9: Indicadores — basic CRUD with definition, values, responsables, and frequencies.
- 9.10: Procesos basic — core catalog (name, code, revision, padre, activo).
- 9.11: Procesos deeper — Árbol/tree view with padre hierarchy + contenido_procesos summaries; the legacy arbol route now serves the modern version.
- 9.12: Documentación — first tree slice (grouped tree view, dedicated page `/admin/documentacion/arbol`).
- 9.14: Aspectos — modern matrix view (grouped by area with all evaluation scores).

**Cross-cuts (the part that pays the most long-term):**

- 9.8: Catalog base extraction (CatalogListado + CatalogFormulario with helpers for getDb, fetchItems, build variables, load/persist, etc.). Everything since has been built on this.
- 9.13: Tree-specific helpers (resolveParentNames, groupItems, initTwig, buildCommonVariables).
- 9.15: List filters (`getFilterParams`, `fetchFilteredItems` with support for activo and others) + form relations (`loadRelated`, `getRelatedLabel`).
- 9.16: Full tree base (`CatalogTree` abstract class). The two current Árbol views (Procesos and Documentación) now extend this base and are smaller and more consistent.

### Why these cross-cuts matter

Without them, every new vertical would have duplicated a lot of DB logic, Twig variables, flash handling, and sidebar code. With the matured base, adding a new view (matrix, tree, filtered list) is much cheaper and less prone to inconsistencies.

The pattern has been to alternate verticals with cross-cuts to avoid accumulating technical debt while moving forward.

### Impact of the human verification

The user reviewed the accumulated state after the autonomous stages. Result: no breakages, no oddities. The modern flows (lists, forms, trees, matrix) work as expected, legacy routes continue to respond where appropriate, and the shared infrastructure (catalog helpers, filters, relations, tree base) feels solid.

This validates that the autonomous mode + non-interactive verification (verify-8.6.sh + playbooks) + human gate after several legs works well for generating verifiable mass.

### Scope and what is not done

Important parts remain legacy:
- Complete workflows (closing Mejora actions, audit findings, approvals).
- Questionnaire engine (used by Aspectos and Auditorías).
- PDF/Excel generation (GenPDF, crearExcel).
- Advanced tree editing and other deep entities.

The deliberate focus has been shells + cross-cuts first, complete workflows later.

### Next steps

The current TODOs suggest mixing:
- More cross-cuts (relations polish).
- Or deeper verticals: full Mejora integration, Formación subs, Auditorías execution, etc.

The clean verification gives confidence to continue the cycle or pause as convenient.

### Related reading

- [Tuqan — Agentic Lessons (13): 100% Autonomous Work Without Babysitting](https://blog.praderas.org/blog/en/tuqan-agentic-autonomous-legs-cron)
- Previous posts in the Tuqan — Modernización series (see the Series widget in the sidebar).
- `.agents/MIGRATION-TODOS.md` in the Tuqan repo for the current state and suggested next slices.

All the work continues to follow the strict ritual: plan first, Docker-only, living docs updates, reproducible verification.

---

*Series: Tuqan — Modernización*