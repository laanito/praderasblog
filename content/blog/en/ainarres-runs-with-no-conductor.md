---
Title: AINARRES runs with no conductor
Description: The third installment of AINARRES: the day I started the loop, walked away, and a real feature reached the main branch on its own — with no person and no director sequencing, re-routing, or fixing anything along the way. And why the two runs that failed taught more than the one that worked.
Date: 2026-06-30 12:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 3
Lang: en
Translation_Key: ainarres-hands-off
Image: /assets/images/ainarres-03-hands-off-hero.webp

---

# AINARRES runs with no conductor

The [second installment](/blog/en/ainarres-builds-itself) ended on a confession and a promise. The confession: although AINARRES already built parts of itself, **there was still a human director assisting** — I sequenced the steps, spotted by hand when to escalate from the cheap model to the expensive one, and cleaned up the odd mess. The promise: the third installment would go straight at that — **take the director out of the loop**. This is the installment where it happened. I started the process, got up from my chair, and a real feature showed up merged into the main branch without my touching anything.

But — as in the previous installment — the clean milestone is the least of it. The interesting part is the **two runs that failed first**, because they taught exactly what it means for a system to work *with nobody holding the baton*.

> If you're new here: AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — the common ground on which work is coordinated — built on PostgreSQL. Tasks are rows; the workflow is data; the agents are deliberately simple and only know "give me the next task I'm allowed to do" and "this one is finished." There is no orchestrator: each agent *pulls* from the queue whatever it's permitted to do.

---

## The three promises, kept

The previous installment put in writing what it would take to remove the director. It was three concrete things, and it's worth telling how each one was solved, because together they're what makes a loop with nobody possible.

**1. That validating a task doesn't "pollute" the shared state.** Each task, before it's accepted, is validated by running real tests. The catch: those tests spin up and tear down their own database — and if that database is *the same one* the swarm coordinates its work on, validating one task wipes out the board for the others. The fix was to split in two: one instance for the swarm to coordinate on (the "live" board) and another, identical one, for the tests to do whatever they like. Now an agent can rebuild its test environment from scratch without grazing anyone else's work.

**2. That capability escalation is an automatic rule of the substrate.** In the previous installment, when the cheap model got stuck, *I* noticed it was time to hand the task to a more capable one. Not anymore: the substrate keeps a count of each task's attempts and, after a few, **the task itself automatically demands a higher tier** — and from then on only a frontier-grade model can claim it. Nobody re-routes anything; the rule lives in the database.

**3. That the agents run as independent processes polling the board.** Instead of me launching each agent when its turn came, there's now a **dumb launcher**: it takes the brief, hands it once to a designer to break into tasks, and then sweeps the workers **from lowest to highest capability, in rounds**. The cheap models do the bulk of the work; the expensive model is the *ceiling*, not the default. And — a detail that matters more than it looks — **it knows when to stop**: when a whole round moves nothing on the board, the loop ends, instead of burning money forever.

---

## The day with no director

With the three pieces in place, the experiment is almost disappointingly simple to describe. I wrote a brief in a text file — "add a command that says how much life a credential has left" — ran one command, and stepped away.

From there, without my intervening:

- A **designer** (frontier model) read the brief and turned it into a self-contained task, with its spec and its validation criterion.
- A **cheap implementer** (a low-cost model on the *opencode* harness) claimed the task, wrote the code and the test, validated it on its own, and pushed it to review.
- The **reviewer** and the **integrator** (the independent *grok* harness) read the change, approved it, opened the *pull request*, and actually merged it into the main branch.

The board drained in **a single round**. The feature — `ainarres ttl`, which also builds on one the swarm itself had shipped in an earlier batch — landed live on the main branch, its test green. No person sequenced, re-routed, or patched anything while it ran. That is, precisely, taking the director out of the loop.

---

## What we actually learned (the failed runs)

Before that clean day there were two attempts that ended in a strange state: the feature got built fine… but the board ended up with junk on it, and the launcher, rightly, refused to say "done." The fascinating part is **why** they failed: not because any agent was incompetent, but because of something far subtler.

It turns out one of the agents, while validating, ran the *entire* test battery (it shouldn't have, but it did). And that battery, by the way it was wired, **pointed at the live database** — the swarm's — instead of its own. The result: the tests seeded their own fake data onto the real board, and the other agents were left circling phantom tasks.

The temptation, there, is obvious: *"tell the agent not to run the whole test battery."* And it's the wrong lesson. In a system with no director **you can't lean on every component behaving**; you have to make misbehaving *harmless*. So the fix asked nothing of the agent: it made the test battery, no matter who launches it or from where, **incapable** of touching the live board. We put the boundary in the wiring, not in good intentions.

It's the same lesson as the previous installment, seen from another angle. There we found that whoever integrates the code has to be *independent* of whoever directs, because a prohibition you can dodge by delegating it is no prohibition. Here: an invariant that depends on an agent "remembering not to do something" is no invariant. **The substrate enforces; you don't trust.** That, in one phrase, is the whole bet of the project.

(The second lesson, smaller but dear: **knowing when to stop is a feature**. A loop of agents that doesn't know when there's no work left is an open invoice. Having the launcher end on its own when a whole round moves nothing is what separates an experiment from a resource bleed.)

---

## What we haven't done yet (let's be honest)

As always, it's worth marking the line between what's demonstrated and what's promised:

- **I still chose the brief and started the loop.** "No director" refers to what happens *during* the run: nobody sequences or rescues. But someone still presses the start button and decides what gets built.
- **The integrator is launched by a person**, not the loop — and on purpose: it's the security boundary we described in installment 2, where the director is *forbidden* to merge on its own.
- It all ran on **a single instance, with one worker per tier**. No several frontier models cooperating yet.

Put another way: we've removed the director from the *conducting*, not from the *starting*. The next step — the system working unattended against a criterion the person doesn't even choose — is the close of this version.

---

## What's next

From here the horizon is the usual one, but closer. First, finishing the autonomy: a genuinely **unattended** trial, where the only human role is to switch the system on. After that, the two big ideas that have been on the table from the start: **governance** — the workflow itself withdrawing permissions from whoever proves they don't do their job well — and **federation**: several frontier models forming teams toward a common goal, coordinating only through a substrate that has already shown, end to end, that it can coordinate independent agents with nobody holding the baton.

---

## To read and explore

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design, the plans, and the retrospective for each milestone live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **2**: [AINARRES builds itself](/blog/en/ainarres-builds-itself).

*(Transparency note, as in the earlier installments: this article was written by an AI agent under human direction, about a project whose whole point is, precisely, for AIs to coordinate their own work. The director-less loop described here — and the two runs that failed before it — happened exactly as told.)*
