---
Title: 'AINARRES: the first brief that wasn''t a spec'
Description: Every request I had ever handed the swarm was a specification in disguise — the function named, the strings quoted, the assertions listed. This one was a problem, a contract it may not reopen, and permission to refuse. It came back as a four-node dependency graph, built in eighteen seconds, and four merged pull requests in seventy-one minutes: the first time this machine has been handed something to decide rather than something to transcribe. Two findings came with it, and both were about my work rather than its.
Date: 2026-09-02 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 15
Lang: en
Translation_Key: ainarres-first-problem-brief
Image: /assets/images/ainarres-15-first-problem-brief-hero.webp

---

# AINARRES: the first brief that wasn't a spec

Last installment I handed the operator's seat to an agent that had built none of the system, gave it no signing key, and watched it run a delivery. It held. It also found two places where the machine was behaving correctly and explaining itself badly, which is a category I had no way to see from the inside.

This week the same operator stopped being tested and started working. And the thing that changed was not the operator. It was the shape of what I gave it.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work is coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ever ask "give me the next task I'm allowed to do" and "this one's finished". **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it to govern itself and watch its own health, turned the loop into a standing service, taught it to wake only the workers the pending work needs, and gave the operator's chair a name and a credential it does not issue to itself.

## Fourteen installments of specifications wearing a costume

I need to admit something about every "brief" in this series so far.

They were specifications. Not in intent — in fact. When I wrote the request that produced the last delivery, I named the function to extend, quoted the exact strings it should print, listed the acceptance assertions one by one, and specified which file the tests went in. Then a designer model read it and produced a task, and I recorded the design cost as a line item.

Which means I had been paying a designer to restate my own spec in the system's vocabulary. The 860,000 tokens the designer spent in installment 14 bought a *translation*. The thinking had already happened — in my head, at the keyboard, before the machine saw anything. I called it a brief because it arrived through the intake channel, and the channel does not care what is inside.

This week's request was different, and I want to quote the part that mattered:

> This brief is intentionally not an implementer spec. The designer decomposes it. If the DAG cannot be built from the ADR without inventing a router or removing the poll, say so and stop — do not ship a different design.

Three things in one paragraph. A **problem** rather than a solution: *the idle supervisor only notices new work on its next poll tick, up to fifteen seconds late; remove that latency*. A **contract not to reopen**: the decision was already made in an architecture record months ago, and the invariants were listed — the poll stays as a permanent backstop, the service never becomes a router, the notification carries no truth. And, last, **permission to refuse**: if this cannot be built inside those constraints, say so and stop.

That last sentence is the one I had never written before. Everything up to it delegates a task. It delegates a judgement.

## What came back

Seventy-one minutes, four merged pull requests, and — the part I did not expect — a **dependency graph** rather than a list.

In eighteen seconds the designer created four tasks. Two with no prerequisites. One gated on the first. The last gated on two of the others. Nobody told it to slice the work that way; the shape came out of reading the architecture record and noticing which pieces could not be tested until other pieces existed.

```
A  the substrate trigger        pickle → sonnet → grok       done 16:50
B  the wake primitives          pickle✗ grok → grok → grok   done 17:01
C  wiring the supervisor        pickle (20 min) → grok       done 17:31
D  proving the success gate     grok → grok → grok           done 17:39
```

The feature itself is small and worth a sentence, because its design is the interesting part. The board now fires a database notification whenever a write could make work claimable — a new task, an advance, an unblock. The standing supervisor listens on that channel and wakes at once instead of sleeping out its interval. And the interval **stays**, permanently, because a task that becomes claimable by a lease quietly expiring writes no row at all and therefore fires nothing. The notification is an optimization over the poll, never a replacement for it. A notification that never arrives costs latency. It can never cost work.

That distinction — *this signal carries no truth, so losing it is only ever slow* — is written into the migration's own header, along with a note that it is a deliberate, bounded exception to a rule the project otherwise keeps, and that a migration which *did* persist or decide something would be a stop-and-rethink signal rather than a slice to build.

I did not write that paragraph. I wrote the constraint it is reasoning about.

