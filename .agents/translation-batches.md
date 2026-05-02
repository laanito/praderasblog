# ES→EN translation: batching, context, and honest metrics (agent instructions)

**Audience:** humans and agents doing migration work. **Not** public site copy.  
**Canonical ledger:** `translation-migration-tracker.md` (backlog table, vocabulary, batch list).  
**Worked example of wrap-up tone and length:** `content/blog/reviviendo-praderas-dia-9-…` ↔ `content/blog/en/reviving-praderas-day-9-…`.

---

## Before you translate

1. Read `translation-migration-tracker.md` — current batch status, vocabulary, and `Translation_Key` rows.
2. Read `post-template.md` — `Lang`, `Translation_Key`, tags, date format.
3. Run `python3 scripts/frontmatter_audit.py` before merge when you touch front matter.
4. **Do not rely on local PHP** (`php -l`, `php index.php`, etc.) in agent workflows unless the environment explicitly provides PHP on PATH. This repo is valid Pico/PHP but many sandboxes lack a PHP binary—**skip PHP checks** rather than failing the task; note the gap for humans/CI if relevant.

---

## Why we use batches (operational + editorial)

| Reason | What to do |
|--------|------------|
| **Model context window** | Each chat/task only “sees” what fits in the window (instructions + file excerpts + history). Packing the whole archive into one pass **drops** early material or compresses it → worse fidelity and naming drift. **Scope one batch per PR** when possible. |
| **Reader coherence** | **Never split a series across batches** (e.g. half of *Reviviendo Praderas* in one PR and half later). Ship a **whole** `Series_Slug` in the same batch unless the human owner explicitly slices an oversized series with a documented exception. |
| **Glossary stability** | After each batch, **update the vocabulary table** in `translation-migration-tracker.md` for new recurring EN choices (UI labels, series titles, roadmap phrases). |
| **Reviewability** | Prefer batch sizes a human can review in one sitting. If a series is huge (e.g. 13 chapters), still **one batch** for coherence, but use **internal sub-steps** (commit series in order) — do not split the **published** series across PRs. |

---

## Explaining “context” to readers (when you write meta posts)

Many readers do not know what engineers mean by **context**:

- **Context = everything in the model’s working window** for that turn (system + user messages + tool/read file output), not “the whole internet” or “the whole repo.”
- Windows have a **finite size** (tokens). Overflow means older or peripheral text **stops being available** verbatim → more inconsistency and hallucination risk on names, links, and cross-post references.
- **Batches** are partly a **human PR** convention and partly a **fit-in-window** strategy so each pass keeps enough relevant source text in view.

When documenting time or “AI speed,” be precise:

- **Human wall-clock time** — calendar time the person spent (review, PR, steering). Example from real work: **~20 minutes** for a well-scoped batch is plausible; **do not** confuse with “the model ran for 20 minutes.”
- **Localization specialist order-of-magnitude** — useful as **comparison** (often many hours for equivalent glossary + metadata + QA), not as a flex or a universal law.
- **Do not** attribute multi-hour “AI-assisted duration” to a batch unless that is **actually measured** human time; agents must not invent inflated parallel timelines.

---

## Batch list (high level)

The **numbered batch plan** (8 batches: flagship series first, then topic clusters) lives in `translation-migration-tracker.md` → section **Batch migration plan**. Update that section when batch boundaries or status change; this file holds **rules**, not the live table.

---

## Checklist per batch (merge gate)

- [ ] All posts in the batch have **matching** `Translation_Key` on ES (`content/blog/…`) and EN (`content/blog/en/…`) pairs.
- [ ] `Lang: es` / `Lang: en` set where ambiguity exists (Spanish posts should set `es` when paired).
- [ ] EN series title follows vocabulary (*Reviviendo Praderas* → *Reviving Praderas*); `Series_Slug` stays shared.
- [ ] Tracker backlog rows + vocabulary updated.
- [ ] Optional but encouraged: **one short meta post** (ES + EN) when a batch completes — explain batch index, why batches exist, **context** in plain language, and **honest** human wall time (see Day 9 pair as style reference).
- [ ] **No local PHP required:** do not block on `php -l` or running Pico from the agent shell if `php` is unavailable (see `repo-context.md` → Quick Operational Notes).

---

## Changelog (in-repo)

- **2026-05-02:** Explicit rule: skip local PHP checks when PHP is not on PATH; point to `repo-context.md` for agent guardrails.
- **2026-04-30:** Initial instructions distilled from Day 9 wrap-up: batches, context window, series integrity, glossary, honest wall-clock vs localization comparison.
