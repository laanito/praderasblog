---
Title: Reviviendo Praderas (Día 11) — batch 3 (ciberseguridad y privacidad), etiquetas EN y Acerca en inglés
Description: Cierre del lote “seguridad + privacidad + geolocalización”, capa de nombres EN para etiquetas sin romper el vocabulario YAML, /en/tags y /en/about-picocms, y comparación de tiempos (reloj vs orden de magnitud senior).
Date: 2026-05-02 12:15PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 11
Lang: es
Translation_Key: praderas-day-11-batch-3-security-ui
Image: /assets/images/day11-comfyui-sdxl-batch3-security-privacy-ui-hero.webp

---

# Reviviendo Praderas (Día 11) — qué se hizo y por qué

Bitácora del trabajo publicado en una sola PR: **traducción batch 3**, **superficie UI pendiente** (etiquetas, lateral, Acerca de Pico), y **documentación para agentes** sobre lo que queda fuera de este corte.

## Reloj de pared (honestidad metodológica, como en el Día 9)

- **Inicio de la sesión de trabajo (referencia):** `2026-05-02 11:47:03 CEST`
- **Antes de commit + push (referencia):** `2026-05-02 11:59:18 CEST`

La métrica útil para el lector es **cuánto calendario ocupó el flujo humano** (dirección, revisión, merge), no un “tiempo de CPU” del modelo.

## Cambios publicados (resumen)

1. **Batch 3 de migración ES→EN** — Seis pares nuevos (`Translation_Key` `praderas-b3-cs-intro`, `…-digital-world`, `…-cs-advanced`, `…-internet-not-safe-ii`, `…-social-privacy`, `…-geolocation`) alineados con el plan por **clúster temático** (ciberseguridad, privacidad, geolocalización como pieza de privacidad técnica).
2. **Etiquetas “traducibles” sin reescribir el archivo** — Las etiquetas siguen siendo el **vocabulario canónico en español** en el front matter (compatibilidad con scripts y taxonomía); en UI EN mostramos **etiquetas legibles en inglés** vía mapa en `65-Multilingual.php`, mientras los enlaces `?tag=` conservan la clave española.
3. **`/en/tags`** — Hub de etiquetas emparejado con la página española (`Translation_Key: praderas-nav-tags`); la plantilla filtra entradas **solo del idioma activo** para no mezclar corpus en las vistas filtradas.
4. **`/en/about-picocms`** — Par con `acerca-de-picocms`; el menú EN deja de mandar “About” al artículo solo en español.
5. **Plantillas** — `sidebar.twig`, `post.twig`, `categories.twig`, `tags.twig`, `breadcrumbs.twig` y pie de `post.twig` alineados con `content_lang` donde faltaba cobertura.
6. **`.agents`** — `multilingual-ui-backlog.md` con trabajo pendiente explícito (búsqueda, archivo cronológico EN, etc.) y actualización del tracker.

## Por qué estas decisiones

- **Lote por clúster** — Mantiene coherencia de lectura y reduce deriva de vocabulario entre artículos relacionados (misma regla que batch 1 y 2).
- **Mapa de etiquetas EN en el plugin** — Cambiar las claves en cientos de entradas rompería auditorías y enlaces históricos; una **capa de presentación** es el compromiso más barato y reversible.
- **Tags filtrados por idioma** — Un lector EN en `/en/tags?tag=…` espera ver **posts EN**; mezclar todo el archivo sería incoherente con `/en/categorias`.

## Comparación con un senior “a mano” (orden de magnitud, no presupuesto)

Orden de magnitud razonable para un **senior full-stack + redacción técnica en inglés**, sin asistente, misma entrega:

| Bloque | Estimación indicativa |
|--------|-------------------------|
| Seis traducciones de posts + revisión de tono y consistencia | **8–18 h** |
| Twig/PHP (tags EN, sidebar, categorías, post, migas, pie) + pruebas manuales | **3–7 h** |
| Pareja About + navegación + `hreflang` | **1–2 h** |
| Entrada de bitácora ES/EN + tracker + doc `.agents` | **1.5–3 h** |
| **Total** | **~13.5–30 h** repartidas en varios días |

Ventaja observada del flujo asistido: **~12 minutos de reloj** entre las dos marcas de tiempo anteriores para una entrega del mismo alcance funcional, con la salvedad habitual: la **responsabilidad editorial y legal** sigue siendo humana; el asistente acelera borrador + cambios mecánicos.
