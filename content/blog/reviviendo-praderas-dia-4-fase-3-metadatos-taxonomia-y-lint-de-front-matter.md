---
Title: Reviviendo Praderas (Día 4) — Fase 3: metadatos, taxonomía y lint de front matter (sí, tocaba ordenar la casa)
Description: Fase 3 en marcha: rellenamos tags faltantes, normalizamos formato de fecha, fijamos taxonomía canónica y añadimos un script de auditoría para que futuros cambios no rompan consistencia editorial.
Date: 2026-04-25 10:40AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 4
Lang: es
Translation_Key: praderas-day-4-phase-3-metadata-taxonomy-frontmatter-lint
---

# Reviviendo Praderas (Día 4) — Fase 3: ordenar metadatos sin llorar

Hay días de “feature shiny” y hay días de **fregar la cocina**. Hoy tocaba lo segundo, y era exactamente lo que pedía la **Fase 3**: consistencia editorial, no fuegos artificiales.

## Lo que encontramos al auditar

Antes de tocar nada, pasamos una revisión de front matter en `content/blog`:

- 13 posts sin `Tags` (o con `tags` en minúscula).
- Un caso de fecha en formato raro (`2023-08-14 9:00`) frente al resto.
- Taxonomía canónica definida, pero no forzada automáticamente.

Resultado: contenido útil, pero con deuda silenciosa que acaba apareciendo en filtros, SEO y navegación por categorías.

## Lo que hicimos en esta fase

1. **Completamos los `Tags` faltantes** en los 13 artículos pendientes, usando la taxonomía ya existente del blog.
2. **Normalizamos key casing** (`tags` -> `Tags`) donde hacía falta.
3. **Ajustamos el formato de fecha** fuera de estándar para que no quede otro caso suelto.
4. **Añadimos un script de auditoría** (`scripts/frontmatter_audit.py`) para validar:
   - campos obligatorios,
   - formato de fecha,
   - tags fuera del set canónico.
5. **Documentamos plantilla editorial** en `.agents/post-template.md` para que próximos agentes/humanos partan de la misma base.

## Por qué esto importa más de lo que parece

Cuando los metadatos son inconsistentes, el blog “parece funcionar”… hasta que no: filtros incompletos, bloques de relacionados pobres, problemas de indexación y decisiones editoriales a ciegas.

Con esta pasada, dejamos el contenido en una línea más robusta: **menos sorpresas, más predictibilidad**.

## Cierre del Día 4

Fase 3 no da titulares bonitos, pero evita dolores acumulados. Hoy no estrenamos pantalla nueva: estrenamos **disciplina de contenido**. Y eso, en un blog que quiere durar, vale oro.
