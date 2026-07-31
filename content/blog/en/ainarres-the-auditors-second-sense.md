---
Title: AINARRES: the auditor's second sense — watching health and spend
Description: The project's auditor used to judge one thing — did a delivery meet its brief. This installment gives it a second sense: watching the fleet's operational health and its token spend, so a stuck worker or a quietly-expensive one gets surfaced to a person. It also tells the honest story of the run that shipped this feature — which first stalled when a model was retired overnight, and then finished itself hands-off once the harness was fixed.
Date: 2026-07-31 11:30PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 9
Lang: en
Translation_Key: ainarres-operational-facet
Image: /assets/images/ainarres-09-second-sense-hero.webp

---

# AINARRES: the auditor's second sense — watching health and spend

The last real milestone gave the project an **auditor** — a human-held role that reviews the one thing a rule shouldn't judge alone: whether a delivery actually met the request it was built for. That's the auditor's *quality* sense. This installment gives it a **second sense** — an *operational* one: watching whether the machine is healthy, and whether anyone is burning fuel for nothing.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work gets coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ask "give me the next task I'm allowed to do" and "this one's done." **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it to govern itself — to withdraw a capability from a worker that proves bad at its job. A human just starts the loop; AINARRES develops AINARRES.

The interlude before this one ended on a cliffhanger. I'd widened the swarm into a fleet of models, and one cheap model had **spun** — it looped for ten minutes asking for a tool it didn't have, produced nothing, and *nothing bounced*. No reviewer rejected it; no counter moved. I promised that failure would come back as the argument for the next feature. Here it is.

---

## Two ways to waste effort — and why they need different catches

Designing the spend watch forced a correction worth stating plainly, because it changed what I built. There are two ways an AI worker wastes effort, and they are **not** caught the same way:

- A **spinner** loops and produces nothing. Crucially, it never *advances* a task — so the system never records any spend for it at all (spend is stamped when a worker moves a task forward; a worker that never moves one leaves no trace). A spend signal, by itself, is **blind** to a pure spinner.
- An **overspender** does deliver — it just burns, say, fifty times the tokens its peers do for the same result. Because it delivers, its cost *is* recorded. This one a spend signal catches cleanly.

So the honest picture is a division of labour, not one magic signal. The interlude said "a spend signal is exactly what catches the spinner" — that's *aspirationally* true (a spinner does burn tokens) but *mechanically* wrong, and the difference matters. The catch for a spinner is a **health** watch: a claim held with no progress, a stall, a stranded task. The catch for an overspender is a **spend** watch. Together they cover the operational failures that no per-task review can see — the exact blind spot the spinner exposed.

---

## The watch is dumb; the flag is the judgment

The load-bearing rule from the governance season carries straight over: **the system measures, a person decides.** So this feature is split cleanly in two.

The **watch** is dumb and read-only. One view compares each worker's cost-per-delivery, *for a given role*, against the median of its peers *in that same role* (you compare reviewers to reviewers, not to integrators), and surfaces anyone past a tunable multiple. It also surfaces the odd case of a worker that recorded some spend but shipped nothing. It ranks nobody good or bad; it just says "this one is expensive." A second view lists the flags a person has already raised. Health reuses machinery that already existed — the list of stalled and stranded claims.

The **flag** is the auditor's recorded judgment. Reading the watch, the human-held auditor records that a worker is *spinning* or *overspending* — a note in an append-only ledger, and **nothing else**. It writes no penalty. A flagged worker keeps working until a person decides otherwise. This is deliberate: "is this a genuine spin, or just a legitimately hard task?" is a judgment, and judgments in this system live with a person, never in a rule.

And one line is drawn sharper than the rest: **an overspend flag never nudges toward a ban.** An expensive model that passes is *expensive*, not *unfit* — cost is not competence. The report even labels it as such: a spinning concern reads as **"review,"** an overspend reads as **"cost."** A person may still escalate a *spinner* to a ban (a worker that produces nothing at a job is failing at it) — but that stays a human act, through the same audited, human-only path the governance season built. Spend can never quietly become a ban.

---

## The run that shipped this — and stalled, and finished itself

Here's the honest part, because this project's blog has always told the messy version.

