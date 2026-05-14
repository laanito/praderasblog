---
Title: Reviviendo Praderas (Día 21) — `export_cover.py` y la clave `Translation_Key`
Description: Nuevo flag `--translation-key` para parchear `Image:` en el par ES/EN sin listar dos rutas; exclusión mutua con `--patch-markdown`; portada Comfy dedicada (seed 21052026).
Date: 2026-05-14 05:30PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 21
Lang: es
Translation_Key: praderas-day-21-export-cover-translation-key-flag
Image: /assets/images/day21-comfyui-sdxl-translation-key-patch.webp

---

# Reviviendo Praderas (Día 21) — de dos rutas a una clave

En el **Día 19** documentamos **`--patch-markdown`**: tras exportar la portada, el script **sustituye o inserta** la línea **`Image:`** en uno o más `.md`. En la práctica, los pares ES/EN obligaban a **pasar dos rutas** cada vez. Hoy cerramos el hueco del checklist **fila 7** con **`--translation-key`**: el script **recorre** `content/blog/*.md` y `content/blog/en/*.md`, lee el front matter y localiza **exactamente dos** entradas que comparten la misma **`Translation_Key`**; luego aplica el mismo **`Image:`** que ya derivamos de **`--output`** (o de **`--image-value`**).

## Reloj de pared

- **Implementación Python + documentación + bitácora:** orden de **decenas de minutos** de agente + revisión ligera (sin cronometrar al segundo aquí).  
- **ComfyUI + `cwebp` + parche por clave:** similar al **Día 19** en magnitud cuando la GPU está libre.

## Qué se hizo

1. **`export_cover.py`** — flag **`--translation-key <KEY>`** (mutuamente excluyente con **`--patch-markdown`**). Resolución determinista: primero el markdown bajo **`content/blog/`**, después el de **`content/blog/en/`**. Si hay **0**, **1** o **más de 2** coincidencias, el comando **falla** con lista de rutas encontradas (evita parches ambiguos).
2. **Portada del día** — raster **dedicado** **`day21-comfyui-sdxl-translation-key-patch.webp`** (mismo grafo SDXL ubersimple; **seed `21052026`**; prompt CLIP en la sección siguiente; ~**91 KiB** en este export). Misma regla que desde el **Día 20**: **un fichero por `Translation_Key`**, sin reusar portadas de otros días.
3. **`.agents/comfyui-cover-images.md`** — fila **7** del checklist: **parche por clave** documentado; § migración y “pendientes” alineados con el nuevo flujo.

## Prompt positivo CLIP (portada Día 21)

> Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract twin document panels suggesting paired Spanish and English markdown files linked by a shared key motif, subtle mirrored shapes and soft link lines without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading

## Cómo reproducir (export + WebP + parche por clave)

Con ComfyUI en marcha y los dos posts ya existentes con la misma **`Translation_Key`**:

```bash
python3 scripts/comfyui/export_cover.py \
  --output assets/images/day21-comfyui-sdxl-translation-key-patch.png \
  --positive "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft dawn meadow light, abstract twin document panels suggesting paired Spanish and English markdown files linked by a shared key motif, subtle mirrored shapes and soft link lines without readable text, gentle teal and grass-green accents, calm professional atmosphere, no logos, no watermarks, high detail, tasteful color grading" \
  --seed 21052026 \
  --prefix praderas_day21_export \
  --webp --webp-delete-png \
  --translation-key praderas-day-21-export-cover-translation-key-flag
```

Solo **parche** (sin Comfy), si el **WebP** ya está en disco:

```bash
python3 scripts/comfyui/export_cover.py --skip-comfy \
  --output assets/images/day21-comfyui-sdxl-translation-key-patch.webp \
  --translation-key praderas-day-21-export-cover-translation-key-flag
```

## Próximo foco

- Resto **fila 9** (`ffmpeg` u otros códec) si hace falta; **CI** (fila 8) cuando la generación salga del portátil.  
- **Retrofit** de portadas en archivo (`.agents/comfyui-cover-images.md` § *Retrofit plan*) — ahora el parche por clave reduce fricción al tocar muchos pares.
