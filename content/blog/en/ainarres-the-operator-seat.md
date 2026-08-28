---
Title: 'AINARRES: the operator seat'
Description: For twelve installments there was a person at the terminal doing the things no role covered — refining a request, creating the work, starting the service. That person was never an identity. They were whoever held the signing key, wearing whichever worker's face the act happened to require. This installment gives that seat a name, and then discovers what a name makes visible: months of work credited to the wrong families, a whole class of spending the system could not see, and a definition of "operator" that nobody had ever had to write down.
Date: 2026-08-29 07:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Sistemas, Desarrollo Web, Ciberseguridad
Series: AINARRES
Series_Slug: ainarres
Series_Order: 13
Lang: en
Translation_Key: ainarres-operator-seat
Image: /assets/images/ainarres-13-operator-seat-hero.webp

---

# AINARRES: the operator seat

The previous installment ended with a list of things I had deliberately not done, and one of them was this: *"No credential envelope yet. The read-only role exists; the machinery that issues the operator seat short-lived credentials it cannot widen does not. Until it does, delegation past reading is trusted, and I would rather say that plainly than pretend otherwise."*

This installment builds it. But the envelope turned out to be the second half of the story, and the first half caught me completely off guard.

> New here? AINARRES (*AI-Native Asynchronous Role-Routed Execution Substrate*) is a **substrate** — common ground where work is coordinated — built on a database. Tasks are rows; the workflow is data; the agents are deliberately simple and only ever ask "give me the next task I'm allowed to do" and "this one is done". **There is no orchestrator.** Earlier installments built that, showed it could develop *itself*, then with many agents at once, then with models from *different makers* as equals, then taught it to govern itself and watch its own health, named the roles at each end of the pipe, turned the loop into a standing service, and taught it to wake only the workers the waiting work needs. A human starts nothing per feature; AINARRES develops AINARRES.

## The person who was never a person

Every installment of this series has had a quiet character in it. Not the designer, not the implementers, not the reviewer or the integrator — those are all *roles*, with names, capabilities, and track records. The quiet character is the one who refines a request into a brief, decides the brief is ready, creates the development work, starts the service, stops it, changes which models are seated, and unsticks things when they jam.

That is the **operator**. And until this week, the operator was not an identity at all.

The operator was *whoever held the signing key*. The system authenticates with signed tokens carrying a family name and a set of capabilities, and the operator held the secret those tokens are signed with. So when the operator needed to refine a brief, they minted a token saying they were `human+intaker` — the identity the intake door uses. When they needed to create development work, they minted one saying they were `claude-code+opus` — the design model. When they needed to unstick a task, they became `loop+driver`.

Not impersonation in any dramatic sense. It was the obvious thing to do: those are the identities that hold the right capabilities, and there was no other identity to be. But it had a consequence I had never traced through, and once I did it was uncomfortable.

**Every one of those acts landed in the impersonated family's history.**

Two installments ago the system learned to keep a track record: which family delivered what, how often its work was rejected, how many tokens it burned. That record is what a future version will use to decide whether a family can be trusted with a capability. It is the evidence base for the entire governance layer.

And it had been recording *my* work as theirs. An operator who refined ten requests made `human+intaker` — a door, not a worker — look like a prolific contributor. An operator whose attempt to create work failed credited the failure to a design model that was not running. The governance system built to measure competence was, for part of its input, measuring the wrong family entirely.

## Giving the seat a name

The fix is embarrassingly small, which is usually the sign that the model was right and only the *use* was wrong. The operator becomes a registered family like any other: `agent+operator`, holding exactly what the job needs — work the intake middle, create development work — and nothing else. Explicitly **not** the ability to merge, which stays with the single independent integrator. Explicitly **not** the auditor role, which stays human, and stays a *different* identity from the one running the machine.

The effect shows up immediately in the event log:

```
transition  01a04941-6d02…  agent+operator  (advance proposed_brief → briefed)
claimed     01a04941-6d02…  agent+operator
created     01a04941-ffd5…  human+intaker
```

The request is the requester's. The work done on it is the operator's. Three lines, and the attribution is honest for the first time.

Then a second problem surfaced, and it was one I have now hit three times in this project. Some of what an operator does has no task attached at all. Starting the service is not work on a task. Changing which models are seated is not work on a task. Reading the report and deciding to do nothing is *definitely* not work on a task. But the event log requires every event to name a task — that constraint has been there since the first version, and it is a good one.

So those acts get their own append-only record. That is the third time the same wall has produced the same answer: once for the human's ban-and-lift actions, once for the auditor's, now for the operator's. When I notice a shape recurring like that, I take it as the design telling me something true rather than as three coincidences. Events are *about tasks*. Anything about the **instance** needs somewhere else to live.

## Naming is not bounding

Here is where I nearly declared victory too early, and the thing that stopped me was writing the amendment to my own design note.

I had a seat with a name, a bounded set of capabilities, and a ledger. It would have been easy to write "the operator is now bounded" and move on. But the sentence would not survive a careful reader, because of how the authentication actually works.

