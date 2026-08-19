---
Title: 'AINARRES: the crank is gone — the machine that runs itself'
Description: For every feature this series describes, a human still did two things by hand — started the machine, and hand-wrote the request that fed it. This installment retires both. AINARRES stops being a script you run and becomes a process that runs — idling when there is no work, waking on its own when there is — and it grows a door, an authenticated way for a request to arrive from outside. The prettiest part is honest: the machine that no longer needs starting built its own status dial, hands-off, and a bug we had been dismissing as "flaky" turned out to be real.
Date: 2026-08-19 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 11
Lang: en
Translation_Key: ainarres-standing-service
Image: /assets/images/ainarres-11-standing-service-hero.webp

---

# AINARRES: the crank is gone — the machine that runs itself

Every installment of this series has described a machine that develops software with no one conducting it. And every single time, a human still did two small things **by hand**: I *started* the machine (I typed the command that ran the loop), and I *hand-wrote the request* that told it what to build. Those are the last two touch-points — the machine's on-switch, and its front door. This installment retires both.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — a shared ground where work is coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ask "give me the next task I'm allowed to do" and "this one's done." **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it to govern itself and to watch its own health, and finally named the two roles at the ends of the pipeline — the one who shapes a request coming in, and the one who audits the result going out. A human only started the loop; AINARRES develops AINARRES.

## The crank is gone

Until now the loop was a **crank**: I pulled it (ran a command), it decomposed one request into tasks, drained them to *done*, and then it **stopped**. Stopping was actually the proof it had worked — a clean exit meant "the run finished itself, no human needed." But a thing you have to crank isn't really *running*; it's a script you invoke.

So the loop became a **standing service**. Instead of exiting when the board is empty, it now **idles** — it sits there holding nothing, spawning nothing, checking every so often whether work has appeared. When something does, it **wakes**, drains it exactly as before, and goes back to idle. Feed it another request an hour later and it wakes again — same process, no restart. The machine stopped being a script you run and became a **process that runs**.

That flip sounds small and is quietly load-bearing. For three versions, "the loop *ends*" was a safety property — proof it wouldn't spin forever burning effort. Inverting it to "the loop *idles safely*" had to keep that guarantee without losing the hands-off proof. So: an idle service costs almost nothing (it holds no work, just a cheap peek at the board); a genuinely *stuck* board (something no worker can move) doesn't get poked over and over — the service notices, marks itself **stalled**, and waits for a human instead of thrashing. The proof that it's still hands-off is no longer "it exited cleanly" but "a request fed to the *running* machine reached the finished product with no one in the loop." If anything that's a stronger claim — hands-off across an endless stream of requests, not one run.

## A scaler, never a boss

Here is the line I was most careful about, because it's where "no orchestrator" could quietly die.

A standing service that watches for work and launches workers to match is *one asking-price away* from becoming the very thing this whole project exists to abolish: a boss that decides **who does what**. The discipline is a single rule — the service is a **demand-scaler, never a router**. It is allowed exactly one question: *"is there work waiting that my roster could do?"* If yes, it makes sure there's capacity; the workers themselves still **pull** the specific tasks they're each allowed to take, exactly as before. The service never picks *which* task goes to *which* worker. I kept its question deliberately dumb — "is there *any* work?" — precisely because the dumbest possible question is the one that *cannot* smuggle in routing. The moment a supervisor starts deciding assignments, you've regrown the conductor. It doesn't.

It does lean on the governance from earlier versions: it won't bother launching a worker the substrate has temporarily benched (it'd just be turned away anyway). But that's *reading* a decision the substrate already made, not making one.

## A door, not a crank

The other half is the front door. Until now a request entered the only way I could feed it: I typed it in myself. This installment gives the request-shaper (the *intaker*, from last time) a real **channel** — a small local endpoint you can send a request to, and it becomes a properly-filed request the machine then works on its own.

This is the first time in the whole project that AINARRES faces *outward* at all — the first time something other than my own hands can put work in. That's a genuinely different security posture, so it came with its threat model written **first**, not bolted on after. And the first stage is deliberately modest: the door is **local only** (it listens on the loopback address — it isn't on the open internet), and it's gated by a **pre-shared key** — no key, no entry. I wrote the widening (real accounts, transport security, rate limits) down as a *contract for later*, so "small now" doesn't become a trap.

