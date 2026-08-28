---
Title: 'AINARRES: only wake who the work needs'
Description: The machine that runs itself had an expensive habit — whenever any task was waiting, it started every worker it had, and most of them found nothing to do. This installment teaches the substrate to report what is waiting in terms of capabilities, so the service can wake exactly the workers that can serve it. The interesting part was not the saving. It was what happened when the machine found a capability nobody holds — and, correctly, told me to hire someone for a job I had deliberately kept for myself.
Date: 2026-08-28 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 12
Lang: en
Translation_Key: ainarres-demand-shaped
Image: /assets/images/ainarres-12-demand-shaped-hero.webp

---

# AINARRES: only wake who the work needs

The previous installment retired the crank: AINARRES stopped being a script I ran and became a process that runs, idling when the board is empty and waking when work appears. It worked. It also had an expensive habit that idling hid completely — **when it woke, it woke everyone.**

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work is coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ever ask "give me the next task I'm allowed to do" and "this one is done". **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it to govern itself and watch its own health, named the roles at each end of the pipe, and finally turned the loop into a standing service with an authenticated door. A human starts nothing per feature; AINARRES develops AINARRES.

## The bill nobody was reading

An idle service costs almost nothing. Waking one costs real money, and the old wake was indiscriminate: the service asked the board a single question — *is there any claimable work?* — and if the answer was yes, it started **every configured worker**. One task sitting at *review* would boot the entire pool of implementers: a process launch and a model session each, for workers that would look at the board, find nothing they were allowed to touch, and exit.

I watched it happen twice in a row on a real run last week and only then did the shape of the waste land. The board had exactly one task. The service started six workers. Five of them had nothing to do and did it perfectly.

That is the coarsest possible gate, and it was the right first cut: the dumbest question is the one that *cannot* smuggle in a routing decision, which is the property this whole project is built to protect. But coarse is expensive, and the cost is paid in tokens — the one resource this machine consumes in quantity.

## Demand, in capability terms

The fix had to thread a needle. To wake fewer workers, something must know which workers *could* serve the waiting work. That sounds exactly like the beginning of a dispatcher, and a dispatcher is the thing I have spent twelve installments refusing to build.

The way through is to notice that the substrate already knows the answer, and to make it say only that much. A task is movable by whoever holds a particular set of capabilities — that is not a new idea to add, it is the same test the substrate already applies when an agent asks for work. So the substrate grew one read-only view that reports pending work as **capability bundles with counts**:

```
bundle                                             pending
{lane:dev, role:designer}                                3
{lane:dev, role:implementer}                             1
{lane:dev, role:reviewer}                                2
{capability:integrate, lane:dev, role:integrator}        1
```

Read that carefully, because what is *absent* is the point. No task identifiers. No priorities. No payloads. No worker names, no mention of a "tier", no idea that a service exists at all. The substrate is saying "this many pending things need someone holding at least these capabilities" — and nothing else.

The service then does the matching **against its own configuration**, which is where knowledge of workers has always lived: it knows which harness runs which role, because that is its config file. A worker is started if some demanded bundle fits inside its declared capabilities, and it is alive, and governance has not suspended it. Three conditions, one gate.

The nice thing is how cleanly the bundles land on the roster. The last line above — the one that needs the merge capability — fits exactly one family, the single integrator that has always been the merge queue. Nobody had to encode that. It falls out of what each participant declares it can do.

The pool now also sizes to demand: one pending task starts one implementer instead of three. The ceiling is unchanged; only the floor moved.

And every part of it degrades to the old behaviour. If the demand view can't be read, every live worker counts as demanded. If a worker's backend can't be probed, it counts as alive. An outage in an optimisation must change your *bill*, never your *correctness* — so both failures collapse back to "wake everyone", which is merely expensive.

## The capability nobody holds on purpose

Here is the part I did not see coming, and it is the reason this installment is worth reading.

The moment the gate went live, the machine reported that it could not serve a piece of pending work, and told me what to do about it:

```
⚠ unserviceable: 1 task(s) need {lane:intake, role:intaker}
  — no configured family provides it; seat one
```

It was right on the facts and wrong in the worst possible way. That waiting item was a raw request that had come in through the door — and the capability to shape a raw request into a workable brief is one that **no worker holds, deliberately**. Two installments ago I gave that job a name and kept it for a person, on purpose: the substrate withholds it from every agent, so an unshaped request is *invisible* to the swarm until a human has shaped it. That is not a gap in the roster. That is the boundary.

So the machine, reasoning correctly from what it could see, advised me to dismantle the one thing standing between "a swarm that builds what I ask" and "a swarm that decides what to ask for".

The fix is small and the lesson is not. The configuration now *declares* which capabilities are human-held — shaping a request, and the qualitative audit at the other end — and demand for those reads differently:

```
⚠ awaiting a human: 1 task(s) need {lane:intake, role:intaker}
  — this capability is human-held by design; a person must act
```

Same fact, opposite instruction. And a second-order benefit fell out of it: the service used to burn a full wake cycle discovering that an unshaped request was unworkable. Now it reads that from the demand report and starts nothing at all — it names the thing it is waiting for, which happens to be me, and holds.

I keep finding that the honest version of a feature is cheaper than the clever one. A machine that knows which of its gaps are *supposed* to be gaps spends nothing trying to fill them.

## Delegating the terminal, not the keys

Running alongside this was a different question: could an agent hold the seat I have been sitting in? Not doing the work — the swarm does the work — but *operating the instance*. Watching the board, seating and unseating models, unsticking things, starting and stopping the service.

