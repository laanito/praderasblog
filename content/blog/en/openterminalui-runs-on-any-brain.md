---
Title: OpenTerminalUI — A Terminal That Runs on Any Brain
Description: The AI features in OpenTerminalUI don't care which model you run. One small client speaks a single spec — so the same code works against a local Ollama, LM Studio, or a hosted API, and degrades honestly when there's no model at all.
Date: 2026-07-08 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Privacidad, Sistemas
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 3
Lang: en
Translation_Key: openterminalui-run-on-any-brain
Image: /assets/images/openterminalui-03-any-brain-hero.webp

---

# OpenTerminalUI — A Terminal That Runs on Any Brain

Most of the OpenTerminalUI story so far has been about *data* — being honest about market numbers, making the portfolio private. This one is about the *brain*: the language model behind the terminal's AI features. The design goal was blunt and a little contrarian for 2026 — **the terminal must not care which model you run.** Local or hosted, open weights or a big API, your laptop or someone's cloud: same code, same features, and if there's no model at all, an honest "unavailable" instead of a fabricated answer.

This is the deep-dive I owed from [installment 1](/blog/en/openterminalui-forking-a-financial-terminal) as #3 — arriving after the milestone posts, but it's the piece that makes the AI features in [1.0](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) and the portfolio work actually portable.

---

## Background: what "AI features" even means here

A few of the terminal's screens hand the model some structured data and ask for a short, sectioned analysis: an investment briefing for a stock, a plain-English read on a backtest, a risk narrative for your portfolio. Separately, the private "second brain" needs **embeddings** — numeric fingerprints of text so it can find your notes by meaning, not keyword. (*An LLM is a large language model — the thing that writes the prose. Embeddings are a different call to the same kind of server that turns text into vectors for search.*)

All of that needs to talk to *some* model server. The question is **which**, and the answer this fork insists on is: **whichever you point it at.**

---

## The problem: one integration per provider is a trap

The pre-fork lineage had gone the usual route — provider-specific code. There was an LM Studio client, and the assumption that you'd run *that*. It works right up until you don't want LM Studio: now you're writing a second integration for Ollama, a third for a hosted API, each with its own quirks, and the features fork into "works with X but not Y." For a self-hosted, **private-first** terminal that's exactly backwards — the whole point is that *you* choose where inference happens, ideally on your own machine.

There's a happy accident that makes the trap avoidable: almost everything now speaks the **OpenAI chat-completions spec**. Ollama, LM Studio, vLLM, llama.cpp, OpenAI itself, OpenRouter, Groq, Together — they all expose the same `POST /chat/completions` shape. If you target the *spec* instead of a *product*, "which provider" collapses into one line of configuration.

---

## What we did

The fork replaced the provider-specific paths with **one small async client** that speaks that shared spec — chat completions and its embeddings sibling — and nothing else. A few decisions gave it its character:

- **Point it anywhere with a base URL.** The default is a local Ollama (`http://localhost:11434/v1`); change one setting and it's LM Studio, or a hosted endpoint. Local servers ignore the API key; hosted ones get it as a bearer token — so the *same* client covers both without a branch.
- **A structured-output ladder.** Some screens need JSON back, not prose. But providers disagree on how strict JSON is requested: OpenAI-grade endpoints accept a full `json_schema`; others only a loose "JSON mode"; some, nothing at all. So the client tries the strictest form first and **steps down** — strict schema → loose JSON → plain text — whenever an endpoint rejects the previous one. You get the most structure a given brain supports, never a hard failure because it didn't support the fanciest option.
- **Honest degradation, not fabrication.** Before asking anything, a feature checks the endpoint is reachable; if the model is off or unreachable, the answer is a labelled *"AI analysis is unavailable — start your local model."* Never an invented one. That's the same integrity rule the rest of the terminal follows, applied to the brain.
- **One client, many features.** The briefings, the risk narrative, the news-sentiment read, and the second brain's embeddings all go through this single client and one shared output schema — so a single frontend card renders every kind of insight, and there's exactly one place to reason about timeouts, keys, and fallbacks.

The old LM Studio-specific code didn't get a dramatic funeral; its settings live on as quiet back-compat aliases so nobody's existing config breaks, and everything now routes through the one client.

---

## Why this path

- **Target the spec, not the vendor.** Betting on the OpenAI-compatible shape (rather than any one product) is what turns "support another provider" into "change a URL." It's the cheapest kind of future-proofing: the ecosystem already converged; we just leaned on it.
- **Degrade in steps, not cliffs.** Assuming every endpoint supports strict JSON schemas would break half the local servers people actually run. The ladder means the feature works on a modest local model *and* gets stricter guarantees on a capable one — same code.
- **Local by default is a privacy stance, not a demo shortcut.** Defaulting to Ollama on `localhost` means the out-of-box experience sends your holdings and notes to **your own machine**, not a third party. Pointing it at a hosted API is a deliberate opt-in you make, not the default you have to escape.

---

## Impact

The practical payoff: you can run the terminal's AI on a laptop with Ollama and never send a byte of portfolio data off-device — or point it at a big hosted model for a sharper analysis — and **nothing in the feature code changes**. Adding a new provider is configuration, not development. And because embeddings ride the same client, the private second-brain search is just as portable as the prose features.

It also keeps the AI features *honest by construction*: with no model configured they say so, plainly, everywhere — the risk narrative, the briefings, the search. No screen quietly invents an analysis to look busy.

---

## Scope: what this is *not*

- **No streaming yet.** Responses come back whole, not token-by-token. Fine for short sectioned insights; a future nicety for longer generations.
- **Best-effort structure, not a guarantee.** The ladder maximises structure per provider, but a small local model can still return something the schema can't fully honour — so the parsing tolerates code fences and stray prose, and features sanitise what they get rather than trusting it blindly.
- **Not a model recommender.** The terminal runs *your* choice of brain; it doesn't rank models or manage their lifecycle. That's yours (or Ollama's) to own.

---

## What comes next

This client is the quiet foundation the next release leans on hard: **v1.2 — research that interrogates**, an "is this hype?" layer that uses your local model and your own notes *adversarially* rather than as a cheerleader. When the brain is interchangeable and private by default, an interrogation feature is something you can actually trust to stay on your side of the screen. Still owed from #1: the wiring-audit detective story (#2).

---

## Related reading

- [OpenTerminalUI — Forking a Financial Terminal to Work Beyond India](/blog/en/openterminalui-forking-a-financial-terminal) — #1: the premise and the data-layer rebuild.
- [OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) — #4: why the first stable release refused to show fabricated data as live.
- [OpenTerminalUI — Retiring the Portfolio That Was Secretly Shared](/blog/en/openterminalui-retiring-the-shared-portfolio) — #6: the private-portfolio consolidation, where the local-first AI narrative shows up.
- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(A transparency note, in this blog's spirit: this article was written by an AI agent under human direction — the same agent that did the engineering it describes. Fitting, for a post about which brain runs the show.)*
