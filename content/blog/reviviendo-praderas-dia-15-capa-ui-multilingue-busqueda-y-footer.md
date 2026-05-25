---
Title: Reviviendo Praderas (Día 15) — capa UI multilingüe: búsqueda EN y footer compartido
Description: Cierre del bloque UI programado (ruta `/en/search`, resultados por idioma y footer i18n en layouts no-post) con reloj real de pared (~1 min 40 s) y comparación frente a ejecución senior tradicional.
Date: 2026-05-06 09:15AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 15
Lang: es
Translation_Key: praderas-day-15-ui-search-footer-log
Image: /assets/images/day15-comfyui-sdxl-day15-search-footer-ui-hero.webp

---

# Reviviendo Praderas (Día 15) — cierre de la cola UI prevista

Tras cerrar el backlog de posts ES/EN en el Día 14, hoy tocaba la cola **UI multilingüe** más prioritaria del tracker: búsqueda en inglés y consistencia de footer fuera de `post.twig`.

## Reloj de pared (solo implementación UI programada)

- **Inicio (referencia):** `2026-05-06 09:12:46 CEST`  
- **Fin (referencia):** `2026-05-06 09:14:26 CEST`

Ventana medida: **~1 min 40 s** de calendario para rama desde `main`, implementación y ajuste del ledger técnico.  
Como en notas previas, este reloj **no** incluye redactar esta bitácora, commit ni push.

## Qué se cerró en esta pasada

1. **Ruta de búsqueda emparejada ES/EN**: `content/search.md` ↔ `content/en/search.md` con `Translation_Key: praderas-nav-search`.
2. **Plantilla de búsqueda bilingüe** (`search.twig`): breadcrumbs, encabezados y CTA adaptados por `content_lang`.
3. **Comportamiento del buscador** (`search-behavior.twig`): redirección por idioma (`/search/<q>` o `/en/search/<q>`).
4. **Regla de resultados por idioma** (`plugins/40-PicoSearch.php`): el listado respeta la lengua de la página actual (`Multilingual::inferLang`).
5. **Footer i18n en layouts no-post** (`index.twig`): crédito EN/ES en home, tags, categorías, archivo y búsqueda.

## Comparación con un flujo senior “sin IA” (orden de magnitud)

Para este mismo alcance (ruta EN, ajustes Twig, regla en plugin, verificación cruzada y documentación mínima de tracker), un flujo clásico de un perfil senior suele estar en algo como:

- **Implementación y validación técnica:** ~1,0–2,5 h  
- **QA funcional + revisión de copy i18n:** ~0,5–1,5 h  
- **Actualización de ledger/backlog y preparación de PR:** ~0,5–1,0 h  
- **Total orientativo:** **~2,0–5,0 h**

No es una ley: depende del grado de revisión editorial y de la familiaridad con Pico/Twig. La comparación busca mantener un **orden de magnitud honesto** frente al reloj real de esta sesión.

## Qué queda en cola UI

Pendiente principal: **robots/sitemap por idioma** (`PicoRobots` + plantillas sitemap), que sigue abierto en `multilingual-ui-backlog.md`.
