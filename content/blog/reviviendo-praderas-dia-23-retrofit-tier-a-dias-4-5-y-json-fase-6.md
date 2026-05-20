---
Title: Reviviendo Praderas (Día 23) — retrofit Tier A (Días 4–5) y arranque Fase 6 (JSON para agentes)
Description: Dos pares ES/EN con portadas Comfy (Fase 3 metadatos y pulido visual) más el plugin 70-BlogJson con /blog.json y hermanos documentados en .agents/blog-json-api.md.
Date: 2026-05-20 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 23
Lang: es
Translation_Key: praderas-day-23-tier-a-days-4-5-phase-6-blog-json-log
Image: /assets/images/day23-comfyui-sdxl-tier-a-json-phase6-hero.webp

---

# Reviviendo Praderas (Día 23) — portadas que alcanzan la Fase 3 y JSON sin HTML

Hoy ejecutamos el plan doble acordado: **seguir el retrofit Tier A** (cadencia ~2 pares ES/EN) y **abrir la Fase 6** con un primer corte de API JSON estable para agentes y herramientas.

## Reloj de pared (orden de magnitud)

- **Retrofit Días 4 y 5 (Comfy + WebP + `--translation-key`):** ~**1 h** (dos generaciones SDXL + auditoría).  
- **Fase 6 slice 1 (`70-BlogJson.php` + documentación):** ~**45 min** de agente.  
- **Bitácora ES/EN:** misma sesión.

---

## Bloque A — Retrofit Tier A: Días 4 y 5

| `Translation_Key` | WebP | Semilla | ~KiB |
|-------------------|------|---------|------|
| `praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint` | `day04-comfyui-sdxl-phase3-metadata-taxonomy-frontmatter-hero.webp` | `04052026` | ~112 |
| `praderas-day-5-visual-polish` | `day05-comfyui-sdxl-visual-polish-readability-hero.webp` | `05052026` | ~55 |

Metáforas visuales: **Día 4** — etiquetas ordenadas y siluetas de documento (taxonomía / lint); **Día 5** — ritmo tipográfico y tarjetas con aire (pulido visual sin frameworks nuevos).

### Comando de reproducción (Día 4)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day04-comfyui-sdxl-phase3-metadata-taxonomy-frontmatter-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract organized tag chips and gentle taxonomy tree shapes suggesting metadata consistency and front matter lint, subtle checklist and document silhouettes without readable text, calm grid of soft data cards, grass-green and warm neutral tones, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 04052026 \
  --prefix praderas_day04_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint
```

### Comando de reproducción (Día 5)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day05-comfyui-sdxl-visual-polish-readability-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract typography rhythm and generous vertical whitespace suggesting calm long-form reading, subtle design token swatches and refined card elevations without readable text, gentle grass-green accent on warm neutrals, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 05052026 \
  --prefix praderas_day05_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-5-visual-polish
```

Tick en `.agents/retrofit-cover-queue.md`: filas **4–5** → `done`. Siguiente objetivo Tier A: **Días 6–7**.

---

## Bloque B — Fase 6: JSON v1 (listados + artículo)

Nuevo plugin **`plugins/70-BlogJson.php`** y contrato en **`.agents/blog-json-api.md`**.

| Endpoint | Contenido |
|----------|-----------|
| `GET /blog.json` | Listado posts **español** (`blog/*`, sin `blog/en/*`) |
| `GET /blog/en.json` | Listado posts **inglés** |
| `GET /blog/{slug}.json` | Artículo ES + cuerpo markdown |
| `GET /blog/en/{slug}.json` | Artículo EN + cuerpo markdown |

Cada ítem incluye `translation_key`, `alternate_url` cuando hay par, `tags` en español canónico (igual que YAML), `reading_time_minutes` estimado y cabecera `Cache-Control: public, max-age=3600`.

**Fuera de este corte:** `search.json`, filtros por etiqueta, generación estática, entradas JSON en el sitemap.

### Prueba rápida (stack local)

```bash
curl -sS 'http://localhost:8080/blog.json' | head
curl -sS 'http://localhost:8080/blog/reviviendo-praderas-dia-4-fase-3-metadatos-taxonomy-y-lint-de-front-matter.json' | jq '.post.title'
```

---

## Próximo foco

- **Tier A** — filas **6–16** (~2 pares/día).  
- **Fase 6 v1.1** — `search.json` o pre-generación si hace falta un integrador concreto.  
- **Tier A** — portada de esta bitácora: `day23-comfyui-sdxl-tier-a-json-phase6-hero.webp` (semilla `23052026`).