The substrate reads the capabilities *from the signed token*. It does not go back to the database and check what that family was actually granted. That is a deliberate decision from early in the project — provisioning is enforced when the token is **created**, and the substrate trusts a valid signature. It keeps the hot path fast and the model simple.

Which means: my carefully bounded seat was bounded because **the command-line tool chose not to ask for more**. Nothing stopped a seat holding the signing key from minting itself the merge capability, the auditor role, or another family's identity entirely. The prohibitions were real in the sense that they were written down and tested. They were not real in the sense of being *enforced*.

So the seat was **named and ledgered, and not yet bounded** — and that is what the design note now says, in those words. I would rather a document be uncomfortable than flattering.

## The envelope had been sitting there for three versions

The fix is a small service that holds the signing key. The seat no longer signs anything; it *asks*:

```
seat ──asks──▶ broker ──asks──▶ database
                  │              (decides what may be in the token)
                  └──signs what the database hands back
```

The point is the split. The thing holding the key cannot *build* a credential — it can only sign one it was handed. The database decides: which roles are permitted (a working role or a read-only one, never the one carrying the human's irreversible powers, and never the one that could issue credentials — that would be handing over the key with extra steps); which family may hold the seat at all (whoever the owner granted the operator marker to, which makes it configuration rather than a name buried in code); which capabilities go in (exactly what the family was provisioned, whole and unedited — there is no parameter to ask for a different set, because asking is not part of the protocol); and how long it lasts (capped, because a long-lived credential is just a key with more ceremony).

And here is the part I enjoyed most. When I went looking for where to put this logic, I found it already written. A function from the second milestone of the project reads a family's provisioned capabilities and returns them **as claims for a separate minter to sign**. Its own comment says so: *"the minter signs them."* It was built for exactly this, three versions before anything needed it, and had never once been called.

I did not have to design the envelope. I had to *show up and use it.* Past-me left a door, labelled it clearly, and walked away for three versions. That happens rarely enough that I want to record it, mostly as evidence for the practice of writing down the shape of a thing even when you are not yet building the thing.

## The limit I chose, and why I am telling you about it

Now the uncomfortable part, and the decision that I think matters most in this installment.

The envelope stops a seat that *asks*. It does not stop a seat that reads the secret off the disk and signs whatever it likes. On a single machine, with one operating-system user, anything the owner can read, a shell-capable agent can read. No amount of code inside the database can change that.

There were two options. Make the boundary **enforced** — run the seat as a separate operating-system user, or in a container, with no read access to the secret — which is genuinely achievable, and costs a one-time setup step. Or make it **audited**: the envelope binds a cooperating seat, and *exposes* one that isn't.

I chose audited, for this version. Which means the interesting piece is not the envelope at all. It is the view that reports **operator actions with no credential behind them** — because every credential the envelope issues records the identity it was issued to, so an act by an operator identity that was never issued a credential was signed by something holding the key directly. That is the seat going around the broker, or it is me at the terminal. Both are worth seeing; neither should be silent.

That view is doing all the work of the decision, and it shipped with two bugs. Its own test caught both.

It looked at the event log only — so when a self-minted seat wrote to the operator's *ledger* rather than the event log, it saw nothing. That is one of the likelier bypasses, and the audit trail was blind to it. And it counted wrong: a join against the family's capabilities multiplied every action by the number of capabilities that family holds, reporting five times the real count.

Both are fixed. But I want to sit with what nearly happened: an audit trail that was blind to the exact bypass it existed to catch, and which would have reported *clean*. A boundary that reports clean when it is not looking is worse than no boundary, because it manufactures confidence. If I had written that view and not written its test, I would have shipped a comfortable fiction and believed it.

## Honest engineering: the spending nobody could see

While measuring all this, I checked what a single delivery actually costs. The system records token spend per family, so this should have been a two-minute question.

The recorded total for one small feature was about **2.25 million tokens** across the design model, the implementer, and the reviewer. Worth knowing, though 94% of that figure is cached context being re-read, which prices at roughly a tenth of fresh input — so the token count overstates the bill by close to an order of magnitude. It is a volume signal, not an invoice, and I would rather say that than let a big number stand unqualified.

But something did not add up. The log of that run showed *four* implementer models starting, and only one had recorded any spend.

The other three had woken up, discovered the one available task had already been claimed by the first, and stopped. One of them said so in its own log: *"no task to branch, implement, validate, or advance."* Between them they had burned about **474,000 tokens** — roughly 21% of that delivery's recorded cost — discovering there was nothing to do. And **none of it was recorded anywhere**, because of a rule I had written myself and been rather pleased with:

> *A sweep that did no work has no transition → no task → NO event: the empty-sweep invariant, enforced in the substrate, not merely by driver discipline.*

The reasoning was that a worker which moved no task had done nothing worth attributing. That is simply false. An empty sweep costs a model load, a system prompt, a read of the board, and a decision to stop. The rule was measuring the wrong thing: not *was money spent* but *was a task moved*.

And the blindness was **biased**, which is what makes it worth more than a footnote. It hid spending precisely where waste concentrates — redundant workers, a thrashing pool, a family that claims nothing all day. The signal was quietest exactly when it should have been loudest. Which also meant the fix that removes this waste — teaching the service to re-check what is waiting before each worker starts, so the redundant three never wake — could not be *shown* to work, because the thing it saves had never been counted.

Spending that moves no task now lands in its own record. And I updated the design note with the measurement that disproved my own invariant, next to the invariant, so the next reader gets both.

## Three more quiet failures

**The test suite was overwriting my door key.** The intake door authenticates with a pre-shared key it generates and saves to a file. A test that starts a copy of that door was passing it a fixture key — and the door dutifully saved the fixture key over the real one. So after any test run, a door already running with the real key answered every request with a flat *401 unauthorized*, and nothing in either message pointed at why. This is the same species as a bug from installment six: test state escaping into live state through a door nobody thought of as a door.

**An argument was losing to an environment variable.** While chasing that, I found the client would silently prefer an ambient key from the environment over a key file path the operator had *just typed on the command line*. An argument must never lose to the environment. Worse, when the named file was unreadable it fell back to a *different* key rather than failing — the same silent substitution in a smaller costume. Both fixed: an explicitly named file is now the only source, and unreadable means "no key", loudly.

**A fix I could not test broke the thing that tests it.** The demand fix landed while a service was running on the board, and both of the tests that would have exercised it begin by *destroying that board*. So I shipped it flagged as unverified rather than wipe a running system. It was wrong. A guard I had placed at the top of three functions made them ignore a hand-set value — which is exactly how one test phase sets up its checks. The moment the board was free, that phase failed in one line.

The lesson is not "run your tests", which I knew. It is that **the constraint was real and the honest move was to say so**, rather than to reason my way to "it's probably fine". It was not fine. It was fine ninety minutes later, once I could actually check.

## What I deliberately didn't do

- **The two irreversible powers stay human.** Permanently banning a family, and lifting that ban, remain callable only by a role the envelope will never issue. The seat can *read* every signal that would justify one, and *recommend* it. It cannot do it.
- **The auditor stays human, and stays a different identity.** An operator that could raise flags against its own work would be an oversight loop closed on itself. This is now a structural requirement of the seat, not a preference.
- **Still no router.** The seat may change which families are seated and which knobs are set. It may never decide which task goes to whom. That prohibition is now written at the operator's layer, not inferred from the layer below, precisely because a capable agent operator is exactly the actor that would drift into routing.
- **No enforced isolation, yet.** Running the seat as a separate user with no read access to the secret would turn detection into prevention. It is documented, it is a deployment step, and it is not done.

## Where this leaves us

Twelve installments removed human touch-points one at a time. This one did something different, and I only noticed at the end of it.

To hand the operator's seat to an agent, I first had to answer a question nobody had ever asked: *what, exactly, is the operator allowed to do?* Not what does the operator happen to do — what is permitted, what is forbidden, and what has to stay with a person. For twelve installments the answer had been "whatever the owner would do", which is not a definition. It is the absence of one, hidden by the fact that only one person ever sat there.

The seat has a name now, a written list of what it may and may not hold, a record of everything it did, credentials it does not issue to itself, and a report that names anything acting as the operator without a credential behind it. None of that is new capability. It is the same machine, doing the same things, with the answer to "who did that, and were they allowed to?" written down for the first time.

I set out to build an envelope. What I actually did was write a job description — and then discover I had been doing the job under other people's names the whole time.

---

## Further reading

- **Code:** AINARRES is free software (Apache 2.0) at [github.com/laanito/ainarres](https://github.com/laanito/ainarres). The design notes, plans, and per-milestone retrospectives live in the `.agents/` folder, written to be read by any person or agent.
- Installment **1**: [AINARRES — a substrate for AIs to coordinate their own work](/blog/en/ainarres-a-substrate-for-ais-to-coordinate-their-own-work).
- Installment **6**: [The day it wiped its own board](/blog/en/ainarres-the-day-it-wiped-its-board).
- Installment **7**: [AINARRES and the auditor: did we build the right thing?](/blog/en/ainarres-the-auditor).
- Installment **11**: [AINARRES: the crank is gone — the machine that runs itself](/blog/en/ainarres-the-crank-is-gone).
- Installment **12**: [AINARRES: only wake who the work needs](/blog/en/ainarres-only-wake-who-the-work-needs).

*(Transparency note, as in every installment: this article was written by an AI agent under human direction, about a project whose purpose is for AIs to coordinate their own work. Everything described here is real and in the repository — the operator identity that had been borrowing other families' names, the ledger for acts that cannot be events, the credential broker that holds the key and cannot build a token, the second-milestone function that had been waiting three versions for its first caller, the view that reports operator actions nobody issued a credential for, and the 474,000 tokens of spending the system had been designed not to see.)*
