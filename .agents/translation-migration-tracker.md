# Translation & content migration tracker (ES → EN)

**Purpose:** Plan and record the move from Spanish-only posts to paired EN content (`content/blog/en/`, same **`Translation_Key`** as the Spanish file). This file is the **working ledger** for agents and humans: status, vocabulary, and language rules—not the public site.

**Related:** `.agents/README.md` (hub index), `phase-5-6-plan.md` (Phase 5 goals), `post-template.md` (`Lang`, `Translation_Key`), `translation-batches.md` (batching rules, context explainer, honest time reporting), homepage pair `content/index.md` ↔ `content/en/index.md`. **Optional heroes on older pairs:** when retrofitting **`Image:`** on translated posts, follow the same **paired-path** rule as new work — see **`comfyui-cover-images.md`** § *Retrofit plan* and tick progress in **`retrofit-cover-queue.md`**.

---

## Editorial eras (reference for translators & agents)

These facts are also stated on the **homepages** (`index.md` / `en/index.md`). Keep wording aligned when you touch either language.

| Period | Archive / site | Human vs IA (high level) |
|--------|----------------|---------------------------|
| **~2020** | Articles recovered from an older blog | **100% human** authorship in the source material we kept. |
| **2023–2024** | Site built with human intent | **Human-built** stack and structure; **published body text** in that phase was **IA-generated** (human oversight varied by post). |
| **2026** | Current rebuild (*Reviviendo Praderas*, Phase 5, etc.) | **IA-driven workflows** for planning, implementation, review, and audit; a person sets **direction / “what to solve”** (CEO/mind), **without** routine hands-on coding or the kind of line-by-line editorial review a traditional team would do. |

When in doubt, prefer **honest, dated** statements over vague “AI-assisted” language if the homepage has already committed to a stronger formulation.

---

## Language & URL rules (short)

- **Spanish posts:** `content/blog/<slug>.md` → `/blog/<slug>` (canonical bulk of the archive).
- **English posts:** `content/blog/en/<slug>.md` → `/blog/en/<slug>`.
- **Paired pages:** identical **`Translation_Key`** in front matter; optional **`Lang`**: `es` / `en`.
- **UI copy in EN only:** `content/en/*.md` (e.g. `/en`, `/en/blog`, `/en/series`, `/en/categorias`).
- **Tag taxonomy:** Canonical **`Tags` values stay Spanish** in Markdown (audit + URLs). **English UI labels** and **category blurbs** load from **`scripts/tag_vocabulary.json`** via `plugins/65-Multilingual.php` (`tag_label_en`, `tag_blurb_es`, `tag_blurb_en` in Twig); see **Vocabulary** and `multilingual-ui-backlog.md`.

---

## Translation backlog (posts)

**Legend:** `done` · `draft` · `todo` · `n/a` (no EN planned)

