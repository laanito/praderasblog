---
Title: Praderas migration (3) — Talking to Hermes in Talk
Description: How to get a real Hermes answering in Nextcloud Talk: not a containerized CLI wrapper, but the usual agent, with Docker only to sandbox what it runs.
Date: 2026-08-23 11:00PM
Template: post
Author: Luis Amigo
Tags: Sistemas, Productividad, Inteligencia Artificial
Lang: en
Translation_Key: migracion-praderas-03-hermes-talk
Series: Migración Praderas
Series_Slug: migracion-praderas
Series_Order: 3
Image: /assets/images/migracion-praderas-03-hermes-talk-hero.webp
---

In the [previous chapter](/blog/en/migracion-praderas-02-cloud-in-a-new-home) the cloud was already on the new VPS. The next itch was obvious: **talk to an agent in the same place we already talk to each other**. Not another Telegram. Not another tab. Nextcloud Talk.

That sounds like a half-hour integration. It isn’t. The failure is not in the chat. It is in **which program you think you are calling** when you type `hermes`.

### What we wanted

There is a main Hermes — the usual one, on the laptop — and there needs to be another that stays **on**. The one at home holds the long context. The one on the server answers at three in the morning, checks a service, drops a file, comes back to Talk. Same family name, different job.

Nextcloud already knows bots: a shared secret, a webhook, a signed message back into the room. The bridge that ties that to Hermes Agent exists and is open source. The public recipe we found — a post on the Nextcloud forum — gets the drawing right: **Talk calls the bridge; the bridge calls a real Hermes; Hermes replies**.

We, eager to “keep everything in Docker”, put the agent *inside* the same container as the bridge. The compose file looked tidy. The conversation did not.

### The detour: the wrapper is not the agent

The pip package that shares the CLI’s name installs **a slice**. It starts. It has a version. It accepts `chat -q`. It is not the usual install: it lacks the skills tree, the home (`~/.hermes`) the agent expects, and the same judgment about what to print when it reasons.

The symptom is almost funny if it is not happening to you. The webhook returns 202. The bot is in the room. You type “hola” and you get the **inner monologue**: *explanation* slugs, the whole turn, sometimes a generic bridge error. Talk is not broken. The bridge is doing its job: **it forwards whatever the CLI dumped on stdout**.

Patching the bridge to use another flag and strip tags is tempting. It is also admitting you are bargaining with a stand-in. We stopped. The real fix was **taking Hermes out of the container**.

### The shape that works

Docker is not the waste. The waste is **the wrong things inside Docker**.

- The **official CLI** is installed on the server the same way as on any machine: the installer, the binary on the user’s `PATH`, a separate **profile** so it does not mix with the laptop Hermes.
- The **bridge** is a small host process (venv + user service). It calls that binary, not a `hermes` invented in an image.
- **Docker** stays as the terminal backend: what the agent *runs* goes into a container. That shrinks the blast radius. It does not shrink the agent into a pip library.
- Talk remains a **bot**, not a user with a password. The bot secret is nobody’s login.

That is closer to the community tutorial — without a private overlay network — and more honest about what we asked for: an automation Hermes with **reduced scope**, not a truncated Hermes.

The App Store ExApp is the same bridge in another wrapper. It wants AppAPI and a daemon that talks to the Docker socket. We did not need that door open for this stretch. Standalone is enough.

### How it feels when it clicks

You leave a message in a room. The bot does not dump a log. It answers as if you were in the terminal, except the thread stays in Talk, one click from the rest of the house (files, notices). Laptop Hermes is still the one you already know. This one does not pretend to replace it.

There is still sanding to do: fewer tools than at home, a drier profile, notices that should never reach the chat. That is later work. Today closes on **having found the right piece**.

### Reproduction (anonymized)

No real paths, no secrets. If you copy this, change names and mint your own keys.

1. Install Hermes Agent **on the host** (the official installer, not `pip install hermes-agent` as the only runtime).
2. Create a **profile** just for Talk. Your model and provider. Set `terminal.backend` to `docker` if you want the shell isolated. Set `display.show_reasoning` to false: Talk is not a TUI.
3. Clone [nextcloud-talk-hermes-bridge](https://github.com/robertlmann02/nextcloud-talk-hermes-bridge), make a venv, `pip install -e .`. Do not patch the bridge to “fix” stdout: if the CLI is the real one, `hermes chat -q` is enough.
4. In the bridge `.env`: public Nextcloud URL, bot secret, `HERMES_BIN` pointing at the host binary, `HERMES_PROFILE` at the automation profile, `HERMES_HOME_DIR` at that user’s home. Empty skills until they exist in that home. YOLO off.
5. A systemd user unit plus linger, so it survives logout. Health at `http://127.0.0.1:8788/health`.
6. If Nextcloud sits behind a proxy, a `location` that forwards `/hook` to that port. Install the bot with `occ talk:bot:install` (webhook + response) and add it to **one** room with `talk:bot:setup`.
7. Generate the bot secret once and share it only between Nextcloud and the bridge. Do not use a human password.

There is a write-up on the [Nextcloud forum](https://help.nextcloud.com/t/connecting-hermes-agent-to-nextcloud-talk-using-nextcloud-talk-hermes-bridge/246436) that draws this same flow. The sentence we were missing — and paid for in an afternoon — is this: **the bridge expects a real Hermes, not a packaged namesake**.

Mail still lives on the old box. The new [Time4VPS](https://billing.time4vps.com/?affid=8565) VPS already has cloud, calls, and an agent you can talk to. Tomorrow we sand. Today is enough.
