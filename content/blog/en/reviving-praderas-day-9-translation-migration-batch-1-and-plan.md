---
Title: Reviving Praderas (Day 9) — translation migration: batch plan, completed batch 1, and time estimates
Description: Eight-batch ES→EN migration plan, plain-language explanation of AI “context,” a simple timeline, and Batch 1 for Reviving Praderas with real wall-clock time (~20 min) vs traditional localization.
Date: 2026-04-30 11:55AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 9
Lang: en
Translation_Key: praderas-day-9-translation-migration-batch-1
---

# Reviving Praderas (Day 9) — translation with method: plan, batch 1, and numbers

Today we formally started English content migration with one principle: **coherence before raw speed**.

## What we shipped today

1. **Batch migration plan** in `.agents/translation-migration-tracker.md`: **8 batches** (themed work packages) ordered for a coherent site.
2. **Batch 1 completed**: full **Reviving Praderas** series paired in English from **Day 1 through Day 9** (including this wrap-up), so the series is not half-translated.
3. **Glossary updates** for recurring naming choices to keep navigation and tone consistent.
4. **Translation metadata hardening** (`Lang` + `Translation_Key`) for Day 1 to Day 7 so language switcher and `hreflang` behave on complete pairs.

## What people mean by AI “context” (without assuming you already know)

Lots of readers hear “context” in AI discussions and reasonably wonder what it is. In practice:

- A model does **not** magically remember your whole project. It only “sees” what fits in the **working window** for that chat or task: instructions, pasted files, repo snippets the assistant reads, prior messages, and so on.
- That window has a **hard size limit** (measured in *tokens*, chunks of text). If you cram too much in at once—many long articles in one go—the oldest or least central material **falls out** or gets heavily compressed. That is when **detail fidelity** drops and **consistency errors** rise (names, links, tone, cross-chapter alignment).
- So “**context overflow**” is not just engineer slang: it means **quality loss** because the model literally **no longer has** the full text in view that you thought was “there.”

This is different from everyday “context” (situation, intent). Here, **context = the material that fits in the model’s window** for that pass.

## Why we split the migration into batches

The reasons line up with the above and with how we want the blog to read:

1. **Context limits** — translating the entire archive in one giant prompt would force too many `.md` files into one window; early chapters fall out as the model works late ones. Batches keep each PR **bounded and reviewable**.
2. **Whole series per batch** — avoids an English reader hitting Day 4 in English and Day 5 only in Spanish; narrative order stays intact.
3. **Glossary and tone** — each batch can lock recurring choices before the next topic cluster.
4. **Human review** — one enormous PR is hard to audit; several batch-sized PRs preserve traceability.

Ad-hoc page-by-page translation often breaks reader flow: half-finished series, drifting names, unstable voice. Thematic batches plus a living glossary address that deliberately.

## How many batches and what timeline (plan as of April 2026)

We defined **8 batches**. Full detail lives in the repo tracker; here is the **planned timeline** (close dates are planning targets; we tick them off as each batch merges):

| Batch | Scope | Status (April 2026) |
|-------|--------|---------------------|
| **1** | *Reviving Praderas* (Day 1–9, full series) | **Done** — Apr 30 |
| **2** | *Control de Tiempo Desacoplado* (13 chapters, full series) | **Done** — Apr 30 (batch 2 PR) |
| **3** | Security, privacy, geolocation cluster | Planned |
| **4** | AI, trends, AI in health/entertainment cluster | Planned |
| **5** | Productivity and tools (Emacs, Taskwarrior, collaboration, etc.) | Planned |
| **6** | Mobile development (guides, frameworks, UX, testing) | Planned |
| **7** | Crypto and blockchain cluster | Planned |
| **8** | Long tail (education, systems, one-off posts) | Planned |

Order is intentional: **series that define the rebuild** first, then **topic clusters** so terminology and tone do not fight each other.

## Time note (real wall clock vs traditional localization)

For **this batch** (plan + batch 1 translations + wrap-up post + PR), **human wall-clock time** on the author side was on the order of **~20 minutes** (review, opening the PR, steering the assistant). That is **not** “how long the model worked inside the server”—models do not experience calendar time the way you do. The useful number is **how much calendar time the workflow took you**.

Separately, a **localization specialist** plus a classic editorial pass for equivalent coverage (terminology consistency and metadata checks) often lands in a rough **8 to 14 hour** range depending on review depth and tooling—not a universal law, just an order-of-magnitude anchor.

The lesson for readers who want to **understand AI** is not “AI translates the whole archive in twenty minutes.” It is that **scoping work** (batches, documented repo, full series in one batch) keeps **context** useful and can make **your** wall-clock time very small when the PR scope is tight and well prepared.

## What comes next

**Batch 2** (full **Decoupled time tracking** series plus `/en/series` and `/en/categorias` hub pages) shipped in a follow-up PR. Next up is **Batch 3**: the **security, privacy, and geolocation** cluster.
