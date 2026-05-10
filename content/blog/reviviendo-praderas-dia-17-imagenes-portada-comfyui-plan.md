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
---

# Reviviendo Praderas (Día 17) — portadas sin azar (plan + ComfyUI)

Tras cerrar el ítem SEO de sitemaps por idioma (Día 16), retomamos el hilo de **Priority 2** en `proposed-improvements.md`: sustituir **imágenes placeholder aleatorias** por algo **predecible** o **controlable**, sin obligar a un flujo editorial distinto al que ya usamos (Markdown + front matter).

## Qué decidimos en esta sesión

1. **Dos carriles complementarios**
   - **Determinista en repo:** claves tipo `Cover:` mapeadas a archivos estáticos (cero GPU, ideal para agentes que solo emiten YAML).
   - **ComfyUI (local):** generar un raster a partir de `Title` / `Description` + un bloque de estilo fijo, luego **commitear** la imagen y referenciarla (por ejemplo `Image:`) cuando toque publicar.

2. **Validación técnica real**  
   Contra `http://127.0.0.1:8188` comprobamos el flujo estándar: `POST /prompt` → `GET /history/{prompt_id}` → `GET /view?...`. Un grafo mínimo **SD 1.5 en 512²** sirvió de cableado; un grafo **SDXL “ubersimple”** (`SDXL/sd_xl_base_1.0.safetensors`, **1024×768**, `euler` + `sgm_uniform`, negativo largo, `VAEDecode` con VAE del checkpoint) dio **resultado visual claramente mejor** — alineado con la intuición de que el problema no era “solo el VAE” sino el **stack completo**.

3. **Qué no hicimos aún**  
   No hay plugin Pico ni cambio en `post.twig`/`page-meta.twig` en esta rama: el objetivo fue **documentar el punto de partida** para sesiones futuras, no acoplar el blog a un servicio GPU.

## Dónde queda documentado para agentes

- **`.agents/comfyui-cover-images.md`** — precondiciones, flujo API, tabla del grafo, seguridad (localhost vs túnel), checklist de integración (assets, front matter, Twig, script, CI, lint).
- **`scripts/comfyui/sdxl_ubersimple.api.json`** — plantilla lista para sustituir el texto del nodo positivo (`3`) y ajustar `seed` / prefijo de `SaveImage`.

También enlazamos desde **`repo-context.md`**, **`post-template.md`** (campo `Image:` futuro) y **`proposed-improvements.md`** para que el descubrimiento no dependa del chat.

## Próximos pasos (cuando se priorice)

1. Elegir convención única (`Image:` u otra) y extender `frontmatter_audit.py` si hace falta.  
2. Añadir hero + `og:image` en Twig con rutas resueltas y fallback limpio.  
3. Script pequeño (Python) que lea un `.md`, rellene el JSON y guarde PNG bajo `assets/` (o política con Git LFS).  
4. Decidir si la generación vive **solo en local** o en CI con instancia alcanzable y secretos.

## Reloj de pared (orientativo)

Documentación + plantilla + esta bitácora + PR: **~25–40 min** de calendario en una sola pasada; la validación previa de ComfyUI fue sesión aparte.
