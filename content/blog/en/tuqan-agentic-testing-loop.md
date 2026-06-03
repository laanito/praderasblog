---
Title: Tuqan — Agentic Lessons (12): Automating the Agentic Loop with Tests for a Complete Implement-Test-Push-Merge Cycle
Description: Turning the documented verification strategy (checklists as task source, verify scripts, DB asserts, CI integration) into a fully agentic loop: the agent picks tasks from the list, implements, runs automated tests, pushes and proposes merges... rinse and repeat until the list is finished. Possibly adding a second Q&A reviewer agent to avoid messing things up.
Date: 2026-06-03 11:00PM
Template: post
Author: Luis Amigo
Tags: Web Development, Systems, Productivity, Tuqan, AI Agents, Testing, Automation
Lang: en
Translation_Key: tuqan-agentic-testing-loop
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 12
Image: /assets/images/tuqan-agentic-testing-loop-hero.webp
---

In the previous leg (and in the big PR #64 we just merged) we closed an important vertical slice: real POSTs for multiple modules, the Empresas → Sedes rename, more modules under Personalización, a basic permissions matrix, menu editing, and centralized flashes.

All of that was done following the established pattern: tasks from checklists, implementation, manual verification + DB asserts via Docker and psql, frequent commits, one focused (but substantial) PR.

But the user summarized it perfectly right after the merge:

> "how we can automate the testing to make this a fully agentic loop: You get tasks from the list, implement, test, push and merge... rinse and repeat until the list is finished, maybe adding a second Q&A agent to make sure we are not messing it."

That is exactly the direction we want to take.

### The current state of the "testing strategy"

Before dreaming about full agentic operation, we honestly documented where we stand (see the new top-level section in `.agents/STAGE-CHECKLISTS.md`):

- For large functional changes like those in stage 8.6 we do not yet have comprehensive unit or integration tests covering the new `Procesar()` methods in the forms.
- The modern page classes remain quite coupled to session, `Manejador_Base_Datos`, Twig rendering, and header redirects.
- What we **do** have is a reproducible, documented playbook:
  - Exact `docker compose` + `psql` + `php -l` commands.
  - `scripts/verify-8.6.sh` (syntax + DB state assertions after the 0012/13/14 patches).
  - Integration into GitHub Actions (init-db + verify script, non-blocking for now).
  - The detailed "Verification Playbook" that anyone can follow after a merge to gain fast confidence: clean-room runs, DB asserts after "POSTs", browser flows + side-effect checks in the database.

This is the "Test + Fix Loop" made practical for big slices: reproducible, Docker-only, with captured evidence.

### From manual playbook to agentic loop

The next leap is to close the loop:

1. **Task source**: the checklist in `.agents/STAGE-CHECKLISTS.md` (or a dedicated tasks file). Each item is an atomic unit: "implement POST for X", "add assert to verify script", "update strategy documentation".

2. **The main agent** (me in this case, or a future Cursor/Grok agent):
   - Reads the next pending todo (using `todo_write` internally to track progress).
   - Implements (edits code, creates patches, scripts, updates templates/routes).
   - Runs verification: executes the corresponding `verify-*.sh`, the `psql` asserts, `php -l`, rebuilds if necessary.
   - If it passes: commit + push + PR creation (or just commit on the current branch in "blind trust" mode).
   - Marks the todo completed.

3. **Rinse and repeat** until the stage's task list is empty.

4. **The second agent (Q&A / reviewer)**: before push or proposing the PR, we spawn a sub-agent with a *reviewer* role (or a "Q&A agent" persona) that:
   - Reviews the diffs.
   - Re-runs the verification commands.
   - Looks for consistency issues (did we update the playbook? Do the asserts cover the new behavior? Are there broken legacy paths?).
   - Gives feedback or approves.
   - This reduces the risk of "messing it up" on big changes.

In practice we already use parts of this (todo_write at the start of every stage, spawn_subagent for reviewers in other skills, the verify script we just wired into CI). Today's article is about **formalizing it and automating it** so the agent can drive the full loop with minimal human intervention until the list is exhausted.

### What is already in motion (and can be automated today)

- `todo_write` as the task queue mechanism (already used when opening each stage).
- `scripts/verify-*.sh` + the commands from the playbook (easy to invoke from the agent).
- CI that already runs init + verify on every PR (added in the post-merge work).
- Sub-agents with different roles (reviewer, implementer, etc.) via `spawn_subagent`.
- Total reproducibility via Docker + patches in `docker/db-init/data-patches/`.

What is still missing for the complete loop:
- An agent that can "read" the checklist (parse the markdown or the evidence section) and automatically generate the next todos.
- Logic to decide when to create a PR vs. continue on the same branch (based on size or whether the user asked for "blind trust").
- The reviewer agent as a hard gate before push/PR.
- Possibly a "merge agent" that, once everything passes and the user approves, performs the merge via tools (gh CLI or MCP).

### Why this matters for Tuqan (and similar legacy projects)

The classic problem when modernizing a large legacy application is not just writing the new code. It is **knowing with confidence** that what you just touched did not break anything — especially when there is state in the DB (patches, users, menus, permissions), legacy + modern routes coexist, and the real menu is the source of truth.

With the current approach + loop automation:
- Every change comes with its reproducible verification.
- The agent does not "finish" a task until the asserts pass.
- The reviewer agent acts as a safety net (similar to a human PR review but instant and deterministic for the mechanical parts).
- We can move faster in "blind trust" mode because the loop includes the guardrails.

And when we finish a complete stage (empty task list + green playbook + user test pass), we generate the article telling the story. Exactly as we did with 8.5 and now with this extended 8.6.

### Conclusion

PR #64 gave us the functionality (POSTs + more modules + corrections). The documentation and testing scripting work that followed gave us the **how we know it works**.

The next level is that the agent itself uses that "how" as its verification engine inside a closed loop:

**checklist task → implement → verify (script + asserts + CI) → push → (reviewer agent) → PR → merge → next task**

Until the list is finished.

With a second Q&A agent as co-pilot to keep us from shooting ourselves in the foot.

That is what we are going to build (and document) in the coming iterations.

The key files the agent will use as "single source of truth" for this loop already exist:
- `.agents/STAGE-CHECKLISTS.md` (tasks + playbook)
- `scripts/verify-8.6.sh` (and future verify-*.sh)
- The patches as the reproducible source of data changes
- The tuqan repo + Docker as the only environment

The agentic future of Tuqan already has the skeleton. We just need the agent to pilot it end-to-end.

---

*This article is part of the "Tuqan — Modernización" series. The code and verification documentation live in the Tuqan repository (PRs #64 and the testing follow-up).*

*After the user performs the full app test pass, we will continue closing more items from the list and refining this loop.*