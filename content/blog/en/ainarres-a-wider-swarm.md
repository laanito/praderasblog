---
Title: AINARRES: a wider swarm, and the worker that spun
Description: An interlude after the governance season. AINARRES grew from one cheap AI worker into a graded fleet of models from different makers — and in doing so hit three lessons that rhyme: some workers fail in ways no rule catches, org policy decides who can even work, and every model counts its own tokens differently. This is the story of widening the swarm and teaching it to measure itself — and why that measurement becomes the auditor's next job.
Date: 2026-07-07 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 8
Lang: en
Translation_Key: ainarres-wider-swarm
Image: /assets/images/ainarres-08-fleet-hero.webp

---

# AINARRES: a wider swarm, and the worker that spun

The last season was about teaching the system to say **no** — to withdraw a capability from a family that proves bad at its job, and to route the judgments a rule shouldn't make to a person. That season closed. This installment is an *interlude*: not a new milestone, but a real stretch of work with lessons worth writing down. I set out to do something mundane — give the swarm **more workers** — and it turned into a small tour of how messy a *diverse* fleet of AI models really is.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work gets coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ask "give me the next task I'm allowed to do" and "this one's done." **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it self-governance. AINARRES develops AINARRES; a human just starts the loop.

Until now, almost all the real coding work was done by a single cheap model. That's fragile — one free API, one set of quirks. So I widened the bench.

---

## From one worker to a graded ladder

The swarm's implementers went from one to a small, ordered fleet — models from different makers, at different price-and-quality points:

- a **cheapest** tier meant to take first crack at easy work,
- the **primary** free-API workhorse (run several-at-once for throughput),
- an **80-billion-parameter** peer of similar cost but different temperament,
- a **higher-quality** fallback from yet another maker,
- and above them all, the frontier model that reviews, integrates, and rescues anything the cheaper tiers can't finish.

The idea is old and sensible: cheap models do the bulk, pricier ones catch what they drop. Adding a worker became a known recipe — register the model's identity in the substrate, wire it into the ladder, done. What I *didn't* expect was that each new worker would teach me something the last one hadn't.

---

## The worker that spun

The cheapest model I added went straight into a ditch. Handed a real task, it reached for a tool called `str_replace_editor` — a tool that doesn't exist in its harness. The harness refused. So it tried again. And again. It sat there for about ten minutes calling for a tool it would never get, producing nothing, while the system counted it as *busy working*.

A human reading the board noticed within minutes, and another worker quietly picked the task up and finished it. No harm done — the substrate is built so a stalled worker can't corrupt anything (its late results are rejected, the real codebase stays clean). But the failure is the interesting part. **Nothing bounced.** No reviewer rejected the work; no counter ticked. The model wasn't *wrong*, it was *spinning* — a failure mode that leaves no mark on any of the signals the system watches. I disabled that model the same day: a worker that can't drive its own tools is worse than no worker.

Hold that thought. It matters later.

---

## Who is even *allowed* to work

The next worker — a coding agent from a different maker — refused to run at all. To operate unattended, it wanted a "run everything" switch flipped, and my organization's administrator had **disabled** that switch centrally. Reasonable: "let this agent run any command without asking" is exactly the kind of thing an admin should be able to turn off.

The blunt override was gone, but a finer instrument was there all along: an **allowlist**. Instead of "allow everything," you pre-approve *specific* actions — let this agent run `git`, run the test command, run the language runtime, and nothing else. With that short list in place, the agent runs headless perfectly well, entirely **within** the administrator's policy. The lesson has a nice shape to it: when the sledgehammer is taken away, reach for the scalpel. **Preallow, don't force.** It's a better security posture anyway — the agent can do its job and *only* its job.

---

## Everyone counts differently

The other half of this interlude was about **measurement**. A few seasons ago the system started keeping a per-worker track record — how much each delivered, how often its work bounced — and, crucially, *how many tokens it burned*. Tokens are the raw fuel of an AI model; counting them tells you what a worker **costs**, kept deliberately separate from whether it's any **good** (an expensive model that always passes is *expensive*, not *bad*).

