---
Title: AINARRES: the intaker — shaping the request before the work
Description: The project has an auditor watching the back end of the work; this installment names the role at the front — the intaker, who turns a raw request into a well-formed brief before anything starts. The pleasing part is how little it took: two-tier creation, where the intaker may open a request but only the designer may turn it into work, fell out of a rule the project already had, with no new code. With both ends named, the whole chain — from the person with a request to the auditor who checks the result — is finally named end to end.
Date: 2026-08-16 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web
Series: AINARRES
Series_Slug: ainarres
Series_Order: 10
Lang: en
Translation_Key: ainarres-intaker
Image: /assets/images/ainarres-10-intaker-hero.webp

---

# AINARRES: the intaker — shaping the request before the work

The last installment gave the project an **auditor** with a second sense — watching the *health* of the machine and its *spend* after the work is done. That's the **back** end of the pipeline. This one names the role at the **front**: the **intaker**, the one who takes a raw, half-formed request and shapes it into a brief clear enough to build from.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work gets coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ask "give me the next task I'm allowed to do" and "this one's done." **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it to govern itself and to watch its own health. A human just starts the loop; AINARRES develops AINARRES.

For this whole series there has been one role the human quietly kept playing by hand: **turning a vague idea into a well-formed brief.** Every time I've fed the loop a feature, I first sat down and *shaped* it — what exactly is being asked, what's in scope, what "done" means. That shaping is real work, and it has a name in any team that takes requests: the **intaker**, or consultant — the person who sits between the customer and the builders and pins the request down before a line of code is written. This installment makes it a first-class role in the substrate, so the whole chain — **Customer → Intaker → Designer → builders → Auditor** — is named end to end.

---

## Two levels of "creating work," and a rule we already had

Here is the part I find genuinely pleasing, because it cost almost nothing.

There are **two** different acts of "creating work" in this project, and they must not be the same person's call:

- The **intaker** opens a *request* — "here is something we should probably do." That's the start of a conversation, not a work order.
- The **designer** turns an accepted request into *actual tasks* — the decomposition the builders pick up. That's committing the machine to build.

You want a wall between these. The person who can *raise* a request shouldn't automatically be the person who can *commit the team to build it*, and vice-versa. Two creators, two levels.

Now — the project already had, from the federation season, a small rule about who may create work: **you can only create a task in a lane if you hold the role that takes the first step on it.** Creating work *is* starting it, so only someone who could take that first step may open it. It's data-driven — the rule reads the workflow itself, with no lane's name written into the code.

So naming the intaker took **no new machinery at all.** I added a second lane — an **intake** lane — whose first step belongs to the *intaker*. And the rule the project already had immediately did the rest:

- an **intaker** can open a request in the intake lane (it holds that first step) — but **cannot** create tasks in the build lane (that first step belongs to the designer);
- a **designer** can create tasks in the build lane — but **cannot** open a request in the intake lane (that first step belongs to the intaker).

Two creators, two levels, **one unchanged rule.** The core of the whole milestone was a bit of configuration — a new lane, a new role — and *zero* new logic. When a feature you expected to be fiddly turns out to be a consequence of a primitive you already had, that's usually a sign the primitive was the right one.

---

## What the brief becomes

A request that clears intake becomes a **brief**, and the brief is a small, deliberate thing. When the designer later breaks it into build tasks, each of those tasks carries a quiet back-reference to the brief it came from. That link matters more than it looks: it's the thread from *the original request* to *everything built for it* — exactly the thread the auditor needed and didn't have. Last season the auditor could point at a shipped piece of work and ask "was this a good delivery?"; now it can ask the sharper question — "did all of this actually answer what was **asked**?" The front bookend hands the back bookend the contract it audits against.

It's also, on purpose, the smallest possible move. The brief is just a **task in a lane** — it reuses the exact machinery every other piece of work already runs on. No new "brief" object, no second creation path, no special case. The lightest thing that could name the role, and nothing more.

---

## What I deliberately did not do

The honest list, as always:

- **Intake has no conversation yet.** The soul of the role is a back-and-forth — asking the requester the awkward clarifying questions until the request is actually pinned down. v6 has no channel for that, so in practice the "dialog" is still me, editing the brief by hand. The intaker is **named and seated** here; a real customer↔intaker exchange is the job of the *channel* that comes next.
- **The brief-to-work link is a convention, not a law.** It's a reference the designer writes, not something the database enforces — because there's no first-class "brief" object to enforce it against yet. Making the delivery a real, referenceable thing (and auditing a brief's *whole* set of tasks at once) is deferred, deliberately, to when it earns its weight.
- **One person can still wear both hats.** In v6 that person is me — I hold both roles, so I can both open a request and decompose it. The *separation* is real and provable (give two different workers the two different roles and each is refused in the other's lane), but whether the system should *forbid* one worker holding both is a question for the federated era, not this one.
- **A stalled request has no formal "abandon."** A brief that should die is handled by a human deciding so; a proper expire/reject path on the intake lane is a later refinement. v1 keeps the workflow minimal on purpose.

---

## Both bookends, and a swarm that grew

With the intaker named, **both ends the human used to hold by hand are now roles in the substrate** — the intaker who shapes the request going in, the auditor who watches the health and the result coming out. The full chain is named for the first time.

And a small note that pleases me, because it's the whole point of this project: this very feature — the part of the report that shows the intake board — was built **by the swarm, hands-off.** In the same stretch, the fleet also grew: a new small model that runs entirely on my own laptop joined the pool of builders (vetted first, against the tools it would actually have to use — a lesson an earlier, hallucinating model taught the hard way), and the frontier reviewer-integrator was upgraded to a newer model. The upgraded frontier merged this feature to the main line on its own, no human doing the merge. The machine that develops the machine got a little bigger, and kept running.

---

## Where this leaves us

v6 set out to **seat the bookends**, and it's done: the auditor at the back, the intaker at the front, both additive, both human-held for now, no change to the shape of the system. That was always the modest half of the plan.

The immodest half is next. Both new roles are still exercised the same way everything has been since installment one — by hand, on a loop I start on a laptop. The intaker wants a **channel** to actually talk to whoever has a request; the auditor's watch wants to be **always on**, not read off a report when I happen to look. That is the **standing service** — AINARRES turned from a script I run into something that runs on its own, fed by a real door, watched by these roles instead of by me. It's also the first time this project would face the outside world, so it comes with a security question asked *up front*, not bolted on.

The loop was always scaffolding. Naming the bookends was the last thing to do before building the real thing.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **5**: [Federated AINARRES: two AI makers on one board](/blog/en/ainarres-federation).
- Installment **7**: [AINARRES and the auditor: did we build the right thing?](/blog/en/ainarres-the-auditor).
- Installment **8**: [AINARRES: a wider swarm, and the worker that spun](/blog/en/ainarres-a-wider-swarm).
- Installment **9**: [AINARRES: the auditor's second sense — watching health and spend](/blog/en/ainarres-the-auditors-second-sense).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository — the intaker role and the intake lane, the two-tier creation that fell out of the existing create-rule with no new code, the brief that links a request to the work built for it, and the report block that a newly-grown swarm built and merged for itself.)*
