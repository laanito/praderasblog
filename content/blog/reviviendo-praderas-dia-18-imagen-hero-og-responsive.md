---
Title: Reviviendo Praderas (Día 18) — `Image:` en portada, Open Graph y formato responsive (sin Picsum)
Description: Implementación de hero opcional desde front matter, metadatos sociales, tarjetas de listado con miniatura o placeholder neutro, CSS para imágenes en cuerpo y hero; ComfyUI listo para producción en generación; bitácora con reloj de pared.
Date: 2026-05-11 10:05AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 18
Lang: es
Translation_Key: praderas-day-18-cover-image-hero-social-responsive
Image: /assets/images/day18-comfyui-sdxl-cover-responsive.png
---

# Reviviendo Praderas (Día 18) — portada seria en el blog

Cierra el siguiente bloque del plan de **Día 17 / `.agents/comfyui-cover-images.md`**: el sitio ya **entiende `Image:`**, muestra un **hero** estable en anchos bajos, rellena **`og:image`** (y Twitter `summary_large_image` cuando aplica), y elimina **picsum** de listados, etiquetas y búsqueda. **ComfyUI** queda declarado **listo para producción** en el lado de **generación** de assets; lo que sigue abierto es solo automatizar el script / CI.

## Reloj de pared (implementación + artículo + docs)

- **Inicio:** `2026-05-11 09:56:08 CEST`  
- **Fin:** `2026-05-11 09:59:35 CEST`  

Ventana medida: **~3 min 30 s** de calendario en esta sesión (rama desde `main`, cambios Twig/CSS/PHP, auditoría de front matter, actualización de `.agents`, esta bitácora, commit y push). No incluye una pasada humana de diseño fino ni despliegue en producción.

## Qué se implementó

1. **`Image:` en front matter** — registrado en `65-Multilingual.php` (`onMetaHeaders`) para que Pico exponga `meta.image` en Twig.
2. **`post.twig`** — hero opcional con `pradera-hero-figure` / `pradera-hero-img`; sin `Image:` no se inyecta nada (se elimina el placeholder Picsum fijo).
3. **`page-meta.twig`** — URL absoluta para `og:image` y `twitter:image`; `twitter:card` pasa a `summary_large_image` cuando hay imagen.
4. **Listados** — `list-card-thumb.twig` + includes en `blog.twig`, `blog-en.twig`, `tags.twig`, `search.twig`: miniatura si el post tiene `Image:`, si no **gradiente neutro** (sin peticiones externas).
5. **`praderas-theme.css`** — `max-width: 100%`, `object-fit: contain`, `max-height` con `vh` en el hero; reglas para `.post-body img` / `figure` para que imágenes Markdown no rompan el cuerpo en móvil.
6. **`scripts/frontmatter_audit.py`** — también recorre `content/blog/en/*.md` y comprueba que rutas `Image:` no HTTP existan en disco.
7. **Día 18** — PNG **dedicado** generado con ComfyUI (ver sección siguiente); **Día 17** sigue usando el PNG de humo `day17-comfyui-sdxl-example.png` como referencia histórica del plan.

## Imagen de esta entrada (ComfyUI / SDXL)

Esta bitácora lleva **`Image: /assets/images/day18-comfyui-sdxl-cover-responsive.png`**: hero, vista previa social y miniatura de listado leen la misma ruta (sin duplicar `![](...)` en el cuerpo).

- **Grafo:** `scripts/comfyui/sdxl_ubersimple.api.json` (mismo que el plan del Día 17).
- **Exporte reproducible:** `python3 scripts/comfyui/export_cover.py` con `--output` apuntando a ese fichero, **`--seed 18052026`**, prefijo de guardado distinto por tanda (p. ej. `praderas_day18_export`).
- **Prompt positivo (CLIP, inglés):**

> Wide cinematic editorial illustration for a Spanish tech blog named Praderas, soft green meadow at golden hour, subtle abstract UI wireframes and gentle grid lines suggesting responsive web layout and Open Graph cards, calm modern typography shapes, no readable text, no logos, peaceful professional atmosphere, high detail, tasteful color grading

La política de **migración de imágenes** (nombres bajo `assets/images/`, par ES/EN con el mismo `Image:`, no reutilizar en silencio el PNG de otro día) está en **`.agents/comfyui-cover-images.md`**.

## ComfyUI “production ready”

En documentación: la **instancia y el grafo SDXL** están listos para **producir** imágenes en local; el blog **no** llama a Comfy en runtime — solo consume archivos estáticos o URLs que el flujo de generación deje en el repo.

## Pendiente (siguiente PR cuando toque)

- Cerrar el hueco del checklist: parche automático de front matter (`Image:`) tras generar PNG; hoy existe **`scripts/comfyui/export_cover.py`** solo para **exportar** el raster.  
- CI opcional y política LFS si el peso de binarios crece.
