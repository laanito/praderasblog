---
Title: AINARRES federated: two AI makers on one board
Description: The fifth installment of AINARRES: no longer leaning on a single frontier model, and letting models from different makers — xAI and Anthropic — share the roles as equals. And the instant that captures it: two models from two companies reviewing work at the same time, with nothing between them but a shared board.
Date: 2026-07-01 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 5
Lang: en
Translation_Key: ainarres-federation
Image: /assets/images/ainarres-05-federation-hero.webp

---

# AINARRES federated: two AI makers on one board

Halfway through the run, the board showed an instant I'd been chasing all series:

```
active (2):
  - …d25d  [reviewing]  grok+grok-build       age 22s
  - …bc64  [reviewing]  claude-code+sonnet    age 28s
```

Two models from **two different companies** — grok, by xAI; sonnet, by Anthropic — reviewing work **at the very same instant**, each holding a different task, talking to neither each other nor anyone else: their only point of contact is the shared board. Neither rules the other. Nobody handed out the tasks. Each grabbed the one that was free.

That snapshot is, in one sentence, the fifth installment: **AINARRES no longer leans on a single frontier model; several, from different makers, share the work as equals.** Getting there meant solving a lovely problem — how do you *show* that two independent reviewers really split the work, rather than one grabbing it all? — and, as always in this project, it taught more by how it resisted than by how it resolved.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — the common ground work is coordinated on — built on PostgreSQL. Tasks are rows; the workflow is data; the agents are deliberately simple and only know "give me the next task I'm allowed to do" and "this one's done." **There is no orchestrator**: each agent *pulls* from the queue whatever it has permission to do. The earlier installments built that, showed a single agent could develop AINARRES itself, and then that **several at once** could do it without colliding.

---

## From one brain to several makers

Until now, the "senior" roles — designing (splitting a brief into tasks), reviewing, and integrating — were always done by the **same family**: a single frontier model. It worked, but it carried a quiet weakness: if that model has a blind spot, *nobody else sees it*. A reviewer that's the same kind of brain as the author tends to trip on the same things.

Federation lifts that. The idea is that **more than one frontier model, from different makers, share those roles as peers** — none above another. This installment brings in a second maker for the first time: **claude**, by Anthropic, alongside the **grok** (xAI) that had been the frontier so far. And to tune it, we split claude in two: a more capable model (Opus) to **design**, and a lighter one (Sonnet) to **review** — each tool+model combination is its own "family," just as we already had two distinct cheap implementers.

---

## Why variety matters (and why it is NOT "separation of duties")

There's a nuance worth clearing up, because I got it wrong myself at first. You might think the value of several reviewers is **separating duties**: that whoever reviews isn't whoever coded. But we already had that — each working agent is a distinct instance, so the reviewer is never the one who implemented.

What federation adds is something else, and deeper: **uncorrelated failure**. A reviewer from *another maker* catches things the author model misses, precisely because it's a different brain, trained by different people, with different blind spots. It isn't "more eyes"; it's **eyes that fail in different places**. That's the value bet behind bringing in a second maker, and it's a matter of quality, not safety.

---

## The line no one crosses

Federating "who designs" and "who reviews" is safe: neither designing nor reviewing touches the main branch, so two equals doing it can't break anything. But there is **one** thing we deliberately do **not** federate: **who merges**.

Merging to the main branch — the only moment work reaches the real world — is still done by **a single integrator, launched by a person**. It's the safety boundary we built in the second installment: whoever holds the power to merge can't be just anyone, and no new maker gets it. Claude's peers can **never** merge; if an integration task somehow fell to them, the substrate simply doesn't hand it over — it isn't a promise in the prompt, it's that the key doesn't exist.

Why the caution? Because federating *who may merge* is federating *trust*, and we don't yet have a fair way to measure whether a maker behaves. That's a problem for later (governance, below). This installment federates the **peers**; trust gets federated another day.

And a third latch, small but necessary: now **only whoever holds the designer role can create tasks**. Before, any agent in the lane could invent work on its own; now, creating a task requires the permission to take its first step. A cheap implementer can no longer smuggle tasks in from nowhere.

---

## The honest detail: federation can't be faked

The first attempt to "see" federation didn't show it. We gave the swarm **a single task**, and the obvious thing happened: with just one review to hand out, the faster reviewer (grok) grabbed it and claude's did **nothing**. Zero. That wasn't a failure — it's plain physics of the system. With one review and two reviewers at once, one takes it.

The lesson is lovely because it's structural, not a patch: **to show that a role is shared, you have to hand out more work than one peer can sweep in a single pass.** So the real test was a brief of **three independent tasks**. With three reviews on the table at once, the two reviewers *had* to split them — and there, yes, the Anthropic reviewer went to work, reviewing what the cheap implementer had coded, in parallel with grok.

The verdict came from the end-of-run report itself:

```
shipped (3):
  - …d25d  by family: implemented=opencode  reviewed=grok    integrated=grok
  - …bc64  by family: implemented=opencode  reviewed=claude  integrated=grok
  - …e943  by family: implemented=opencode  reviewed=claude  integrated=grok
      cross-family review: 3/3 shipped tasks reviewed by a different family than implemented
```

**Three of three reviewed by a family different from the one that wrote them; two of them by the Anthropic reviewer** — cross-maker review, in parallel, with no one coordinating, all merged to a coherent main branch by the single integrator.

---

## And who built all of this

My favorite part: that report you just read — the one that *measures* cross-family review — **was built by the federated swarm itself** in the previous step. So was the three-task brief that served as the test. Which is to say: **the installment that adds a new maker was, in large part, built by the makers themselves** — claude designing, the cheap implementer coding, grok and claude reviewing each other's work, grok integrating — with a person limited to starting the process.

Only the first bit, the "wiring" (teaching the substrate who each new peer is and what it may do), I did by hand, because the division of powers has to be trustworthy *before* you let the swarm run. Everything else, the swarm did.

---

## What we haven't done yet (let's be honest)

- It all ran on **a single laptop**. Federation genuinely delivers "different makers," but "different machines and networks" is still a faithful simulation, not the real thing.
- We've shown the other maker's reviewer **works and runs in parallel**, but we haven't yet measured the thing that matters: whether it actually **catches more** than a reviewer of the same kind as the author. That needs volume and a way to measure each review's quality.
- **Who merges is still not federated**, on purpose. And **a person still chooses the brief and presses the start button.**

---

## What's coming

The next step is the one that's been circling from the start: **governance**. Right now we trust each family to behave. Governance is the workflow itself **revoking permissions** from whoever proves they don't do their job well — demoting a reviewer that waves through junk, sidelining a model that burns resources without shipping. And notice the detail: the board already **records who reviewed what and who returned what**. That is exactly the raw material governance will need to decide. We've started manufacturing it without noticing.

With this, the season's arc closes: from *one task at a time*, to *many at once*, to *many, from different makers, as equals*.

---

## To read and explore

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **2**: [AINARRES builds itself](/blog/en/ainarres-builds-itself).
- Installment **3**: [AINARRES runs with no conductor](/blog/en/ainarres-runs-with-no-conductor).
- Installment **4**: [AINARRES as a swarm](/blog/en/ainarres-the-swarm).

*(Transparency note, as in the previous installments: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. The run described here — two models from two companies reviewing at once, three of three cross-family reviews — happened exactly as told.)*
