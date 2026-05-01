---
Title: Home
Description: Blog Praderas — technology with rigor and calm. A flat-file Pico site with a transparent story: how the archive and the 2026 rebuild are produced, including English posts alongside Spanish.
Template: index
Lang: en
Translation_Key: praderas-home
---

# Welcome to Blog Praderas

**Praderas** is a place to **explore technology** with rigor and without pointless noise: crypto and security, productivity, AI, development, systems. We aim to explain what matters—with context.

## What this site is in 2026

The site runs on **[Pico CMS](https://picocms.org/)**: Markdown, light Twig templates, no database. We are doing a **deliberate rebuild**: navigation, categories, search, breadcrumbs, related posts, SEO, series—and **Spanish + English** as a first-class layout (`Translation_Key` pairs, language switcher, `hreflang`).

## How this archive is produced (three moments—and today)

We state this plainly so you know **what you are reading**:

- **Around 2020:** Many articles are **recoveries** from an older blog. That source material is **human-written**.
- **2023–2024:** The stack and information architecture were **human-driven**; the **published prose** from that period was **generated with AI** (how much a human checked each piece varied).
- **From 2026 (this rebuild):** **Planning, coding, review, and audit** run as **AI-led workflows**. A person still provides **direction and intent**—what problem we solve, what “done” means in product terms (think **CEO / mind**, not hands on the keyboard). There is **no** traditional expectation of a human doing line-by-line implementation or the same kind of editorial pass a classic team would apply.

That is a strong claim; we make it **on purpose**. The *Reviving Praderas* series documents the technical choices; the **human role here is strategic**, not artisanal, for this phase of the project.

## How to get around

- **Blog** — Spanish index at [`/blog`](/blog) (paginated). English posts are listed at [`/en/blog`](/en/blog).
- **Series** — English hub at [`/en/series`](/en/series); Spanish hub stays at [`/series`](/series). Detail URLs use `/en/series/<slug>/` or `/series/<slug>/` to match the page language.
- **Categories** — English topic map at [`/en/categorias`](/en/categorias) (counts for English posts only). Spanish categories remain at [`/categorias`](/categorias). Tag **labels** are still shared across languages; `/tags` is unchanged for now.
- **Search** — Sidebar widget; stopwords depend on page language.
- **About** — Notes on Pico and approach (still primarily Spanish; a dedicated EN About page is optional follow-up).

## What you can expect

- **Technical articles and guides** written with care at the sentence level—while being honest that the **process** behind them is the one described above.
- **Content that ages**; when something is wrong or outdated, we say so and iterate.
- **Clarity** about tooling: agents, repos, and limits—no magical “AI wrote nothing” disclaimers that contradict the homepage.

## Language switch

When a page has a matching translation (same **`Translation_Key`** in the front matter), the header offers the other language. Paired pages emit **`hreflang`** alternates and consistent **`og:locale`** metadata.

## Closing

No miracle newsletters—just a maintained blog that tries to stay **useful** and **readable**. Open the [blog in Spanish](/blog), the [English blog index](/en/blog), browse [categories in English](/en/categorias) or [series in English](/en/series), or use search: a few clicks, plenty to read.
