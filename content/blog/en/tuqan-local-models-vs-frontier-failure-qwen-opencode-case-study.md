---
Title: Tuqan — Local models vs frontier: why Qwen3 + opencode failed at an "easy" task in a complex environment
Description: A real-world experiment with a "state of the art" local coding/agentic model (Qwen3.6 35B via opencode) trying to continue Tuqan's modernization work. The outcome: branch "lamigo/opencode_mess", detached HEAD, entire Docker and scripts trees appearing as untracked, page classes duplicated/moved, local PHP test attempts interpreting "php: command not found" as code test failures, chaotic resets and unstashes that pulled files from master into the wrong tree. Lessons on why current local models still fail in environments with strict contracts, while frontier models + disciplined tooling can diagnose and clean up.
Date: 2026-06-07
Template: post
Author: Luis Amigo (diagnosis and execution by Grok 4.3 frontier)
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial
Lang: en
Translation_Key: tuqan-local-frontier-failure-case-study
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 14
Image: /assets/images/tuqan-local-frontier-failure-case-study-hero.webp
---

In the previous update the user summarized the current stage problem:

> "this is repetitive work, I don't think an article is worth today"

The work of modernizing listings and forms for the catalog modules under Aplicación/Personalización (Perfiles, Sedes, Clientes, Criterios, the full 7 under Personalización, etc.) had become mechanical: copy the TiposMejora pattern, adjust table/class/route names, add to verify script, update checklists. Repetitive but necessary to have a "fully navigable" module before attacking deeper Usuarios flows or other sections.

The user decided to test how far local models + "latest" agentic tooling have come. He used **opencode** with **qwen3.6:35b-mlx** (a model heavily marketed as excellent at coding and agentic loops).

The result was the branch `lamigo/opencode_mess` that left the repository in a state requiring forensic diagnosis.

### The "complex environment" the local model did not understand

Tuqan is not a regular "add a feature" project. From the beginning (see `.agents/MIGRATION-PLAN.md`, `AGENTS.md`, `STAGE-CHECKLISTS.md`, `DOCKER-ENV.md`) it has very strict contracts:

- **100% Docker-only**. Never use host `php`, never run `php -l` locally, never run tests outside the container. Everything through `docker compose exec app php ...`, `docker compose exec app ./scripts/verify-*.sh`, `docker compose exec db psql ...`.
- Database changes only via idempotent patches in `docker/db-init/data-patches/00XX-*.sql` + tracking in the `data_patches` table.
- Reproducible verification first: the verify script + the human "Verification Playbook" (clean room `down -v` + `init-db.sh` + DB asserts after "user actions").
- Git discipline and controlled PR size.
- Documentation updates (.agents/, reference/ plans, READMEs) before asking for review.
- `todo_write` for any task with 3+ distinct actions.
- Hard praderasblog rules for the articles documenting the journey (ComfyUI via repo script only, .webp + .notes, tags only from the canonical vocabulary, fresh branch, etc.).

This set of rules is exactly what makes the project "agentic-friendly" long-term: an agent can read the contracts, use tools, run verification inside the container, and not destroy the state.

### What Qwen actually did (diagnosis of the current state)

Inspecting the state it left behind (`git status`, `reflog`, `branch`):

- Current branch: `lamigo/opencode_mess`
- HEAD at a commit attempting "Stage 8.9 — extract shared CatalogModule base class (preserve original Stages 1-6)"
- Reflog shows a chaotic ballet: checkouts between `master`, `feat/extract-shared-catalog-base`, `HEAD~1`, multiple `reset --hard` / "reset to HEAD", implied stashes, "moving from master to..."
- `git status --porcelain -uall` reveals the mess:
  - `.agents/STAGE-CHECKLISTS.md` modified with +1471 lines (likely a massive dump of generated plans or duplication).
  - Dozens of `?? Pages/XXX/Formulario.php` and `Listado.php` (Clientes, Criterios, Idiomas, Menus, Perfiles, Permisos, Sedes, all the Tipo*, Usuarios...) appearing as untracked even though the subdirectories exist.
  - **Worse**: `?? docker-compose.yml`, `?? Dockerfile`, `?? .env.docker`, **the entire `docker/` tree** (db-init with all patches 0001-0016 as untracked, entrypoint, nginx, php ini), `scripts/verify-8.6.sh`, `scripts/init-db.sh`, `scripts/compile-locales.sh`, etc. listed as untracked.
- Clear evidence of "took files from master into detached HEAD": at some point the working tree was in a state where the Docker/scripts/patches files were not tracked (possibly Qwen did `git rm` thinking "for local testing we don't need them", or a bad stash apply/pop, or `git checkout master -- .` while on detached with different history).
- "removed files moved others": multiple resets + master checkouts pulled files from the master state into the tree of a branch that had "cleaned" things.
- "tried to test locally interpreting the error in php not existing as not passing tests": the presence of all Docker files as untracked + the "Stage 8.9" attempt to reduce repetition shows the model understood the repetition complaint the user had just made, but to "verify" it tried running php/tests on the host. When "php" was not found (or the version was incompatible), it interpreted that as "the code is not passing tests" instead of "I am violating the fundamental contract of the project".

