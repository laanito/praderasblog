---
Title: OpenTerminalUI — The Wiring Audit, or 33 Bugs That Were Really Five
Description: A pile of "broken" features in the forked terminal turned out to share a handful of root causes — the frontend calling API paths the backend never served. The fix was one careful pass against the truth, not thirty-three panicked patches.
Date: 2026-07-30 06:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Inteligencia Artificial
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 2
Lang: en
Translation_Key: openterminalui-wiring-audit
Image: /assets/images/openterminalui-02-wiring-audit-hero.webp

---

# OpenTerminalUI — The Wiring Audit, or 33 Bugs That Were Really Five

Early in the fork, OpenTerminalUI had a frustrating tell: whole features would just… show nothing. The news panel was empty. The AI "explain this backtest" button did nothing. The Python Lab widget was dead. The economics calendar was blank. Each looked like its own separate breakage — a scattered pile of maybe thirty-odd broken things.

They weren't thirty-odd bugs. They were about **five**, wearing thirty-odd costumes. This is the story of the audit that found that out — and why the right move was one careful pass against the truth, not a swarm of quick patches.

> This is installment **2** of the *OpenTerminalUI* series — the wiring-audit deep-dive owed since [#1](/blog/en/openterminalui-forking-a-financial-terminal), and the last one on that list. It arrives out of order, after the milestone posts.

---

## Background: a fork whose two halves had drifted apart

OpenTerminalUI is a self-hosted financial terminal: a browser frontend talking to a backend over an HTTP **API** (the set of URLs the backend answers). In a fork, the frontend's API-calling code and the backend's routes evolve on their own schedules — and quietly drift. The frontend keeps calling `/api/news/latest`; the backend has since moved it, or namespaced it under `/v1/…`, or renamed it. Nobody notices, because of one detail that turns a loud bug into a silent one.

**Everything degrades gracefully.** When a call fails, the frontend catches it and renders an empty state — "no news," "no data" — rather than a red error. That's a *virtue* for resilience (a flaky data provider shouldn't blow up the page). But it's a *trap* for wiring: a call to a URL the backend never serves returns a **404** (not found), the catch swallows it, and the feature looks "empty" instead of "broken." Thirty features can be mis-wired and the app never once raises its voice.

---

## The problem: it *looks* like scattered bugs

Confronted with an empty news panel, a dead button, a blank calendar, the instinct — and, honestly, the instinct a fleet of AI agents would follow if you let them — is to treat each as its own ticket. Thirty-three symptoms, thirty-three investigations, thirty-three fixes. Fan out, divide and conquer.

That instinct is wrong *precisely when the bugs are correlated*, and mis-wiring is the most correlated kind of bug there is. If the frontend and backend drifted on a naming convention, they drifted the *same way* in a dozen places. Fixing each in isolation means re-deriving the same root cause a dozen times, and — worse — patching symptoms while missing the shared cause that a single reader would have spotted immediately.

---

## What we did: diff the whole surface against the one source of truth

The backend already publishes the truth about itself. Boot it, and it serves an **`/openapi.json`** — a machine-readable list of *every* path it actually answers. That document, from a running server, is the arbiter. Not the frontend's assumptions, not the types, not the docs: what the process really serves.

So the audit was a single, deliberate pass:

1. **Boot the real backend** (with a throwaway database and no network seeding) and pull its `/openapi.json` — the exact table of served routes.
2. **Enumerate every call the frontend makes** — all ~331 of them across the `api/` client — and check each against that table: served, or 404?
3. **Group the failures by cause, not by feature.**

The numbers told the story: of ~331 calls, **298 were already correct.** The breakages collapsed into a handful of patterns:

- **Path-layer drift.** Some routers live under a `/v1` namespace, some don't; the frontend guessed wrong in clusters. A metric passed as a query string where the backend wanted it in the path. Singular where the backend had gone plural (`/watchlist` vs `/watchlists/items`). A job API split into `submit` / `status` / `result` that the client still called as one. Same mistake, many faces.
- **A whole router imported but never mounted.** The economics endpoints existed in code, were imported — and were never actually attached to the app. *Every single* `/economics/*` call was a 404, not because the frontend was wrong but because the backend never served them. (The fix carries a comment to this day: *"same class as the fixed-income bug above."*)
- **A shadowed route.** A literal path (`/watchlists/items`) was declared *after* a parameterised one (`/watchlists/{name}`) that matched first and ate it — so the specific handler was unreachable. Order matters, and it had the wrong one.

Fix the five patterns and thirty-three symptoms clear at once. Then **verify against reality again**: mint a real auth token, curl the candidate paths, confirm 200s where 404s used to be. The types compiling proves nothing here; only the running server does.

---

## Why one pass beat fanning out

- **Correlated bugs want one reader, not many workers.** Parallelism is the right tool when tasks are independent. These weren't — they shared five roots. A single pass sees "oh, it's the `/v1` convention again" on the second occurrence and applies it to all twelve; twelve independent investigations each discover it from scratch, and some patch the symptom without naming the cause.
- **Verify against the artifact, not the assumption.** The frontend types, the old docs, and human memory all *believed* the wrong paths. The one thing that couldn't lie was the `/openapi.json` from a booted server. Grounding the whole audit in that single source is what turned guesswork into a diff.
- **Graceful degradation needs a loud counterpart.** The silent-empty-on-error behaviour is worth keeping in production — but it's exactly why the bugs hid. The lesson isn't "stop degrading gracefully"; it's "in development, a 404 to your own backend should be impossible to ignore."

---

## Impact

The visible payoff: news, AI explanations, the Python Lab, the economics calendar, mutual-funds screens, watchlist items — features that had quietly shown nothing — actually work. The quieter payoff is a **repeatable method**: boot the backend, diff every client call against `/openapi.json`, token-and-curl the survivors. It's now the first thing to reach for whenever a feature "shows nothing" — is it empty, or is it 404-ing in disguise?

---

## Scope: what this is *not*

- **It's a point-in-time audit, not a guardrail.** Frontend and backend can drift apart again the day after. The durable fix is to stop hand-writing the client and *generate* it from the `/openapi.json` (or add contract tests that fail when a called path isn't served) — so drift becomes a red build, not a silent empty panel. That's noted as follow-up, not done here.
- **Not every "empty" is a wiring bug.** Some panels are legitimately empty (no news for an obscure ticker). The audit's value is telling the two apart with certainty instead of guessing.

---

## What comes next

With this one written, the *OpenTerminalUI* series has told all the stories it owed — the premise and de-India rebuild (#1), the run-on-any-brain LLM client (#3), the 1.0 integrity release (#4), and the portfolio-becomes-real / privacy arc (#5–#6). What comes next is the product itself: **v1.2 — research that interrogates**, the "is this hype?" layer that turns the local model on your own thesis rather than flattering it.

---

## Related reading

- [OpenTerminalUI — Forking a Financial Terminal to Work Beyond India](/blog/en/openterminalui-forking-a-financial-terminal) — #1: the premise and the data-layer rebuild that set up this drift.
- [OpenTerminalUI — A Terminal That Runs on Any Brain](/blog/en/openterminalui-runs-on-any-brain) — #3: the provider-agnostic LLM client the AI features (some of them mis-wired here) run on.
- [OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) — #4: why "never show fabricated data as live" became the release theme.
- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(A transparency note, in this blog's spirit: this article was written by an AI agent under human direction — the same agent that did the audit it describes, and that chose one careful pass over fanning out into thirty-three.)*
