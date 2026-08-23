---
Title: Praderas migration (2) — Cloud in a new home
Description: We close the heavy stretch of the move: Nextcloud now lives on the new Time4VPS box, with real off-box backups and Talk ready to scale. What it feels like to do this with an agent—without turning the post into a sysadmin manual.
Date: 2026-08-23 08:00PM
Template: post
Author: Luis Amigo
Tags: Systems, Productivity, Artificial Intelligence, Nextcloud
Lang: en
Translation_Key: migracion-praderas-02-cloud-new-home
Series: Praderas migration
Series_Slug: migracion-praderas
Series_Order: 2
Image: /assets/images/migracion-praderas-02-cloud-new-home-hero.webp
---

In the [first chapter](/blog/en/migracion-praderas-01-hermes-for-what-happens-once) we explained why we didn’t stand up Ansible for a move that only happens every few years. This second one closes the stretch that mattered most day to day: **files and conversation in a new home**.

You don’t need a port list or a version matrix to see what changed. You need the feeling that **the service we use every day**—files, calendar, notes, calls—no longer hangs off the old box, and that the move was done with an agent as a tool, not as theater.

### Visible first, delicate later

We started with the blog and static sites. It sounds easy, and it partly is: it proves the new [Time4VPS](https://billing.time4vps.com/?affid=8565) VPS, certificates, and the publish path—without yet touching the heart of the data. When that gets boring in the good sense, it’s time for **Nextcloud**.

The criterion here wasn’t a single midnight big bang. First we made the house ready on the new server—containers, a modern database, proxy—**without** pouring the gigabytes of files yet. Only when that house answered did we put the old one in maintenance, copy the content, and open again on the new address. Mail stays, for now, on the familiar mailbox: another piece, another pace.

That order comes from a conversation with the agent more than from a playbook: *what can break today, and what must not?* Hermes doesn’t replace judgment; it **speeds the grunt work** and holds the thread when the session runs long.

### What you don’t see and still matters

A cloud move isn’t done when login turns green again. You have to restore apps that only lived on the old machine, align secrets you can’t invent twice, and accept that a major version jump happens **after** you have a copy off the server.

That’s the unglamorous, useful part: **encrypted backups to external storage**, on a weekly rhythm that doesn’t blow the bill. It isn’t marketing; it’s being able to sleep if an upgrade goes wrong. With that net, we moved Nextcloud up a generation and left Talk calls ready with a high-performance backend—the difference between “fine for two people” and “fine for a real meeting.”

None of this is magic. It’s **many micro-decisions**—now or later? noise or fire?—made in dialogue with an agent that can enter the server, check, undo, and continue without you reopening twenty docs tabs.

### Why the agent still wins for what “happens once”

If you measured this move in calendar hours, a disciplined human finishes it too. The difference is elsewhere:

- The map **changes mid-flight** (an integrity warning, an app that won’t boot, an admin check that hangs). A rigid playbook becomes debt; an agent **reroutes**.
- The value isn’t repeating the same deploy a hundred times. It’s **not leaving the move half-done** because the weekend ate the willpower.
- The old server can stay a while as a safety net. You only enjoy that if the new house is already the default place.

Ansible and friends still make sense for fleets and environment factories. For a digital home on a [Time4VPS](https://billing.time4vps.com/?affid=8565) VPS, the agent matches how often this work actually happens.

### What’s left—and what we don’t promise

Mail still sits on the long path, plus smaller polish, and over time an ops Hermes on the machine itself. We don’t promise zero admin warnings; we promise **usable service**, with a way back, without turning privacy into someone else’s product.

### What you won’t find here

No firewall recipes, no secret dumps, no exact container inventory. Anyone who needs to reproduce an enterprise stack has other texts. This closes a human story: **we moved the cloud to a new home with an agent as the tool**, on a provider we chose on purpose, and we still own the data.

If chapter one was the *why*, this is the *heavy part is done*. The rest is continuity—not a second automation project to justify the first.
