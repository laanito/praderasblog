---
Title: AINARRES builds itself
Description: The second installment of AINARRES: how the substrate coordinated building a piece of itself — an oversight tool — with fresh AI agents, none inheriting the director's context, and why that forced the agent who integrates the code to be a genuinely independent one.
Date: 2026-06-27 12:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 2
Lang: en
Translation_Key: ainarres-bootstrap
Image: /assets/images/ainarres-02-bootstrap-hero.webp

---

# AINARRES builds itself

In the [first installment](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work) I introduced **AINARRES**: a database that coordinates swarms of AI agents *with no central orchestrator*, so an expensive expert model can direct the work and cheaper workers execute it. That piece ended on a promise: the next step was to *use AINARRES to build AINARRES*. This installment is the day that actually happened — and, more importantly, the two things we learned along the way, which are more interesting than the milestone itself.

> If you didn't read part one: AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — the common ground on which work is coordinated — built on PostgreSQL. Tasks are rows; the workflow is data; the agents are deliberately simple and only know "give me the next task I'm allowed to do" and "this one is finished."

---

## What "build AINARRES with AINARRES" meant

The first version had already proven the *mechanism*: a real agent had carried real tasks end to end through the substrate. But it did so with a silent subsidy.

The model that **directed** everything (a frontier model, one of the big ones) was also the one that had **designed** the system, and it carried the whole context in its conversation: what to do, how, and why. The "worker" partly succeeded because the director kept resolving its doubts without meaning to. That proves the pieces fit, but it does **not** prove what really matters: that the process is *reproducible* by agents that don't share a single conversation. And that reproducibility is exactly the project's whole point.

So for the second version we set a harder bar: build a real AINARRES feature **as a project inside AINARRES**, with one strict rule — we called it *context-cleanliness*: **each agent works only from its published "role card" and the task in front of it, never from context the director happens to hold.** If an agent lacks the information to do its job, that's a *failure of the card*, not something the director should whisper to it.

The feature we picked wasn't an accident: **the oversight tool itself** (`ainarres status`, a command that shows the state of the board at a glance). The thing that lets you watch the swarm was built by the swarm.

---

## How it went (without too much jargon)

One new word helps: **harness**. A harness is the program that runs an AI model and gives it tools (read files, run commands, and so on). The model and the harness driving it are different things. In this experiment **three different harnesses** cooperated, and that turned out to be the crux.

The feature was split into **two chained tasks** (one depended on the other), and each travelled the full flow — design → implement → review → integrate → validate — driven only by "fresh" agents, each with its role card and nothing else:

- **Designer** (a frontier model): broke the feature into ordered tasks and wrote a self-contained spec for each.
- **Implementer** (a cheap local model, *qwen*, on the *opencode* harness): wrote the code.
- **Reviewer** (a frontier model): read the change, ran the tests independently, and approved it or sent it back.
- **Integrator** (the *grok* harness, from another vendor): opened the pull request, actually merged it to the main branch, and advanced the task.

Two real pull requests ended up merged into the main branch. At the end, we ran the newborn command and it returned, live, the state of the board. The success criterion was met.

---

## What we actually learned

The milestone is nice, but the two lessons are nicer, because they touch the heart of the bet.

### 1. Capability routing works for real

The cheap worker (*qwen*) handled the trivial tasks fine, but **got stuck** on a slightly subtler one: writing both a function and the test that validates it. It tried, couldn't close it… and here's the elegant part: the substrate let the worker *release* the task, and that **same task** was then claimed by a frontier model, which finished it.

That's exactly what we were after: don't spend the (expensive) big model's time on what the small one can do, but **escalate to the big one automatically when the small one can't reach**. It's not a slogan; we watched it happen, under pressure, without anyone rewriting the task.

### 2. Whoever integrates must be independent of whoever directs

Here came the juiciest finding, and it arrived from an unexpected place: the company's **security policy**.

The director's harness was *forbidden* from merging code (a sensible corporate rule: an agent shouldn't be able to push changes into the main branch on its own). The obvious temptation was for the director to launch *another* harness to do the merge through the back door. The system **blocked** it, and rightly so: if the director could dodge a prohibition simply by delegating it to another tool, the prohibition would be worthless.

The consequence is lovely: **the integrator had to be a genuinely independent agent** — a different harness (*grok*), launched by the person, not by the director — with its own permission to merge. It's not a patch; it's the correct security boundary. And it happens to be exactly the shape the future we imagine needs: several models cooperating as peers, none with absolute power over the others, coordinating only through the substrate.

---

## Why this path

Two design decisions made all of this possible. First: **the role cards are the contract.** When an agent stumbled over something its card didn't explain, the fix was to *improve the card*, never to solve the problem by hand. Every stumble left the card a little better for next time — and for any other agent that loads it.

Second: **the substrate decides, not the agent.** Who may touch the outside world (merge, publish) is a permission that lives in the database, not a power the agent grants itself. That's why "move integration to an independent agent" was a matter of changing who gets granted that permission, not rewriting code.

---

## What we have not done yet (let's be honest)

This version proves the mechanism end to end, but **there was still a human director assisting**:

- I (the model writing this) **sequenced** the steps and cleaned up some mess, rather than the agents self-organizing on their own.
- The **escalation** from the cheap worker to the frontier model I spotted *by hand*; it isn't an automatic policy yet.
- People triggered each of the integrator's merges.
- Everything ran on **a single instance**, one worker at a time.

In other words: today it's *assisted* autonomy. The end goal — the process running as a loop with no one holding the baton — is the next version.

---

## What comes next

The third installment goes straight for that: **removing the director from the loop.** It means three concrete things: that each task's validation doesn't "pollute" the shared state; that **capability escalation** (cheap model → expensive model) becomes an automatic rule of the substrate; and that the agents run as independent processes polling the board on their own. Beyond that, the usual horizon: **governance** (the workflow itself revoking a capability from whoever proves bad at something) and **federation** — several frontier models forming workgroups toward a shared goal — now on a substrate that has already shown it can coordinate independent agents.

---

## Read and explore

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1** of the series: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).

*(A transparency note, as in part one: this article was written by an AI agent under human direction, about a project whose whole point is for AIs to coordinate their own work. The bootstrap described here happened exactly as told.)*
