---
Title: Tuqan — Agentic Lessons (11): Aplicación Fully Navigable (Profiles, Companies, Menus, Languages, and Permissions)
Description: Closing the vertical slice for the Aplicación section under Administration. Full Perfiles, real Empresas, and modern pages for Menus, Idiomas, and Permisos. The entire submenu is now navigable before we dive into POST handling.
Date: 2026-06-02 10:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-stage-8-5-aplicacion-navegable
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 11
Image: /assets/images/tuqan-stage-8.5-aplicacion-hero.webp
---

In the last leg we closed an important cycle inside the **Administration → Aplicación** branch.

After having Profiles and Empresas working with the modern layout, the natural next step was to finish the remaining entries that already existed in the legacy menu:

- **Menus**: now displays the real structure (`menu_nuevo` + translations) in a readable way.
- **Idiomas**: simple listing + basic create/edit (respecting the limited scope we defined: only availability and selection for now).
- **Permisos**: profile overview page with the door open for the future menu permission assignment matrix.

The outcome is that the entire **Aplicación** submenu is now fully navigable with the collapsible sidebar, the red header bar, and the modern page pattern we've been using since Usuarios.

### Why finishing this before POSTs mattered

One lesson that keeps repeating in this project is that **being able to navigate matters more than it looks at first glance**.

As long as pages kept falling into Placeholder, it was hard to feel the real weight and hierarchy of the legacy menu. Having real pages (even if only GET for now) suddenly made several things click:

- The workflow of "open the menu and get where I want" actually works.
- The recent sidebar UX improvements (persistent accordion state + collapsed by default) became immediately valuable while testing these new screens.
- It becomes obvious which parts of the legacy system still depend on the old form logic and which ones we can start replacing in an orderly fashion.

Closing this vertical slice also gave us a very clean natural stopping point before jumping into full POST, validation, and create/edit flows.

### Small side lessons

- The Docker entrypoint change to automatically compile `.mo` files from `.po` on every container start emerged naturally while working on the Idiomas section.
- The menu state persistence fix we shipped recently proved its real value here: being able to expand "Aplicación", move between Perfiles, Empresas, Menus, etc., and come back without everything collapsing is surprisingly pleasant when you're bouncing between several screens in a row.

### Next steps

With Aplicación fully navigable, the next big block will be turning on forms (POST + validation) for these same modules. Perfiles and Empresas are the most natural candidates to start with, since they unlock real work on the Usuarios side.

As always, the menu continues to be the compass.

---

This article complements the work on the `feat/stage-8.5-profiles-empresas-personalizacion` branch of the Tuqan repository and the updated documentation in:

- [.agents/STAGE-CHECKLISTS.md](https://github.com/laanito/tuqan/blob/master/.agents/STAGE-CHECKLISTS.md) (Stage 8.5 section)
- [.agents/MIGRATION-PLAN.md](https://github.com/laanito/tuqan/blob/master/.agents/MIGRATION-PLAN.md)

The corresponding PR on the Tuqan side will be opened/merged shortly with the full set of changes from this stage.
