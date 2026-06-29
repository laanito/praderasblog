---
Title: OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature
Description: How we cut the first stable release of a self-hosted financial terminal by spending the whole budget on one thing — never showing fabricated data as if it were live.
Date: 2026-06-29 07:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Economia
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 4
Lang: en
Translation_Key: openterminalui-stable-1-0
Image: /assets/images/openterminalui-04-stable-1-0-hero.webp

---

# OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature

After months of forking, rebuilding, and de-Indianizing a Bloomberg-style financial terminal, we tagged **v1.0.0**. The strange part — and the point of this post — is that 1.0 added almost no new features. We spent the entire release on a single, unglamorous promise: **the terminal will never show you a fabricated number as if it were live.**

> This is installment **4** of the *OpenTerminalUI* series — the 1.0 milestone. The two deep-dive installments promised earlier (the wiring audit and the run-on-any-LLM story) are still on the way as #2 and #3; this one is the capstone.

---

## Background: a terminal you're supposed to trust with money

OpenTerminalUI is a self-hosted financial terminal — live charts, screeners, portfolios, options analytics, an AI research layer — that you run on your own machine. Earlier installments covered [why we forked it and rebuilt its India-centric data layer](/blog/en/openterminalui-forking-a-financial-terminal) for US, European, and crypto markets.

The fork has a north star: an open, private terminal that helps an individual invest **without being fooled** — by the market, by hype, or by the tool itself. That last clause turns out to be the hard one. A weather app that guesses is mildly annoying. A financial tool that shows you a confident, precise, **wrong** number is dangerous, because precision reads as truth.

A couple of terms, defined once:

- **Mock / fallback data** — placeholder values a program serves when the real source fails, so the screen isn't empty.
- **Degraded** — our label for "we have no live data here right now," shown honestly instead of filled in.

---

## The problem: software that lies politely

When we audited the backend before 1.0, we found a recurring, well-intentioned pattern: when a real data source was missing or rate-limited, the code **made something up** so the UI looked alive. The examples were sobering:

- The commodities panel served `2100.0 + random()` as a "live" gold price.
- Charts had a synthetic random-walk fallback — backtests could run on a price history that **never happened**.
- The order book was generated from a hash of the symbol name — a plausible-looking ladder of pure fiction.
- The Relative Strength screens returned hardcoded Indian blue-chips, presented as live rankings.

None of this was malicious. It's the path of least resistance under "never show an empty panel." But for a tool whose entire reason to exist is *not being fooled*, polite lies are the worst possible bug. They don't crash, they don't log, they just quietly mislead.

---

## What we did: make "I don't know" a first-class answer

The fix wasn't one patch; it was a convention and a sweep.

| Bucket | What it covered |
|--------|-----------------|
| **A — Integrity** | Audit every fabrication site; wire real data or mark it degraded |
| **B — De-India defaults** | Western-market defaults so the release identity is coherent |
| **C — Robustness** | Back off from rate-limited APIs instead of hammering them |
| **D — Version contract** | One real version number, honestly surfaced |
| **E — Docs** | Write down, plainly, what works and what doesn't |

The heart of it is bucket A. We established a single **degraded-data convention**: when there's no live source, the API returns an *empty* result plus a small machine-readable marker — a reason (`no live source`, `missing API key`, `rate limited`) and the source it tried. The front end reads that marker and shows a banner. The rule is one sentence: **nothing fabricated may look live.** Gold prices, charts, order books, relative strength — each fabrication site became either real data or an honest blank with a reason attached.

Around that, the supporting work:

- **Robustness (C).** A shared retry-with-jitter plus a circuit breaker, so when a free-tier API starts returning "too many requests," the terminal backs off and serves cached data instead of pounding a provider that's already down. Responses from the data providers are now cached in a persistent layer, so identical requests don't re-spend a daily quota.
- **A real version contract (D).** The project had drifted into *two* version numbers — the front end said `0.4.0`, the backend said `0.2.0`. We reconciled them to a single `1.0.0`, made the front end derive its version from one source, and surfaced it in the health endpoint and the UI footer.
- **Honest docs (E).** A "what works out of the box vs. what needs an API key" matrix, and a **Limitations** page that says out loud the uncomfortable things: there's no free live economic-calendar source (we ship a clearly labelled sample); dividend forward dates are *estimates*; several screens — bonds, movers, insider trades, the tape — are honest empty stubs until a real feed is wired.

---

## Why integrity over features

It would have been more *fun* to add features. We deliberately didn't, for three reasons.

- **Trust is the product.** For a research terminal, a feature that occasionally lies is worth *less than zero* — it poisons confidence in every other number on the screen. A blank panel that says "no live source" costs you nothing; a fake gold price can cost you a trade.
- **A version number is a promise.** Calling something `1.0` says "you can build on this." We didn't want the first stable tag to sit on top of a layer of random-walk charts and hashed order books. 1.0 had to mean *honest*, not *feature-complete*.
- **Empty is recoverable; fake is not.** A degraded marker is a to-do list — it tells you (and future contributors) exactly where a real data source still needs wiring. Fabricated data hides that gap and makes it look solved.

---

## Impact

The terminal now fails honestly. Where it has live data — US/EU/crypto charts and quotes via free sources, crypto fundamentals, the local-LLM research layer — it shows it. Where it doesn't, it says so, with a reason, instead of inventing a number. For someone using it to make real decisions, that's the difference between a tool and a toy. And for the next contributor, the degraded markers are a precise map of what's left to build.

---

## Scope: what 1.0 is *not*

Honesty about the edges, in the spirit of the release itself:

- **The degraded stubs are still stubs.** Relative Strength, bonds, hotlists, insider trades, and the time-&-sales tape return honest empty results — the *real* data engines behind them are future work, not part of 1.0.
- **No free Level-2 depth** for US/EU equities exists, so that order book is empty-and-labelled rather than faked; a brokerage-API adapter is planned.
- **The release smoke-test matrix is manual for now.** We verified the core flows by hand across database and key combinations; automating that matrix is a deliberate to-do, not a silent gap.

---

## What comes next

- **v1.1 — multi-asset depth:** widen the real coverage (EU/crypto heatmaps, portfolio cash and transactions, more live order-book sources).
- **v1.2 — AI-native deepening:** lean into the private "second brain" research layer that grounds answers only in your own notes.
- And the two series deep-dives still owed from installment 1: the wiring audit ("the bug that looked like 33 bugs") and the run-on-any-LLM rebuild.

---

## Related reading

- [OpenTerminalUI — Forking a Financial Terminal to Work Beyond India](/blog/en/openterminalui-forking-a-financial-terminal) — installment 1: the premise and the de-India data-layer rebuild.
- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI), tagged `v1.0.0`. The engineering notes live in the repo's `.agents/`- and `docs/wiki/`-style folders, written for people and agents alike.

*(A transparency note, in this blog's spirit: this article was written by an AI agent under human direction — the same agent that did the engineering it describes.)*
