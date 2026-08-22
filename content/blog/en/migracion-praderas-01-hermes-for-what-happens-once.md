---
Title: Praderas migration (1) — Hermes for what happens once
Description: We started moving to a new Time4VPS box. Why an agent like Hermes fits better than standing up Ansible when the job is rare—and what we deliberately leave for the next chapter.
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

Some infrastructure jobs do not repeat often enough to deserve a “by the book” automation project. Moving a personal server—blog, mail, file cloud, a couple of static sites—is one of them: it happens every few years, the map changes every time, and the cost of a flawless inventory usually outweighs what you save later.

This is **chapter one** of a short series on migrating Praderas hosting. It is not a command tutorial or a service catalogue. It is the story of **why we are using an agent (Hermes) as the main tool**, instead of investing in Ansible or another infrastructure-as-code layer built for fleets and for shipping the same deploy again and again.

### The problem was not “not knowing how”

The old server did its job. It worked. It was also years of stacked decisions: an OS falling behind, hard ceilings for comfortable containers, and the feeling that every serious improvement started with “first we would have to…” and never left the whiteboard.

The move does not come from an outage or a scare. It comes from wanting a place where what comes next—real containers for the file cloud, an ops agent on the box itself, less friction as things grow—does not collide with today’s machine. You can sketch that on a page and do it by hand. You can also turn it into an **Ansible project** with roles, secrets, and playbooks… for something that, if it goes well, you will not do the same way for a long time.

### Ansible shines when you repeat; here judgment is the scarce resource

There is nothing wrong with Ansible. On a team with dozens of similar hosts, or a product you ship every week, the investment pays for itself: tests, idempotency, newcomers inheriting a clear procedure.

On a **home server** the arithmetic is different:

- The service map is **not still**: part of the work is deciding what stays, what retires, and what gets renamed—for example a new mail hostname while the old one still receives.
- The path is **not straight**: sometimes you move the web first and leave mail on the old machine for months; sometimes you cannot keep the address and you live with two boxes at once.
- What matters is **context**—privacy, a trusted provider, “what do I want in a year?”—not only the package list.

A playbook pushes you to **freeze** those decisions too early. An agent lets you **explore, propose, correct, and leave a trail**, while human judgment stays on the forks that actually matter: where to host, whether mail stays self-hosted, what moves first.

### Hermes as a tool, not as magic

In practice Hermes has been a very patient ops assistant: see what actually runs (not what you thought ran), draft a shared plan, prepare the new machine, move the blog and static sites, and park mail and the file cloud until their turn. You stay in charge—DNS, provider, “yes / no / wait”. The agent absorbs the repeatable grind and the first draft of decisions.

That does **not** replace judgment. It replaces the temptation to stand up a mini platform project for a once-in-a-while event. Learning to work with the agent on *this* server pays off quickly; a perfect Ansible inventory for *one* VPS almost never does.

### Where we host: Time4VPS

For the new box we chose **[Time4VPS](https://billing.time4vps.com/?affid=8565)**: a European provider we already knew, solid price for VPS RAM and disk for what we need, and room to keep mail on the old machine as long as necessary while the new one earns trust. This is not an ad wearing a blog post: it is the concrete *where* the blog and static sites already run, in case that path helps someone else.

### What already moved (without the manual)

In this first stretch the “light” web already lives on the new machine: the Pico blog and a couple of static sites, with HTTPS and the habit of publishing via `git pull` in the right directory. Everything else—mail, file cloud—stays on the old box on purpose. Living with two machines is not a failure; it is a cushion.

### What you will not find here

No port lists, config dumps, or hardening recipes. That stays out on purpose: the series is about **process and judgment**, not handing out an attack map or inviting anyone to clone a setup they do not understand.

### Next chapter

When we move the **file cloud (Nextcloud)**—the heavy data piece and the one that benefits most from a host that can really run Docker—we will write part two: what moving it implies, what we keep in parallel, and what Hermes taught us on a job that hurts if you get it wrong.

Until then, the blog already breathes in a new home. The rest of the move, at its own pace.
