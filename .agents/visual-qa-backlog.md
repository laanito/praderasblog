# Visual QA backlog — article layout, tables, code, and review process

**Purpose:** Track **known UI weaknesses** in long-form reading (post body, tables, code blocks) and define a **repeatable validation process** so visual work does not depend on chat memory or ad-hoc “looks fine to me”.

**Related:** Day 5 shipped baseline polish in `praderas-theme.css` (`proposed-improvements.md`, `day5-consultant-feedback.md`). This file captures **gaps that appeared as content grew** (more tables, technical posts, Tuqan series).

---

## Reported / observed issues (2026-05-26)

| ID | Area | Symptom | Likely cause (repo) | Priority |
|----|------|---------|---------------------|----------|
| **V-01** | **Reading width** | Post body feels **too narrow** on desktop; lots of empty space beside sidebar | `col-lg-8` column + `.pradera-main--reading { max-width: 42rem; }` in `praderas-theme.css` (~672px prose) | **High** |
| **V-02** | **Tables** | Markdown tables look flat, cramped, or “Bootstrap default”; poor header contrast and mobile overflow | No dedicated `.post-body table` rules in `praderas-theme.css`; rely on unscoped Bootstrap `.table` | **High** |
| **V-03** | **Code blocks** | `pre` blocks adequate but dated; inline `code` pink (`--bs-code-color`) clashes with meadow brand | Bootstrap vars in `styles.css`; minimal override in theme | **Medium** |
| **V-04** | **Code blocks** | No line-highlight, language label, or copy affordance | Not implemented (optional future) | **Low** |
| **V-05** | **Tables on mobile** | Horizontal scroll without visual hint; cells touch edges | Missing `overflow-x: auto` wrapper styling + padding | **Medium** |
| **V-06** | **Lists in articles** | Dense bullet lists in ship logs hard to scan | No extra spacing / nested list rhythm in `.post-body` | **Low** |
| **V-07** | **Hero + prose transition** | Jump from full-width hero to narrow column can feel abrupt | Layout only; optional max-width tweak on hero caption | **Low** |
| **V-08** | **Figures / captions** | `figure`/`figcaption` barely styled | Partial rules exist for images; captions weak | **Low** |
| **V-09** | **Tag pills in header** | Generally OK post–Day 5; verify contrast on all tag colors | `tag_styles` map in `post.twig` | **Low** |
| **V-10** | **Sidebar vs main balance** | On xl screens, 8/4 grid + 42rem cap leaves “dead zone” | Grid + max-width interaction | **Medium** |

---

## Target outcomes (acceptance)

When an item is **done**, a reviewer should confirm:

1. **Readable measure** — Body text on desktop is roughly **65–75 characters** per line (often ~`48rem`–`52rem` max-width, or relaxed cap inside `col-lg-8`), not ~40rem unless intentional for poetry-style narrow.
2. **Tables** — Header row distinct, zebra or borders subtle, `overflow-x: auto` on small viewports, cell padding ≥ `0.5rem`.
3. **Code** — `pre` uses brand-neutral background, monospace stack, comfortable padding; inline code readable (not hot-pink on green-tinted site).
4. **No regressions** — Listing cards, nav, sidebar, related posts, language switcher unchanged or improved.

Document before/after in a *Reviviendo Praderas* post or a short PR note with screenshots (optional).

---

## Visual validation process (every UI PR)

Use this checklist **before merge** and **after deploy** to production (`https://blog.praderas.org`).

### A. Pick sample pages (fixed set)

| Route | Why |
|-------|-----|
| `/blog/reviviendo-praderas-dia-25-tier-a-cierre-tuqan-y-para-agentes` | Long ship log, table, many sections |
| `/blog/tuqan-php8-docker-migration-plan` | Wide glossary **table**, technical prose |
| `/blog/tuqan-stage-3-config-secrets-query-safety` | External-agent article; average technical depth |
| `/blog/reviviendo-praderas-dia-4-fase-3-metadatos-taxonomia-y-lint-de-front-matter` | Code/command mentions |
| `/en/blog/reviving-praderas-day-25-tier-a-complete-tuqan-for-ai-agents` | EN layout + switcher |
| `/blog` or `/en/blog` | Cards unchanged |

Add the URL you changed if the PR touches a specific template.

### B. Viewports (browser or DevTools)

| Label | Width | Check |
|-------|-------|--------|
| Mobile | 375px | No horizontal page scroll; tables scroll inside wrapper; code wraps or scrolls |
| Tablet | 768px | Sidebar collapses/stack; prose readable |
| Desktop | 1280px | Prose width intentional; sidebar aligned |
| Wide | 1536px | No excessive empty gutter unless design choice |

### C. Per-element checks

- [ ] **H1** only in header area; body starts at **H2** (no duplicate H1 in markdown).
- [ ] **Links** in body: visible hover, not low contrast.
- [ ] **`pre` / `code`** readable, scroll if long line.
- [ ] **`table`**: headers legible; borders not harsh black grid.
- [ ] **Images / hero**: no overflow; WebP loads.
- [ ] **Language switcher** on paired posts.

### D. Automated / repo guards (lightweight)

| Step | Command / action |
|------|------------------|
| Lint CSS touch | Search `praderas-theme.css` diff only affects intended selectors |
| HTML sanity | View source: no unclosed `<div>`, no script leaks from old `blog.twig` bugs |
| Optional later | Playwright screenshot diff of sample routes (not in repo yet) |

### E. Sign-off template (PR description)

```markdown
## Visual QA
- Sample: /blog/...
- Viewports: 375 / 1280 checked
- V-01 reading width: [unchanged / improved — note]
- V-02 tables: [unchanged / improved — note]
- V-03 code: [unchanged / improved — note]
```

---

## Suggested implementation slices (theme)

| Slice | Scope | Files | Notes |
|-------|--------|-------|--------|
| **S1 — Prose width** | Relax or responsive `max-width` on `.pradera-main--reading` | `praderas-theme.css`, maybe `post.twig` | e.g. `min(48rem, 100%)` or 52rem on `lg+` |
| **S2 — Tables** | `.post-body table`, `thead`, `tbody tr`, wrapper | `praderas-theme.css` | Consider `table-responsive` wrapper in Twig or CSS `display: block; overflow-x: auto` on wrapper |
| **S3 — Code** | `pre`, `code`, optional `pre code` | `praderas-theme.css` | Override `--bs-code-color` inside `.post-body` |
| **S4 — Pass** | Run process § B–E on sample URLs | — | Ship log optional |

Order: **S1 → S2 → S3** (biggest reader impact first).

---

## Relationship to other docs

| Doc | Role |
|-----|------|
| `proposed-improvements.md` | Open backlog § Article body visuals + link here |
| `day5-consultant-feedback.md` | Original visual sequence (historical) |
| `article-authoring-guide.md` | Points authors to visual QA after publish |
| `editorial-guidelines.md` | Prose quality; does not replace layout QA |

---

## Changelog

- **2026-05-26:** Initial backlog (V-01–V-10) + validation process + implementation slices S1–S4.
