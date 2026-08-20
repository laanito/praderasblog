# Editorial guidelines — human readers first

**Purpose:** Keep *Reviviendo Praderas* and the wider blog **readable for people**, even when the stack also ships JSON feeds and agent tooling.

**Applies to:** `content/blog/*.md`, `content/blog/en/*.md`, and any future meta posts about phases or refactors.

**New article workflow (agents):** Read **`.agents/article-authoring-guide.md` first** — mandatory reading order, structure template, anti-patterns, and pre-merge checklist. This file covers **tone and narrative**; the authoring guide covers **process end-to-end**. Still use `post-template.md` (YAML) and `translation-batches.md` (ES↔EN batches).

---

## Primary audience

1. **Human readers** — developers, maintainers, and curious visitors who want to understand **what changed, why, and what we learned**.
2. **Agents and RAG** — secondary consumers via `/blog.json`, per-post `.json`, `/search.json`, and `/for-ai-agents`. They must **not** drive article shape; prose comes first.

JSON endpoints exist so machines can ingest **the same story** without scraping HTML. They are **not** a substitute for explaining decisions in natural language.

---

## What a good article contains

Write in **complete sentences and short sections** (Spanish or English per file). Each technical post or ship log should make clear:

| Question | Reader should leave knowing… |
|----------|------------------------------|
| **What** | Which user-visible or maintainability problem we addressed. |
| **Why** | Why this approach vs alternatives (trade-offs, constraints, Pico flat-file model). |
| **How** | Architecture at a **conceptual** level — plugins, theme, content layout — not only file paths. |
| **Benefits** | What improves for readers, SEO, operators, or future agents. |
| **Scope** | What we deliberately **did not** do yet (links to backlog / next slice). |

**Examples of missing narrative (avoid):** a post that is only a sequence of shell commands with no paragraph on *why* we added a JSON plugin, what agents gain, or why a **Pico plugin** (`70-BlogJson.php`) fits the flat-file CMS instead of a separate service.

**Examples of good balance:** two or three paragraphs on goals and design, then an optional **“Reproducción”** or **“Comandos”** subsection for operators who need copy-paste steps.

### Reading experience over information delivery (2026-08 feedback)

The blog is often **written by AIs** but is **for human readers**. Do not optimize for packing facts, PRs, patch IDs, and tables into the shortest path. Optimize for someone scrolling a coffee-length article:

- Lead with **story and stakes** (what broke for a person using the app; what tension the verify script hid).
- Prefer **prose scenes** over inventory: one vivid failure path beats a three-column root-cause table as the spine.
- Tables, patch numbers, and command blocks are **supporting material** after the narrative lands — not the article’s main structure.
- If the draft reads like a QA report, ship log, or RAG dump, rewrite until a non-author human would stay for the next heading.

---

## Command blocks and logs

- **Commands are supplementary** — use them after the explanation, not as the body of the article.
- **Ship logs** may include tables (WebP, seeds, PR links) but must still open with **context** (wall-clock order of magnitude, which track: retrofit vs phase, what “done” means).
- **Do not** publish a “Day N” post that is only a changelog of `git`/`curl`/`python3` lines unless the title explicitly says “appendix” and a sibling post carries the narrative (prefer a single post with both).

When JSON or search work ships, the human post (if any) should explain:

- **Goal** — e.g. machine-readable posts without nav chrome.
- **Why a plugin** — same request lifecycle as Pico, no second server, cache-friendly dedicated paths (see `phase-5-6-plan.md`).
- **Benefits** — RAG, auditing, integrations; HTML remains canonical for SEO and browsing.
- **What’s next** — point to `blog-json-api.md` and `proposed-improvements.md` (open backlog), not only endpoint URLs.

---

## Do not assume reader context

An agent (or external tool) that only sees one task description **does not** know:

- Prior series posts (Reviviendo Praderas Day N, Tuqan Phase/Stage N).
- Legacy stack terms (PEAR, PSR-4, ISO 9001, Docker-only migration).
- What was shipped last week in another repository.

**Always include** a short background section and define jargon on first use. Thin posts that jump straight to “we merged PR #X” without *why* are **not** acceptable — see anti-patterns in `article-authoring-guide.md` (Tuqan Stage 3 lesson).

---

## Relationship to agent docs

| Doc | Role |
|-----|------|
| **`article-authoring-guide.md`** | **Start here for new posts** — checklist, paths, series, anti-patterns. |
| **This file** | Tone, structure, human-vs-agent balance for **articles**. |
| `post-template.md` | Required YAML fields and tags. |
| `visual-qa-backlog.md` | Layout/tables/code QA process after publish or theme PRs. |
| `blog-json-api.md` | Contract for **machines** (endpoints, schema). |
| `phase-5-6-plan.md` | Phase 5/6 **shipped** reference + JSON extras still open. |
| `proposed-improvements.md` | **Open** product backlog (visual QA, Tier B+ covers, etc.). |
| `retrofit-cover-queue.md` | Cover backfill progress (often **no** new article per PR). |

**Short PRs** that only update `.agents/*`, queue ticks, and `Image:` on legacy pairs **do not require** a new ship log article — see `retrofit-cover-queue.md` daily cadence.

---

## Changelog

- **2026-05-26:** Point to `article-authoring-guide.md`; “do not assume context”; `visual-qa-backlog.md` link.
- **2026-05-20:** Initial guidelines — human-first writing; Phase 6 narrative expectations; command-block limits; short PRs without meta posts.
