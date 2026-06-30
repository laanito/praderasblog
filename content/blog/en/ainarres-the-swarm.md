---
Title: AINARRES as a swarm: when a worker falls, the work goes on
Description: The fourth installment of AINARRES: the leap from doing one task at a time to many agents working at once. And the moment that explains it best — a worker hung mid-task, I killed it, and another agent finished the job on its own, with nobody reassigning anything.
Date: 2026-07-01 12:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 4
Lang: en
Translation_Key: ainarres-swarm
Image: /assets/images/ainarres-04-swarm-hero.webp

---

# AINARRES as a swarm: when a worker falls, the work goes on

Halfway through a run, something small happened that captures this whole installment better than any diagram.

One of the cheap workers — a low-cost AI model running on a free API — **hung** in the middle of its task. The API had hit its request limit and was returning error after error. The worker was never going to finish. So I did the only thing you do with a stuck process: I **killed** it.

And then, without me touching anything else, the interesting part happened. The substrate noticed that task had been left **orphaned** — claimed by someone who was no longer there — **released** it, and on the next pass **another, more capable agent claimed it and finished it**. Nobody reassigned the task. Nobody said "hey, so-and-so crashed, let someone else take it." I just killed a stuck process; the system handled the rest.

That, in one sentence, is the heart of the fourth installment: **a worker can die mid-task and the work still reaches its destination.** But getting there took a bigger leap first — from *one at a time* to *many at once* — and, as always in this project, the attempt that *failed* taught the most.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — the common ground work is coordinated on — built on PostgreSQL. Tasks are rows; the workflow is data; the agents are deliberately simple and only know "give me the next task I'm allowed to do" and "this one's done." **There is no orchestrator**: each agent *pulls* from the queue whatever it has permission to do. The first three installments built that and showed a single agent could develop AINARRES itself, with no one conducting.

---

## From pipeline to swarm

Until now, AINARRES worked like a **pipeline**: one task at a time. A worker took something, did it, handed it on, and only then did the next thing begin. It worked and it was *correct* — the third installment proved that — but it was slow by design, deliberately, to isolate one variable at a time.

The fourth installment lifts that restriction. The goal is a **swarm**: several independent agents working **at the same time** toward one goal. A designer splits the brief into independent tasks where it can; a pool of workers picks them up in parallel; and the result is integrated so the main branch **never breaks**.

That raises three problems that didn't exist when there was only one worker:

1. **They mustn't step on each other.** Two agents editing, committing, and switching branches in the *same* copy of the code corrupt each other's work.
2. **They mustn't grab the same task.** If three pull from the queue at once, none should walk off with what another already took.
3. **The main branch must stay coherent.** If three things finish at once, they can't be merged blindly on top of one another.

The neat part is that the second problem was **solved from the start**: the database hands out tasks with a lock that guarantees each one goes to a single agent, no matter how many pull at once. The serialization — the "one at a time" — lived only in the *launcher*, by choice. Removing it was mostly a matter of stopping the deliberate braking.

---

## A clean room for every worker

The first problem — not stepping on each other — is solved by giving each worker its own **clean room**: its own copy of the code (a git *worktree*), spun up instantly, where it can edit and commit without anyone else seeing it. When it finishes, its work lives on its own branch; the disposable copy is thrown away.

The third problem — coherence — is solved with a **single integrator**. While many *implement* at once, one piece does the **merging**, and it does it one at a time: it takes the branch, **rebases it onto the latest main**, **re-validates** that it's still green after that rebase, and only then merges it. If there's a conflict it can't resolve cleanly, it **sends the task back** rather than forcing a dirty merge. That serialized merge queue is exactly what lets implementation run wide without the main branch ever breaking.

---

## The honest failure (which is where you learn)

The first swarm run looked like a success: three tasks, three pull requests, all green. But looking at the event log — that ledger the previous installment taught us to read — the swarm was an illusion: **one worker had done all three tasks, one after another**. There was no parallelism.

