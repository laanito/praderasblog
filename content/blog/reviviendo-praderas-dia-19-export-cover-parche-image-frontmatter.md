---
Title: Reviviendo Praderas (Día 19) — `export_cover.py` parchea `Image:` en el Markdown
Description: Flag --patch-markdown (y --skip-comfy) en el script de exportación ComfyUI; PNG dedicado Día 19; checklist del plan actualizado; bitácora con reloj de pared.
Date: 2026-05-12 11:15AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 19
Lang: es
Translation_Key: praderas-day-19-export-cover-image-frontmatter-patch
Image: /assets/images/day19-comfyui-sdxl-export-frontmatter.webp

---

# Reviviendo Praderas (Día 19) — del PNG al front matter en un solo comando

Cierra el hueco **“script + `Image:`”** del checklist en **`.agents/comfyui-cover-images.md`**: `scripts/comfyui/export_cover.py` puede, tras bajar el raster de ComfyUI, **insertar o sustituir** la clave **`Image:`** en uno o varios `.md` (por ejemplo el par ES/EN de esta bitácora). Opcionalmente **`--skip-comfy`** permite solo parchear cuando el PNG ya existe.

## Reloj de pared (implementación + artículo + docs)

- **ComfyUI + `--patch-markdown` en este par de ficheros:** `2026-05-12 11:03:57 CEST` → `11:04:31 CEST` (**~35 s** de reloj en la máquina local; domina la cola GPU + descarga + parche UTF-8).  
- **Resto de la sesión** (rama, código Python, textos ES/EN, `.agents`, commit/push): no medido al segundo en esta bitácora; orden de magnitud **decenas de minutos** de trabajo de agente + revisión mínima.

## Qué se implementó

1. **`export_cover.py`** — argumentos nuevos:
   - **`--patch-markdown`** — lista de rutas bajo el repo; tras export (o con **`--skip-comfy`**) se añade o reemplaza **`Image:`** en el **primer** bloque YAML (regex seguro, UTF-8).
   - **`--image-value`** — ruta del sitio explícita (`/assets/...`); si se omite, se **deduce** desde **`--output`** respecto a la raíz del repo (`config/config.yml` + `content/`).
   - **`--skip-comfy`** — no llama a la API; exige que **`--output`** ya exista y que haya **`--patch-markdown`** (flujo “solo front matter”).
   - **`--dry-run-patch`** — imprime qué haría el parche sin escribir.
2. **PNG Día 19** — **`assets/images/day19-comfyui-sdxl-export-frontmatter.webp`** (mismo grafo SDXL ubersimple; **seed `19052026`**; prompt orientado a “automatización / YAML / Git” en tono Praderas; ver EN gemelo para el texto CLIP).
3. **`.agents/comfyui-cover-images.md`** — fila **7** del checklist: export + **`--patch-markdown`** entregado; queda **abierto** el atajo opcional que resuelva rutas del par ES/EN por **`Translation_Key`** sin listar dos paths a mano. Orden **“next steps”** en el doc del plan: (1) ese pulido opcional, (2) **fila 9** (peso / **`ffmpeg`**), (3) **CI** (fila 8).

## Cómo reproducir el parche (ejemplo)

Tras generar el PNG (o con archivo ya existente):

```bash
python3 scripts/comfyui/export_cover.py --skip-comfy \
  --output assets/images/day19-comfyui-sdxl-export-frontmatter.webp \
  --patch-markdown \
    content/blog/reviviendo-praderas-dia-19-export-cover-parche-image-frontmatter.md \
    content/blog/en/reviving-praderas-day-19-export-cover-image-frontmatter.md
```

En la misma pasada que Comfy (sin `--skip-comfy`), añadir **`--patch-markdown …`** al final del comando que ya usabas con **`--positive`**, **`--seed`** y **`--prefix`**.

## Pendiente (cuando toque)

- Resolver pares **ES/EN** por **`Translation_Key`** sin pasar dos rutas a mano (opcional).  
- **Fila 9** del plan: **`ffmpeg`** / WebP u optimizadores PNG para reducir peso antes de commit.
