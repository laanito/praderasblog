---
Title: Reviviendo Praderas (Día 24) — retrofit Tier A (Días 8–9) y Fase 6 v1.1 (search.json)
Description: Portadas WebP para el anuncio de Fase 5 multilingüe y el plan de migración por lotes (Días 8–9), más search.json y campos de agente en listados JSON sin duplicar el ranking HTML.
Date: 2026-05-24 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 24
Lang: es
Translation_Key: praderas-day-24-tier-a-days-8-9-phase-6-search-json-log
Image: /assets/images/day24-comfyui-sdxl-tier-a-search-json-hero.webp

---

# Reviviendo Praderas (Día 24) — portadas de la Fase 5 y búsqueda para agentes

Retomamos el plan acordado tras la pausa: **seguir el retrofit Tier A** con cadencia de ~dos pares ES/EN por día y **avanzar la Fase 6** sin abrir un segundo servidor ni romper el modelo flat-file de Pico.

## Reloj de pared (orden de magnitud)

- **Retrofit Días 8 y 9 (Comfy + WebP + `--translation-key`):** ~**1 h** (dos pasadas SDXL + auditoría de front matter).
- **Fase 6 v1.1 (`search.json` + campos en listados):** ~**40 min** de agente.
- **Bitácora ES/EN:** misma sesión.

---

## Bloque A — Retrofit Tier A: Días 8 y 9

Las filas **8** y **9** de `.agents/retrofit-cover-queue.md` seguían en Picsum: el anuncio de **Fase 5 multilingüe** y el post que formaliza el **plan de migración por lotes**. Ambos son piezas narrativas clave de la serie; merecían el mismo tratamiento que los Días 1–7.

| `Translation_Key` | WebP | Semilla | ~KiB | Metáfora visual |
|-------------------|------|---------|------|-----------------|
| `praderas-phase-5-multilingual` | `day08-comfyui-sdxl-phase5-multilingual-hero.webp` | `08052026` | ~75 | Puentes suaves entre dos idiomas (sin texto legible) |
| `praderas-day-9-translation-migration-batch-1` | `day09-comfyui-sdxl-translation-migration-batch1-hero.webp` | `09052026` | ~57 | Colas de documentos en oleadas ordenadas (migración por lotes) |

**Decisión:** un solo archivo WebP por par ES/EN (`Image:` idéntico en ambos `.md`), como en el resto del retrofit. No reutilizamos portadas de otros días: cada `Translation_Key` tiene su propio raster bajo `assets/images/`.

### Reproducción (Día 8)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day08-comfyui-sdxl-phase5-multilingual-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract paired language glyphs and gentle bridge shapes suggesting bilingual content without readable text, subtle hreflang link motifs as soft curves, warm grass-green and neutral tones, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 08052026 \
  --prefix praderas_day08_export \
  --webp --webp-delete-png \
  --translation-key praderas-phase-5-multilingual
```

### Reproducción (Día 9)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day09-comfyui-sdxl-translation-migration-batch1-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract stacked document lanes and gentle batch queue shapes suggesting translation migration in orderly waves, subtle parallel paths converging without readable text, calm grass-green accents on warm neutrals, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 09052026 \
  --prefix praderas_day09_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-9-translation-migration-batch-1
```

Tick en la cola Tier A: filas **8–9** → `done`. Siguiente objetivo habitual: **Días 10–11** (~dos pares el próximo día hábil).

---

## Bloque B — Fase 6 v1.1: búsqueda JSON y campos para agentes

En el Día 23 abrimos **`/blog.json`** y hermanos con **schema 1.0**. Hoy subimos a **1.1** con dos mejoras pedidas en `.agents/phase-5-6-plan.md`:

### Por qué un plugin y no un microservicio

Seguimos la misma línea que v1: **`70-BlogJson.php`** intercepta rutas dedicadas en el ciclo de Pico, reutiliza páginas ya cargadas y delega el **ranking de búsqueda** a **`40-PicoSearch.php`** mediante `searchBlogPosts()`. Así los agentes obtienen el **mismo orden de relevancia** que un humano en `/search/<término>`, sin parsear HTML ni mantener un segundo despliegue.

### Qué cambió

| Entrega | Detalle |
|---------|---------|
| **`GET /search.json?q=…`** | Resultados solo de entradas `blog/*` en español |
| **`GET /en/search.json?q=…`** | Mismo contrato en inglés (`blog/en/*`) |
| **Listados y detalle** | Nuevos campos: `word_count`, `estimated_tokens`, `modified_at` también en **listados** (antes `modified_at` solo en detalle) |
| **Respuesta de búsqueda** | Clave `results[]` (no `posts[]`) + `meta.query` + `search_rank` por ítem |

`estimated_tokens` es una **aproximación** (`ceil(strlen(body) / 4)`) para presupuestar ventanas de contexto en RAG — no sustituye un contador del modelo que consuma el JSON.

**Fuera de este corte (v1.2):** página pública `/for-ai-agents`, filtros por etiqueta en JSON, entradas JSON en el sitemap.

### Prueba rápida (stack local)

```bash
curl -sS 'http://localhost:8080/search.json?q=multilingue' | jq '.meta'
curl -sS 'http://localhost:8080/blog.json' | jq '.posts[0] | {title, word_count, estimated_tokens, modified_at}'
```

Contrato completo: **`.agents/blog-json-api.md`**.

---

## Próximo foco

- **Tier A** — filas **10–16** (≈2 pares/día).
- **Fase 6 v1.2** — `/for-ai-agents` como página de descubrimiento para herramientas que no leen `.agents/` en git.
- **Portada de esta bitácora** — generada al cierre del PR (`day24-comfyui-sdxl-tier-a-search-json-hero.webp`, semilla `24052026`).
