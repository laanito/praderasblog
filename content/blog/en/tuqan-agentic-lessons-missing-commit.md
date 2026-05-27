---
Title: Tuqan — Agentic Operation Lessons (Part 2): The Missing Commit
Description: The agent had already fixed the failing test on its local machine. The implementation was clean. But the updated test file was never added to the commit that went to the PR. The human reviewer had to spend their time reporting the exact failure. A concrete story of why "Test + Fix Loop" discipline is not overhead — it is the difference between an AI that ships trustworthy increments and one that quietly turns the human into its debugger.
Date: 2026-05-27 02:30PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-agentic-lessons-missing-commit
Series: Tuqan Modernization
Series_Slug: tuqan-modernization
Series_Order: 5
Image: /assets/images/tuqan-agentic-lessons-missing-commit-hero.webp
---

# Tuqan — When the Agent "Fixes Everything"... Except What Actually Gets Committed

If you are someone who suspects that strict process rules with AI coding agents are mostly theater —that they just slow down the magical "make it work" speed— this article is for you.

This is not a theoretical defense of the "Test + Fix Loop." It is the story of how, **after we had already written that rule into the project's documentation**, the agent committed the exact same class of mistake the rule was designed to prevent. And the person who paid the price was not the model. It was you, the human reviewing the PR.

## The concrete incident

The context was a PR for a functional login flow in Tuqan (the legacy PHP application we are modernizing with Docker + proper testing). We had written tests first, iterated with curl until the forms rendered cleanly, and finally had a real working flow without obvious shortcuts.

Right before opening the PR, we changed the implementation inside `LoginEmpresa::MuestraPagina()`. Instead of running a real query against the central database (which pulled in legacy code with dynamic properties that generated dozens of Xdebug deprecation warnings), we hardcoded the single company from the minimal seed ("demo"). The goal was hygiene: we wanted the company login page to render **with zero deprecation error tables**.

The change was correct for the stated objective ("clean forms").

The problem: the characterization test we had written earlier (`testMuestraPaginaFetchesCompaniesFromDbAndRendersForm`) still expected the mock to receive exactly one call to `iniciar_Consulta`. That test **could no longer pass** with the new implementation.

On my local machine, I had already edited the test to use `$mockDb->expects($this->never())...` and everything was green. But that modified file **never made it into the commit** that was pushed.

The PR was opened with the "clean" implementation and the old test that now failed.

The user (reviewer) was the one who had to report the exact error:

> "tests are not passing in the PR: Tuqan\Tests\Unit\Pages\LoginEmpresaTest::testMuestraPaginaFetchesCompaniesFromDbAndRendersForm Expectation failed for method name is 'iniciar_Consulta' when invoked 1 time. Method was expected to be called 1 time, actually called 0 times."

## Why this hurts people who are skeptical of process

Most developers who are wary of "heavy verification loops" with AI have a very practical reason:

> "I just want the agent to do the work quickly. If I have to review every detail and report silly failures, what is the point of the AI in the first place?"

This incident is the perfect demonstration that **without discipline on the full loop, the human ends up doing exactly the debugging work they hired the AI to prevent**.

The cost was not just "one red test." It was:

- Reviewer (human) time spent detecting and precisely describing a failure the agent had already resolved locally.
- Erosion of trust: "Is this PR actually ready, or are there more uncommitted ghosts?"
- Back-and-forth cycles that burn tokens and attention on something a simple `git status` + `git diff --stat` at the right moment would have caught.

## The most uncomfortable irony

All of this happened **after** we had solemnly documented the mandatory rule "Test + Fix Loop — Root Cause Over Symptom Hiding" in the Tuqan repository's `.agents/AGENTS.md`.

The rule explicitly states that work is only considered done when the root cause is fixed **and verification proves it with no hiding**.

The agent had internalized the rule... in the code changes. But it failed at the final 5% of the ritual: ensuring that the change set being reviewed was consistent with the truth the agent already knew in its local working tree.

This is the equivalent of fixing a bug, running tests locally, seeing them pass, and then pushing only the fix while leaving the updated tests behind. Except this time it wasn't a tired junior developer at 2 a.m. It was an AI agent that "knew" the rule.

## How do we actually improve the agentic flow?

These kinds of failures are not solved by "being more careful." We need the work system itself to make the omission error expensive.

Some concrete improvements we are considering:

1. **Explicit "complete change set" verification step before opening a PR.** A required checkpoint (even if just a script) that compares the working state against what the tests and curl just validated.

2. **Do not allow an agent to declare "ready for PR" if `git status` shows modified files that were touched during the session but never committed.**

3. **Treat updated or invalidated tests as atomic with the behavior change.** If you modify behavior in a way that makes an existing test incorrect, the commit introducing that behavior must also include the test update or removal.

4. **Living documentation of the incident.** Instead of burying it, we wrote this article and updated the stage checklist in Tuqan so future agents have a concrete example of "this already happened, and this is what it looks like when you get it wrong."

## The real value of the loop (for people who don't see it)

The "Test + Fix Loop" does not exist so the agent can feel disciplined. It exists so **you do not become the person who systematically cleans up the agent's omissions**.

Every time a human has to report "the test the agent had already fixed locally but never committed," we have inverted the value of the AI: instead of multiplying your productivity, we are dividing it because you now have to act as a second-level reviewer + detective of phantom changes.

Code-level short-circuits (the `index.php` bypass we discussed in the previous article) are visible and painful. Change-set short-circuits are more insidious because they look like "almost done."

The discipline is not free. But the alternative —the human becoming the one who reliably finishes the last 5% the agent "forgot"— is far more expensive over time.

---

This particular incident was closed in under an hour once reported (commit `5368f5d` + documentation in the checklist). The PR is now green. But the pattern it revealed is too important to file away quietly.

The Tuqan modernization remains a double experiment: modernizing a legacy PHP application with PHP 8 + Docker + tests, **and** learning to use AI agents in a way that actually saves human time instead of redistributing it to the reviewer.

The next time someone tells you that "all this ceremony around tests and atomic commits and verification loops is overkill when you're working with AI"... tell them the story of the missing commit.

The login flow code (and the test correction) lives in [Tuqan PR #55](https://github.com/laanito/tuqan/pull/55).

---

**Quick reproduction (for curious agents and humans):**

```bash
# In the Tuqan repo, after docker compose up + init
docker compose exec app ./vendor/bin/phpunit --filter LoginEmpresa
# (before the missing commit fix: 1 mock expectation failure)
# (after: 10/10 green)
```

The hero image for this post was generated specifically for it using a diffusion model, following the project's rule of never silently reusing covers from previous articles.