| Translation_Key | ES path (content/blog/) | EN path (content/blog/en/) | Status | Notes |
|-------------------|-------------------------|----------------------------|--------|--------|
| `praderas-home` | _(N/A — use `content/index.md`)_ | _(N/A — use `content/en/index.md`)_ | done | Home pair; editorial-era text must stay in sync. |
| `praderas-day-1-technical-audit` | `reviviendo-praderas-dia-1-auditoria-tecnica-y-plan-con-agentes-ia.md` | `reviving-praderas-day-1-technical-audit-and-ai-agent-improvement-plan.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-2-phase-1-listing-search-pagination` | `reviviendo-praderas-dia-2-fase-1-plantilla-listado-busqueda-y-paginacion.md` | `reviving-praderas-day-2-phase-1-listing-search-and-pagination.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-3-phase-2-navigation-categories-breadcrumbs-related` | `reviviendo-praderas-dia-3-fase-2-navegacion-categorias-crumbs-y-posts-relacionados.md` | `reviving-praderas-day-3-phase-2-navigation-categories-breadcrumbs-and-related-posts.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint` | `reviviendo-praderas-dia-4-fase-3-metadatos-taxonomia-y-lint-de-front-matter.md` | `reviving-praderas-day-4-phase-3-metadata-taxonomy-and-frontmatter-lint.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-5-visual-polish` | `reviviendo-praderas-dia-5-pulido-visual-y-lectura.md` | `reviving-praderas-day-5-visual-polish-readability-and-brand-tone.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-6-series-and-collections` | `reviviendo-praderas-dia-6-series-y-colecciones.md` | `reviving-praderas-day-6-series-and-collections.md` | done | Batch 1: Reviving Praderas. |
| `praderas-day-7-phase-4-seo-discoverability` | `reviviendo-praderas-dia-7-fase-4-seo-y-descubrimiento.md` | `reviving-praderas-day-7-phase-4-seo-social-metadata-and-date-archive.md` | done | Batch 1: Reviving Praderas. |
| `praderas-phase-5-multilingual` | `reviviendo-praderas-dia-8-fase-5-multilingue-modelo-y-metadatos.md` | `reviving-praderas-day-8-phase-5-multilingual-content-model.md` | done | Phase 5 announcement pair. |
| `praderas-day-9-translation-migration-batch-1` | `reviviendo-praderas-dia-9-migracion-de-traducciones-batch-1-y-plan.md` | `reviving-praderas-day-9-translation-migration-batch-1-and-plan.md` | done | Batch 1 closure note + time estimates. |
| `praderas-day-10-batch-2-multilingual-hubs` | `reviviendo-praderas-dia-10-batch-2-traduccion-y-arreglos-multilingues.md` | `reviving-praderas-day-10-batch-2-translation-and-multilingual-fixes.md` | done | Daily log: batch 2 + EN hubs + ~35 min wall clock vs localization band. |
| `praderas-nav-series` | _(use `content/series.md`)_ | _(use `content/en/series.md`)_ | done | EN nav + `hreflang`; series detail also `/en/series/<slug>/`. |
| `praderas-nav-categories` | _(use `content/categorias.md`)_ | _(use `content/en/categorias.md`)_ | done | EN categories hub; tag counts scoped to EN posts. |
| `praderas-ctd-01` | `desarrollo-de-arquitecturas-desacopladas-creando-una-aplicacion-de-control-de-horas.md` | `decoupled-architectures-time-tracking-app-overview.md` | done | Batch 2. |
| `praderas-ctd-02` | `implementacion-de-postgrest-creacion-de-una-potente-api-rest.md` | `postgrest-rest-api-setup.md` | done | Batch 2. |
| `praderas-ctd-03` | `mejora-de-seguridad-y-acceso-con-nginx-en-postgrest.md` | `nginx-postgrest-https-hardening.md` | done | Batch 2. |
| `praderas-ctd-04` | `autenticacion-y-uso-de-tokens-jwt-en-postgrest.md` | `postgrest-jwt-authentication.md` | done | Batch 2. |
| `praderas-ctd-05` | `introduccion-a-react-es5-es6-y-su-ejecucion.md` | `react-es5-es6-intro.md` | done | Batch 2. |
| `praderas-ctd-06` | `disenando-la-base-de-datos-para-tu-aplicacion-de-control-de-tiempo.md` | `database-schema-time-tracking-app.md` | done | Batch 2. |
| `praderas-ctd-07` | `configuracion-de-la-base-de-datos-para-tu-aplicacion-de-control-de-tiempo.md` | `database-roles-permissions-time-tracking.md` | done | Batch 2. |
| `praderas-ctd-08` | `creacion-de-tablas-restantes-en-la-base-de-datos-para-tu-aplicacion-de-control-de-tiempo.md` | `database-extra-tables-time-tracking.md` | done | Batch 2. |
| `praderas-ctd-09` | `interactuando-con-la-api-rest-de-tu-aplicacion-de-control-de-tiempo.md` | `rest-api-client-time-tracking-app.md` | done | Batch 2. |
| `praderas-ctd-10` | `creando-un-frontend-en-react-para-la-gestion-de-roles.md` | `react-frontend-role-management-setup.md` | done | Batch 2. |
| `praderas-ctd-11` | `desarrollo-de-una-aplicacion-de-gestion-de-roles-en-react.md` | `react-role-management-list-edit.md` | done | Batch 2. |
| `praderas-ctd-12` | `desarrollo-de-una-aplicacion-de-gestion-de-proyectos-en-react.md` | `react-project-management-crud.md` | done | Batch 2. |
| `praderas-ctd-13` | `creacion-de-usuarios-en-tu-aplicacion-de-control-de-tiempo-con-react.md` | `react-create-user-time-tracking.md` | done | Batch 2. |
| `praderas-b3-cs-intro` | `introduccion-a-la-ciberseguridad.md` | `intro-cybersecurity-online-safety-tips.md` | done | Batch 3: security / privacy cluster. |
| `praderas-b3-cs-digital-world` | `ciberseguridad-en-el-mundo-digital-protegiendo-tu-informacion-en-un-entorno-conectado.md` | `cybersecurity-in-the-digital-world.md` | done | Batch 3. |
| `praderas-b3-cs-advanced` | `ciberseguridad-avanzada-protegiendo-datos.md` | `advanced-cybersecurity-protecting-data.md` | done | Batch 3. |
| `praderas-b3-internet-not-safe-ii` | `internet-no-es-un-lugar-seguro-ii-privacidad.md` | `internet-not-safe-part-ii-privacy.md` | done | Batch 3. |
| `praderas-b3-social-privacy` | `como-mantener-tu-privacidad-en-las-redes-sociales-consejos-y-buenas-practicas.md` | `social-network-privacy-best-practices.md` | done | Batch 3. |
| `praderas-b3-geolocation` | `geolocalizacion-en-internet.md` | `geolocation-on-the-internet.md` | done | Batch 3 (privacy / IP). |
| `praderas-day-11-batch-3-security-ui` | `reviviendo-praderas-dia-11-batch-3-ciberseguridad-privacidad-y-capa-ui.md` | `reviving-praderas-day-11-batch-3-security-privacy-and-ui-layer.md` | done | Daily log: batch 3 + UI + ~12 min wall vs senior band. |
| `praderas-nav-tags` | _(use `content/tags.md`)_ | _(use `content/en/tags.md`)_ | done | EN tag hub + paired `hreflang`. |
| `praderas-nav-about-picocms` | _(use `content/acerca-de-picocms.md`)_ | _(use `content/en/about-picocms.md`)_ | done | EN About page; nav target. |
| `praderas-nav-blog-listing` | _(use `content/blog.md`)_ | _(use `content/en/blog.md`)_ | done | `/blog` ↔ `/en/blog` + language switcher via `hreflang` pair. |
| `praderas-nav-archive` | _(use `content/archivo.md`)_ | _(use `content/en/archivo.md`)_ | done | `/archivo` ↔ `/en/archivo`; bilingual `archive.twig`. |
| `praderas-nav-search` | _(use `content/search.md`)_ | _(use `content/en/search.md`)_ | done | `/search` ↔ `/en/search`; UI copy + redirect base now branch by `content_lang`; results filtered by active language in `PicoSearch`. |
| `praderas-b4-ai-games-evolution` | `evolucion-de-la-inteligencia-artificial-y-los-videojuegos.md` | `ai-evolution-and-video-games.md` | done | Batch 4: AI cluster. |
| `praderas-b4-ai-early-disease-detection` | `deteccion-temprana-de-enfermedades-ia.md` | `ai-early-disease-detection.md` | done | Batch 4. |
| `praderas-b4-ai-medicine` | `inteligencia-artificial-en-la-medicina.md` | `artificial-intelligence-in-medicine.md` | done | Batch 4. |
| `praderas-b4-ai-society-impact` | `inteligencia-artificial-y-su-impacto-en-la-sociedad-avances-desafios-y-reflexiones.md` | `ai-impact-on-society.md` | done | Batch 4. |
| `praderas-b4-ai-entertainment` | `impactoo-de-la-ia-en-entretenimiento.md` | `impact-of-ai-on-entertainment.md` | done | Batch 4 (ES slug keeps typo *impactoo*). |
| `praderas-b4-neural-nets` | `redes-neuronales-fundamentos-y-aplicaciones.md` | `neural-networks-fundamentals.md` | done | Batch 4. |
| `praderas-day-12-batch-4-archive-blog-log` | `reviviendo-praderas-dia-12-batch-4-ia-archivo-blog-y-bitacora.md` | `reviving-praderas-day-12-batch-4-ai-archive-blog-daily-log.md` | done | Daily log: batch 4 merge recap; follow-up PR ~20 min wall (11:56:12 → 12:16:46 CEST). |
| `praderas-b5-remote-work-tips` | `consejos-para-el-teletrabajo-eficiente-como-maximizar-tu-productividad-desde-casa.md` | `remote-work-productivity-tips.md` | done | Batch 5: productivity cluster. |
| `praderas-b5-etherpad-guide` | `guia-completa-de-etherpad-colaboracion-en-tiempo-real-y-edicion-en-grupo.md` | `etherpad-realtime-collaboration-guide.md` | done | Batch 5. |
| `praderas-b5-redmine-guide` | `guia-completa-de-redmine-gestion-de-proyectos-y-tareas-simplificada.md` | `redmine-project-task-management-guide.md` | done | Batch 5. |
| `praderas-b5-taskwarrior-guide` | `guia-completa-de-taskwarrior-gestion-de-tareas-eficiente.md` | `taskwarrior-task-management-guide.md` | done | Batch 5. |
| `praderas-b5-focalboard-guide` | `focalboard-tu-solucion-todo-en-uno-para-la-gestion-de-tareas-y-proyectos.md` | `focalboard-task-project-management.md` | done | Batch 5. |
| `praderas-b5-nextcloud-deck` | `nextcloud-con-deck-tu-solucion-de-nube-privada-y-groupware.md` | `nextcloud-deck-private-cloud-groupware.md` | done | Batch 5. |
| `praderas-day-13-batch-5-productivity-log` | `reviviendo-praderas-dia-13-batch-5-productividad-herramientas-y-bitacora.md` | `reviving-praderas-day-13-batch-5-productivity-tools-and-daily-log.md` | done | Daily log: batch 5 + ~12 min wall vs ~14.5–33 h band. |
| `praderas-b6-first-mobile-app` | `como-construir-tu-primera-aplicacion-movil-un-enfoque-practico-paso-a-paso.md` | `building-your-first-mobile-app-practical-step-by-step.md` | done | Batch 6: mobile fundamentals. |
| `praderas-b6-mobile-dev-beginners` | `desarrollo-de-aplicaciones-moviles-guia-paso-a-paso-para-principiantes.md` | `mobile-app-development-beginners-guide.md` | done | Batch 6. |
| `praderas-b6-mobile-ui-ux` | `diseno-de-interfaz-de-usuario-ui-y-experiencia-de-usuario-ux-en-aplicaciones-moviles-un-enfoque-practico.md` | `mobile-app-ui-ux-practical-approach.md` | done | Batch 6. |
| `praderas-b6-mobile-testing-strategy` | `estrategia-de-pruebas-en-desarrollo-de-aplicaciones-moviles-aproximaciones-frameworks-y-mejores-practicas.md` | `mobile-app-testing-strategy-frameworks-tdd.md` | done | Batch 6. |
| `praderas-b6-mobile-frameworks` | `explorando-los-distintos-frameworks-de-desarrollo-de-aplicaciones-moviles.md` | `mobile-development-frameworks-explained.md` | done | Batch 6. |
| `praderas-b6-mobile-languages-tools` | `lenguajes-y-herramientas-esenciales-para-el-desarrollo-de-aplicaciones-moviles.md` | `mobile-development-languages-and-essential-tools.md` | done | Batch 6. |
| `praderas-b7-celestia-tia` | `analizando-crypto-celestia-token-tia.md` | `celestia-tia-token-overview.md` | done | Batch 7: crypto / chain (not financial advice). |
| `praderas-b7-blockchain-crypto-intro` | `introduccion-a-blockchain-y-el-mundo-crypto.md` | `blockchain-and-crypto-introduction.md` | done | Batch 7. |
| `praderas-b7-bitcoin-node` | `crea-tu-propio-nodo-bitcoin.md` | `run-your-own-bitcoin-node.md` | done | Batch 7. |
| `praderas-b7-electrum-server` | `electrum-que-es-y-por-que-es-bueno-tener-tu-propio-servidor-de-electrum.md` | `electrum-wallet-and-personal-server.md` | done | Batch 7. |
| `praderas-b8-laptop-buying-guide` | `como-elegir-una-computadora-portatil-guia-para-tomar-la-decision-correcta.md` | `laptop-buying-guide.md` | done | Batch 8: systems / productivity / society tail. |
| `praderas-b8-ubuntu-vs-debian` | `comparando-ubuntu-y-debian.md` | `ubuntu-vs-debian-comparison.md` | done | Batch 8. |
| `praderas-b8-future-of-education` | `el-futuro-de-la-educacion.md` | `future-of-education-digital-era.md` | done | Batch 8. |
| `praderas-b8-emacs-guide` | `guia-completa-de-emacs-aumenta-tu-productividad-con-un-editor-poderoso.md` | `emacs-productivity-guide.md` | done | Batch 8 (Emacs guide tail from batch 5 note). |
| `praderas-b8-linux-vm-productivity-tools` | `herramientas-de-productividad-de-software-libre-para-linux-y-vm.md` | `libre-linux-vm-productivity-tools.md` | done | Batch 8; internal links to paired EN productivity posts. |
| `praderas-b8-remote-team-productivity-tools` | `herramientas-para-incrementar-la-productividad-en-el-trabajo-remoto.md` | `remote-team-productivity-tools.md` | done | Batch 8 (distinct from `praderas-b5-remote-work-tips`). |
| `praderas-b8-debian-11-install` | `instalacion-de-debian-11-paso-a-paso.md` | `debian-11-install-step-by-step.md` | done | Batch 8; linked from EN `praderas-ctd-01` overview. |
| `praderas-b8-online-learning-benefits` | `las-ventajas-del-aprendizaje-en-linea-flexibilidad-y-oportunidades.md` | `online-learning-benefits-flexibility.md` | done | Batch 8. |
| `praderas-b8-emerging-tech-trends-society` | `tendencias-economicas-emergentes.md` | `emerging-tech-trends-society-business.md` | done | Batch 8 (ES filename says “economicas”; body is tech trends). |
| `praderas-b8-future-tech-innovation-horizon` | `tendencias-tecnologicas-futuras-navegando-por-el-horizonte-de-la-innovacion.md` | `future-tech-trends-innovation-horizon.md` | done | Batch 8 (closes batch 4 “future tech” tail). |
| `praderas-day-14-batch-6-7-8-translation-finale-log` | `reviviendo-praderas-dia-14-batch-6-7-8-cierre-traducciones-y-reloj.md` | `reviving-praderas-day-14-batches-6-7-8-translation-finale-and-clock.md` | done | Daily log: batches 6–8 closure + ~9.5 min wall vs ~43.5–102.5 h band. |
| `praderas-day-16-sitemap-robots-lang-log` | `reviviendo-praderas-dia-16-sitemap-robots-por-idioma.md` | `reviving-praderas-day-16-sitemap-and-robots-per-language.md` | done | Day 16 log: sitemap index + `sitemap-es.xml` / `sitemap-en.xml` (`PicoRobots` + theme Twig). |
| `praderas-day-17-comfyui-cover-images-plan` | `reviviendo-praderas-dia-17-imagenes-portada-comfyui-plan.md` | `reviving-praderas-day-17-cover-images-comfyui-plan.md` | done | Day 17 log: ComfyUI cover pipeline planning + `.agents/comfyui-cover-images.md` + `scripts/comfyui/sdxl_ubersimple.api.json`. |
| `praderas-day-18-cover-image-hero-social-responsive` | `reviviendo-praderas-dia-18-imagen-hero-og-responsive.md` | `reviving-praderas-day-18-cover-hero-og-responsive.md` | done | Day 18 slice + follow-ups: `Image:` / Picsum **hero + og** on `blog/…` posts (`praderas-macros.twig`), listing thumbs, `export_cover.py`, migration plan, **`image-prompt-guidelines.md`**. |
| `praderas-day-19-export-cover-image-frontmatter-patch` | `reviviendo-praderas-dia-19-export-cover-parche-image-frontmatter.md` | `reviving-praderas-day-19-export-cover-image-frontmatter.md` | done | Day 19: `export_cover.py` **`--patch-markdown`**, **`--skip-comfy`**, **`--image-value`**, **`--dry-run-patch`**; `day19-comfyui-sdxl-export-frontmatter.webp`. Pair-path polish → **Day 21** **`--translation-key`**. |
| `praderas-day-20-image-webp-agents-readme-consolidation` | `reviviendo-praderas-dia-20-peso-imagen-webp-indice-agents.md` | `reviving-praderas-day-20-image-weight-webp-agents-index.md` | done | Day 20: **WebP** for Day 17–19 covers (`cwebp`, `webp_cover.sh`, `export_cover.py --webp`); remove heavy PNGs; **`.agents/README.md`** hub; docs refresh. |
| `praderas-day-21-export-cover-translation-key-flag` | `reviviendo-praderas-dia-21-export-cover-clave-traduccion.md` | `reviving-praderas-day-21-export-cover-translation-key.md` | done | Day 21: `export_cover.py` **`--translation-key`**; `day21-comfyui-sdxl-translation-key-patch.webp`; checklist row 7 shipped; **`frontmatter_audit.py`** **`Translation_Key`** guard (same PR follow-up). |
| `praderas-day-22-phase-5-vocabulary-tier-a-retrofit-log` | `reviviendo-praderas-dia-22-cierre-fase-5-vocabulario-y-retrofit-tier-a.md` | `reviving-praderas-day-22-phase-5-vocabulary-closure-and-tier-a-retrofit.md` | done | Day 22: Phase 5 UI closure recap (merged PR A) + Tier A retrofit rows 2–3 WebP; `day22-comfyui-sdxl-phase5-vocabulary-tier-a-retrofit-hero.webp`. |
| `praderas-day-23-tier-a-days-4-5-phase-6-blog-json-log` | `reviviendo-praderas-dia-23-retrofit-tier-a-dias-4-5-y-json-fase-6.md` | `reviving-praderas-day-23-tier-a-days-4-5-and-phase-6-json.md` | done | Day 23: Tier A retrofit rows 4–5 WebP + Phase 6 `70-BlogJson.php` / `blog-json-api.md`. |
| `praderas-day-24-tier-a-days-8-9-phase-6-search-json-log` | `reviviendo-praderas-dia-24-retrofit-tier-a-dias-8-9-y-busqueda-json.md` | `reviving-praderas-day-24-tier-a-days-8-9-and-search-json.md` | done | Day 24: Tier A retrofit rows 8–9 WebP + Phase 6 v1.1 `search.json` + listing agent fields. |
| `tuqan-phase-0-strategic-foundation` | `tuqan-phase-0-strategic-foundation-audit-and-roadmap.md` | `tuqan-phase-0-strategic-foundation-audit-and-roadmap.md` | done | Tuqan series opener; ES file canonical path `content/blog/` (not `blog/es/`). |