Writing that seat down as a document, rather than as a habit, immediately exposed something uncomfortable. Every other actor in this system is modelled: workers are families with declared capabilities, the integrator is the one family trusted to push, the two end roles have their own features. The *operator* was modelled nowhere, because the operator had always been me, and I hold the signing key. An agent placed in that seat inherits the key — and with it the ability to lift any suspension the substrate placed on anyone, grant itself the merge capability, or act as any family at all. Every boundary the governance work built is enforced against workers and utterly vacuous against the operator.

So the decision, written down: **delegate the terminal, not the keys.** Three things stay with a person — custody of the signing key, the two irreversible governance acts (a permanent ban and its lifting), and the qualitative audit. Everything else can be handed over.

The first brick was smaller than expected and more interesting. "Just give the agent a read-only token" turned out not to exist: the role I had been using for oversight carries permission to call the permanent-ban and lift verbs. It reads everything, and it can also do the two things I had just reserved for myself. Monitoring-only delegation, with that token, is monitoring-only by *instruction* — a sentence in a document — rather than by structure.

Now there is a fourth database role that reads every oversight view and can call **nothing**. Its done-test is the part I like: the same identity, the same empty capability list, only the role differs — and where one reaches the permanent-ban verb's body, the other is refused at the door. The boundary is the role, so the seat cannot widen itself by asking for more capabilities.

The other thing a delegated watcher needs is a detector for the failure a human would have noticed by feel. Leases handle a worker that dies — the claim lapses, someone else picks the task up. They cannot handle a worker that is *alive and going nowhere*: it holds a live claim, renews it, and the board looks busy forever. With me at the terminal that is a curiosity in the daily report. With nobody reading, it is the silent failure. So the substrate now reports those too, in two flavours: held-and-silent, and renewing-the-claim-while-never-progressing. The second one is the interesting signature — the renewal itself is invisible in the event log, but arithmetic on the lease exposes it.

## Three ways to look dead

This installment's honesty section is a good one, because all three findings are the same species: things that fail *quietly*.

**The design pass had been a reviewer for a whole version.** The wrapper that runs the design model chose its instructions by inference — if a brief file was supplied, act as a designer; otherwise, act as a reviewer. Reasonable, until the standing service introduced a design pass that runs *without* a brief file, sweeping the board continuously. That pass got the reviewer instructions, while holding only designer capabilities. It never decomposed anything. There was even a comment in the code cheerfully describing behaviour that did not exist. Nothing failed; a whole capability was simply absent, and the tests didn't cover the shape because the shape was new. Modes are now declared, not inferred.

**A stuck board killed the supervisor.** The design says: when a full round moves nothing, hold — mark yourself stalled and wait for a human, rather than spinning. The implementation instead *exited*, because of a single line where a function's non-zero return propagated out of the loop under a strict shell setting. Nothing had ever driven a genuinely stuck board, so nothing had exercised the path. An always-on process that dies when the board gets stuck is precisely the failure you don't notice: no crash report, no alert, just nothing happening.

**And then the test harness shot the patient.** This one cost hours. While proving the new gate, the supervisor kept vanishing mid-run — no error, no signal, its status file frozen on the last good tick, which reads *exactly* like a hang. It was not hanging. My own test was killing it: bash runs an EXIT trap **inside a subshell that dies**, and one of my assertions piped output into a `grep -q`, which exits at the first match and takes down the writer with a broken pipe. The trap fired in that dying subshell, ran the cleanup that kills the supervisor, and the main script sailed on. So the assertion passed, and a later phase failed ninety seconds afterwards against a corpse.

I want to name the general lesson, because it is the one I'll carry: **make "dead" and "disagreed" impossible to confuse.** When a test now fails, it prints the supervisor's pid, its state, and the age of its last tick. Two minutes of output design would have saved most of that afternoon.

## What I deliberately didn't do

- **No push-wake.** The service still polls on an interval; a database notification that wakes it the instant work appears is designed and unbuilt. That is latency, not cost, and cost was the problem in front of me.
- **No credential envelope yet.** The read-only role exists; the machinery that issues the operator seat short-lived credentials it cannot widen does not. Until it does, delegation past reading is *trusted*, and I would rather say that plainly than pretend otherwise.
- **Still no router.** The service decides which *kinds* of live worker to start. It never decides which task goes to whom, and it never reads a task's content to prefer one capable family over another. Using cost data to pick a model per task would be a dispatcher, and it stays out.
- **The auditor stays human.** In fact the operator decision pushes it further out: the seat that runs the machine and the seat that judges how it ran must be different identities.

## Where this leaves us

The machine now wakes only the workers the waiting work needs, sizes its pool to the size of the queue, notices when a model's backend has quietly died, and — when it can't serve something — says which capability is missing and whether that means *seat a family*, *bring a backend back*, or *this one is waiting for you*.

That last branch is the one I find myself thinking about. I set out to reduce a token bill, and what I got was a machine that can articulate the shape of its own dependence on me: not as a limitation it discovered, but as a boundary it was told to respect, and now reports on. Every installment of this series has removed a human touch-point. This is the first one where the machine started keeping track of the touch-points that are meant to stay.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **6**: [The day it wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **10**: [AINARRES: the intaker — shaping the request before the work](/blog/en/ainarres-the-intaker).
- Installment **11**: [AINARRES: the crank is gone — the machine that runs itself](/blog/en/ainarres-the-crank-is-gone).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository — the capability-terms demand view that names no task and no worker, the gate that starts only what the waiting work needs, the human-held capabilities the machine now reports instead of trying to fill, the read-only database role that reads everything and can call nothing, and the afternoon lost to a test harness that was killing the very process it was measuring.)*
