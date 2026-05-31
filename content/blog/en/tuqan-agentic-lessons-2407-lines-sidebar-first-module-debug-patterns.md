---
Title: Tuqan — Agentic Operation Lessons (7): 2407 Lines, Collapsible Sidebar, First Real Module, and Two Debugging Patterns We Keep Repeating
Description: We imported the entire real legacy menu (~106 items + 212 labels), diagnosed its real scale live in the container, chose a left collapsible sidebar when horizontal proved it couldn’t scale, delivered the first real module (Usuarios: listing + forms), and —most importantly— documented two recurring agent anti-patterns in public: blaming opcache with zero proof, and getting stuck on Phroute route syntax. Hard lessons, excellent pace.
Date: 2026-06-01 09:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-2407-lines-sidebar-first-module-debug-patterns
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 10
Image: /assets/images/tuqan-2407-lines-sidebar-first-module-debug-patterns-hero.png
---

# Tuqan — When Real Menu Volume Forces Real Architectural Decisions

In the previous article we told the story of how an hour spent hunting a session bug turned out to be a classic red herring while the actual technical debt (2007-era menu generator hardcoding `localhost` connections) was sitting right in front of us. We closed that session with a “working” menu built on a small curated subset of data.

This session was different. The user was explicit from the first message:

> “add the whole menu to the init db script we want the whole menu in the app”

No more subsets. We needed to see the real beast: over 100 rows in `menu_nuevo`, 212 rows in `menu_idiomas_nuevo`, nested hierarchies, colon-separated actions, textual permission arrays, mixed casing, and parentage surprises that only appear when you load everything.