## Batch migration plan (to avoid context overflow)

1. **Batch 1 — Reviving Praderas complete series (Day 1 to Day 9)**  
   Status: **done in this PR**.
2. **Batch 2 — Control de Tiempo Desacoplado complete series (13 chapters)**  
   Status: **done** — EN posts + paired hub pages `/en/series`, `/en/categorias`, routing/theme fixes for EN navigation.
3. **Batch 3 — Core security + privacy cluster**  
   Status: **done** — six ES/EN post pairs + Day 11 log; UI: `tag_label_en`, `/en/tags`, `/en/about-picocms` (see `multilingual-ui-backlog.md`).
4. **Batch 4 — AI and future-tech cluster**  
   Status: **done** — core AI slice shipped earlier; remaining trend posts paired in Day 14/batch 8 closure (`praderas-b8-future-tech-innovation-horizon`, `praderas-b8-emerging-tech-trends-society`).
5. **Batch 5 — Productivity and collaboration tooling cluster**  
   Status: **done** — six ES/EN guides + Day 13 log (earlier PR); Emacs + libre Linux VM productivity list paired in Day 14/batch 8 (`praderas-b8-emacs-guide`, `praderas-b8-linux-vm-productivity-tools`).
6. **Batch 6 — Mobile development fundamentals cluster**  
   Status: **done** — six ES/EN fundamentals posts (`praderas-b6-*`) + Day 14 log documents the batch closure.
