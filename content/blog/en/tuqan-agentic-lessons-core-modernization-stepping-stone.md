---
Title: Tuqan — Agentic Operation Lessons (Part 3): When Patching Stops Being a Shortcut
Description: The agent applied the compatibility patch that had worked successfully before. But the user pointed out that this pattern would repeat with every old library in the project. This led to a real change in the migration plan: treating the modernization of core functionalities as a required stepping stone before moving forward.
Date: 2026-05-28 12:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-agentic-lessons-core-modernization-stepping-stone
Series: Tuqan Modernization
Series_Slug: tuqan-modernization
Series_Order: 6
Image: /assets/images/tuqan-agentic-lessons-core-modernization-stepping-stone-hero.webp
---

# Tuqan — When the Agent Realizes That Patching Is No Longer a Shortcut

If you believe that repeatedly applying compatibility patches is a pragmatic way to move forward when working with AI agents on legacy code, this article is also for you.

This is not a critique of pragmatism. It is the story of how, after several months successfully using the same pattern, the user forced us to question whether that pattern was still sustainable.

## The Incident That Changed the Direction

We were closing the final details of the real database-backed login PR. As a last improvement before merge, we added a minimal landing page and working logout functionality. During testing, a new deprecation appeared:

> Return type of Twig\Node\Node::count() should either be compatible with Countable::count(): int, or the #[\ReturnTypeWillChange] attribute should be used...

It was the exact pattern we had successfully used multiple times before: on Illuminate, on Phroute, and in other parts of Twig. We applied the minimal patch (`#[ReturnTypeWillChange]`), documented that it was temporary, and everything became clean again.

The complete flow (login → landing → logout) worked with zero Xdebug warnings.

Then the user wrote:

> "we will be hitting twig issues for every step we take and will be fixing them to make it usable same for the other classes, starting clean might be a timeline accelerator"

They were not asking us to stop delivering. They were pointing at something deeper: that the "patch when it appears" approach was going to repeat itself with virtually every old library we still depend on (Twig, Former, the legacy query builder, etc.). Every new piece of functionality we touched would generate more of this kind of work.

## The Real Change in the Plan

As the very last step before merge, we updated the migration plan.

Instead of continuing to treat these library updates as tactical work that emerges organically, we decided to formalize an explicit intermediate phase:

**"Core Functionality Modernization" as a required stepping stone.**

This phase is not a return to big-bang rewriting. It is the recognition that, after building a minimum viable working app using pragmatic patches on top of 2010-era dependencies, there comes a point where it becomes more efficient (and more honest) to dedicate focused effort to modernizing the foundations on which everything else will be built.

The plan now reflects that this stage should be considered prerequisite infrastructure before entering full business logic modernization.

## What This Means for Working with Agents

There is an uncomfortable lesson here.

The pattern of "apply the patch that already worked before" is extremely seductive for an agent. It is locally efficient, reduces noise, and allows continued delivery of visible value. And in many moments, it is the right decision.

The problem appears when that pattern becomes the default strategy for too long. Every patch we apply today is a decision we postpone. And postponed decisions tend to accumulate.

The user was not asking us to stop shipping. They were asking us to recognize when a shortcut stops being a shortcut and becomes the main path.

This is probably one of the most valuable ways a human can correct an agent: not only by asking that something specific be fixed, but by pointing out that the way we are deciding what to fix (and how) carries an accumulated cost that no longer justifies itself.

---

PR #56 of Tuqan now includes this reflection as part of its final documentation. The change to the migration plan is, paradoxically, one of the most concrete and useful outcomes of all the work of the past few weeks.

Sometimes the biggest advance is not adding a new feature. It is having the clarity to stop applying the same old patch.

---

**Quick reproduction:**

The main changes from this cycle (landing page + logout + Twig patch + plan update) live on the branch `feat/real-db-auth-no-shortcuts` in the Tuqan repository.

The hero image for this article was generated specifically to reflect the tension between repeatedly applying patches and deciding to strengthen the foundations. It follows the same calm Praderas editorial style used in the previous article of this series.

