---
Title: Reviviendo Praderas (Día 13) — batch 5 (productividad y colaboración) y métricas de reloj
Description: Seis guías ES→EN (teletrabajo, Etherpad, Redmine, Taskwarrior, Focalboard, Nextcloud+Deck), corrección menor en ES, y comparación de reloj (~12 min) frente a orden de magnitud sin IA.
Date: 2026-05-04 14:40PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 13
Lang: es
Translation_Key: praderas-day-13-batch-5-productivity-log
Image: /assets/images/day13-comfyui-sdxl-batch5-productivity-tools-hero.webp

---

# Reviviendo Praderas (Día 13) — qué se hizo y por qué

Bitácora del **batch 5** del plan de migración (cluster **productividad y colaboración**): seis pares de posts técnicos en inglés, alineados con el tipo de contenido que ya publicábamos en español sobre herramientas libres y teletrabajo.

## Reloj de pared (esta PR)

- **Inicio de la sesión de trabajo (referencia):** `2026-05-04 14:28:19 CEST`
- **Justo antes de commit + push (referencia):** `2026-05-04 14:39:58 CEST`

Entre las dos marcas hay del orden de **~12 minutos** de calendario humano (dirección, traducciones, front matter, tracker `.agents`, rama y push). No confundir con “tiempo de máquina”: lo que importa para el lector es **cuánto reloj ocupó el flujo** con dirección explícita.

## Cambios publicados (resumen)

1. **Seis pares ES→EN** — `praderas-b5-remote-work-tips`, `…-etherpad-guide`, `…-redmine-guide`, `…-taskwarrior-guide`, `…-focalboard-guide`, `…-nextcloud-deck` (teletrabajo + stack de productividad / self-host).
2. **Correcciones menores en ES** — título de sección “Distracciones” (antes “Distorsiones”) en teletrabajo; **cierre de frase** en el artículo de Focalboard (el Markdown estaba cortado a mitad de línea).
3. **`.agents`** — tracker: batch 5 marcado como hecho para este slice, filas nuevas, changelog y vocabulario breve donde aplica.

## Por qué este lote

- **Coherencia temática** — Guías de herramientas y hábitos remotos comparten lector y vocabulario (“tablero”, “sincronización”, “self-host”); mezclarlas con, por ejemplo, solo móvil o solo crypto habría dispersado el glosario.
- **Tamaño de PR** — Seis posts largos con comandos ya es un paquete revisable; el resto del batch 5 “amplio” (Emacs, Taskwarrior-adjacent, otras guías) puede ir en **5b** si hace falta dividir.

## Comparación con un flujo sin IA (orden de magnitud)

| Bloque | Estimación indicativa (senior redacción técnica EN + revisión) |
|--------|------------------------------------------------------------------|
| Seis artículos de guía + consistencia de términos | **12–28 h** |
| Front matter, claves, comprobación de tags | **1.5–3 h** |
| Tracker + changelog + rama/PR | **1–2 h** |
| **Total** | **~14.5–33 h** repartidos en varios días |

La ventaja del flujo asistido aquí es el **ratio** entre esa banda y **~12 min** de reloj para la misma entrega estructural, con la salvedad habitual: **criterio, merge y responsabilidad** siguen siendo humanos; el asistente acelera borrador y trabajo mecánico.