The part I like: the door isn't the *only* wall. Even if the doorman had a bug, the identity it lets in can do exactly one thing — **open a request** — and nothing else. It literally cannot commit the team to building anything, cannot merge, cannot touch the build lane. That's not enforced by the door; it's enforced by the substrate underneath, the same rule from the federation era. A door with a broken lock still opens onto a room where the walls hold. Defence in depth, for free, because the capability model was already right.

## The flake that was a bug

An honest detour, because this series has always shown the failures too.

There was a test I'd been quietly dismissing for a while as "flaky on a loaded machine" — it checked that a long-running worker keeps its work reserved by sending a periodic heartbeat. It failed now and then; I'd shrugged. The rule I try to hold is *a test you routinely ignore is worse than no test* — it trains you to ignore the whole suite. So I chased it.

It was not the test. It was a real bug, one that only a **long-lived** process ever hits — which is exactly what the new standing service is. The machinery reused a network connection between beats; the database's front-end had quietly closed that connection in the gap; and reconnecting **stalled for almost exactly three seconds, every time**. First beat: 34 milliseconds. Every beat after: ~3,019 milliseconds. So the heartbeat kept arriving three seconds late, a short reservation lapsed, and the work looked abandoned. In the old crank-and-exit world nothing ran long enough to notice. Standing up a permanent process turned a latent, invisible defect into a visible one — and a one-line fix (ask for a fresh connection each time) cleared it. "Flaky" is very often a real defect wearing a disguise; a machine that never turns off is a good way to unmask it.

## The machine built its own dial

And the part that is the whole point of this project. The standing service needed a readout — a way to glance and see *is it idle, is it working, is it stuck*. I didn't write that readout. I wrote a one-page description of what it should say, and handed it to **the swarm** — and the machine built the instrument for reading *itself*, hands-off: one model wrote it, a different maker's model reviewed it, and the integrator merged it to the main line on its own. The thing that no longer needs starting built its own status dial. That recursion — the system extending the system — is, quietly, the thesis.

## What I deliberately didn't do

The honest list, as always:

- **No screen.** You talk to the door with a command, not a web page. The read-only "everything is already a database view" makes a dashboard nearly free to add later, but a real interface is its own piece of work, deferred on purpose.
- **The door stays local.** One host, one owner, a shared key. Facing the real internet — real accounts, encryption, abuse controls — is a later, deliberate step behind the contract I wrote down, not this one.
- **One machine.** The whole design is shaped so that *many* of these services could run side by side without stepping on each other — that's the point of the "scaler, never a boss" rule. But I stood up exactly one. Many-machines is the next season.
- **The service checks; it isn't nudged.** It wakes by *looking* every few seconds, not by being *told* the instant work arrives. The instant-notification version is a clean, cheap upgrade — noted, not built.
- **Intake is still a monologue.** The door takes a request; it doesn't yet hold the back-and-forth conversation that pins a fuzzy request down. That dialogue is a later slice.

## Where this leaves us

This was the immodest half of the plan, and it's done. AINARRES is no longer a script I run on a laptop; it's a **process that runs**, idling and waking on its own, with a guarded door a request can arrive through and its own dial to read its own state. The loop, all along, was scaffolding. This is the thing the scaffolding was for.

What's left is genuinely *more* of the same idea, not a different one: a screen for the humans, a wider and properly-secured door, and — the real horizon — *many* of these services, from many places, coordinating over one shared truth without a boss anywhere in sight. That last one is what the "scaler, never a boss" rule was quietly built to make safe. Another season.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **5**: [Federated AINARRES: two AI makers on one board](/blog/en/ainarres-federation).
- Installment **6**: [The day it wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **10**: [AINARRES: the intaker — shaping the request before the work](/blog/en/ainarres-the-intaker).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository — the standing service that idles and wakes, the demand-scaler that never routes, the local pre-shared-key door that opens onto the substrate's own gate, the three-second connection stall that a permanent process finally exposed, and the status readout that the swarm built for the very service it runs on.)*
