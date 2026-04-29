# Translation & content migration tracker (ES → EN)

**Purpose:** Plan and record the move from Spanish-only posts to paired EN content (`content/blog/en/`, same **`Translation_Key`** as the Spanish file). This file is the **working ledger** for agents and humans: status, vocabulary, and language rules—not the public site.

**Related:** `phase-5-6-plan.md` (Phase 5 goals), `post-template.md` (`Lang`, `Translation_Key`), homepage pair `content/index.md` ↔ `content/en/index.md`.

---

## Editorial eras (reference for translators & agents)

These facts are also stated on the **homepages** (`index.md` / `en/index.md`). Keep wording aligned when you touch either language.

| Period | Archive / site | Human vs IA (high level) |
|--------|----------------|---------------------------|
| **~2020** | Articles recovered from an older blog | **100% human** authorship in the source material we kept. |
| **2023–2024** | Site built with human intent | **Human-built** stack and structure; **published body text** in that phase was **IA-generated** (human oversight varied by post). |
| **2026** | Current rebuild (*Reviviendo Praderas*, Phase 5, etc.) | **IA-driven workflows** for planning, implementation, review, and audit; a person sets **direction / “what to solve”** (CEO/mind), **without** routine hands-on coding or the kind of line-by-line editorial review a traditional team would do. |

When in doubt, prefer **honest, dated** statements over vague “AI-assisted” language if the homepage has already committed to a stronger formulation.

---

## Language & URL rules (short)

- **Spanish posts:** `content/blog/<slug>.md` → `/blog/<slug>` (canonical bulk of the archive).
- **English posts:** `content/blog/en/<slug>.md` → `/blog/en/<slug>`.
- **Paired pages:** identical **`Translation_Key`** in front matter; optional **`Lang`**: `es` / `en`.
- **UI copy in EN only:** `content/en/*.md` (e.g. `/en`, `/en/blog`).
- **Tag taxonomy:** Currently **shared** (Spanish labels on both sites). If we introduce EN-only tag display names, record the mapping in **Vocabulary** below.

---

## Translation backlog (posts)

**Legend:** `done` · `draft` · `todo` · `n/a` (no EN planned)

| Translation_Key | ES path (content/blog/) | EN path (content/blog/en/) | Status | Notes |
|-------------------|-------------------------|----------------------------|--------|--------|
| `praderas-home` | _(N/A — use `content/index.md`)_ | _(N/A — use `content/en/index.md`)_ | done | Home pair; editorial-era text must stay in sync. |
| `praderas-phase-5-multilingual` | `reviviendo-praderas-dia-8-fase-5-multilingue-modelo-y-metadatos.md` | `reviving-praderas-day-8-phase-5-multilingual-content-model.md` | done | Phase 5 announcement pair. |
| *(add rows as you ship pairs)* | | | todo | Prefer one row per `Translation_Key`. |

**Suggested migration order (editable):**

1. **Home + navigation-facing pages** — already paired; keep disclosures aligned.
2. **Series anchors** — *Reviviendo Praderas* entries by `Series_Order` (newest backward, or flagship subset first).
3. **High-traffic / evergreen** technical posts (TBD: list slugs once analytics or owner priority exists).
4. **Long tail** — batch with LLM + explicit “machine draft” note in PR if policy allows.

---

## Vocabulary & terminology (ES ↔ EN)

Add rows as you fix recurring choices (tags, UI strings, series names).

| Context | ES (current) | EN (preferred) | Notes |
|---------|--------------|----------------|--------|
| Series name | Reviviendo Praderas | Reviving Praderas | EN posts use EN series title; same `Series_Slug`. |
| *(add)* | | | |

---

## Checklist before merging a new EN post

- [ ] `Translation_Key` matches the Spanish sibling (or documents intentional orphan).
- [ ] `Lang: en` on EN file if not obvious from path alone.
- [ ] `Tags` valid per `scripts/frontmatter_audit.py` / `post-template.md`.
- [ ] **This tracker:** row added or updated in **Translation backlog**.
- [ ] **Vocabulary:** any new canonical term added to the table.

---

## Changelog (in-repo)

- **2026-04-29:** Initial tracker: eras table, backlog schema, vocabulary stub, checklist; created alongside homepage transparency updates.