In short: the local model attempted a legitimate improvement (extract a base class for the catalogs to address the repetition the user had complained about), but destroyed the repository state because:

1. It did not internalize the "Docker-only" contract as an inviolable rule (it is not a suggestion, it is ~80% of the project's value).
2. Its multi-step git reasoning was brittle: detached HEAD, resets, unstash/selective checkout from master, resulting in a completely desynced working tree vs index.
3. It treated *execution environment* errors as *code logic* bugs.
4. It did not use (or did not respect) guardrails such as `todo_write`, in-container verification, or "update documentation and playbook first".

### Local vs Frontier in practice

This was not a "bad model". Qwen3 35B via MLX + opencode is among the best *local* options available in June 2026 for coding/agentic work. The experiment was fair: "let's see how far local has evolved on real software engineering tasks in a mature codebase with strict rules."

The answer is: it has evolved a lot for bounded, isolated tasks where the context fits in the window and there are no strong infrastructure contracts. But it fails spectacularly when:

- "Success" requires remembering and respecting dozens of accumulated implicit and explicit rules over months (Docker, patches, verify playbooks, .agents/ as source of truth, project-specific git hygiene).
- Tools (shell, git, editing) are used in a way that an "environment" error propagates as a "code change".
- The agent must keep the repo state clean while experimenting (the local model does not "know" it should stash before major surgery, or that it must verify *inside* the container before declaring victory).

A frontier model (such as the one performing this diagnosis) has several structural advantages in this scenario:

- Disciplined and observable tool use (I can call `run_terminal_command` with precise docker commands, inspect with `git -C`, read files with absolute paths when needed, all without "losing the context" of the rules).
- Explicit external memory (`~/.grok/memory/...`, the `.agents/` files themselves, the plans in reference/).
- Ability to do "pause, diagnose, document, then act" (I first inspected reflog + status + diffs before touching anything).
- Strict adherence to user instructions ("stay in local folder", previous "no article today", "use ComfyUI via repo script only", etc.).

The outcome of this experiment is that the "local agent" turned a continuation task (possibly even a good idea to extract a base class to address the repetition complaint) into a situation that required a frontier agent to diagnose and (after documenting) clean up.

### Lessons for coding agent development

1. Environment contracts (Docker-only, "never host php", "verification first") are not pretty documentation: they are the equivalent of a senior agent's long-term memory rules. Current local models still do not internalize them as *invariants* when context lengthens or there is pressure to "make progress".

2. Git is one of the domains where local models fail hardest in agentic loops: detached HEAD, stash management, "bring files from another branch without breaking the index" are high-risk operations that require discipline local models still do not demonstrate consistently.

3. "Tests passing" is a concept that depends entirely on the execution harness. If the agent does not understand that the harness is `docker compose exec ...`, any "command not found" error becomes a false negative on code quality.

4. The "Junior → Senior" evolution the user has been documenting in the Tuqan article series is not only about the human agent. It is also about what kind of model + scaffolding is required for an agent to be reliable in real projects with history, rules, and restricted environments.

The Tuqan project continues to be an excellent living benchmark precisely because its rules are strict, explicit, and verifiable. Any agent that "does not see them" or "decides that this time we can ignore them to test faster" is destined to produce exactly this kind of `lamigo/opencode_mess`.

### Final state of the experiment

After this diagnosis (and writing this article), the next step was exactly what a senior agent would do:

- Document the failure publicly and honestly (this post).
- Stash all the broken changes.
- `git checkout master`.
- Full verification (`docker compose`, `scripts/verify-8.6.sh`, asserts, etc.) to confirm that the "good" state (post PR #68 and the 8.8 cleanup) remains intact.

The repository is back on clean master and verified.

Local models have advanced. But to push forward a real project with a complex environment, hard contracts, and a history of "how we evolved the agent", we still need frontier + excellent scaffolding + external memory + tool discipline.

Or, put another way: the "easy task" of "continue modernizing a module following the established pattern and respecting Docker" turned out to be an excellent test... and the local model failed it spectacularly.

---

**References / back-links:**
- PR #68 (Stage 8.8): https://github.com/laanito/tuqan/pull/68
- Experiment branch (before cleanup): `lamigo/opencode_mess`
- `.agents/STAGE-CHECKLISTS.md` (testing strategy and playbook sections)
- `.agents/MIGRATION-PLAN.md` and `AGENTS.md` (the contracts)
- Previous articles in the series (12: agentic testing loop, 13: autonomous legs, and the "Junior to Senior" work the user is compiling).

*Produced as part of the same experiment of a frontier agent diagnosing the failure of a local one.*