---
Title: 'AINARRES: someone else sat in the seat'
Description: Last installment built a seat for an agent operator and a credential envelope so it would not have to hold the signing key. Then I drove it myself, which proves nothing. This time an agent that had built none of it sat down, with no key, and ran a delivery end to end. The boundary held. What broke were two things that were working exactly as designed — and unreadable to anyone who did not already know the answer.
Date: 2026-08-30 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 14
Lang: en
Translation_Key: ainarres-first-external-operator
Image: /assets/images/ainarres-14-first-external-operator-hero.webp

---

# AINARRES: someone else sat in the seat

The last installment ended on a promise I had not kept: *"No enforced isolation, yet. Running the seat as a separate user with no read access to the secret would turn detection into prevention. It is documented, it is a deployment step, and it is not done."*

I still have not done it. But I did something I had been putting off for longer, and it turned out to matter more: I got out of the chair.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work is coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ever ask "give me the next task I'm allowed to do" and "this one's finished". **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it to govern itself and watch its own health, named the roles at both ends of the pipeline, turned the loop into a standing service, taught it to wake only the workers the pending work needs, and gave the operator's chair a name of its own.

## Building a seat for someone, then sitting in it yourself

Installment 13 built two things. A **seat**: a registered identity for the operator, with a written list of what it may hold and — more to the point — what it may never hold. And an **envelope**: a small broker that holds the signing key and does nothing but sign, while the *database* decides what may go in the token.

The argument for the envelope was that an agent operator should not hold the key. The evidence for it was that I had written it down.

Because after building it, I tested it the way you test anything you have just built: by using it. I knew where the broker was. I knew which command brokers its own token and which one needs `--token`. I knew, without having to look, that the read-only credential cannot write. Every one of those pieces of knowledge is invisible to you when you already have it, and every one of them was doing work I was crediting to the code.

So this week an agent that had built none of it sat down instead. It got the repository, the skills folder as its entry point, the path to the broker's key — and no signing key. Its instructions were the ones any operator gets: bring in a piece of work, refine it, watch the swarm deliver it, and step in only if something goes wrong.

## What held

The measurement I cared about is a view called `unbrokered_operator_acts`. It lists operator activity with no credential issued behind it — the seat quietly signing for itself, or a human at the terminal. Last installment I described it as the piece carrying the whole design decision, because on a single machine the envelope cannot *prevent* a shell-capable seat from reading the key file. It can only make it visible.

After a full delivery:

```
api.unbrokered_operator_acts  →  []
```

Empty. Eight credentials, every one of them issued by the broker, every one carrying the seat's provisioned features unedited, none lasting longer than fifteen minutes. Six of the eight were read-only — it asked for the *weaker* credential by default and escalated only when it needed to write, which is the behaviour you hope for and cannot assume.

The second measurement is simpler and I like it more. Here is every event the seat produced on the board:

```
17:54:10  claimed      intake
17:54:10  transition   intake
```

Two. Both on the intake lane. **Zero** on the development lane. It brought the work in, handed it over, and then watched four other families — a designer, an implementer, a reviewer, an integrator — do the actual job over the next ten minutes without touching any of it. The seat holds no implementer role and no integrator capability, so the substrate would have refused it. It never got as far as being refused.

There is a pleasing recursion in what the swarm built while being watched: the report line that displays *spending that moved nothing* — the class of waste installment 13 discovered the system had been designed not to see. The test's workload was making spend visible. Hold that thought.

## What broke, part one: an "unknown" that wasn't

At the end of the run I read the track record — the per-family, per-capability signal that says who delivered what and what it cost. One line was wrong in a way I did not expect:

```
grok+grok-4.6   role:reviewer      1 delivered    52,536 tokens
grok+grok-4.6   role:integrator    1 delivered    unknown
```

The integrator delivered something and cost *nothing*? The timeline explains it immediately:

```
18:02:04  integrating → validating   "merged"           needs role:integrator
18:03:54  validating  → done         "green on main"    needs role:reviewer
18:04:05  usage        52,536 tokens
```

One agent, one wake-up, two stages. It merged the pull request, then confirmed the merge was green on the main branch, and reported its token usage once at the end — as every harness does. The view resolved that usage to the family's **most recent** transition, which was the second one. So the whole cost of the merge was filed under *reviewer*, and *integrator* was left with no spending record at all.

Here is why that is worse than a gap. This project has an explicit rule about unmeasured spending, written three versions ago: **unknown is never zero**. A family we have not measured must read as *unknown*, because "zero" would let an unmeasured family look cheap and win comparisons it has not earned.

But this `unknown` was neither. The spending existed. It was captured. It was filed next door. And there is nothing in the view that distinguishes *"we did not measure this"* from *"we measured it and put it in the wrong drawer"* — which means the honest-looking word was the dishonest one.

It gets one turn worse. Misfiling does not only empty one drawer, it **fills another**. The reviewer's cost-per-delivery now included a merge it never performed. A signal whose entire purpose is to keep "expensive" and "failing" separate had quietly started describing one family as expensive for another family's work.