7. **Batch 7 — Crypto + blockchain cluster**  
   Status: **done** — four ES/EN posts (`praderas-b7-*`) in Day 14 PR.
8. **Batch 8 — General tech and education long-tail**  
   Status: **done** — ten ES/EN posts (`praderas-b8-*`) covering systems, education, trends, remote tooling lists, Debian install, online learning + Day 14 finale log.

---

## Vocabulary & terminology (ES ↔ EN)

Add rows as you fix recurring choices (tags, UI strings, series names).

| Context | ES (current) | EN (preferred) | Notes |
|---------|--------------|----------------|--------|
| **Tag source of truth** | `scripts/tag_vocabulary.json` | same file | `label_en`, `blurb_es`, `blurb_en` per canonical tag; audit enforces parity with `CANONICAL_TAGS`. |
| Series name | Reviviendo Praderas | Reviving Praderas | EN posts use EN series title; same `Series_Slug`. |
| Series name | Control de Tiempo Desacoplado | Decoupled time tracking | Same `Series_Slug: control-de-tiempo-desacoplado`; EN detail URLs use `/en/series/<slug>/`. |
| Nav (primary) | Inicio, Blog, Series, Categorías, Acerca | Home, Blog, Series, Categories, About | `nav.twig`; EN targets under `content/en/`. |
| Breadcrumb home | Inicio | Home | `content_lang` branch in templates. |
| Sidebar label | Artículos recientes | Recent posts | Sentence case in EN. |
| Sidebar CTA | Ver archivo | View archive | Links to `/archivo` or `/en/archivo`. |
| Sidebar search button | Buscar | Search | `sidebar.twig`. |
| Listing CTA | Leer más → | Read more → | `blog.twig` (ES `/blog`), `blog-en.twig`, `tags.twig`, `search.twig`. |
| Blog pager (ES `/blog`) | Entradas anteriores / Entradas recientes | — | Spanish-only route by design; `blog.twig`. |
| Blog pager (EN `/en/blog`) | — | Older posts / Newer posts | `blog-en.twig` + `10-Pagination.php` filter `blog/en/*`. |
| Blog pager indicator | Página N de M | Page N of M | Listing templates. |
| Post date line | Publicado el … | Published on … | `post.twig`. |
| Related block | Entradas relacionadas | Related posts | `post.twig` `aria-label`. |
| Post nav | Entradas anteriores y posteriores | Previous and next posts | `post.twig`. |
| Tag hub title | Resultados de la búsqueda para etiqueta: | Posts tagged: | `tags.twig` (filter view). |
| Categories footnote | … buscador del lateral | … or search | EN links to `en/search` (Day 15+). |
| Roadmap wording | Fase 4: SEO y descubrimiento | Phase 4: SEO and discoverability | EN series posts. |
| Tag pills / hubs (UI EN) | Ciberseguridad, Privacidad, … | Cybersecurity, Privacy, … | Canonical YAML unchanged; display from `tag_vocabulary.json`. |
| Productivity guides (batch 5) | Guía Completa de … | “Complete guide” / concise EN `Title` | Product names unchanged (Taskwarrior, Redmine, Etherpad, Focalboard, Nextcloud). |
| Mobile cluster (batch 6) | desarrollo móvil / frameworks | mobile app development, frameworks | Canonical tag `Aplicaciones Moviles` in YAML. |
| Security cluster (batch 3) | ciberseguridad, privacidad | cybersecurity, privacy | Paired posts + Day 11 UI slice. |
| Crypto posts (batch 7) | inversión / token hype | neutral/educational EN; disclaimers | Not financial advice; prefer official docs over stale figures. |
| SQL schema name (CTD series) | `control_tiempo` | keep `control_tiempo` in SQL snippets | EN posts explain; ORM may translate field names separately. |
| Editorial eras (home) | 2020 human / 2023–24 IA prose / 2026 IA-led engineering | same facts in EN | Keep `index.md` ↔ `en/index.md` aligned. |

