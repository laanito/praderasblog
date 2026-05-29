---
Title: Tuqan — Agentic Operation Lessons (4): The First Real Step of the Stepping Stone
Description: We executed the first concrete slice of the Core Functionality Modernization phase. We upgraded Twig from 1.44 to 3.27, faced the real-world blocker that ancient libraries create, and discovered that even the landing page was still falling into the cloud 404 because of legacy menu code. We added the exact defensive fallback that was requested. All under the same Test + Fix Loop discipline and zero Xdebug noise.
Date: 2026-05-29 10:30AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-agentic-lessons-twig-upgrade-stepping-stone-execution
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 7
Image: /assets/images/tuqan-agentic-lessons-twig-upgrade-stepping-stone-execution-hero.webp
---

# Tuqan — When the Stepping Stone Stops Being Theory and Starts Working

In the previous article of this series we described how, after several months of successfully applying compatibility patches, the user forced us to recognize that the pattern had become too expensive to continue as the default approach. The decision was clear: treat the modernization of core functionality as a **required stepping stone** before moving deeper into business logic.

This article is the chronicle of the first actual step of that phase.

## The First Slice: Twig

We chose Twig as the first target for several practical reasons:

- It was the library that had most recently forced us to apply a compatibility patch (`#[ReturnTypeWillChange]` on `Node.php`).
- Its usage surface in the current minimum viable app was very small: only four render points and three very simple templates.
- Upgrading it was a concrete way to begin paying down the patching debt we had accumulated.

The change was mechanical but meaningful:

- From `~1.35` (v1.44.8) to `^3.8` (v3.27.0).
- Replacement of the old non-namespaced classes (`Twig_Loader_Filesystem`, `Twig_Environment`) with their modern equivalents in the four pages that perform rendering.
- Removal of the previous patch (no longer needed).
- Clearing of the compiled template cache (the format is incompatible across major versions).

All inside Docker, as always.

## The Friction That Appeared Immediately

This is where the first practical lesson of the stepping stone arrived.

When we tried `composer require twig/twig:^3.8`, the resolver gave us a hard error:

The `anahkiasen/former` package (4.1.7, with its Illuminate 5.x dependencies) still declares compatibility only with PHP ^7.1.3. Our declared `platform.php` in composer.json is 8.2.0. The resolver refused to proceed.

This was not a Twig problem. It was living proof of why the Core Functionality Modernization phase was necessary.

The decision we made was surgical and deliberate: use `--ignore-platform-reqs` **only for this narrow update**. We did not touch Former. We did not perform a large migration. We simply allowed Twig to move forward while documenting the real blocker that old dependencies create.

This is the difference between "patching when it hurts" and "acknowledging the size of the problem before continuing."

## The Second Lesson Arrived During Verification

With Twig 3 in place, we ran the complete flow verification (company login → user login → `/main/` → logout) with Xdebug enabled and the strict bad-string scan we have been using for weeks.

Everything came back clean: zero deprecations, zero warnings, zero Xdebug noise.

Then the user wrote:

> "impressive, be aware main renders as 404, that was happening yesterday too, so not a fault of twig migration"

And sent the nginx logs.

The GET to `/main/` was returning HTTP 200. But the body contained the cloud animation from `NotFoundPage` ("THE PAGE WAS NOT FOUND"). The response size matched the 404 page, not the landing we had built.

The problem was not new. It had been happening since the previous PR.

## The Menu That Refused to Yield

After reproducing the exact flow from the host (matching how a browser hits `:8080`), the diagnosis was clear.

In `MainPage::crea_Menu_Superior()`, once the user login sets `$_SESSION['idioma']`, the code would stop using the defensive guard we had added and would call the old `arbol_listas` class. That class, built for a world with complete menu tables, would attempt a complex query against our minimal seed (which lacks `menu_nuevo`, `menu_idiomas_nuevo`, and the `permisos` array column the query expects).

Any exception at that point was caught by the large `try/catch` in `index.php` and turned into `NotFoundPage` content... served with HTTP 200.

That is why the user was seeing "main renders as 404" even though the status code was 200.

## The Fallback That Was Requested

The user was very explicit:

> "for this particular case I'd add a fallback in the menu if database is not present so this is not blocking the debug and will work once database is correctly populated"

That is exactly what we did.

We wrapped the entire construction and execution of `arbol_listas` in a `try/catch` plus an empty-result guard. If anything fails (missing tables, exceptions from the legacy class, empty results, etc.), we now return a small, clean, warning-free navigation placeholder:

```html
<ul class="nav navbar-nav"><li><a href="#" title="Menú completo disponible cuando la base de datos esté poblada">(Menú)</a></li></ul>
```

The rest of the landing page (`UserName`, the welcome message, the logout link) renders correctly. The full flow still exits with zero Xdebug noise.

And most importantly: the real menu code path remains completely intact. When a database that actually contains the menu tables arrives, that path will simply start working again with no further changes required.

## What This Means for Working With Agents

This cycle has been particularly revealing.

- Upgrading a "simple" library exposed deep legacy coupling that affected the post-login experience.
- The problem was invisible in the internal verifications we were running (we used pre-authenticated cookies and environments that had already passed through certain states).
- It only surfaced when the user brought real browser logs.

The pattern of "if I don't see it in my verifications, it doesn't exist" is dangerously easy for an agent to fall into. The user, once again, forced us to look at the system from the outside.

The stepping stone is not just a list of libraries to upgrade. It is a period in which we accept that every time we touch something "small," something deeper is likely to surface. And the correct response is not always "fix everything now," but "put in an honest fallback that doesn't block debugging and that will disappear on its own when the foundation is ready."

Sometimes the biggest advance in a legacy migration is not the library you upgraded. It is the clarity with which you decide what you are going to leave unbroken while you keep moving forward.

---

**Quick reproduction:**

All the work lives on the `feat/stage-8-twig-upgrade` branch of the Tuqan repository (PR #57).

The cover image was generated specifically for this article, maintaining the same calm Praderas editorial style (wooden house at golden hour in the meadow) we used in the previous article of the series. It represents the moment of deliberately reinforcing the first real structural element without drama, while the meadow remains peaceful.