The feature landed the usual way: the trust-critical core — the flag, the ledger, the watch views — I built by hand and checked before it ran live, because a rule about *recording a judgment against a worker* has to be right first. The last piece — the report block that renders the whole thing — was left to the swarm to build itself, hands-off, validated with a database-free unit test.

That last run **stalled.** The designer drafted it, a cheap implementer built it, a reviewer from a different maker checked it and passed it — and then the single worker that's allowed to *merge* crashed on startup. Overnight, its model had been **retired**: the exact model id the integrator was pinned to no longer existed, and the tool refused to launch. The reviewed change sat there, finished and unmerged, with no one able to land it.

Two things are worth pointing at. First, **nothing broke.** The task simply waited at the "ready to merge" step; the substrate is built so a missing worker stalls progress, never corrupts it. The board told the true story to the first person who looked. Second, **the fix was small and the recovery was clean.** I pointed the integrator at the model's current name (and, because this system keeps each model's track record separate, treated the new model as a genuinely new worker identity — a rename, not a silent swap). Then I re-ran the loop. It noticed the task was already most of the way done, *skipped straight to where it left off*, and the now-working integrator merged the change on its own. No human did the merge. The single-integrator property the federation season established held all the way through.

And then the payoff. The very first time the finished report rendered on real fleet data, the new operational block **caught something**:

> operational (health & spend watch):
> — the review peer, as reviewer: overspending — ~15× the peer median (cost)

That's the feature working exactly as designed. One reviewer had spent about fifteen times the median of its peers for the same kind of work. It wasn't *wrong* — it's a heavy-reasoning model, and maybe that thoroughness is worth it. But it's **expensive**, the watch said so plainly, tagged it *cost* (not a ban), and left the call to a person. The auditor's second sense opened its eyes and immediately saw something real.

---

## What I deliberately did not do

The honest list, as always:

- **The spend watch needs peers to mean anything.** Comparing a worker to the median of its role is meaningless when only *one* worker holds that role — which is often the reality right now (one model does most of the implementing; the others are idle insurance). A single-worker role produces no anomaly, correctly and on purpose, rather than a false alarm. The watch earns its keep only once the fleet is genuinely exercised.
- **One worker's spend is still measured coarsely.** The frontier model plays several roles in one work session, and the system attributes its spend to only one of them — so as an integrator it still reads "unknown." Honest, documented, fixable later.
- **Operational judgment is subjective and unscored.** The flag records a note in plain language; "spin versus a hard task" is the person's call. An automatic spin-detector that *proposes* flags is a later refinement, not this one.
- **A quiet spinner can still slip through.** The health watch keys on stalls and stranded claims; a worker that spins *and* releases its task cleanly within its time window leaves little trace. A tighter progress heartbeat is future work.

---

## Where this leaves us

This seats the **back bookend** — the role that watches delivery and fleet health *after* the work, now with the spend sense the interlude argued for. It's still exercised by hand, on the same loop I start on a laptop; the always-on version comes later.

What's next is the **front bookend**: the *intaker*, the role that shapes a raw request into a well-formed brief *before* the work starts. Name both ends, and the full chain — from the person with a request, through the workers, to the auditor who checks the result — is named in the substrate end to end. And then, the horizon this project has been building toward from the first installment: turning AINARRES from a script I start on a laptop into a **standing service** — always on, fed by a real channel, watched by these roles instead of by me. The loop was always scaffolding. The service is the real thing.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **2**: [AINARRES builds itself](/blog/en/ainarres-builds-itself).
- Installment **3**: [AINARRES runs with no conductor](/blog/en/ainarres-runs-with-no-conductor).
- Installment **4**: [AINARRES: the swarm](/blog/en/ainarres-the-swarm).
- Installment **5**: [Federated AINARRES: two AI makers on one board](/blog/en/ainarres-federation).
- Installment **6**: [The day the swarm wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **7**: [AINARRES and the auditor: did we build the right thing?](/blog/en/ainarres-the-auditor).
- Installment **8**: [AINARRES: a wider swarm, and the worker that spun](/blog/en/ainarres-a-wider-swarm).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository — the two failure modes and their two different catches, the watch that measures while a person decides, the overnight model retirement that stalled the run, the hands-off resume that merged the change with no human doing the merge, and the very first real reading of the spend watch flagging a review peer at roughly fifteen times its peers' cost.)*
