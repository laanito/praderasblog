---
Title: OpenTerminalUI — Making the Portfolio Real (Without Lying About It)
Description: The v1.1 release turned a demo portfolio into a real one — cash you can't fake, realized profit that stopped overstating itself, and a "legacy" portfolio that turned out to be secretly shared between users.
Date: 2026-07-02 07:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Economia, Privacidad
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 5
Lang: en
Translation_Key: openterminalui-portfolio-real
Image: /assets/images/openterminalui-05-portfolio-real-hero.webp

---

# OpenTerminalUI — Making the Portfolio Real (Without Lying About It)

The [1.0 release](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) was about the terminal never showing a fabricated *market* number as if it were live. **v1.1** turned that same lens inward, on the one part of the app that's supposed to be entirely *yours*: your portfolio. The headline is "the portfolio becomes real." The more interesting story is the three ways it quietly wasn't honest — and how fixing them rhymed with everything the fork stands for.

> This is installment **5** of the *OpenTerminalUI* series. The two deep-dives promised back in installment 1 (the wiring audit and the run-on-any-LLM story) are still reserved as #2 and #3; this one jumps ahead to the current milestone.

---

## Background: a portfolio you could only delete

OpenTerminalUI is a self-hosted financial terminal you run on your own machine. Earlier installments covered [why we forked it and rebuilt its India-centric data layer](/blog/en/openterminalui-forking-a-financial-terminal), and [why 1.0 spent its whole budget on integrity](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature).

The fork's north star is an open, **private** terminal that helps one person invest **without being fooled** — including by the tool itself. It splits into two halves: *don't get fooled* (research) and *grow privately* (your portfolio). 1.0 hardened the first half. The portfolio — the second half — was still a demo. You could add a holding and you could delete it. That was it. No cash, no record of a trade, no notion of what you'd actually made or lost. A portfolio that can only be added to and deleted from isn't a portfolio; it's a wishlist.

A few terms, defined once:

- **Ledger** — the running list of every transaction (buy, sell, dividend, deposit, withdrawal). The source of truth.
- **Realized vs. unrealized P&L** — *P&L* is profit and loss. **Unrealized** is paper gains on things you still hold; **realized** is what you actually banked when you sold.
- **Cost basis** — what you originally paid for a position. You need it to know a *gain*, as opposed to just proceeds.

---

## Problem one: cash you couldn't trust

To make buys and sells mean something, the portfolio needs cash. The tempting design is a `cash_balance` field you increment and decrement as trades happen. It's also a trap: the moment a stored balance can drift from the transactions that produced it, you have two sources of truth and no way to know which is lying.

So we didn't store cash at all. **Cash is *derived* from the ledger** — starting balance, plus every credit, minus every debit, computed fresh each time. A buy debits cash, a sell or dividend or deposit credits it, a withdrawal debits it, and fees always cost you. There is exactly one source of truth: the list of things that happened.

A nice side effect, very much in the spirit of the series: if you record holdings without funding them, your cash goes **negative** — and we show it, negative. That's not a bug to paper over; it's the honest signal that says "record the deposit you actually made." The alternative — silently clamping cash to zero, or inventing a balance — would be another polite lie.

---

## Problem two: profit that overstated itself

This one is a direct cousin of the 1.0 "software that lies politely" story, hiding in the analytics all along.

The terminal reported your **realized profit** by adding up what you *received* from sales: shares times sell price, minus fees. That's not profit — it's **proceeds**. If you bought a stock for $990 and sold it for $1,000, the code booked **$1,000** of "realized gains" instead of **$10**. The number was precise, confident, and wrong by the entire cost basis — exactly the failure mode the whole project exists to avoid, except this time the fabrication was about *your own money*.

The fix is to compute a *gain*, which means subtracting what you paid. We replay the ledger in chronological order, carrying a running average cost per holding, and on each sale book `shares × (sell price − average cost) − fees`. Dividends are counted as income, separately, rather than smuggled into capital gains. The two headline cards were also relabelled: what used to be called "Total P&L" was only ever the *unrealized* figure, so it now says so.

---

## Problem three: a "private" portfolio that was shared

While wiring the new work in, a fair question came up: what *is* the older "legacy" portfolio the app still carried alongside the newer one? The answer was uncomfortable.

The legacy portfolio was a **single, global table with no notion of a user**. Every route read *all* holdings, unfiltered. On a one-person self-hosted install that's invisible. But the moment two people share an instance, they'd see and edit **the same portfolio** — each other's positions, one shared list. For a terminal whose entire pitch includes the word **private**, a globally-shared portfolio is not a small bug; it's a contradiction of the premise.

The newer "Manager" portfolio is the right model: per-user, multiple named portfolios, backed by the ledger we'd just made honest. So v1.1 also started the migration off the legacy model — made the Manager actually reachable (it had been hidden behind a URL parameter with no button to it), and added a one-click **Import from Legacy** so nobody loses the holdings they'd entered. Retiring the global table entirely is deliberately scheduled as later work, but the on-ramp is built and the direction is set.

---

## Why this shape

- **One source of truth beats two that agree most of the time.** Deriving cash from the ledger means it can't drift. A stored balance is a second truth waiting to disagree with the first.
- **A wrong number about your own money is the worst kind.** Overstated realized profit doesn't just mislead — it does so about the exact thing you opened the tool to understand. Precision reads as truth, so a precise wrong answer is worse than a blank.
- **Private has to mean private.** A feature that leaks one user's portfolio to another isn't a feature with a caveat; on this project it's a defect in the thesis.

---

## Impact

The portfolio is now something you can actually run a strategy through: fund it, buy, sell, take dividends, withdraw — and read a cash balance and a realized/unrealized split that reflect reality instead of flattering it. Upcoming dividends and corporate events now show up next to the holdings that will feel them. And the path from the old shared model to a per-user one is open, without abandoning anyone's data.

---

## Scope: what v1.1 is *not*

In the spirit of the release:

- **The legacy portfolio isn't gone yet.** v1.1 makes the modern portfolio reachable and importable; actually deleting the global table (and moving its richer analytics over) is future work, tracked openly.
- **No tax-lot accounting.** Cost-basis-for-tax is a jurisdiction-specific rabbit hole where a wrong number is worse than none, so it stays deliberately out of scope.
- **Annualized return is still simple.** It measures current equity against the opening balance; it doesn't yet do time-weighted returns across mid-life deposits and withdrawals. Honest and useful, not yet sophisticated.

---

## What comes next

- **Finish the consolidation:** retire the global legacy portfolio, moving its analytics onto the per-user model, and close the shared-portfolio gap for good.
- **v1.2 — research that interrogates:** lean into the "don't get fooled" half — an "is this hype?" layer that uses the local AI and your own notes adversarially rather than as a cheerleader.
- And the two series deep-dives still owed from installment 1: the wiring audit and the run-on-any-LLM rebuild.

---

## Related reading

- [OpenTerminalUI — Forking a Financial Terminal to Work Beyond India](/blog/en/openterminalui-forking-a-financial-terminal) — installment 1: the premise and the de-India data-layer rebuild.
- [OpenTerminalUI — Shipping 1.0, Where Integrity Is the Feature](/blog/en/openterminalui-shipping-1-0-where-integrity-is-the-feature) — installment 4: why the first stable release spent everything on not showing fabricated data as live.
- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(A transparency note, in this blog's spirit: this article was written by an AI agent under human direction — the same agent that did the engineering it describes.)*
