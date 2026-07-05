---
Title: OpenTerminalUI — Retiring the Portfolio That Was Secretly Shared
Description: The last installment showed the "legacy" portfolio was a single table shared by every user. This one is the story of actually deleting it — and discovering that "just delete a table" meant untangling thirteen things quietly leaning on it.
Date: 2026-07-05 06:00PM
Template: post
Author: Luis Amigo
Tags: Privacidad, Sistemas, Desarrollo Web
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 6
Lang: en
Translation_Key: openterminalui-retire-shared-portfolio
Image: /assets/images/openterminalui-06-retire-shared-portfolio-hero.webp

---

# OpenTerminalUI — Retiring the Portfolio That Was Secretly Shared

[Last time](/blog/en/openterminalui-portfolio-becomes-real) I admitted an uncomfortable thing about OpenTerminalUI: the old "legacy" portfolio was a single, global table with **no notion of a user**. On a one-person self-hosted install it's invisible; the moment two people share an instance they'd see and edit *the same* holdings. For a terminal whose whole pitch includes the word **private**, that's not a small bug — it's a contradiction of the premise.

That post built the *on-ramp* (a way to move your holdings onto the modern per-user portfolio) but stopped short of the hard part. **v1.1.0** finished the job: the shared portfolio is gone. This is the story of how — because "just delete the table" turned out to mean something much bigger.

> Installment **6** of the *OpenTerminalUI* series. It picks up directly from [#5](/blog/en/openterminalui-portfolio-becomes-real); the two deep-dives promised back in #1 (the wiring audit, the run-on-any-LLM story) are still owed as #2 and #3.

---

## Background: two portfolios, one of them global

A quick recap for anyone landing here first. OpenTerminalUI is a self-hosted financial terminal you run on your own machine. It carried **two** portfolio systems side by side:

- **Legacy** — one flat, global table (`Holding`: ticker, quantity, average cost, no `user_id`). No cash, no transactions. But it was what the rich analytics were built on.
- **Manager** — a newer, per-user system: multiple named portfolios, a real transaction ledger, the cash and P&L work from the previous release. (*P&L = profit and loss.*)

The plan was always to make the Manager the *only* portfolio and delete the global one. What made that daunting wasn't the delete — it was everything attached to it.

---

## The problem: "delete a table" was a lie

I went in expecting to remove a model, a few routes, and a page. Then I actually searched for who reads that global table. The answer was **thirteen** places — and not peripheral ones:

- the obvious surfaces — home, cockpit, the heads-up overlay, the launchpad, the correlation dashboard;
- and whole analytical subsystems — **risk metrics, stress testing, factor analysis, dividends, the report generator**, even the **AI plugin context** that hands "your portfolio" to a language model.

Several of them had a comment that said, almost sheepishly, *"get all holdings for the current user"* — while doing no such thing; they read everyone's. Each one was simultaneously a **privacy leak** and a **hard dependency**: the instant the table vanished, they'd crash. You can't delete the thing until nothing needs it — and thirteen things needed it.

---

## What we did

The shape of the fix came from one decision and one refusal.

**One source of truth for "your portfolio."** I added a per-user **primary portfolio** — simply, your earliest-created one — and a single small function that returns its holdings in the *exact shape* the old global table produced. Every call site that used to ask the global table now asks this function instead. Because the shape matched, most consumers changed by **one line**. Background jobs (news ingestion, price prefetch) that legitimately need *all* held tickers got a separate helper that returns just the union of symbols — no per-user data, so no leak.

**A refusal to do it in one big bang.** Deleting a load-bearing table in a single commit is how you get a scary, unreviewable diff and a bad weekend. So it split cleanly in two:

1. **Repoint everything** to the per-user source — while the old table still physically exists. After this step the leak is closed *everywhere*, but nothing is deleted yet, so it's easy to verify.
2. **Delete** the table, its routes, and the dead code — now that provably nothing reads it.

**Parity before deletion.** The legacy view had analytics the Manager didn't: a correlation matrix, a benchmark-overlay curve, the fuller risk metrics, and Brinson **attribution** (a standard way to explain *why* a portfolio beat or trailed its benchmark). Deleting the old view would have quietly removed features. So first I ported all of it onto the per-user Manager — plus the AI risk narrative and the backtester — and only then removed the old screen. A reader mid-review even caught two I'd missed (the AI narrative and backtesting); they went in before anything was deleted.

Then, and only then: the global `Holding` table, its `/api/portfolio` endpoints, the tax-lot code, and the migration button itself (its reason to exist was gone) were removed. The watchlist feed, which was always meant to be global, stayed exactly as it was.

---

## Why this path

- **Match the old shape and the migration is boring.** The temptation is to "modernize" every call site while you're in there. Resisting that — returning the *legacy* shape from the new source — turned a scary refactor into thirteen one-line edits and kept the risk in one small, well-tested function.
- **Repoint, then delete.** Two honest, reviewable steps beat one heroic diff. After step one the privacy problem was already solved; step two was just removing proven-dead code.
- **Parity is a precondition, not a follow-up.** "We'll re-add the analytics later" is how features quietly die in a refactor. Porting them *first* meant the deletion removed only duplication, never capability.

---

## Impact

The shared-across-users portfolio no longer exists anywhere in the codebase. Every surface — dashboards, risk, stress, factor, dividends, reports, the AI context — reads the logged-in user's own portfolio, by construction. The portfolio API is entirely per-user now. And the net change was **about 850 fewer lines**: the most satisfying kind of release, where "done" looks like less code, not more.

For a solo self-hoster nothing visibly breaks — which is the point; the fix was for the multi-user case that quietly betrayed the "private" promise. Along the way the per-user portfolio also picked up a small ergonomic win: a **Sell** button on each holding that pre-fills the trade form for that exact position, instead of retyping it.

---

## Scope: what this is *not*

- **Tax-lot accounting stays shelved.** It went out with the legacy code and isn't coming back soon — it's a jurisdiction-specific rabbit hole where a *wrong* number is worse than none, and easy to mistake for tax advice.
- **The AI risk narrative is on-demand.** It runs on your local model (Ollama by default), so it waits for a click rather than spending a minute of inference every time a portfolio loads; with no model configured it says so plainly instead of inventing an analysis.

---

## What comes next

With the passive-integrity and the private-portfolio halves both done, the next release leans into the active side of the north star — **research that interrogates**: an "is this hype?" layer that uses the local model and your own notes *adversarially* rather than as a cheerleader. And the two series deep-dives still owed from #1 — the wiring audit and the run-on-any-LLM rebuild.

---

## Related reading

- [OpenTerminalUI — Making the Portfolio Real (Without Lying About It)](/blog/en/openterminalui-portfolio-becomes-real) — #5: the cash spine, honest P&L, and where this privacy story began.
- [OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) — #4: why the first stable release spent everything on not showing fabricated data as live.
- [OpenTerminalUI — Forking a Financial Terminal to Work Beyond India](/blog/en/openterminalui-forking-a-financial-terminal) — #1: the premise and the de-India data-layer rebuild.
- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(A transparency note, in this blog's spirit: this article was written by an AI agent under human direction — the same agent that did the engineering it describes.)*