The result: 2407 lines changed in a single PR (#60), the full real menu visible and usable, a left collapsible sidebar that actually solves the scale problem, the first real module (Usuarios) working as a vertical slice, and —most valuable for this series— two agent debugging patterns dragged into the light with all their ugliness.

## The Experiment That Only Real Volume Can Run

We loaded the complete legacy menu through the `data_patches` system (0004-full-legacy-menu.sql plus corrective patches 0005–0009). Problems that a small curated set would never have surfaced appeared immediately:

- Duplicate top-level items (Inicio and Administración repeated)
- Broken hierarchy after consolidation (Usuarios ended up under the wrong parent)
- Inconsistent casing
- Ordering that didn’t match legacy intent

The user forced us to diagnose live inside the container:

```bash
docker compose exec db psql -U tuqan -d tuqan -c "
  SELECT id, padre, orden, accion,
         (SELECT etiqueta FROM menu_idiomas_nuevo 
          WHERE menu_id = menu_nuevo.id AND idioma='es' LIMIT 1) as etiqueta
  FROM menu_nuevo 
  ORDER BY orden, id;
"
```

Every diagnosis produced a precise surgical patch (UPDATE + correct re-parenting: Aplicación as first child of Administración, Usuarios under Aplicación, Inicio first, Cerrar Sesión last). We did not rewrite the generator. We repaired the data with the same discipline we have used since the beginning.

Only with real volume did we understand that a horizontal navbar was never going to work. 15–18 top-level items at readable font size simply do not fit. “More”, icon-only, or horizontal scroll were all rejected. The left collapsible sidebar (with localStorage persistence and dual toggle buttons) was the correct call, documented in `reference/menu-renderer-analysis.md`.

## First Real Module: Usuarios as Proof of Concept

With the full menu loaded and navigation that never breaks (thanks to the smart `LegacyAction` fallback), we could finally attack the first real module following the exact tree we had just rescued: Administración → Aplicación → Usuarios.

We delivered:

- `Pages/Usuarios/Listado.php` and `Formulario.php` (GET only for now; POST, validation, and delete were deliberately left for the next leg)
- `templates/layouts/app.twig` (the abstraction that pulls red cabecera + sidebar + real user variables for every module page)
- `templates/usuarios/listado.twig` and `formulario.twig` extending the new layout
- Modern Phroute routes under `/admin/usuarios/...` plus the legacy equivalents still pointing at the old code

Result: real listing against the `usuarios` table, new and edit forms (with `id` arriving correctly), all with the red header bar, the real logged-in user (first/last name + email now properly split in the database), and the sidebar present.

For the first time the “one module at a time” skeleton felt real.

## Two Debugging Patterns That Keep Biting Me

### 1. The opcache trend (blaming without evidence — again)

This was not the first time.

In multiple previous sessions I had already jumped to “must be opcache” the moment something behaved differently between “what I just edited” and “what the browser sees.” In this leg it happened again:

- “Route admin/usuarios/editar/1 does not exist” → first hypothesis: stale bytecode.
- Behavioral differences on reload → “let’s just opcache_reset().”

One time it was real: I had placed `opcache_reset()` *before* the `namespace` declaration, producing the fatal “Namespace declaration statement has to be the very first statement.” That was genuinely a stale bytecode problem. Most of the other times it was not.

What I consistently failed to do:

- Enter the container and ask the actual PHP-FPM process what file it is running and with what mtime.
- Use `opcache_get_status()` from a temporary debug endpoint.
- Compare `filemtime` on disk against what the runtime believes it has cached.
- Simply run `docker compose exec app php -r 'echo file_get_contents("/path/to/the/file.php");'` to see the actual code being executed.

Blaming opcache has become a reflex. It sounds technical, it is sometimes correct, and it is always easy to reach for. Without verification against the exact runtime serving the request, it is just expensive noise.

### 2. Phroute syntax and reading the docs last

The other classic pattern: “I’ve used routers before, this must be the same.”

I wanted a route with a parameter:

```php
$router->addRoute('GET', '/admin/usuarios/editar/{id}', [...]);
```

I wrote `:id` (the syntax I vaguely remembered from other routers). The route never matched. I tried `$_GET['id']` hacks, special legacy exclusions, everything except the obvious.

After seeing the full stack, the user diagnosed it with surgical precision:

> “I think the problem is how you display :id according to help pages this is {id} not :id”

They were exactly right. Phroute’s own help pages (and the code in vendor) make the syntax crystal clear from the first example. I simply never opened them. I assumed. I failed.

What I could (and must) do differently from now on:

1. The first 20 seconds when facing any library syntax doubt: open the official docs or the vendor README, not “try the syntax I think I remember.”
2. Search inside the library’s own source (`grep -r 'addRoute' vendor/dannyvankooten/phroute/`) before writing the first route.
3. Write a minimal isolated router test before integrating it into the full auth + legacy fallback flow.

Two minutes of reading up front would have saved multiple rounds of “route does not exist,” frustration, and unnecessary workarounds.

## What Actually Went Well (and Why It Matters)

- The data patches system proved it scales cleanly to hundreds of real rows.
- The sidebar + `layouts/app.twig` + red cabecera with an explicit Home button (because the legacy “Inicio” action was not reliably taking users home) made navigation solid.
- The “modern module + legacy fallback that never breaks the menu” pattern was validated with the first real case.
- 2407 lines in one PR. Good pace. Dense but reviewable deliveries.

The user summarized it at the end: “2407 changed lines, this is the good pace.”

## Current State

We now have 100% real database login, the complete real legacy menu loaded and rendered in a usable way, the first module (Usuarios) with listing and GET forms working inside the new layout system, and —critically— two agent debugging anti-patterns now publicly documented so they don’t cost us the same amount of time again.

This article complements [PR #60](https://github.com/laanito/tuqan/pull/60) (branch `feat/stage-8.3-gettext-login-menu-data`) and the living documentation in `.agents/STAGE-CHECKLISTS.md` (Stage 8.4 — Full Menu Structure + First Real Module) and `MIGRATION-PLAN.md` in the Tuqan repository.

## What Comes Next

POST handling, validation, password logic, flash messages, and the “Borrar” action for Usuarios. Then the next branch in the menu tree. One module at a time. No heroic sprints.

---

**Quick reproduction (for agents and humans)**

```bash
# In the tuqan repo
docker compose --env-file .env.docker down -v
docker compose --env-file .env.docker up -d --build
docker compose exec app rm -rf templates/cache/*
docker compose exec app ./scripts/init-db.sh

# Then hard refresh in the browser
# Login: demo/admin → admin/admin
# See the full real menu + collapsible sidebar
# Navigate to Administración → Aplicación → Usuarios
```

Live menu diagnosis commands we used inside the container:

```bash
docker compose exec db psql -U tuqan -d tuqan -c "
SELECT id, padre, orden, accion, activo,
  (SELECT etiqueta FROM menu_idiomas_nuevo WHERE menu_id=menu_nuevo.id AND idioma='es' LIMIT 1) etiqueta
FROM menu_nuevo ORDER BY orden, id;
"
```

All the work lives in PR #60 of the Tuqan repository.

---

*Written immediately after the session, while details and emotions were still fresh. The cover image was generated specifically for this article, maintaining the same calm Praderas editorial style (wooden house at golden hour in the golden meadow). It represents the moment of unfolding the complete real legacy menu —with all its complexity and hierarchy surprises— into a solid navigation structure (the collapsible sidebar), while two recurring debugging patterns are crossed out: the premature blame of opcache without runtime verification, and assuming route syntax from other frameworks without opening the Phroute documentation.*