---
Title: Reviviendo Praderas (Día 20) — peso de portadas WebP y índice de `.agents`
Description: Portadas Comfy en WebP (~50 KiB vs ~1 MiB PNG), `export_cover.py --webp`, `webp_cover.sh`, checklist fila 9; nuevo `README.md` en `.agents` como hub de documentación consolidada.
Date: 2026-05-13 11:45AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 20
Lang: es
Translation_Key: praderas-day-20-image-webp-agents-readme-consolidation
Image: /assets/images/day20-comfyui-sdxl-webp-agents-index.webp
---

# Reviviendo Praderas (Día 20) — que el héroe no bloquee el HTML

Los PNG de **SDXL 1024×768** que commiteamos en los días **17–19** pesaban **~0,9–1,0 MiB** cada uno: el navegador competía con el documento y el **hero** se sentía “pegado” o tardío. Hoy priorizamos **fila 9** del plan (peso) con **WebP** vía **`cwebp`** y dejamos un **índice único** para la carpeta **`.agents/`**.

## Reloj de pared

- **Codificación `cwebp` (tres portadas) + sustitución de rutas:** orden de **segundos** en máquina local.  
- **Sesión completa** (rama, script, artículos ES/EN, `.agents`, commit/push): **decenas de minutos** de trabajo de agente + revisión ligera (sin cronometrar al segundo en esta bitácora).

## Qué se hizo

1. **WebP en repo** — `assets/images/day17-comfyui-sdxl-example.webp`, `day18-comfyui-sdxl-cover-responsive.webp`, `day19-comfyui-sdxl-export-frontmatter.webp`, **`day20-comfyui-sdxl-webp-agents-index.webp`** (~**45–76 KiB** según portada, calidad **82**). Los **PNG** grandes se **eliminan** del árbol git para no arrastrar peso muerto.
2. **`export_cover.py`** — flags **`--webp`** y **`--webp-delete-png`**: tras bajar el PNG de Comfy, opcionalmente genera el `.webp` y puede borrar el PNG.
3. **`scripts/comfyui/webp_cover.sh`** — envoltorio mínimo sobre **`cwebp`** para lotes (`brew install webp` si falta el binario).
4. **Front matter y posts** — cada día con portada Comfy usa su **`.webp` dedicado** (Días **17–20**); el par ES/EN comparte la misma ruta **`Image:`**; `scripts/frontmatter_audit.py` sigue validando que el fichero exista en disco.
5. **`.agents/README.md`** — **hub** con tabla de archivos y orden de lectura recomendado; la “consolidación” es **índice + enlaces**, sin fusionar megadocumentos.
6. **Documentación enlazada** — `comfyui-cover-images.md` (estado 2026-05-13, fila **9 → Partial**), `repo-context.md`, `post-template.md`, `image-prompt-guidelines.md`, `translation-migration-tracker.md`, `proposed-improvements.md`, `multilingual-ui-backlog.md`.
7. **Portada de este día (Día 20)** — **`Image: /assets/images/day20-comfyui-sdxl-webp-agents-index.webp`** (~**76 KiB**); raster **propio** (antes se reutilizaba la del Día 18 por error). Regla en `.agents`: **un fichero por artículo** / `Translation_Key`, salvo que el texto explique un reuso intencionado.

## Prompt positivo CLIP (portada Día 20)

> Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract feather-light layers and stacked translucent cards suggesting compressed image weight and a documentation index hub, subtle floating grid shapes like folded README pages without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading

- **Semilla (`--seed`):** `20052026`  
- **Prefijo Comfy (`--prefix`):** `praderas_day20_export`

## Cómo reproducir esta portada (ejemplo)

Con ComfyUI en marcha (`http://127.0.0.1:8188` por defecto):

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day20-comfyui-sdxl-webp-agents-index.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft golden meadow light, abstract feather-light layers and stacked translucent cards suggesting compressed image weight and a documentation index hub, subtle floating grid shapes like folded README pages without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 20052026 \
  --prefix praderas_day20_export \
  --webp --webp-delete-png \
  --patch-markdown \
    content/blog/reviviendo-praderas-dia-20-peso-imagen-webp-indice-agents.md \
    content/blog/en/reviving-praderas-day-20-image-weight-webp-agents-index.md
```

## Comandos útiles

```bash
# Tras un export PNG existente:
bash scripts/comfyui/webp_cover.sh assets/images/mi-portada.png

# En la misma corrida que Comfy:
python3 scripts/comfyui/export_cover.py --output assets/images/mi.png ... \
  --webp --webp-delete-png --patch-markdown content/blog/foo.md
```

## Próximo foco

- Pulido opcional **fila 7** (`Translation_Key` → rutas para `--patch-markdown`).  
- Resto **fila 9** si hace falta **`ffmpeg`** u otro códec; **CI** (fila 8) cuando la generación salga del portátil.
