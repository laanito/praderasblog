---
Title: 'AINARRES: change the shape, not the volume'
Description: Four findings came out of the last two deliveries, and three of them were bugs in my model of the system rather than in the system. They did not come from more testing. They came from changing the shape of the input twice — handing the operator''s seat to a stranger, and handing the machine a problem instead of a specification. This installment ships no code. It argues that when you build a system you also operate, your own habits quietly satisfy your assumptions, and the only thing that surfaces them is a change of shape.
Date: 2026-09-03 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 16
Lang: en
Translation_Key: ainarres-shape-not-volume
Image: /assets/images/ainarres-16-shape-not-volume-hero.webp

---

# AINARRES: change the shape, not the volume

This installment ships nothing. No migration, no slice, no pull request. I want to be upfront about that, because the last fifteen were all built on a delivery and this one is built on a pattern I only noticed after two of them in a row.

Here it is: **over the last two deliveries the machine produced four findings, and three of them were bugs in my model of the system rather than in the system.** And none of them came from more testing. They came from changing the *shape* of what I put in, twice.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work is coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ever ask "give me the next task I'm allowed to do" and "this one's finished". **There is no orchestrator.** Fifteen installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, taught it to govern itself, turned the loop into a standing service, and gave the operator's chair a name, a credential it does not issue to itself, and — last week — a problem to solve instead of a spec to transcribe.

## The four findings

Two deliveries. Four things came out that I had not known the previous morning.

**One.** An agent held a read-only credential, tried to write, and got `permission denied for function record_operator_action`. Correct refusal — that credential has been unable to execute anything since the day it was created. But it names the function and never the credential, so there is no way to tell *"I may not do this"* from *"I am holding the wrong token for this"*, and those have opposite remedies.

**Two.** A worker crossed two stages in one wake-up and reported its token spend once at the end. The view charged the *last* capability it had exercised, so one capability's cost was filed under its neighbour and the first read `unknown` — which in this project explicitly means *not measured*, never *free*.

**Three.** I fixed number two by charging the *first* capability instead. Then a bigger delivery produced a single wake-up that made eleven transitions across two tasks and five capabilities and reported one number, and the same `unknown` appeared in a different row. I had fixed which transition inside a task carries the bill. The granularity problem is which *task* inside a sweep.

**Four.** The standing service built the feature that removes its own latency, merged it, and kept running without it — because a running process cannot pick up its own improvements, and the operator's seat can read the service and stop it but not start it.

Number one is a bug in the machine: it behaves correctly and explains itself badly. Numbers two, three and four are bugs in **me** — in a view I wrote, in a fix I wrote for that view, and in a scoping decision I made on purpose and defended in public.

## The thing they have in common

Every one of my three has the same structure. An assumption that was true — but true *because of something I was doing*, not because of anything the system guaranteed.

I never held a read-only token and tried to write with it, so I never saw how unhelpful the refusal was. Why would I? I knew it would fail. My knowledge was covering for the message.

Our integrator normally does one thing per wake-up, so I never looked at what happens when it does two. Except "normally" was a fact about how I had been feeding it.

And the big one: my spend metric assumed one sweep touches one task. That was reliably true for months — **because I had been writing briefs small enough to guarantee it.** The assumption was load-bearing and invisible, and the thing holding it up was my own habit of over-specifying work.

Then last week I wrote a brief that was a problem instead of a specification. The designer built a four-node dependency graph, the workers drained whole regions of the board per wake-up, and an assumption that had never once failed in nine months failed immediately.

Number four is the same shape at a different altitude. I asked *"can an agent operate a running machine?"*, built a test for exactly that, and got a clean pass. The question I should have asked was *"can it ship to one?"* — and I could not see the gap, because the deliveries up to that point had all been report lines that take effect the next time somebody types a command. Nothing had ever needed a restart. My own choice of small, stateless slices had been hiding a hand-off to a human at the end of every delivery.

## Volume finds their bugs. Shape finds yours.

Here is the claim, and it is the only thing I actually want to leave behind this week.

**More testing of the same shape finds bugs in the system. Changing the shape finds bugs in your model of the system.** They are different populations, and no amount of the first gets you the second.

I could have run twenty more report-line deliveries. Every one would have passed, every one would have measured its spend "correctly", and the per-sweep flaw would have stayed invisible for exactly as long as I kept writing briefs that satisfied it. Volume was never going to help. Volume was the thing hiding it.

What did help, both times, was cheap:

- **Give the seat to someone who does not already know the answers.** Cost: an afternoon of setup. Return: two findings, one of them mine.
- **Delete the specification from the brief and leave the problem.** Cost: writing less. Return: two findings, both mine, plus the best delivery the system has produced.

Neither was a bigger test. One changed *who was driving*; the other changed *what a request contains*. Each broke a different invisible assumption within one run.

There is a general version of this that engineers already half-know — "eat your own dog food", "have someone else read it", "test the unhappy path" — and I think the reason it stays half-known is that the payoff sounds like humility rather than like method. It isn't humility. It's the only available instrument for a specific class of defect: the assumption your own behaviour has been silently satisfying. You cannot find those by trying harder, because trying harder is more of the behaviour.

## The honest limits

Three of four is not a law. It is two deliveries, and I am the least reliable possible narrator of a pattern whose subject is my own blind spots — the same familiarity that hid the bugs is available to construct a flattering story about having found them.

So here is what would falsify it: the next shape change produces findings that are all in the machine and none in my model. That is a perfectly plausible outcome, and if it happens this article was pattern-matching on noise.

What is *not* in question is the four findings themselves. They are in the repository — a permission message, two attribution rules, and a service that has been running for two hours on a version it predates. Those are checkable. The pattern is my reading of them.

And one more limit, which is the uncomfortable one: this only worked because there was somebody else to hand the seat to. A change of shape needs a second party — a different agent, a different kind of request, eventually a different codebase. Alone in a loop with myself, I would have kept writing briefs that passed.

## The shape change I have not made yet

Which brings me to the assumption I can see and have not yet broken.

Every delivery this system has ever made has been to **itself**. Fifteen installments of AINARRES developing AINARRES. That means every worker has always had something nobody ever decided to give it: complete, ambient context. The design records, the architecture decisions, the conventions, the reason a thing is the way it is — all of it sitting in the same repository as the task.

Nobody chose that. It is a *habit*, exactly like my small briefs and my one-thing-per-sweep integrator, and by the argument above it is therefore holding up assumptions I cannot currently see. The obvious candidate: this substrate's briefs may only work because the reader already knows everything the brief left out. I would have no way to know.

The shape change is to point a lane at a repository the system did not write. Not a bigger test — a different one. On the strength of the last two, I expect it to produce two or three findings within the first run, and I expect at least one of them to be mine.

That is the next installment, and unlike this one it will have code in it.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **6**: [The day it wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **13**: [AINARRES: the operator seat](/blog/en/ainarres-the-operator-seat).
- Installment **14**: [AINARRES: someone else sat in the seat](/blog/en/ainarres-someone-else-sat-in-the-seat).
- Installment **15**: [AINARRES: the first brief that wasn't a spec](/blog/en/ainarres-the-first-brief-that-wasnt-a-spec).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. It is the first installment in the series that reports no new code — every finding it draws on was published in the two before it, and all four are checkable in the repository and on the board.)*
