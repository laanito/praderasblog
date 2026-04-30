---
Title: Reviviendo Praderas (Día 9) — migración de traducciones: plan por lotes, batch 1 completo y tiempos estimados
Description: Abrimos la migración editorial ES→EN con un plan en 8 lotes, completamos el batch 1 de la serie Reviviendo Praderas y dejamos una estimación transparente de tiempos frente a una localización tradicional.
Date: 2026-04-30 11:55AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 9
Lang: es
Translation_Key: praderas-day-9-translation-migration-batch-1
---

# Reviviendo Praderas (Día 9) — traducción con método: plan, batch 1 y números

Hoy abrimos formalmente la migración de contenidos en inglés con una regla simple: **coherencia antes que velocidad bruta**.

## Qué dejamos hecho hoy

1. **Plan de migración por lotes** en `.agents/translation-migration-tracker.md`, dividido en 8 batches para evitar desbordes de contexto.
2. **Batch 1 completado**: serie **Reviviendo Praderas** entera emparejada en EN (Día 1 al Día 8), sin dejar capítulos huérfanos.
3. **Glosario ampliado** con decisiones de nomenclatura recurrentes para mantener consistencia en navegación y tono.
4. **Metadatos de traducción** (`Lang` + `Translation_Key`) reforzados en los capítulos del Día 1 al Día 7 para que el conmutador y `hreflang` funcionen con pares completos.

## Por qué esta forma de trabajar

Traducir "a trozos" páginas sueltas suele romper la experiencia: serie a medias, naming cambiante y decisiones de estilo inconsistentes.

Con lotes temáticos y glosario vivo:

- el lector no cae en capítulos mezclados a mitad de recorrido,
- los términos se repiten de forma estable,
- el coste de revisión baja en cada iteración.

## Estimación de tiempos (IA + flujo PR vs localización tradicional)

Estimación razonable para **lo hecho hoy** (plan + batch 1 + artículo de cierre + PR):

- **Tiempo con flujo asistido por IA (este trabajo):** ~**1.5 a 2.5 horas** de calendario.
- **Tiempo estimado con especialista de localización + circuito editorial clásico:** ~**8 a 14 horas** para un entregable equivalente (misma cobertura, consistencia terminológica y control de metadatos).

No es una comparación universal; depende de contexto, herramientas y nivel de revisión exigido. Pero como orden de magnitud en este repositorio, la compresión de tiempo es real cuando el contexto ya está bien documentado.

## Qué sigue

El siguiente bloque natural es **Batch 2**: la serie **Control de Tiempo Desacoplado** (13 capítulos), manteniendo la misma regla de no dejar medias series.