Why? The three processes did start, but the tool the cheap workers use (*opencode*) keeps its own state in a local database… that's **shared**. Three processes at once collided on that file — "database is locked" — and two of the three died at startup. The survivor did all the work serially.

And here's the lesson, which is prettier than it sounds. The substrate was working perfectly; the task lock was working; the git clean rooms were working. What was missing was isolating **the tool's own internal state**, not just the project's files. The fix was to give each worker not only its own copy of the code, but **its own tool memory**.

Put another way: we're *pretending*, on a single laptop, to be three different machines. And on three different machines each agent would have, for free, its own copy of the code and its own tool. Isolating both on one laptop isn't a dirty hack — it's **faithfully simulating** what real distribution would hand you for nothing.

---

## Why "is it faster?" was the wrong question

The obvious way to measure a swarm is the stopwatch: does it take less time than the pipeline? But that's a bad question when, as here, you're **mimicking a distributed system on a single laptop**. On one machine, real parallelism is capped by things the substrate doesn't control: shared CPU, a free API that serves requests nearly in line anyway, each tool's local state. The stopwatch ends up measuring the cost of the *mimicry*, not the coordination.

The north star isn't "faster on this laptop." It's that the substrate coordinates **genuinely independent workers that could be on different machines, different networks, or different universes** — sharing nothing but the substrate itself. So the real test isn't a number of seconds, it's a question of **correctness**: were there truly several distinct workers, each isolated, carrying distinct tasks **at the same time**, and did it all reach a coherent main branch?

The answer was yes. The live board showed, at one instant, **three distinct workers holding three distinct tasks in progress**, claimed seconds apart. All three merged to a green main branch. AINARRES built a real, multi-part feature of itself with several agents at once, with no one coordinating. The same run would hold just as well with the workers scattered across N machines.

---

## And back to the worker that fell

With all of this in place, that small moment at the start takes on its full meaning. A worker fell — for a very real weakness: a free API's limit — and the work was **neither lost nor stuck**. The substrate released the orphaned task and a capable peer finished it. Nobody held the baton.

It's the same bet as always, from another angle. In a system with no director you can't trust each piece to behave: you have to make **misbehaving — or crashing — harmless**. A worker that dies isn't an emergency; it's an anticipated case. And one honest detail: my *killing* it only sped up what the system does on its own — in a fully unattended run, the task's "lease" would have expired by itself and another would have picked it up anyway, with no one touching a thing.

---

## What we haven't done yet (let's be honest)

- It all ran on **a single laptop**. We simulate distribution (each worker with its own isolated copy and tool), but there aren't yet real agents on different machines.
- The **integrator is still launched by a person**, on purpose: it's the safety boundary from installment 2 — whoever merges can't be whoever directs.
- The **reviewer is a single one** and works serially; with small tasks, that dominates the clock. It has a fix (a pool of reviewers) the day it gets in the way; integration, by contrast, will stay one-at-a-time so coherence holds.
- The experiment's tasks were **small** on purpose, to keep the swarm clean to measure.
- And, as always: **a person still chooses the brief and presses the start button.**

---

## What's coming

The next step is the one this installment keeps hinting at: **federation**. So far the "swarm" is several cheap workers of the same kind plus a frontier model acting as a ceiling. Federation is **several different frontier models** — from different makers — sharing the roles as **equals**, with none ruling the others. That's where "they could be on different machines, networks, or universes" stops being a metaphor. After that, the other big idea that's been on the table from the start: **governance** — the workflow itself revoking permissions from whoever proves they don't do their job well.

---

## To read and explore

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **2**: [AINARRES builds itself](/blog/en/ainarres-builds-itself).
- Installment **3**: [AINARRES runs with no conductor](/blog/en/ainarres-runs-with-no-conductor).

*(Transparency note, as in the previous installments: this article was written by an AI agent under human direction, about a project whose very purpose is for AIs to coordinate their own work. The swarm described here — and the worker that fell mid-task — happened exactly as told.)*
