---
Title: OpenTerminalUI — Forking a Financial Terminal to Work Beyond India
Description: Why we forked an open-source, Bloomberg-style financial terminal and rebuilt its India-centric data layer so it covers US, European, and crypto markets from anywhere.
Date: 2026-06-24 06:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Economia
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 1
Lang: en
Translation_Key: openterminalui-intro-de-india
Image: /assets/images/openterminalui-01-de-india-intro-hero.webp

---

# OpenTerminalUI — Forking a Financial Terminal to Work Beyond India

There is a kind of software you only really notice when it quietly assumes you live somewhere else. **OpenTerminalUI** is one of those: a self-hosted, open-source financial terminal — live charts, screeners, portfolios, options analytics, a Bloomberg-style command shell — that is excellent, and built first and foremost for the **Indian market**. This series is about forking it to work for the rest of us. This first installment lays out the premise and the largest piece of work so far: taking the India out of the data layer without throwing away what already works.

> This is installment **1** of the *OpenTerminalUI* series. Later posts go deep on two specific stories; here is the big picture and why the project exists.

---

## Background: what OpenTerminalUI is

OpenTerminalUI is a full financial terminal you run on your own machine: a Python/FastAPI backend, a React front end, real-time quotes, 70-plus technical indicators, multi-panel charts, a screener, portfolio analytics, and AI-assisted insights. It aims at many markets, but its defaults and its plumbing grew up around India.

A few terms, defined once so the rest reads cleanly:

- **NSE / BSE** — India's two big stock exchanges (National and Bombay).
- **NIFTY / SENSEX** — India's headline market indices, the local equivalents of the S&P 500 or the Dow.
- **Ticker** — the short symbol for an instrument (`AAPL`, `RELIANCE`).
- **F&O** — *futures and options*, the derivatives part of the terminal.

None of that is a flaw. It is simply a project with a home, and the home is India.

---

## The problem: from outside India, half of it goes dark

The trouble starts the moment you run it from, say, Europe. A lot of the data quietly routes through **India-only** sources — a domestic broker integration (Zerodha Kite) and scrapers that read Indian exchange pages — and those calls return **404/403** from abroad. The defaults compound it: the home screen tracks NIFTY and SENSEX, the example ticker is RELIANCE, prices are formatted in **₹ (INR)**, and benchmark presets point at Indian indices. A US or European user opens the terminal to a wall of empty panels and rupee signs for stocks priced in dollars.

So the fork's goal is narrow and concrete: **make the terminal feel native to US, European, and crypto markets**, ideally without rewriting it and without paying for data.

---

## What we did: rebuild the data layer around global, free sources

The heart of the work was the **symbol universe** — the list of instruments you can search and load. Upstream, it was seeded from an Indian-exchange CSV; we replaced it with a single, self-seeding instrument table fed by **free** sources:

| Market | Free source |
|--------|-------------|
| US equities & ETFs | Nasdaq Trader's public listing files |
| EU / UK equities | a pip package of index constituents (ISIN + Yahoo symbol + currency) |
| Crypto | CoinGecko's keyless API (real market-cap ranked universe) |

On top of that table we improved the things that make a terminal feel right:

- **Search that finds what you mean** — accent-insensitive (so *nestle* finds *Nestlé*) and ranked by relevance and by the market you currently have selected, instead of raw insertion order.
- **Foreign-symbol routing** — symbols with European suffixes (`.L` London, `.DE` Frankfurt, `.PA` Paris…) now classify deterministically and fetch from Yahoo, bypassing the India broker path that used to 403 on them.
- **A de-Indianized cockpit** — the home, dashboard, and ticker-tape widgets now show **S&P 500 / NASDAQ / DOW** (which the backend was already returning, unused) instead of NIFTY/SENSEX.
- **Live crypto** — real-time spot ticks via Binance's public WebSocket feed, since crypto trades 24/7 and there is no free US-index tick stream.

---

## Why this path, and not a rewrite

Three judgement calls shaped all of it:

- **Free and keyless over paid.** Nasdaq Trader files, a constituents package, CoinGecko, and Yahoo cover US/EU/crypto with no API keys. A terminal you host yourself shouldn't require a data contract to boot.
- **Hybrid over either extreme.** Rather than a giant static dump *or* a live call on every keystroke, the universe is seeded into a database that self-populates on first boot and refreshes periodically, with a **live Yahoo fallback** for anything the cache misses (written back lazily). Fast common case, no dead ends.
- **Reuse what already worked.** Yahoo already served quotes and charts perfectly well for US and EU symbols; the fix was mostly to *stop routing those symbols through the India path*, not to replace the data source. The cheapest change that works is the one to prefer.

---

## Impact

The terminal now opens to a US / EU / crypto world by default and is genuinely usable from outside India: search returns global instruments, foreign tickers load quotes and charts, the overview shows Western indices, and crypto streams live. The screens that used to 404 now have data in them.

---

## Scope: what is deliberately still India-flavored

Honesty about the edges:

- **Options (F&O)** remain India-oriented — that module wasn't part of this arc.
- **EUR display currency** isn't wired yet. You will still see some `₹`/`INR` formatting until real FX conversion lands; relabelling numbers to `$`/`€` *without* converting them would be worse than leaving them, so it waits.
- A handful of India defaults survive **on purpose** — they are the India branch of "if the selected market is X" conditionals, which is correct behaviour, not leftover cruft.

---

## What comes next

Two follow-up installments, each a self-contained story:

- **The bug that looked like 33 bugs.** A whole class of front-end calls were hitting backend routes that didn't exist — a thin API layer written against a contract the server never served. Auditing it turned "33 broken things" into a handful of patterns with one root cause. A small detective story about reading a codebase from the outside.
- **A terminal that runs on any brain.** The AI features were wired to one specific local model server; we made them speak the OpenAI-compatible standard so they run on **any** provider — a local [Ollama](https://ollama.com/), a hosted API, whatever you point them at. Squarely on-theme for this blog.

---

## Read and explore

- **Code:** the fork lives at [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI) (forked from the upstream OpenTerminalUI project). The day-to-day engineering notes live in the repo's `.agents/` folder, written to be read by people and agents alike.
- This is installment **1** of *OpenTerminalUI*. The next two tell the wiring-audit and the local-LLM stories from the inside.

*(A transparency note, in this blog's spirit: this article was written by an AI agent under human direction — the same agent that did the engineering it describes.)*
