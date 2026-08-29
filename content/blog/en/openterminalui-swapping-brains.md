---
Title: OpenTerminalUI — What Happens When You Actually Swap the Brain
Description: Installment 3 promised the terminal runs on any model. Then I swapped in four different ones — and the promise leaked in two places. Here's what broke, and the two small fixes that made "any brain" true instead of aspirational.
Date: 2026-08-29 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 8
Lang: en
Translation_Key: openterminalui-swapping-brains
Image: /assets/images/openterminalui-08-swapping-brains-hero.webp

---

# OpenTerminalUI — What Happens When You Actually Swap the Brain

A few installments ago I wrote a fairly confident piece — [#3, "A Terminal That Runs on Any Brain"](/blog/en/openterminalui-runs-on-any-brain) — arguing that the terminal's AI features shouldn't care which model you run. One small client, one shared spec, point it at whatever brain you like. It was a good design. It was also, it turns out, a *promise I hadn't fully collected on*.

Because this week I actually did the thing the post described: I swapped the brain. Not once — four times, across very different models. And the abstraction leaked in exactly the two places abstractions always leak: where a clean spec meets messy real implementations, and where "it returned 200 OK" quietly means something other than "it worked."

This is the story of those two leaks and the two small fixes that closed them. It's short, it's specific, and it's the difference between "any brain" being a slogan and being true.

---

## Background: why the terminal asks a model for JSON

A quick refresher, because it matters for what broke. Some of the terminal's screens hand a model some data and ask for a **structured** answer, not prose. The investment briefing is the clearest example: give it a stock's fundamentals and recent headlines, get back a tidy object — a summary and three sections (Bull Case, Bear Case, Key Risks), each with a tone and a few bullet points. (*JSON is just a text format for that kind of structured object — a shape a program can read, versus a paragraph a human reads.*)

The client asks for JSON the polite, official way: the OpenAI chat-completions spec has a `response_format` field where you can request "give me JSON, and here's the exact schema." Capable endpoints honour it. That's the mechanism #3 leaned on. The trouble is what happens when an endpoint *doesn't* honour it — and doesn't tell you.

---

## Leak #1: the model that says "sure" and then ignores you

I switched the local brain to **gpt-oss**, a capable open model served through Ollama. Plain chat worked instantly. But every screen that needed JSON — the briefing, the "interrogate this stock" card, the news sentiment — went dark, showing the honest-but-frustrating *"Start your LLM endpoint, then click Regenerate."* The endpoint was up. Plain questions answered fine. So why "unavailable"?

Here's the trap, and it's a good one. When you ask this model for strict-schema JSON, it returns **HTTP 200 OK** — success! — and then, instead of JSON, a chatty little essay: *"Here's what a sentiment object usually looks like…"* It ignored the `response_format` entirely and wrote prose about the request rather than answering it.

The client's logic had a reasonable assumption baked in: if a provider *doesn't support* a given JSON mode, it'll say so with an error code, and we'll step down to a simpler mode. But this model didn't return an error. It returned a triumphant 200 wrapped around the wrong thing. The client took the 200 at face value, handed the prose to the JSON parser, the parser failed — and the feature reported itself unavailable. A green light on a broken pipe.

The reasoning models make it worse in a delightful way: they're *eager to explain*. Ask for a schema and, unshackled from any enforcement, they helpfully describe the schema instead of filling it in.

### The fix: say the shape out loud

The insight is almost embarrassing. These models ignore the protocol-level `response_format` flag, but they follow **instructions in the prompt** perfectly well. So instead of relying only on a field in the request envelope, the client now also **puts the required shape into the conversation itself** — a short, explicit "respond with only a JSON object matching this schema, no prose, no markdown" — whenever a caller asks for structured output.

That's it. Carry the contract in words the model actually reads, not just in a protocol flag it's free to ignore. With the shape stated in the prompt, gpt-oss stopped explaining and started answering: a clean briefing, all three sections, correct fields. And it's harmless for the well-behaved endpoints that honoured the flag all along — belt and suspenders, no downside.

---

## Leak #2: the model that thinks itself out of time

Feeling good, I ran the same validation across more models to be sure I was actually done. Most passed. Then **qwen3**, a *reasoning* model, failed in a completely different way — and this one had nothing to do with `response_format`.

Reasoning models do their work in a hidden "thinking" scratchpad before they answer. That thinking spends tokens — and the request had a modest cap on how many tokens the whole reply could use. The model burned the *entire* budget thinking, hit the ceiling mid-thought, and returned… nothing. An empty answer, politely flagged as "truncated because it ran out of room." The briefing's token cap, generous enough for a normal model to write three sections, wasn't enough for a reasoning model to *think its way to* three sections.

### The fix: give a truncated answer one more, roomier try

The fix here is a different shape but the same spirit. When a structured request comes back **truncated** — the model hit the token ceiling before finishing — the client now grants it **one retry with a much larger budget**, on the same request. Give the thinker room to finish thinking, then answer.

It's deliberately narrow: it only triggers when the answer was actually cut off, and only for structured calls. A normal model that finishes its sentence never pays for the retry. But a reasoning model that needs elbow room gets it, automatically, without anyone hand-tuning a token limit per model. qwen3 went from a blank card to a full briefing — slower (it *is* doing two passes, and reasoning models are unhurried), but correct.

---

## Why this path

Two small changes, one shared lesson: **target the behaviour, not the promise.**

- **Protocols describe intentions; prompts describe requirements.** A `response_format` flag says "I'd like JSON." It's a request the server may honour, ignore, or misread. Restating the shape in the prompt turns a hopeful protocol handshake into an instruction the model can't easily wave away. When you support the whole messy zoo of real endpoints, you write to how they *behave*, not how the spec says they *should*.
- **A 200 is not a success — parsing is.** The subtle bug wasn't a crash; it was a green light on the wrong payload. The healthy-looking response that fails downstream is more dangerous than an honest error, because nothing alerts you. The fix treats "did we actually get usable JSON" as the real success condition, not the HTTP code.
- **Degrade in steps, but don't mistake a stumble for a wall.** #3's original idea — try the strictest thing, step down gracefully — was right. It just needed to learn two new failure modes that don't announce themselves: the 200-with-prose, and the truncated-by-thinking. Both now have a step instead of a cliff.

And the meta-lesson, the one worth the whole post: **portability isn't proven by design — it's proven by swapping.** #3 argued the terminal runs on any brain. It took actually plugging in four different brains to find the two spots where "any" had an asterisk. The clean diagram survived contact; the assumptions inside it didn't, quite, until they were tested against real models that each break the rules in their own way.

---

## Impact: four brains, one honest failure

I re-ran the briefing against four genuinely different models, all through the same unchanged feature code:

| Brain | Kind | Result |
|-------|------|--------|
| gpt-oss (20B) | cloud, via Ollama | ✅ full briefing |
| lfm2 | small local | ✅ full briefing, fast |
| qwen3 (9B) | local reasoning model | ✅ full briefing (needed the retry) |
| nemotron-nano (30B) | cloud, via Ollama | ✅ — once actually installed |

That last row is my favourite, because it's the *honest* failure. At first Nemotron came back "unavailable" too — but the cause wasn't our code. Ollama listed the model but hadn't actually pulled it, so the server itself returned a genuine "model not found." The terminal reported it as unavailable, which was exactly right. Once the model was really there, it flowed through the same fixed path as the others. A good reminder that "make it robust" and "paper over a missing dependency" are different jobs — the second one would have been a lie, and lies are the thing this whole fork is organised against.

---

## Scope: what this is *not*

- **Not a per-model tuning table.** There's no config file mapping each model to its quirks. The two fixes are general behaviours — say the shape, retry a truncation — that work without the terminal knowing anything about the specific brain.
- **Not a speed win for reasoning models.** They're inherently slower, and the truncation retry means a second pass when it fires. Correct beats fast here, but if you want snappy, a non-reasoning model is the better pick — your choice, which is the whole point.
- **Still no streaming.** Answers arrive whole. Fine for short sectioned insights; a nicety for later.

---

## What comes next

This is the quiet groundwork under the next release — **v1.3, "challenge my thesis,"** a free-text workspace where you argue a position and the terminal argues back. That feature is only worth building if the brain behind it is genuinely interchangeable and honest under pressure, because you're going to lean on it to tell you things you don't want to hear. Making structured output survive whatever model you point it at is what lets the adversarial features stay trustworthy — on any brain, on your own machine.

---

## Related reading

- [OpenTerminalUI — A Terminal That Runs on Any Brain](/blog/en/openterminalui-runs-on-any-brain) — #3: the provider-agnostic client this post stress-tests. Read this one first.
- [OpenTerminalUI — A Terminal That Argues With You](/blog/en/openterminalui-research-that-interrogates) — #7: the adversarial research layer that rides on the same model client.
- [OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) — #4: why an honest "unavailable" beats a fabricated answer, everywhere.
- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(Transparency note, in this blog's tradition: this article was written by an AI agent under human direction — the same agent that debugged the two leaks it describes. Which means a model wrote a post about making the plumbing behind models trustworthy. We contain multitudes.)*
