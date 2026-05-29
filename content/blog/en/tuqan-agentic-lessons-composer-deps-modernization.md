---
Title: Tuqan — Agentic Operation Lessons (5): Modernizing Composer Dependencies (No Leaps of Faith)
Description: We executed a major coordinated round of composer dependency modernization inside the stepping stone phase. We upgraded Monolog to 2.x, Phroute to 2.2, Former to 5.2, jasny/auth to v2, and set a conservative minimum floor at Illuminate 8 (instead of blindly following Former's ^13 promise). We faced and solved the real compatibility problems between Former 5 and modern Illuminate. All of it under the same Test + Fix Loop discipline and zero tolerance for Xdebug noise.
Date: 2026-05-29 09:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-agentic-lessons-composer-deps-modernization
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 8
Image: /assets/images/tuqan-agentic-lessons-composer-deps-modernization-hero.webp
---

# Tuqan — When Old Dependencies Stop Being a Shortcut

In the previous article we described how we executed the first concrete slice of the stepping stone: the Twig upgrade and the defensive menu fallback. That work was important, but it was still a relatively surgical, contained step.

This article tells the story of something significantly more ambitious — and in some ways, more delicate: a large, coordinated round of composer dependency modernization.

## The Context

For months we had been applying targeted compatibility patches (`#[ReturnTypeWillChange]`) every time an old library generated noise under PHP 8.3. It worked. It was pragmatic. But the user had explicitly asked us to do a serious pass over the dependencies before investing more effort in new functionality.

The libraries we cared most about at that point were:

- Monolog (still on 1.x)
- Phroute (2.1.0 with our own patches)
- Former (4.1.7 — the main source of composer resolution pain)
- jasny/auth (v1.0.1)

And underneath everything, the elephant in the room: the very old Illuminate packages that Former continued to drag in.

## The Strategy: The Minimal Version That Actually Solves the Problem

We did not want to make heroic jumps to the latest versions of everything. We wanted to be conservative but effective.

The key decision was to add an explicit floor at the root level:

```json
"illuminate/support": "^8.0"
```

We deliberately did **not** follow Former's loose promise of supporting up to Illuminate 13. We chose the lowest version that, in practice, completely eliminated the ReturnType deprecation noise coming from Container, Collection, Request, and similar classes.

The result was Illuminate 8.83.27.

## What Actually Moved

- **monolog**: 1.27 → 2.11 (required API changes in TuqanLogger)
- **phroute**: 2.1.0 → 2.2.0 + removal of our two manual trim(null) shims
- **former**: 4.1.7 → 5.2.0
- **jasny/auth**: v1.0.1 → v2.2.1
- **fpdf**: small bump

The move to Former 5.2 was the most delicate one. Even though the library declared support for modern Illuminate versions, in practice it was still happily resolving very old Symfony 3.4 and Illuminate 5.5 components. This created new compatibility problems, especially around `UrlGenerator` and `RouteCollectionInterface` resolution.

## The Problem We Didn't Expect (and How We Solved It)

When moving to Illuminate 8, `UrlGenerator` became stricter about its dependencies. Former 5.2, internally, was doing:

```php
$app->bindIf('url', 'Illuminate\Routing\UrlGenerator');
```

And `UrlGenerator` now required `RouteCollectionInterface`, which no one had bound.

The solution was not magic. We had to:

1. Add minimal early bindings in `index.php`.
2. Patch `FormerServiceProvider::make()` so that it would prefer `Container::getInstance()` instead of always creating a brand new empty container when none was passed.

It was a clear reminder that "bumping the package version" is not always enough when the package was never designed with non-Laravel environments in mind.

## What We Learned (Again)

This cycle reinforced several lessons we keep repeating in this series:

- Wide version ranges in `composer.json` can be misleading. They do not mean the library has actually been modernized.
- Sometimes the "minimal version that solves the problem" (Illuminate 8) is far more responsible than following the maximum version a legacy dependency claims to support.
- The hardest problems are rarely in the library you are upgrading — they are in everything that library still drags with it.

## Current State

The complete login flow (empresa → usuario) → `/main/` → logout now works cleanly, with no fatal errors and a very low level of deprecation noise considering where we started.

There is still work ahead (especially around Former and its relationship with the broader Illuminate/Symfony ecosystem), but the dependency foundation is now significantly more sustainable than it was a few weeks ago.

This was, without a doubt, one of the densest and most delicate stretches of the entire modernization process so far.

---

**Quick reproduction:**

All the work lives on the branch `feat/stage-8-composer-deps-modernization` of the Tuqan repository (PR #58).

The cover image was generated specifically for this article, maintaining the same calm Praderas editorial style. It represents the careful, orderly replacement of several old structural supports in the house while the meadow remains peaceful and untouched.

---

**Related articles in the series:**

- [Tuqan — Agentic Operation Lessons (4): The First Real Step of the Stepping Stone](/blog/en/tuqan-agentic-lessons-twig-upgrade-stepping-stone-execution)
- [Tuqan — Agentic Operation Lessons (3): When Patches Stop Being Shortcuts](/blog/en/tuqan-agentic-lessons-core-modernization-stepping-stone)