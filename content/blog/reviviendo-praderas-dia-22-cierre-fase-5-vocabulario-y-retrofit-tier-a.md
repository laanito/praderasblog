---
Title: Reviviendo Praderas (Día 22) — cierre de la Fase 5 (vocabulario UI) y retrofit Tier A (Días 2–3)
Description: Bitácora nocturna: PR fusionado de vocabulario multilingüe (`tag_vocabulary.json`, paginación EN, auditoría) más dos portadas Comfy del retrofit Tier A (Fases 1 y 2) con `--translation-key`.
Date: 2026-05-19 10:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 22
Lang: es
Translation_Key: praderas-day-22-phase-5-vocabulary-tier-a-retrofit-log
Image: /assets/images/day22-comfyui-sdxl-phase5-vocabulary-tier-a-retrofit-hero.webp

---

# Reviviendo Praderas (Día 22) — Fase 5 cerrada en UI y el retrofit vuelve al ritmo

Hoy tocaron **dos frentes** que veníamos arrastrando en la planificación: **cerrar la capa multilingüe de interfaz** (lo que el tracker llamaba pendiente tras el backlog de posts) y **retomar el retrofit de portadas** en *Reviviendo Praderas* con el cadence de **dos pares ES/EN por día** en Tier A. Lo documentamos en **una sola PR** para no fragmentar la revisión nocturna.

## Reloj de pared (orden de magnitud)

- **Cierre Fase 5 UI (vocabulario + plantillas + docs):** del orden de **una hora** de agente + merge en `main` antes de esta rama.  
- **Retrofit Tier A (Días 2 y 3) + Comfy + WebP + parche por clave:** ~**1 h** adicional (dos generaciones SDXL + `cwebp` + auditoría).  
- **Esta bitácora ES/EN:** escrita en la misma sesión.

No confundir con “el modelo corrió una hora”: es **tiempo humano de dirección y revisión** repartido en dos bloques de producto.

---

## Bloque A — Fase 5 UI cerrada (ya en `main`)

Tras fusionar la PR **`feat/day-22-phase-5-vocabulary-and-ui-closure`**, el blog declara **completa** la Fase 5 para el modelo actual: **posts ES/EN emparejados** (hecho en días anteriores) + **superficies de UI** sin mezclar idiomas en la misma página.

### Qué entró en código y documentación

1. **`scripts/tag_vocabulary.json`** — fuente única para cada etiqueta canónica en español (`Tags` en YAML sin cambiar): **`label_en`**, **`blurb_es`**, **`blurb_en`**. Antes las descripciones largas de categorías vivían duplicadas en `categories.twig`; ahora **`65-Multilingual.php`** las expone como `tag_blurb_es` / `tag_blurb_en` junto a `tag_label_en`.
2. **`scripts/frontmatter_audit.py`** — comprueba que el JSON cubre exactamente las diez etiquetas de `CANONICAL_TAGS` y que cada fila tiene los tres campos no vacíos (misma filosofía que el guard de **`Translation_Key`** del Día 21).
3. **`/en/blog` con paginación** — `10-Pagination.php` filtra solo `blog/en/*`; `blog-en.twig` usa `paged_pages` con textos **Older posts / Newer posts / Page N of M** (el listado español en `/blog` sigue en castellano a propósito).
4. **`categories.twig`** — pie EN corregido: enlace a **`/en/search`**; eliminada la nota obsoleta de “búsqueda solo en español” (cerrada en el Día 15).
5. **Agentes** — `multilingual-ui-backlog.md` marcado **cerrado**; tabla de **vocabulario** ampliada en `translation-migration-tracker.md` (nav, paginación, CTAs, series, lotes 3–8); `phase-5-6-plan.md` actualizado; **Phase 6 JSON** sigue sin empezar.

### Qué queda explícitamente fuera (decisión de producto)

- **Etiquetas bilingües en el front matter** (`Tags` distintos por idioma): **aplazado** — migración grande de contenido y URLs. El modelo vigente: **una taxonomía YAML en español** + mapas de presentación en inglés.

---

## Bloque B — Retrofit Tier A: Días 2 y 3 (esta PR)

Seguimos `.agents/retrofit-cover-queue.md` (objetivo **~2 pares/día**). Tras el Día 1 en la PR del 21, hoy tocan **Fase 1** y **Fase 2** del diario de la serie.

| `Translation_Key` | WebP | Semilla | ~KiB |
|-------------------|------|---------|------|
| `praderas-day-2-phase-1-listing-search-pagination` | `day02-comfyui-sdxl-phase1-listing-search-pagination-hero.webp` | `02052026` | ~57 |
| `praderas-day-3-phase-2-navigation-categories-breadcrumbs-related` | `day03-comfyui-sdxl-phase2-navigation-breadcrumbs-hero.webp` | `03052026` | ~75 |

En ambos casos: **`export_cover.py`** con **`--webp --webp-delete-png --translation-key`** parchea **`Image:`** en el par ES/EN sin listar rutas a mano.

### Prompts CLIP (resumen)

**Día 2 (listado, búsqueda, paginación):** pradera cinematográfica, rejilla abstracta de tarjetas de blog, lupa suave, puntos de paginación en el horizonte, sin texto legible.

**Día 3 (navegación, categorías, migas, relacionados):** sendero de migas abstracto, nodos-hub de categorías, tarjetas relacionadas al fondo, acentos teal y verde pradera.

### Comandos de reproducción (Día 2)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day02-comfyui-sdxl-phase1-listing-search-pagination-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract blog listing grid with paired card silhouettes and a subtle magnifying glass shape suggesting search, gentle pagination dots along a calm horizon, responsive column hints without readable text, grass-green and warm neutral tones, professional quiet atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 02052026 \
  --prefix praderas_day02_phase1 \
  --webp --webp-delete-png \
  --translation-key praderas-day-2-phase-1-listing-search-pagination
```

### Comandos de reproducción (Día 3)

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day03-comfyui-sdxl-phase2-navigation-breadcrumbs-hero.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract breadcrumb trail and gentle hub nodes suggesting categories and site navigation, subtle linked paths between soft shapes without readable labels, related content motifs as quiet overlapping cards in the distance, teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 03052026 \
  --prefix praderas_day03_phase2 \
  --webp --webp-delete-png \
  --translation-key praderas-day-3-phase-2-navigation-categories-breadcrumbs-related
```

---

## Próximo foco

- **Tier A** — filas **4–16** del queue (cadencia ~2 pares/día cuando haya capacidad).  
- **Phase 6** — endpoints JSON cuando toque (`phase-5-6-plan.md`).  
- **Checklist Comfy** — filas 8–9 (CI / `ffmpeg`) siguen opcionales.