There was just one problem: only one family of models was actually reporting its token counts. Everyone else read **"unknown."** So I went to close that gap, and discovered that **no two harnesses report usage the same way**. One streams a running tally of per-step counts you have to add up. Another prints a single tidy summary. A third refuses to put usage on its normal output at all — you have to ask for a debug log and fish it out — and even then it counts "input" tokens in a way that overlaps with its cache, so you have to subtract to get an honest number. Four models, four dialects for the same fact.

The fix was to teach the system each dialect, at the edge, so the messy variety never leaks inward: the shared record just sees clean numbers. And one small discipline held throughout — **"unknown" is never treated as "zero."** A worker we can't yet measure reads as *unmeasured*, never as *free*. Pretending an unmeasured model costs nothing would be the most dangerous number in the whole system.

By the end, every worker's spend was visible. The bench is now not just wider — it's **legible**.

---

## Why bother measuring? (the payoff)

Here's where the spinning worker comes back. What do you *do* with a measure of what each model spends?

The tempting answer is "route around expensive models automatically" — but that's a **router**, something that decides who does which task, and AINARRES deliberately doesn't have one (workers pull their own next task; nobody assigns). So routing is off the table, by design.

The *right* answer connects the two halves of this interlude. Remember the worker that spun — burning effort, producing nothing, invisible to every rule? A **spend signal is exactly what catches that.** A model stuck in a loop burns tokens abnormally. A model that "thinks" its way through fifty times more tokens than its peers for the same result is quietly expensive. Neither trips a correctness check — but both light up a *spend* watch.

So the next step isn't a router; it's giving the project's **auditor** — the human-held role introduced last season, who reviews the things rules can't judge — a new thing to watch: **spend anomalies.** The auditor flags a worker that's spinning or consistently overspending, and a *human* decides what to do. It's a flag, not a punishment; the system never bans a model just for burning tokens (that stays a person's call). The measurement makes the invisible failure visible, and routes it to the one who's allowed to judge it. The spinning worker wasn't just a bug — it was the argument for the next feature.

---

## What I deliberately haven't done

As always, the honest list:

- **The new workers are barely exercised.** In every run this stretch, the primary cheap model did the work and the frontier model reviewed it. The other new tiers *ran* but found nothing to do — they're insurance, and a healthy primary leaves them idle. Their real trial comes when the primary's free credits run dry and work genuinely falls through to them — not from a run staged to force it.
- **The higher-quality agent hasn't finished a real task yet.** It's wired, allowlisted, and ready; it simply hasn't been *needed* yet. Wired ≠ proven.
- **The measurement is coarse in one spot.** The frontier model plays several roles in one work session, and the system credits its spend to just one of them — so one of its roles still reads "unknown." Honest, documented, and fixable later; the whole-fleet picture is what matters for now.

---

## Where this leaves things

This was scaffolding work, and it earned its keep twice over: it produced a **measurable fleet**, and it produced the **motivating failure** — the spinning worker — that justifies the next real feature. The two fit together like a key in a lock.

What comes next is bigger. The plan from here: **name the two "bookend" roles** the system still leaves to me — the one who *shapes the request* before work begins, and the one who *audits the delivery and the fleet's health* after (now including that spend watch). And then, finally, the thing this whole project has been building toward: turning AINARRES from a script I start on a laptop into a **standing service** — always on, fed through a real channel, watched by the roles instead of by me. The loop was always scaffolding. The service is the real thing. That's the horizon.

---

## To read and explore

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **2**: [AINARRES builds itself](/blog/en/ainarres-builds-itself).
- Installment **3**: [AINARRES runs with no conductor](/blog/en/ainarres-runs-with-no-conductor).
- Installment **4**: [AINARRES: the swarm](/blog/en/ainarres-the-swarm).
- Installment **5**: [AINARRES federated: two AI makers on one board](/blog/en/ainarres-federation).
- Installment **6**: [The day the swarm wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **7**: [AINARRES and the auditor: did we build the right thing?](/blog/en/ainarres-the-auditor).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository — the graded fleet, the model that spun on a tool it didn't have, the allowlist that replaced the disabled override, and the four different ways four models report the tokens they burn.)*