---

## Checklist before merging a new EN post

- [ ] `Translation_Key` matches the Spanish sibling (or documents intentional orphan).
- [ ] `Lang: en` on EN file if not obvious from path alone.
- [ ] `Tags` valid per `scripts/frontmatter_audit.py` / `post-template.md`.
- [ ] **This tracker:** row added or updated in **Translation backlog**.
- [ ] **Vocabulary:** any new canonical term added to the table.

---

## Changelog (in-repo)

- **2026-05-24:** Day 24 ES/EN log — Tier A retrofit Days 8–9 heroes + Phase 6 v1.1 (`search.json`, listing `word_count` / `estimated_tokens` / `modified_at`); `retrofit-cover-queue.md` rows 8–9 → done.
- **2026-05-20:** Day 23 ES/EN log — Tier A retrofit Days 4–5 heroes + Phase 6 JSON v1 (`70-BlogJson.php`); `retrofit-cover-queue.md` rows 4–5 → done.
- **2026-05-19 (follow-up):** Day 22 ES/EN log (`praderas-day-22-phase-5-vocabulary-tier-a-retrofit-log`) — documents merged Phase 5 UI PR + Tier A retrofit Days 2–3 heroes; tracker row; `retrofit-cover-queue.md` rows 2–3 → done.
- **2026-05-19:** Phase 5 UI closure — `scripts/tag_vocabulary.json` (canonical tag labels + blurbs); `65-Multilingual.php` exposes `tag_blurb_es` / `tag_blurb_en`; `categories.twig` reads plugin maps; EN `/en/blog` pagination; vocabulary table expanded; `multilingual-ui-backlog.md` marked closed (bilingual YAML `Tags` deferred).
- **2026-05-15 (Tier A retrofit):** **`retrofit-cover-queue.md`** row **1** → **done** — Day 1 ES/EN **`Image:`** + **`day01-comfyui-sdxl-technical-audit-hero.webp`** (`--translation-key praderas-day-1-technical-audit`).
- **2026-05-15:** **`retrofit-cover-queue.md`** — Tier A tick table + **daily cadence** (~2 ES/EN pairs/day target, 1 pair floor); linked from **Related** + `multilingual-ui-backlog.md` “How to pick up work”.
- **2026-05-14 (follow-up):** `frontmatter_audit.py` — **`Translation_Key`** duplicate / pairing checks (same PR as Day 21 `--translation-key`).
- **2026-05-14:** Day 21 ES/EN log (`praderas-day-21-export-cover-translation-key-flag`) — **`export_cover.py --translation-key`** + `day21-comfyui-sdxl-translation-key-patch.webp`; tracker row; `comfyui-cover-images.md` checklist row 7 shipped.
- **2026-05-14:** Day 20 ES/EN — **dedicated** hero asset `day20-comfyui-sdxl-webp-agents-index.webp` (no reuse of Day 18); `.agents` rules updated (`post-template`, `image-prompt-guidelines`, `comfyui-cover-images`).
- **2026-05-13 (follow-up):** Linked **cover retrofit** (archive tiers + batches) from **Related** — lives in `comfyui-cover-images.md` (not a separate tracker column).
- **2026-05-13:** Day 20 ES/EN log (`praderas-day-20-image-webp-agents-readme-consolidation`) — **WebP** covers + **`README.md`** hub + `export_cover.py --webp` / `webp_cover.sh`.
- **2026-05-12:** Day 19 ES/EN log (`praderas-day-19-export-cover-image-frontmatter-patch`) — `export_cover.py` **`--patch-markdown`** + **`--skip-comfy`**; `day19-comfyui-sdxl-export-frontmatter.webp`; `.agents` checklist row 7 + committed examples.
- **2026-05-12 (post hero + prompts):** `post.twig` + `page-meta.twig` **Picsum** for `blog/…` without `Image:`; `praderas-macros.twig`; `.agents/image-prompt-guidelines.md`.
- **2026-05-12:** Picsum **fallback** on blog / EN blog / tags / search cards when `Image:` unset (`list-card-thumb.twig`); `.agents` + Day 18 log copy aligned.
- **2026-05-11 (follow-up):** Day 18 ES/EN — dedicated ComfyUI cover raster (`day18-comfyui-sdxl-cover-responsive.webp` after Day 20), `scripts/comfyui/export_cover.py`, image migration plan in `comfyui-cover-images.md`; checklist script row → **Partial**.
- **2026-05-11:** Day 18 ES/EN log (`praderas-day-18-cover-image-hero-social-responsive`) — `Image:` + social meta + listing thumbnails (initially neutral-only, later **Picsum** restored); ComfyUI generation marked production-ready in `.agents/comfyui-cover-images.md`; Day 17 posts gain `Image:` + copy edits.
- **2026-05-10:** Day 17 ES/EN log (`praderas-day-17-comfyui-cover-images-plan`) — ComfyUI cover pipeline planning doc (`.agents/comfyui-cover-images.md`), API template `scripts/comfyui/sdxl_ubersimple.api.json`, links from `repo-context.md`, `post-template.md`, `proposed-improvements.md`.
- **2026-05-10 (follow-up):** Added committed example raster `assets/images/day17-comfyui-sdxl-example.png` (later **WebP**, Day 20) in Day 17 ES/EN posts (Markdown figure; superseded for hero by Día 18 `Image:` wiring).
- **2026-05-10 (follow-up):** Tuqan Phase 0 pair (`tuqan-phase-0-strategic-foundation`) — Spanish source moved from erroneous `content/blog/es/` to canonical `content/blog/`; front matter (Series), body copy, and `.agents` references aligned with repository facts; EN sibling matched.
- **2026-05-10:** Day 16 ES/EN log (`praderas-day-16-sitemap-robots-lang-log`) — per-language sitemaps: `sitemap.xml` as index, `sitemap-es.xml` / `sitemap-en.xml` filtered via `Multilingual::inferLang`; theme templates `sitemap-index.twig`, `sitemap.twig`; `.agents/multilingual-ui-backlog.md` pending row cleared.
- **2026-05-06:** UI backlog slice — paired search route (`content/search.md` ↔ `content/en/search.md`, `praderas-nav-search`), bilingual search template/behavior (`search.twig`, `search-behavior.twig`), language-safe result filtering in `PicoSearch`, and EN footer credit branch in `index.twig`.
- **2026-05-05:** Batches 6–8 closure — twenty ES/EN pairs (`praderas-b6-*`…`praderas-b8-*`) + Day 14 ES/EN log (`praderas-day-14-batch-6-7-8-translation-finale-log`); EN `decoupled-architectures-time-tracking-app-overview` Debian bullet now targets `debian-11-install-step-by-step`; tracker batch statuses updated.
- **2026-05-04:** Batch 5 productivity slice — six ES/EN pairs (`praderas-b5-*`); Day 13 ES/EN log (`praderas-day-13-batch-5-productivity-log`); minor ES fixes (teletrabajo typo, Focalboard closing line).
- **2026-05-03 (follow-up):** Day 12 ES/EN log posts (`praderas-day-12-batch-4-archive-blog-log`) documenting merged batch 4 + `/en/archivo` + blog listing pair; published after main merge as a small transparency PR.
- **2026-05-03:** Batch 4 AI cluster — six post pairs (`praderas-b4-*`); `Translation_Key` on `content/blog.md` ↔ `content/en/blog.md` (language switcher on `/en/blog`); `/en/archivo` + bilingual `archive.twig` + sidebar archive CTA.
- **2026-05-02:** Batch 3 security/privacy cluster (`praderas-b3-*`), paired `/en/tags` + `/en/about-picocms`, `tag_label_en` display map, template i18n for tags/sidebar/post/categories/breadcrumbs; Day 11 ES/EN log (`praderas-day-11-batch-3-security-ui`) with ~12 min wall clock vs ~13.5–30 h senior counterfactual; added `.agents/multilingual-ui-backlog.md`.
- **2026-05-01:** Day 10 ES/EN daily log posts (`praderas-day-10-batch-2-multilingual-hubs`) documenting batch 2 + hub fixes + ~35 min human wall clock vs ~10–18 h localization order-of-magnitude.
- **2026-04-30 (batch 2):** Full *Control de Tiempo Desacoplado* EN pairs (`praderas-ctd-01`…`13`); `content/en/series.md` + `content/en/categorias.md` with `Translation_Key`; `SeriesCollections` + `BlogNeighbors` + `nav.twig` + bilingual `series.twig`/`categories.twig` for EN routes and counts.
- **2026-04-30 (agent docs):** Added `translation-batches.md` — operational instructions distilled from Day 9 (batches, context window, series integrity, glossary, wall-clock honesty); linked from `repo-context.md`, `post-template.md`, and this file.
- **2026-04-30 (follow-up):** Day 9 ES/EN posts updated: plain-language “context” explainer, 8-batch timeline table, batch rationale, and human wall-clock ~20 min (replacing an earlier inflated AI-assisted duration estimate).
- **2026-04-30:** Added batch-based migration plan (8 batches), completed Batch 1 for *Reviving Praderas* (Day 1-9 pairs now available in EN), and expanded vocabulary guidance.
- **2026-04-29:** Initial tracker: eras table, backlog schema, vocabulary stub, checklist; created alongside homepage transparency updates.
