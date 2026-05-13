---
Title: Reviviendo Praderas (Día 17) — imágenes de portada: plan ComfyUI + plantilla SDXL
Description: Sesión de planificación: alternativas sin Picsum, validación local de ComfyUI (`/prompt`, SDXL ubersimple), plantilla JSON en `scripts/comfyui/` y checklist para integrar portadas en Pico sin romper el flujo Markdown-first.
Date: 2026-05-10 07:45PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 17
Lang: es
Translation_Key: praderas-day-17-comfyui-cover-images-plan
Image: /assets/images/day17-comfyui-sdxl-example.webp
---

# Reviviendo Praderas (Día 17) — portadas sin azar (plan + ComfyUI)

Tras cerrar el ítem SEO de sitemaps por idioma (Día 16), retomamos el hilo de **Priority 2** en `proposed-improvements.md`: sustituir **imágenes placeholder aleatorias** por algo **predecible** o **controlable**, sin obligar a un flujo editorial distinto al que ya usamos (Markdown + front matter).

## Qué decidimos en esta sesión

1. **Dos carriles complementarios**
   - **Determinista en repo:** claves tipo `Cover:` mapeadas a archivos estáticos (cero GPU, ideal para agentes que solo emiten YAML).
   - **ComfyUI (local):** generar un raster a partir de `Title` / `Description` + un bloque de estilo fijo, luego **commitear** la imagen y referenciarla (por ejemplo `Image:`) cuando toque publicar.

2. **Validación técnica real**  
   Contra `http://127.0.0.1:8188` comprobamos el flujo estándar: `POST /prompt` → `GET /history/{prompt_id}` → `GET /view?...`. Un grafo mínimo **SD 1.5 en 512²** sirvió de cableado; un grafo **SDXL “ubersimple”** (`SDXL/sd_xl_base_1.0.safetensors`, **1024×768**, `euler` + `sgm_uniform`, negativo largo, `VAEDecode` con VAE del checkpoint) dio **resultado visual claramente mejor** — alineado con la intuición de que el problema no era “solo el VAE” sino el **stack completo**.

## Ejemplo de salida (misma sesión, prueba local)

Archivo en el repositorio: `assets/images/day17-comfyui-sdxl-example.webp` (**1024×768**). Prompt positivo genérico (escritorio / terminal editorial, sin texto legible); solo para **ilustrar calidad** del grafo SDXL descrito arriba, no como portada definitiva de un artículo concreto. **Desde el Día 18** la misma ruta se referencia en el front matter como `Image:` y se muestra como **hero** encima del cuerpo (sin duplicar `![](...)` en Markdown).

### Qué quedó fuera en el Día 17 (y llegó después)

En la publicación original de esta nota aún no había integración Pico para hero ni metadatos sociales; **Día 18** añade `Image:`, `og:image`, tarjeta Twitter grande y estilos responsive — ver la siguiente entrada de la serie (`Translation_Key` contiguo en el tracker).

## Dónde queda documentado para agentes

- **`.agents/comfyui-cover-images.md`** — precondiciones, flujo API, tabla del grafo, seguridad (localhost vs túnel), checklist de integración (assets, front matter, Twig, script, CI, lint).
- **`scripts/comfyui/sdxl_ubersimple.api.json`** — plantilla lista para sustituir el texto del nodo positivo (`3`) y ajustar `seed` / prefijo de `SaveImage`.

También enlazamos desde **`repo-context.md`**, **`post-template.md`** (campo `Image:`) y **`proposed-improvements.md`** para que el descubrimiento no dependa del chat.

## Próximos pasos (tras el Día 18)

1. ~~Elegir convención `Image:` y lint de ruta~~ — hecho en Día 18.  
2. ~~Hero + `og:image` + CSS responsive~~ — hecho en Día 18.  
3. Script pequeño (Python) que lea un `.md`, rellene el JSON ComfyUI y guarde PNG bajo `assets/` (o Git LFS).  
4. Decidir si la generación vive **solo en local** o en CI con instancia alcanzable y secretos.

## Reloj de pared (orientativo)

Documentación + plantilla + esta bitácora + PR: **~25–40 min** de calendario en una sola pasada; la validación previa de ComfyUI fue sesión aparte.