## Where the money went, and what that says

Ten million four hundred thousand tokens for the epic. The distribution is the story:

```
claude-code+opus     designer       6,042,899      58%
opencode+big-pickle  implementer    2,583,742      25%
claude-code+sonnet   reviewer       1,317,000      13%
grok+grok-4.6        reviewer         198,819
grok+grok-4.6        designer         201,343
grok+grok-4.6        integrator        47,342
                     empty sweeps     159,707
```

Design is 58% of the run. Last installment I called that shape a tax, and it is — but this time it bought a dependency graph derived from a constraint document, not a restatement of something I had already decided. Same cost, entirely different purchase. If you want one number for what "hand it a problem instead of a spec" costs, it is roughly six million tokens of somebody thinking.

Two other things in that table are worth naming.

**The cheap implementer failed a real slice.** Slice B was implemented by the cheap local model, reviewed by the frontier model, and **rejected** — then reimplemented by the reviewer's own family and shipped. The same cheap model then spent twenty solid minutes on slice C, the hardest one, and got it through review first time. So "default to cheap" is right and has a failure mode, and the failure mode is visible in the record rather than in a vibe. That is exactly the evidence a seating decision should be made from.

**And one row reads `unknown` again.** The frontier model shipped three implementations and its cost as an implementer is unmeasured.

## The lesson I learned last week, arriving again one level up

Last installment's whole finding was an `unknown` that was not unknown: a sweep crossed two stages, reported its tokens once at the end, and the view charged the *last* capability — so the integrator read `unknown` while its cost sat under the reviewer. I fixed it by charging the **first** capability the sweep exercised, wrote an amendment about why a misfiled number is worse than a missing one, and published a thousand words on how you cannot audit a system for legibility from inside your own familiarity with it.

Then this run produced the same word in a different row, and when I went to look I found I had fixed the instance and missed the class.

Here is one real sweep from Wednesday afternoon. One agent, one wake-up, **one** token report:

```
17:26:45  reviewing    → integrating     (slice C)
17:28:01  integrating  → validating      (slice C)
17:31:11  validating   → done            (slice C)
17:32:04  proposed     → designing       (slice D)
17:32:25  designing    → implementing    (slice D)
17:35:48  implementing → reviewing       (slice D)
17:36:29  reviewing    → implementing    (slice D — rejected its own work)
17:37:03  implementing → reviewing       (slice D)
17:37:18  reviewing    → integrating     (slice D)
17:38:39  integrating  → validating      (slice D)
17:39:42  validating   → done            (slice D)
17:39:53  USAGE  201,343 tokens
```

Eleven transitions. Two tasks. Five different capabilities — reviewer, designer, implementer, integrator, and reviewer again. One number at the end.

I had been arguing about *which transition inside a task* should carry the bill. The actual granularity problem is **which task inside a sweep**, and a sweep is not a task. It is however much of the board one agent can drain before it stops. The designer's largest single report — five million tokens — covers a sweep that designed *two* slices and is anchored, entirely, to one of them. The other slice records a delivery that apparently cost nothing.

So the per-delivery figures in the table above are dividing a sweep-level number by a transition-level count. They are arithmetic. They are not measurement. And `implementer: unknown` for the frontier model is not a gap in the data — it is the same misfiling as last week, one level of nesting up, produced by the very fix I wrote to prevent it.

And the reason it broke is worth more than the bug. Last week's fix was correct for last week's evidence — and that evidence came from runs where every sweep touched exactly one task, because I had been writing briefs small enough that they always did. **A problem brief produces bigger sweeps.** The assumption did not fail because I was careless; it failed because the thing being measured got more capable than the measurement. That is the good kind of broken. The remaining fix is the expensive option I skipped — report usage per transition rather than per sweep — and it is now the most valuable thing on the list.

## Two flags the operator raised, and refused to clear

The operator wrote its own summary of the run, and it did something I want to record: **it declined to sign off on its own work.** Two items, both correct.

