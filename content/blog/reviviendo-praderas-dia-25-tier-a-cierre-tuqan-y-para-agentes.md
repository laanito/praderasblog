---
Title: Reviviendo Praderas (Día 25) — cierre Tier A (Días 10–16), Tuqan PR #44 y /for-ai-agents
Description: Sprint de siete portadas WebP para completar la cola Tier A, página pública para agentes JSON (Fase 6 v1.2), y artículo Tuqan sobre migración PHP 8 y Docker-only.
Date: 2026-05-25 14:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 25
Lang: es
Translation_Key: praderas-day-25-tier-a-complete-tuqan-for-ai-agents-log
Image: /assets/images/day25-comfyui-sdxl-tier-a-complete-for-ai-agents-hero.webp

---

# Reviviendo Praderas (Día 25) — Tier A cerrado y dos frentes en paralelo

Hoy empujamos **más rápido** lo acordado: terminar la cola **Tier A** (Días 10–16), abrir **Fase 6 v1.2** con una página pública para herramientas, y publicar el hito **Tuqan** alineado con [PR #44](https://github.com/laanito/tuqan/pull/44) del repo de la aplicación.

## Reloj de pared (orden de magnitud)

- **Retrofit Días 10–16 (7 pares, Comfy + WebP):** ~**3.5–4 h** (lote en `scripts/comfyui/batch_tier_a_10_16.sh`).
- **Fase 6 v1.2 (`/for-ai-agents` ES/EN):** ~**30 min**.
- **Artículo Tuqan ES/EN:** ~**25 min**.
- **Esta bitácora + portadas Day 25 / Tuqan:** misma sesión.

---

## Bloque A — Tier A: filas 10–16 → `done`

| Orden | `Translation_Key` | WebP (semilla) | ~KiB |
|-------|-------------------|----------------|------|
| 10 | `praderas-day-10-batch-2-multilingual-hubs` | `day10-…-batch2-multilingual-hubs-hero.webp` (`10052026`) | ~102 |
| 11 | `praderas-day-11-batch-3-security-ui` | `day11-…-batch3-security-privacy-ui-hero.webp` (`11052026`) | ~114 |
| 12 | `praderas-day-12-batch-4-archive-blog-log` | `day12-…-batch4-ai-archive-blog-hero.webp` (`12052026`) | ~111 |
| 13 | `praderas-day-13-batch-5-productivity-log` | `day13-…-batch5-productivity-tools-hero.webp` (`13052026`) | ~60 |
| 14 | `praderas-day-14-batch-6-7-8-translation-finale-log` | `day14-…-batch678-translation-finale-hero.webp` (`14052026`) | ~106 |
| 15 | `praderas-day-15-ui-search-footer-log` | `day15-…-day15-search-footer-ui-hero.webp` (`15052026`) | ~56 |
| 16 | `praderas-day-16-sitemap-robots-lang-log` | `day16-…-day16-sitemap-robots-lang-hero.webp` (`16052026`) | ~69 |

**Decisión de ritmo:** en lugar de ~2 pares/día, hoy cerramos **siete** para dejar la serie *Reviviendo Praderas* (Días 1–16) sin Picsum en héroes. El script por lotes reduce fricción; la auditoría `frontmatter_audit.py` sigue siendo obligatoria antes del merge.

**Estado:** `.agents/retrofit-cover-queue.md` — Tier A **completo**. Siguiente retrofits: tiers B+ (series largas, archivo) según `comfyui-cover-images.md`.

---

## Bloque B — Fase 6 v1.2: `/for-ai-agents`

Página pública (ES `/for-ai-agents`, EN `/en/for-ai-agents`, `Translation_Key: praderas-for-ai-agents`) que resume:

- Endpoints JSON **1.1** (`/blog.json`, búsqueda, campos RAG).
- Reglas de idioma y etiquetas canónicas.
- Enlaces al contrato en `.agents/blog-json-api.md`.

**Por qué HTML y no solo `.agents/` en git:** las herramientas en producción no leen el repositorio; necesitan una URL estable en `blog.praderas.org`.

---

## Bloque C — Tuqan (paralelo)

Nuevo capítulo de la serie **Tuqan — Modernización** (`Series_Order: 2`): plan PHP 8, **solo Docker**, PHPUnit en contenedor y roadmap de ocho etapas tras [PR #44](https://github.com/laanito/tuqan/pull/44). El post enlaza el repo; los checklists viven en Tuqan, no aquí.

---

## Próximo foco

- **Tuqan Etapa 1** — fundación Docker en el repo de la aplicación.
- **Retrofit Tier B** — openers de series y cola larga (~2 pares/día o lotes).
- **Phase 6 extras** — filtros por tag en JSON, pre-generación estática (backlog).
