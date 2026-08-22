---
Title: Praderas migration (1) — Hermes for what happens once
Description: We started moving Praderas hosting to a new Time4VPS box. Why an agent like Hermes fits better than investing in Ansible when the job is rare—and what we deliberately leave for the next chapter.
Date: 2026-08-22 09:00PM
Template: post
Author: Luis Amigo
Tags: Sistemas, Productividad, Inteligencia Artificial
Lang: en
Translation_Key: migracion-praderas-01-hermes-once
Series: Migración Praderas
Series_Slug: migracion-praderas
Series_Order: 1
Image: /assets/images/migracion-praderas-01-hermes-once-hero.webp
---

Some infrastructure jobs do not repeat often enough to deserve a “real” automation project. Moving a personal server—blog, mail, file cloud, a couple of static sites—is one of those: it happens every few years, the map changes every time, and the cost of a perfect inventory usually beats the payoff.

This is **chapter one** of a short series on migrating Praderas hosting. It is not a command tutorial or a service inventory. It is the story of **why we are using an agent (Hermes) as the main tool** instead of investing in Ansible or another infrastructure-as-code layer built for fleets and repetition.

### The problem was not “not knowing how”

The old server worked. It also carried years of layered decisions: an OS falling behind, limits for modern containers, and the feeling that every serious improvement started with “first we would have to…” and never started.

The migration does not come from a dramatic outage. It comes from wanting a place where the future—containers for the file cloud, an ops agent on the box itself, less friction as things grow—does not collide with today’s machine. You can plan that on a page and do it by hand. You can also turn it into an **Ansible project** with roles, vaults, and playbooks… for something that, if it goes well, you will not do the same way for a long time.

### Ansible shines when you repeat; here the value is judgment

There is nothing wrong with Ansible. On a team with dozens of identical hosts, or a product you ship every week, the investment pays for itself: tests, idempotency, onboarding new people.

On a **personal box** the arithmetic is different:

- The service map is **not stable**: part of the work is deciding what stays, what retires, and what gets renamed (for example a new mail hostname while the old one still receives).
- The path is **not linear**: sometimes you move the web first and leave mail on the old machine for months; sometimes you cannot keep the IP and you live with two boxes.
- The knowledge that matters is **contextual**—privacy, a trusted hoster, “what do I want in a year?”—not only the package list.

A playbook pushes you to **freeze** those decisions too early. An agent lets you **explore, propose, correct, and leave a trail** while human judgment stays on the forks that actually matter: where to host, whether mail stays self-hosted, what moves first.

### Hermes as a tool, not as magic

In practice Hermes has done the work of a very patient ops assistant: inventory what actually runs (not what you thought ran), draft a shared plan, prepare the new machine, move the blog and static sites, and leave mail and the file cloud for when they are due. You stay in charge—DNS, provider, “yes / no / wait”—while the agent absorbs the repeatable grind and the first draft of decisions.

That does **not** replace judgment. It replaces the temptation to stand up a mini platform project for a once-in-a-while event. The ROI of learning to talk to the agent about *this* server is high; the ROI of a perfect Ansible inventory for *one* VPS is low.

### Where we host: Time4VPS

For the new box we chose **[Time4VPS](https://billing.time4vps.com/?affid=8565)**: a European provider we already knew, solid price for RAM and disk, and room to keep mail on the old machine as long as we need while the new one earns trust. This is not an ad wearing a blog post: it is the concrete *where* the blog and static sites already run, in case that path helps someone else.

### What already moved (without the manual)

In this first stretch the “light” web already lives on the new machine: the Pico blog and a couple of static sites, with HTTPS and the habit of publishing via `git pull` in the right directory. Everything else—mail, file cloud—stays on the old box on purpose. Dual-box is not a failure; it is a cushion.

### What this article is not

You will not find port lists, config dumps, or hardening recipes here. That stays out on purpose: the series is about **process and judgment**, not opening an attack map or cloning someone else’s setup without understanding it.

### Next chapter

When we move the **file cloud (Nextcloud)**—the heavy data piece and the one that benefits most from a host that can really run Docker—we will write part two: what moving it implies, what we keep in parallel, and what Hermes taught us on a job that hurts if you get it wrong.

Until then, the blog already breathes in a new home. The rest of the move, at its own pace.