**Cross-family review was three out of five, not five out of five.** Two of the slices were reviewed by the same family that implemented them. This project treats cross-family review as *measured, not enforced*, on purpose — enforcing it would mean a delivery stalls when a peer is down, and resilience was judged worth more than the guarantee. So this is the design working exactly as specified, and producing a weaker result than you would want. Naming it is the whole point of measuring it.

**And the better one.** The live supervisor has been running for one hour and fifty-six minutes. It reports a fifteen-second poll interval. Push-wake is on the main branch — the four slices merged, the tests are green — and the code that sets the interval to its new sixty-second backstop cadence is not in the running process, because that process started before any of it existed.

Read that again. **The machine built the feature that removes its own latency, merged it, and is still running without it.** It cannot pick up its own improvements without being restarted, and the operator can *read* the process, and *stop* it, and cannot *start* it.

That gap is not new. I left it unbuilt on purpose in installment 14 — the seat has no non-`make` way to start the service, and I said I would let the test tell me whether it mattered. The test has now told me. It does not matter for *operating* a running machine, which is what I asked about. It matters entirely for *shipping to* one. An always-on service that cannot redeploy itself has a hand-off to a human buried at the end of every delivery it makes, and nobody had noticed because until this week the deliveries were report lines that took effect the next time anyone ran a command.

## What I deliberately didn't do

- **Didn't restart the service.** The operator flagged it and left it alone, which was right — bouncing a live supervisor is exactly the class of act that should be a human's until there is a lifecycle command with a record behind it. So the run's own feature is still waiting on me.
- **Didn't fix the per-sweep measurement.** Naming it in the same week I published a fix for its smaller sibling is more useful than shipping a second partial. It goes in the record first.
- **Didn't touch the review guarantee.** Making cross-family review mandatory would trade resilience for a property I can already see. The measurement stays; the enforcement stays off.
- **Still no router.** The service wakes faster now. It still has no opinion whatsoever about *who* claims what. That is the fourteenth installment in a row this sentence has appeared in, and it is still the load-bearing wall.

## Where this leaves us

The interesting result this week was not the feature. It was finding out what happens when the request stops containing the answer.

For fourteen installments I had been writing specs and calling them briefs, and paying a designer to translate them, and reading the resulting cost as overhead. It was overhead — because there was nothing left to decide. Hand the same machinery an actual problem, a contract it may not reopen, and explicit permission to refuse, and the same expense buys a dependency graph, a bounded exception argued in a migration header, and four slices that could not have been written in the wrong order.

It also did the thing I have come to expect from a system that is actually working: it produced two findings I could not have generated myself, and both were about *my* work rather than its. My spend metric rested on an assumption my own habits had been enforcing. My decision to leave the service lifecycle unbuilt was right for the question I asked and wrong for the question I should have asked. A machine that only ever confirms your design is not telling you anything.

So let me say the milestone plainly, because the two findings above are the interesting part and not the important one. **Nine months in, this thing took a problem statement and a constraint document and returned a correct decomposition** — four slices in dependency order that nobody specified, derived from noticing which pieces could not be tested until other pieces existed; a bounded exception to one of the project's own rules, argued in the header of the migration that takes it; and a permanent backstop kept in place for a failure mode the architecture record predicted and the implementation respected. Four families, two makers, seventy-one minutes, no human in the loop between the request and the merges.

Nine months of this has been about removing humans from the critical path. This week the last human step turned out to be at the very end, holding a restart command, in front of a machine that had just finished building the thing it was waiting for.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **6**: [The day it wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **11**: [AINARRES: the crank is gone — the machine that runs itself](/blog/en/ainarres-the-crank-is-gone).
- Installment **13**: [AINARRES: the operator seat](/blog/en/ainarres-the-operator-seat).
- Installment **14**: [AINARRES: someone else sat in the seat](/blog/en/ainarres-someone-else-sat-in-the-seat).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository and on the board — the four tasks created in eighteen seconds with two of them gated on the others, the cheap model's rejected slice and its twenty-minute success on the hard one, the single sweep of eleven transitions across two tasks and five capabilities reported as one number, the five million tokens anchored to one of the two slices they designed, and the supervisor that has been up one hour and fifty-six minutes reporting the poll interval of a version it predates.)*
