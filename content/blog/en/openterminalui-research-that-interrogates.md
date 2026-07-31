---
Title: OpenTerminalUI — A Terminal That Argues With You
Description: The last releases made the data honest and the portfolio private. v1.2 turns the local model on the most dangerous voice in investing — your own conviction — with an "interrogate this" card that pressure-tests the bull case, and yours, grounded in your own notes.
Date: 2026-07-31 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Economia, Privacidad
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 7
Lang: en
Translation_Key: openterminalui-research-interrogates
Image: /assets/images/openterminalui-07-research-interrogates-hero.webp

---

# OpenTerminalUI — A Terminal That Argues With You

Every release of this fork has circled one idea: an open, private terminal to invest *without being fooled*. The earlier ones fought the ways a **tool** can fool you — showing made-up numbers as if they were live ([1.0](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature)), reporting sale proceeds as if they were gains, quietly sharing "your" portfolio with everyone on the box ([1.1](/blog/en/openterminalui-retiring-the-shared-portfolio)).

**v1.2** goes after a harder one: the way *you* fool yourself. The most expensive voice in investing isn't a bad data feed — it's your own conviction, the thesis you've fallen for and now only read the confirming evidence for. So this release builds a feature whose entire job is to **disagree with you**.

> Installment **7** of the *OpenTerminalUI* series. It cashes the cheque [#6](/blog/en/openterminalui-retiring-the-shared-portfolio) wrote — the promise of "research that interrogates" — and builds on the local, provider-agnostic model client from [#3](/blog/en/openterminalui-runs-on-any-brain).

---

## The premise: an analyst that refuses to cheerlead

The terminal already had an *AI briefing* — hand the local model some fundamentals and headlines, get back a tidy bull/bear write-up. (*AI = artificial intelligence; the model here is a local large language model, or LLM, running on your own machine.*) It's useful, but it has a failure mode: ask an eager assistant about a stock and it will happily narrate the story you want to hear. A briefing that's 60% bull case is not a check on your judgment — it's a mirror that nods.

So v1.2 adds a second, deliberately adversarial card: **Interrogate this Stock** (and *this Coin*, and *this Index* — it works on every asset the terminal covers). Same model, opposite job. Instead of selling the story, it prosecutes it. The system prompt tells the model it is a *skeptical devil's-advocate*, not an assistant, and it must return exactly four sections:

- **The Bull Narrative** — the prevailing story, stated plainly (including *yours*, if you've written it down).
- **What Would Have To Be True** — the load-bearing assumptions the story quietly rests on.
- **The Bear Case & Base Rates** — what breaks it, and how often stories shaped like this one disappoint.
- **Already Priced In?** — whether the valuation and price action have *already* swallowed the narrative you're excited about.

It's forbidden from giving buy or sell advice, and told in as many words **not to flatter**. The point isn't a verdict; it's the friction of seeing your own thesis laid on a table and poked.

---

## The part I'm proudest of: it reads your *other* notes

Somewhere in the terminal is a private "second brain" — a per-user store of your own notes, journal entries, and theses, indexed so the model can retrieve over your own writing. The interrogation uses it in two layers.

The obvious layer: your notes **on this exact ticker** get folded into the prompt as *the thesis to challenge*. You wrote "services margin keeps expanding and offsets the hardware slowdown"; the adversary takes that sentence on directly.

The layer I actually care about: it also **semantically retrieves your related notes on *other* tickers** — and instructs the model, when those reveal a recurring pattern, to *name it*. Because the thing you cannot see about your Apple thesis is that it's the same "durable software moat" bet you made on three other names, one of which you journaled as a loss. A person is structurally blind to their own repeated mistakes; a system that has read all your notes at once is not. That retrieval is best-effort and private — it never leaves your machine, and if the note index isn't built yet it simply grounds in less, never in invented material.

That last bit produced the least glamorous but most important fix of the release: writing a note now quietly re-indexes your second brain in the background, so a thought you jot down actually reaches the adversary the next time you interrogate — instead of sitting unseen until some manual step. A feature that reads your notes is worthless if it can't see the note you wrote thirty seconds ago.

---

## Giving the adversary real material

An interrogation is only as good as what it's grounded in, and two blind spots were starving it.

**News that actually exists.** The news layer built its search from the ticker — fine for `AAPL`, useless for a coin or an index, where `"BTC-USD stock"` and `"^GSPC stock"` return essentially nothing. So a single shared function now resolves those to what people *actually* write about: `BTC-USD → "Bitcoin crypto"`, `^GSPC → "S&P 500"`. Because every surface — the news feed, the briefing, and the interrogation — draws terms from that one function, fixing it once lit up real headlines for crypto and indices everywhere at once. No new paid news provider; just asking the right question of the free sources already wired in.

**Sentiment that reads like a trader.** The always-on sentiment scorer is a keyword counter — it sees "beat" and cheers, and completely misses "beat, but guided down." v1.2 adds an opt-in tier that scores a whole page of headlines with the local model in **one** batched call (never one call per headline — that path runs on every list and has to stay cheap), cached per headline. It reads the way a market participant does: an earnings beat with weak guidance is bearish; a resolved overhang is bullish. And it stays honest — every headline is tagged with the engine that actually scored it, and if the model is off, it silently falls back to the keyword count rather than pretend.

---

## Why this shape

- **Two cards, not one smarter card.** I could have made the briefing "more balanced." But a single card that tries to be both salesman and skeptic ends up mush. Two cards with opposite instructions give you a real second opinion, and you can feel the difference between them.
- **Ground it in *your* words, adversarially.** Retrieval-augmented generation (*RAG* — letting the model answer from retrieved documents rather than its memory) is usually sold as a way to be *helpful* about your data. Pointing it the other way — using your own notes to find the hole in your own argument — is the same machinery aimed at the one reader it can help most: you.
- **On-demand, never automatic.** Local inference is slow, so nothing runs until you click. That's not just performance; it matches the ritual. You interrogate a thesis when you're about to act on it, not on every page load.

---

## Scope: what this is *not*

- **It is not advice.** It never tells you to buy or sell; it hands you the bear case and the base rates and lets you do the deciding. That's a deliberate line, not a limitation I'll "fix" later.
- **It does not invent.** With no local model running it says so plainly and shows nothing — the same no-fabrication rule the whole project lives by. The grounding is your notes plus the data on screen; it doesn't reach for facts it wasn't given.
- **"Challenge my thesis" moved to v1.3.** A free-text workspace — paste any argument, not tied to a ticker, and have it torn apart — was tempting to cram in here. It's the natural next step, so it gets its own room in the next release rather than a rushed corner of this one.

---

## What comes next

With the passive-integrity, private-portfolio, and now the adversarial-research pieces in place, the north star is mostly built: a terminal that won't show you fake data, won't leak your holdings, and won't flatter your thesis. **v1.3** opens the free-text "challenge my thesis" surface — the interrogation, unbound from a single ticker. And the standing invitation of this whole project holds: the model is yours, running on your machine, and its job is to be the friend honest enough to tell you you're wrong.

---

## Related reading

- [OpenTerminalUI — Retiring the Portfolio That Was Secretly Shared](/blog/en/openterminalui-retiring-the-shared-portfolio) — #6: the privacy finale that promised this "research that interrogates" release.
- [OpenTerminalUI — A Terminal That Runs on Any Brain](/blog/en/openterminalui-runs-on-any-brain) — #3: the local, provider-agnostic model client every AI feature here rides on.
- [OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) — #4: the first, passive kind of "don't get fooled."
- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(A transparency note, in this blog's spirit: this article was written by an AI agent under human direction — the same agent that did the engineering it describes, and the same kind of model that does the interrogating.)*
