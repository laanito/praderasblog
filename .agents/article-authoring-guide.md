# Article authoring guide — for any agent (new posts)

**Purpose:** Step-by-step instructions so an agent **without chat memory** can publish a correct blog article on the first pass. This file exists because a one-off agent (e.g. external tooling) produced a **too-thin** Tuqan post: valid front matter, weak narrative, wrong tags, and assumptions that readers already knew PEAR, Docker stages, and project history.

**Read this file end-to-end before writing a single paragraph.**

---

## Mandatory reading order (do not skip)

| Order | File | Why |
|-------|------|-----|
| 1 | `.agents/README.md` | Hub: what docs exist. |
| 2 | `.agents/repo-context.md` | Where content lives, URL shapes, ES vs EN trees. |
| 3 | **This file** | Workflow + checklist. |
| 4 | `.agents/editorial-guidelines.md` | Human-first tone (what / why / how / benefits / scope). |
| 5 | `.agents/post-template.md` | YAML fields, canonical tags, `Image:` rules. |
| 6 | `.agents/translation-migration-tracker.md` | Add or update a row for your `Translation_Key`. |
| 7 | *Topic-specific* | See § Topic branches below. |

If the post is **only** a cover retrofit tick (no new narrative), you may skip a new article — see `retrofit-cover-queue.md` and `editorial-guidelines.md`.

---

## What you are writing (pick one)

| Kind | Example | Series / tracker |
|------|---------|------------------|
| **Reviviendo Praderas ship log** | `reviviendo-praderas-dia-N-...` | `Series: Reviviendo Praderas`, `Series_Slug: reviviendo-praderas`, increment `Series_Order`. |
| **Tuqan modernization milestone** | `tuqan-stage-N-...` | `Series: Tuqan — Modernización`, `Series_Slug: tuqan-modernization`, increment `Series_Order`. Read prior Tuqan posts in tracker. |
| **Evergreen / cluster post** | Tutorial, batch translation | Usually **no** day number; still needs `Translation_Key` if EN exists. |
| **Non-post page** | `/for-ai-agents` | Lives in `content/` or `content/en/`, not `content/blog/`. |

**Do not invent** a new series name if the post continues an existing line — search `content/blog/` for the latest `Series_Order` in that slug.

---

## Topic branches (read before drafting)

- **ES ↔ EN pair:** `.agents/translation-batches.md` — whole series in one batch when possible; glossary in tracker.
- **Tuqan / external app work:** Read the **previous Tuqan blog post** and the **application repo** PR or `.agents/` docs there. Explain acronyms (PEAR, PSR-4, ISO, Docker-only) — see expanded `tuqan-php8-docker-migration-plan.md` as a reference for depth.
- **ComfyUI hero:** `.agents/image-prompt-guidelines.md`, `.agents/comfyui-cover-images.md` — one WebP per `Translation_Key`, `--translation-key` on `export_cover.py`.
- **JSON / Phase 6:** `.agents/blog-json-api.md` — human post still explains *why*, not only `curl`.

---

## Anti-patterns (learned from real mistakes)

| Mistake | Why it fails | Fix |
|---------|--------------|-----|
| Short post that only lists PR files or diffs | Readers without repo access learn nothing | Open with **context**; explain decisions in prose |
| Assuming reader knows project history | New visitors and other agents lack memory | One paragraph: what the app/phase is, what was broken, what “done” means |
| English `Tags:` like `Web Development, Security` | Audit fails; YAML tags stay **Spanish canonical** | Use only tags from `post-template.md` |
| Jargon without gloss (PEAR, PDO, Stage 3) | Feels expert-only | One-line plain-language definition on first use |
| Skipping EN sibling or mismatched `Translation_Key` | Breaks language switcher / `hreflang` | Same key on ES + EN files; update tracker |
| `Image:` pointing at another post’s WebP | Violates one-asset-per-article rule | Generate or document explicit reuse in prose |
| EN article linking to Spanish `/blog/slug` only | Poor EN UX | EN body links use `/blog/en/...` where a pair exists |
| Skipping `python3 scripts/frontmatter_audit.py` | Broken tags, duplicate keys, missing images merge silently | Run before every PR |
| Dense “info delivery” tone (tables of P0s, patch lists, command spine) | Feels like a QA report for agents, not a blog for humans | Lead with reading experience: story, stakes, lessons in prose; data after (2026-08 feedback on Tuqan post-9.40 QA article) |

