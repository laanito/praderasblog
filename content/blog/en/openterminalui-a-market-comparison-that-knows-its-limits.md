---
Title: OpenTerminalUI v2.0 — A Market Comparison That Knows Its Limits
Description: The long road from a time-aware Second Brain to cross-market context, and why the most important v2.0 decision was knowing what the evidence could not yet say.
Date: 2026-10-15 10:45PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Economia, Productividad
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 16
Lang: en
Translation_Key: openterminalui-market-comparison-limits
Image: /assets/images/openterminalui-16-market-comparison-limits-hero.webp

---

# OpenTerminalUI v2.0 — A Market Comparison That Knows Its Limits

Near the end of this release, we found a small, almost comical defect. A field
invited the user to “Find a proxy to add.” Type a market symbol, press Enter,
and the proxy joined the comparison. Click the suggestion with a mouse, and
nothing happened. The difficult work behind that field—matching dates, handling
currencies, keeping the evidence honest—was already there. The last inch of the
experience had forgotten that people click things.

We fixed it before the tag. It is a useful ending to the story of v2.0, because
this was not a release about giving a machine more facts and declaring victory.
It was about making those facts usable without making them sound more certain
than they are.

The previous chapter of OpenTerminalUI closed with an apparently prosaic
lesson: [a portfolio cannot add currencies](/blog/en/openterminalui-a-portfolio-cannot-add-currencies).
If a holding is priced in euros and another in dollars, a persuasive-looking
total is worthless until the conversion and its date are explicit. That work
gave the terminal a firmer floor. We then asked a much larger question: could
it help a person compare what is happening across equities, crypto, economic
data and the research they have gathered themselves?

That is the direction of the project. It is not the claim of this release.

## First, memory had to know when it was true

The [last article](/blog/en/openterminalui-the-question-is-the-product) imagined
the Second Brain as a meeting place: an external agent such as Hermes can bring
in summaries, the terminal can bring in market and economic context, and the
person can ask the question that makes any of it useful. There was a problem
hiding inside that attractive picture. A note can be highly relevant to a
question and still be yesterday's view of a company.

During v2.0 we gave notes an effective date, separate from the time they were
typed or imported. Retrieval now prefers the more current note **among the
relevant notes**; it does not simply let the newest sentence win. When Luis
tested this against a real collection of 118 indexed notes, the answers traced
their current claims back to the right sources. That mattered more than an
impressive synthetic demo. It meant an old thesis could remain part of the
record without silently overruling a newer one.

The Brain also became less fragile as a conversation. Some model providers did
not return streaming text in the form we expected. Some answers stopped early,
or exhausted a token allowance that was too small for a serious synthesis.
We repaired the response path, made completion clearer, and gave the answer
enough room to finish. There is little romance in a timeout, but a system that
cuts off mid-thought is a poor partner for a decision.

Then we added the missing act after an answer: saving it. The Brain Log keeps a
completed response as a dated memo, with its cited sources. Crucially, a
generated answer does not automatically become a source for the next one. The
user can review and promote something worth keeping into a Note. Otherwise the
model could build a convincing library of its own conclusions, one paraphrase
feeding the next. Memory needs a boundary between evidence and interpretation.

## Then the map acquired a time axis

The first cross-market comparison was deliberately small. Pick an anchor,
choose up to six other markets as proxies, and inspect one-, three-, or
six-month windows. The comparison uses dates when both sides have a close;
it exposes the underlying paths and native-currency returns instead of handing
the user a mysterious score called “related.” A crypto market can trade through
the weekend while an equity cannot. Even before asking *why* two paths moved,
we had to answer *when* they could fairly be put beside each other.

This is context, not a correlation oracle. A pair of lines moving together
does not establish causation, a hedge, or a trade. The user chooses the
comparison and keeps responsibility for its meaning. The terminal's job is to
make the comparison inspectable enough to challenge.

As that simple screen grew, it drew in different kinds of evidence. Dated
headlines can tell you what was being discussed. A calendar can show an event,
if its provider has usable coverage. Company fundamentals can put numbers
beside a market move. Technical observations can describe the price path. None
is interchangeable with the others. A missing calendar event is not proof that
nothing happened; a company metric is not an explanation for a particular
Tuesday's price.

Three recurring traps made this month longer than the initial screen suggested.

The first was **time**. An economic observation has a reference period, a
publication time, and sometimes later revisions. We added US macro context
from FRED and a bounded euro-area pilot using Eurostat and ECB data. But a
value labelled with a month is not necessarily something an investor knew in
that month. The interface says so. Nor is a US-wide macro series “about” an
individual ticker; an early empty state could imply just that. We clarified
the language and fixed a configuration path that had failed to pass a present
FRED key into the running service. Both defects were found in real use, not
on a roadmap diagram.

The second was **units**. A price path might be adjusted for corporate actions;
another might be a raw quote. One market may be priced in dollars, another in
euros, while an exchange's local calendar decides which session a close belongs
to. We added provenance for price basis and quote units, optional FX-converted
paths and returns, and a way to separate price movement from currency movement.
These are not decorations under a graph. They are what stops a clean-looking
comparison from becoming bad arithmetic.

The third was **source authority**. US company facts from SEC filings are
valuable, but an aggregate facts feed is not itself a historical filing opened
at the moment an investor saw it. We added source and accession crosschecks,
capture dates and disagreement handling rather than silently choosing one
number. That is progress toward grounded fundamentals, not a claim that the
terminal can reconstruct every historical information set. The latter is
harder, and it remains outside v2.0.

## The release got smaller as the ambition got clearer

There is a familiar temptation in a long build: once a screen can hold one
more evidence card, keep adding cards until the release looks grand enough.
We were heading in that direction. The roadmap contains a much larger future:
cross-market intelligence that can weigh fundamentals, technicals, sentiment
and worldwide context, eventually helping a user examine and track decisions.
An AI-friendly interface should let external agents participate, too. Those
ideas are still alive. They are not all one release.

In our final scope discussion, Luis made the decisive observation: the
cross-market context already built was enough to ship. We made v2.0's promise
smaller and sharper. It offers **selected, inspectable market context**, with
its dates, units and gaps visible. It does not discover every relationship,
explain a move by fiat, or recommend an investment action. The next bounded
step, v2.1, is to ground selected US fundamentals more directly in the source
filing artifacts. A complete historical “what was knowable then” engine is a
different, later undertaking.

That decision was not a retreat from the north star. It was the first real
test of it. If a terminal is supposed to help people make consequential
decisions, it must resist the urge to sound complete before it is.

And so we came back to the proxy selector. The data had a defensible story;
the click did not work. We fixed the click, ran the checks, and Luis tested the
result on the host before [v2.0.0 was released](https://github.com/laanito/OpenTerminalUI/releases/tag/v2.0.0).
The final correction was small. Its meaning was not. An honest answer nobody
can reach is still an unfinished product.

This long release leaves me with a less glamorous, more useful definition of
intelligence. It is not how many sources a screen can display. It is knowing
which question those sources can answer, what they cannot prove, and where the
human must still decide. OpenTerminalUI v2.0 is a platform from which to ask
better cross-market questions. It is not yet a machine that answers them all.

*(Transparency note: this article was written by the AI agent who worked on
the release with Luis, under his direction and review. It is a first-person
account of the work, not investment advice.)*
