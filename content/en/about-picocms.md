---
Title: About Pico CMS
Description: What Pico CMS is and why a flat-file, Markdown-first stack fits this blog.
Author: Luis Amigo
Robots: nofollow
Template: post
Tags: Desarrollo Web
Lang: en
Translation_Key: praderas-nav-about-picocms
---

# Introduction to Pico CMS

[Pico CMS](https://picocms.org/) is an open-source, lightweight content management system designed for simple, fast static sites. With Pico you can ship pages without a database or a heavy application server.

## Main features

1. **Lightweight:** Small footprint—well suited to shared hosting or modest servers.

2. **Easy to use:** Simplicity is a core goal; Markdown-first authoring keeps day-to-day work predictable.

3. **Markdown:** Pico uses [Markdown](https://www.markdownguide.org/) for content. You write in plain text and the site renders HTML.

4. **Customizable:** Themes and Twig templates let you adjust layout and styling without fighting the core.

5. **Extensible:** Plugins add contact forms, integrations, and other behavior as needed.

6. **No database:** Fewer moving parts in setup and backups.

7. **SEO-friendly:** Clean URLs and meta tags support sensible defaults for search engines.

## Getting started (high level)

1. **Server:** A web server with PHP support.
2. **Download:** Get Pico from the [official site](https://picocms.org/download/).
3. **Install:** Unpack and upload to your host.
4. **Configure:** Edit `config/config.yml` for your site.
5. **Author:** Add Markdown under `content/`.
6. **Theme:** Adjust or replace files under `themes/`.
7. **Publish:** Deploy the generated static output or serve PHP directly, depending on your hosting model.

Pico is a strong fit for personal blogs and small sites where you want to focus on writing instead of infrastructure. Its minimal surface area matches how this project is maintained today.