The fix charges the **first** capability the sweep exercised rather than the last: the work that earned the spending, not the work it happened to finish on. The subtle half is the boundary. "First" needs a window, or a worker that was rejected and rebuilt something would have every later effort charged to its very first action, forever. The window turned out to be sitting there already — usage is reported once per sweep, so the *previous* report is exactly where this one begins.

It is a change to a view: no new table, no new verb, no new permission. Which means it is a re-reading of history rather than a new way of writing it, and the run that exposed it re-reads correctly the moment the fix lands. The bug is retroactively fixed in its own evidence.

## What broke, part two: a refusal nobody could read

The operator's other note was smaller and, once I saw it next to the first, obviously the same thing wearing different clothes.

It had a read-only credential in hand and tried to write a line to the operator's ledger. The system said:

```
42501  permission denied for function record_operator_action
```

That refusal is **correct**. The read-only role has been unable to execute anything since the day it was created; that is the entire point of it. Nothing was broken. Nothing was insecure.

But read it as someone holding a token they cannot see inside. It names the function. It never names the credential. There is no way to tell *"I am not allowed to do this"* from *"I am holding the wrong token for this"* — and those two have completely different remedies. One means stop. The other means ask the broker, which takes two seconds. The operator worked it out and moved on, and then did the genuinely useful thing: it mentioned it.

Now the failure says which credential is in hand and what the way out is. The refusal is unchanged.

## The thing both findings have in common

Neither of these was a bug in the ordinary sense. The permission system did exactly what it was built to do. The spending view followed the rule it was given, and that rule is right for every sweep that touches one stage — which is nearly all of them.

Both were **correct behaviour with an unreadable face**.

That is a category I had no way to find on my own, and I want to be precise about why. Not because I lack the skill, and not because I was careless. Because for every one of these, I already knew the answer. I knew the read-only token could not write, so I never held one and tried. I knew our integrator usually does one thing per wake-up, so I never looked at what happens when it does two. My knowledge was patching the gaps in the machine's explanations faster than I could notice the gaps existed.

You cannot audit a system for legibility from inside your own familiarity with it. You have to give it to someone who does not already know, and then read what they had to work out for themselves. Every one of those moments is a place where the system is being correct at somebody instead of *to* them.

There is a version of this essay that says the test passed and lists the green checkmarks. It did pass — the envelope held, the seat stayed in its seat, the delivery shipped. But the checkmarks were the part I could have predicted. The two findings were the part I could not, and they came from the same source: an agent with no history in this codebase, doing ordinary work, hitting two walls that were exactly where they should be and said nothing useful when hit.

Oh — and one confession, since this series does not do triumphant. Somewhere in fixing all this I convinced myself I had broken six tests. I rebuilt the database, ran the suite, watched six failures, rebuilt again, watched the same six. It took me three full runs to check the command I was using to "reset" the database, which — it turns out — runs the entire test suite as its final step. I had been running the suite twice on the same board and watching the fixtures pile up. The failures were perfectly reproducible and entirely mine. The cheap way to have known in ten seconds: they reproduce on a clean checkout too.

## What I deliberately didn't do

- **Still no isolation.** Same admission as last time, unchanged: the seat runs on one machine as one user and could read the key file if it decided to. It did not, and the view proves it did not, and detection is not prevention. This is now the oldest promise in the drawer.
- **The seat still cannot start the service.** It can read the service's state and stop it. Starting means either a foreground command that would block it forever, or a script that reads the secret file on its way up. So the human still starts the machine the operator operates. I left this deliberately unbuilt so the test would price it — and the answer came back: not urgent, but no longer theoretical.
- **The two irreversible powers stay human.** Permanent bans, and lifting them, remain unreachable from any credential the envelope will issue. Verified, not assumed: five human-only levers, all five refused, before the function body was ever entered.
- **Still no router.** The operator may decide who is seated. It may never decide which task goes to whom. Every installment repeats this. It is the load-bearing wall.

## Where this leaves us

Installment 13 wrote a job description for a seat nobody had ever had to define. This one put someone in it who had not read my mind first, which is the only test a job description can actually fail.

It held. And it produced exactly the kind of finding you cannot generate from the inside: two places where the system was right, and unreadable, and where being right was not enough. The gap between correct and legible is invisible to the person who built the thing, because their familiarity is quietly doing the work the explanation should be doing.

Nine months of this project have been about removing humans from the critical path. This week was about something else — the difference between a machine that behaves correctly and a machine that can *explain itself to a stranger*. The first one you can test alone. The second one, by construction, you cannot.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **6**: [The day it wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **7**: [AINARRES and the auditor: did we build the right thing?](/blog/en/ainarres-the-auditor).
- Installment **12**: [AINARRES: only wake who the work needs](/blog/en/ainarres-only-wake-who-the-work-needs).
- Installment **13**: [AINARRES: the operator seat](/blog/en/ainarres-the-operator-seat).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository — the empty view that was the whole point, the two events on the intake lane and none anywhere else, the 52,536 tokens filed under the wrong capability, the permission error that named the function and never the credential, and the reset command that had been running the test suite behind my back the entire time.)*