---

## Article structure template (copy the spine)

Use this **outline** in the body (adapt headings to ES or EN):

1. **Opening (2–4 sentences)** — What happened, for whom it matters, link to prior post in the same series if any.
2. **Background** — What a reader must know if they did not read earlier posts (product, stack era, constraints).
3. **Problem / motivation** — Why the old state was risky or insufficient.
4. **What we did** — Concrete changes at conceptual level (not a raw file list); optional small table.
5. **Why we chose this path** — Trade-offs vs alternatives (rewrite, host PHP, big-bang SQL migration, etc.).
6. **Impact** — Users, maintainers, security, tests, agents.
7. **Scope / not done** — What is explicitly out of this PR.
8. **Next steps** — Honest follow-up; link repo + related blog posts.
9. **Related reading** — Bullet links to series siblings (correct language URLs).

Commands, seeds, and WebP tables go in **§ Reproducción** or an appendix **after** sections 1–8.

---

## File paths and front matter

### Spanish post (default)

- Path: `content/blog/<slug>.md`
- URL: `/blog/<slug>`
- `Lang: es` (recommended when paired)

### English post

- Path: `content/blog/en/<slug>.md`
- URL: `/blog/en/<slug>`
- `Lang: en`

### Required YAML

```yaml
---
Title: <Clear title — series name + milestone>
Description: <1–2 sentences for SEO; no jargon without context>
Date: YYYY-MM-DD HH:MMAM
Template: post
Author: Luis Amigo
Tags: <only canonical Spanish tags — see post-template.md>
Lang: es   # or en
Translation_Key: <unique-stable-key>
Series: <exact series title>
Series_Slug: <kebab-slug>
Series_Order: <integer>
Image: /assets/images/<dedicated>.webp   # after export; optional until PR if documented
---
```

---

## Pre-merge checklist (agent)

Copy and tick before opening a PR:

- [ ] Read mandatory docs (§ above).
- [ ] ES file (and EN file if applicable) with matching `Translation_Key`.
- [ ] `Tags` are **canonical Spanish** on both languages.
- [ ] `Series_Order` is correct vs siblings (grep `Series_Slug` in `content/blog/`).
- [ ] Body answers: what, why, how, benefits, scope — not command-only.
- [ ] First use of acronyms explained (or linked to a prior explainer post).
- [ ] EN links target `/blog/en/...` where pairs exist.
- [ ] Row added/updated in `translation-migration-tracker.md`.
- [ ] `Image:` set to a **new dedicated** WebP for this post (paired ES/EN must share the exact same path). For any post in an existing series (Tuqan — Modernización, Reviviendo Praderas, etc.), a new hero is the default. Reuse of another post’s image is only acceptable in rare cases and **must** be explicitly documented in the body text + noted in the PR description. Meta/process posts are not exempt.
- [ ] `python3 scripts/frontmatter_audit.py` exits 0.
- [ ] No secrets, credentials, or `.env` contents in markdown.

---

## After merge (human or agent)

- Spot-check live URL on `https://blog.praderas.org` (hero, language switcher, tables/code if used).
- For visual regressions on article layout, see `.agents/visual-qa-backlog.md`.

---

## Changelog

- **2026-05-26:** Initial guide — mandatory reading order, anti-patterns, structure template, checklist (response to thin Tuqan Stage 3 draft).
