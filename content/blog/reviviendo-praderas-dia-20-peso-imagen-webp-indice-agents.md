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
Image: /assets/images/day18-comfyui-sdxl-cover-responsive.webp
---

# Reviviendo Praderas (Día 20) — que el héroe no bloquee el HTML

Los PNG de **SDXL 1024×768** que commiteamos en los días **17–19** pesaban **~0,9–1,0 MiB** cada uno: el navegador competía con el documento y el **hero** se sentía “pegado” o tardío. Hoy priorizamos **fila 9** del plan (peso) con **WebP** vía **`cwebp`** y dejamos un **índice único** para la carpeta **`.agents/`**.

## Reloj de pared

- **Codificación `cwebp` (tres portadas) + sustitución de rutas:** orden de **segundos** en máquina local.  
- **Sesión completa** (rama, script, artículos ES/EN, `.agents`, commit/push): **decenas de minutos** de trabajo de agente + revisión ligera (sin cronometrar al segundo en esta bitácora).

## Qué se hizo

1. **WebP en repo** — `assets/images/day17-comfyui-sdxl-example.webp`, `day18-comfyui-sdxl-cover-responsive.webp`, `day19-comfyui-sdxl-export-frontmatter.webp` (~**45–64 KiB** cada uno, calidad **82**). Los **PNG** grandes se **eliminan** del árbol git para no arrastrar peso muerto.
2. **`export_cover.py`** — flags **`--webp`** y **`--webp-delete-png`**: tras bajar el PNG de Comfy, opcionalmente genera el `.webp` y puede borrar el PNG.
3. **`scripts/comfyui/webp_cover.sh`** — envoltorio mínimo sobre **`cwebp`** para lotes (`brew install webp` si falta el binario).
4. **Front matter y posts** — todas las **`Image:`** y referencias en serie para Day 17–19 apuntan a **`.webp`**; `scripts/frontmatter_audit.py` sigue validando que el fichero exista en disco.
5. **`.agents/README.md`** — **hub** con tabla de archivos y orden de lectura recomendado; la “consolidación” es **índice + enlaces**, sin fusionar megadocumentos.
6. **Documentación enlazada** — `comfyui-cover-images.md` (estado 2026-05-13, fila **9 → Partial**), `repo-context.md`, `post-template.md`, `image-prompt-guidelines.md`, `translation-migration-tracker.md`, `proposed-improvements.md`, `multilingual-ui-backlog.md`.

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
