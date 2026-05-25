---
Title: "Reviving Praderas (Day 13) — batch 5 (productivity & collaboration) and wall-clock notes"
Description: Six ES→EN productivity guides (remote work, Etherpad, Redmine, Taskwarrior, Focalboard, Nextcloud+Deck), small Spanish fixes, and wall-clock vs no-AI order-of-magnitude.
Date: 2026-05-04 14:40PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviving Praderas
Series_Slug: reviviendo-praderas
Series_Order: 13
Lang: en
Translation_Key: praderas-day-13-batch-5-productivity-log
Image: /assets/images/day13-comfyui-sdxl-batch5-productivity-tools-hero.webp

---

# Reviving Praderas (Day 13) — what shipped and why

This note closes **batch 5** in the migration plan—the **productivity and collaboration tooling** cluster. Six long-form guides now have **paired EN** pages aligned with the Spanish originals on remote work, Etherpad, Redmine, Taskwarrior (+ Taskserver sketch), Focalboard, and Nextcloud with Deck.

## Wall clock (this PR)

- **Session start (reference):** `2026-05-04 14:28:19 CEST`
- **Immediately before commit + push (reference):** `2026-05-04 14:39:58 CEST`

That is on the order of **~12 minutes** of human calendar time (steering, translations, front matter, `.agents` tracker edits, branch + push). Do not confuse with “model runtime”—**your** elapsed time is the honest productivity metric.

## Executive summary

1. **Six ES↔EN pairs** — `praderas-b5-remote-work-tips`, `…-etherpad-guide`, `…-redmine-guide`, `…-taskwarrior-guide`, `…-focalboard-guide`, `…-nextcloud-deck`.
2. **Small Spanish hygiene** — section title **Distractions** spelling fix in the remote-work article; **completed the truncated closing sentence** in the Focalboard Spanish Markdown.
3. **`.agents`** — tracker marks this batch-5 slice **done**, new backlog rows, changelog, light vocabulary where helpful.

## Rationale for the batch boundary

- **Thematic coherence** — remote habits and self-hosted collaboration tools share vocabulary (“boards”, “sync”, “TLS”) and reader intent; mixing with unrelated clusters would blur the glossary.
- **PR size** — six command-heavy guides are already a dense review unit; remaining productivity posts (Emacs, other guides) can ship as **5b** if we need smaller diffs.

## No-AI counterfactual (order-of-magnitude)

| Workstream | Indicative senior technical-writing band |
|------------|------------------------------------------|
| Six guide-length articles + terminology pass | **12–28 h** |
| Front matter + `Translation_Key` hygiene | **1.5–3 h** |
| Tracker / changelog / branch / PR | **1–2 h** |
| **Total** | **~14.5–33 h** spread across days |

The assisted workflow here compresses the **calendar** cost dramatically while keeping **human** accountability for merge and publication judgment—that division is the point of publishing these numbers.
