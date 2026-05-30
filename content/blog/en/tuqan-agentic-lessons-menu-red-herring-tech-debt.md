---
Title: Tuqan — Agentic Operation Lessons (6): Red Herrings, Technical Debt, and the Menu That Refused to Die
Description: We spent an hour hunting a session bug while the real technical debt (2007-era menu generator hardcoding localhost connections) was laughing at us. Real lessons on red herrings, human-AI collaboration, and why the “correct” solution is sometimes worse than a pragmatic one.
Date: 2026-05-30 10:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-menu-red-herring-tech-debt
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 9
Image: /assets/images/tuqan-menu-red-herring-tech-debt-hero.webp
---

# Tuqan — When the Red Herring Eats an Hour of Your Life

In the previous session we had finally gotten the top menu working after importing real legacy data through the new incremental patch system. The user confirmed: “we have menu superior now”.

One hour later the same user reported that going home only showed the “(Menu)” placeholder.

My immediate agent response was the predictable one: “must be a session problem”. I started hunting session persistence like an obsessed detective. I sprinkled `session_write_close()` everywhere, added aggressive logging, diagnostics on every redirect...

The user, calmly, pulled my ear:

> “If username is correctly stored in the session, empresa should be correctly logged too.”

They were completely right. `nombreUsuario` was being stored and displayed perfectly after the real database login. The problem was **not** a general session issue.

We had wasted nearly an hour chasing a textbook red herring.

## The Real Technical Debt

Once we stopped blaming the session, the problem revealed itself in all its glory:

The legacy menu generator (`arbol_listas` and the entire class tree it depends on) was creating PostgreSQL connections using only three parameters:

```php
new Manejador_Base_Datos($login, $pass, $db)
```

That constructor defaulted to `localhost`. In our Docker environment, `localhost` does not reach the `db` service. Connection refused. Exception. Menu placeholder.

On top of that, the `consulta($sql)` method in `Manejador_Base_Datos` unconditionally called `to_String_Consulta()`, which assumed the old query builder (`oQuery`) had been initialized via `iniciar_Consulta()`. When we used direct SQL (the modern, recommended path), `oQuery` was null → another crash.

2005–2010 technical debt still very much alive in 2026.

## The Human-AI Collaboration That Actually Mattered

The most valuable part of the session wasn’t the final technical solutions — it was how we got there:

- The user spotted the red herring long before the agent did.
- They suggested the elegant solution for action mapping: “you can just replace colons with slashes”.
- They pointed out that the “module not available” pages were breaking navigation because they didn’t render the menu.

At every critical junction, human intervention corrected the agent’s course. It wasn’t “the agent solved it”. It was real collaboration where the human brought historical project context, bullshit detection, and the ability to say “you’re chasing the wrong thing”.

## The Solution We Chose (and Why)

In the end we decided not to force the full legacy generator on the modern landing (it had too many broken internal assumptions in Docker). Instead:

- We used a simple, reliable renderer (`buildSimpleMenuHtml`) that correctly respects the company DB host/port.
- We added collapse using Bootstrap (already loaded).
- We built a smart fallback in the route exception handler: any unmapped legacy-style path now lands on `LegacyAction`, which shows a friendly message **while keeping the full menu visible**.

Result: navigation that never breaks, even when the target module isn’t modernized yet.

## Real Lessons from This Session

- Red herrings are especially dangerous when you have powerful diagnostic tools. You can generate a huge amount of noise in the completely wrong direction.
- Technical debt is almost never where you first expect it.
- Sometimes the “correct” solution (making the legacy generator work) is worse than a pragmatic one (controlled simple renderer + smart fallback).
- The human is still the one with historical context and bullshit detection. Agents can be excellent at chasing symptoms. Strategic judgment remains deeply human.
- When you build smart fallback systems, you can turn technical debt into something that doesn’t break the user experience.

## Current State

We now have 100% real database-backed login, a working incremental data migration system, the complete real menu hierarchy loaded and rendered as a collapsible top menu on the modern landing, and legacy action keys automatically translated into clean Phroute paths — with fallbacks that preserve navigation.

This is the first time in a long while that the menu is not just broken decoration.

## What Comes Next

Now that navigation actually works, the natural next move (already documented in the plan) is to start giving real content to modules, following exactly the structure of the menu we just rescued.

One module at a time. Following the tree. No heroic sprints.

---

**Quick reproduction (for agents and humans)**

```bash
# In tuqan repo
docker compose --env-file .env.docker down -v
docker compose --env-file .env.docker up -d --build
docker compose exec app rm -rf templates/cache/*
docker compose exec app ./scripts/init-db.sh

# Real login
# demo/admin → admin/admin → home
```

All the work lives in PR #59 of the Tuqan repository.

**Related reading**
- [Tuqan — Agentic Lessons (5): Modernizing Composer Dependencies](https://praderas.org/blog/en/tuqan-agentic-lessons-composer-deps-modernization)
- Migration plan and full evidence in the `.agents/` folder of the Tuqan repo

---

*Written immediately after the session, while details and emotions were still fresh.*