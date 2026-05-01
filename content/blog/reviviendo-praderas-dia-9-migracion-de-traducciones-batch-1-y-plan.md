---
Title: Reviviendo Praderas (Día 9) — migración de traducciones: plan por lotes, batch 1 completo y tiempos estimados
Description: Plan de migración ES→EN en 8 lotes, por qué importa el “contexto” de la IA, línea temporal del plan y batch 1 de Reviviendo Praderas con tiempos reales de reloj (~20 min) frente a localización tradicional.
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

1. **Plan de migración por lotes** en `.agents/translation-migration-tracker.md`: **8 batches** (lotes) ordenados por tema y por series completas.
2. **Batch 1 completado**: serie **Reviviendo Praderas** emparejada en inglés del **Día 1 al Día 9** (incluido este cierre), sin dejar la serie a medias.
3. **Glosario ampliado** con decisiones de nomenclatura recurrentes para mantener consistencia en navegación y tono.
4. **Metadatos de traducción** (`Lang` + `Translation_Key`) reforzados en los capítulos del Día 1 al Día 7 para que el conmutador y `hreflang` funcionen con pares completos.

## Qué es el “contexto” cuando hablamos de IA (sin jerga innecesaria)

Mucha gente oye “contexto” en chats de IA y no sabe a qué se refiere. En la práctica es esto:

- El modelo **no tiene memoria mágica** de todo tu proyecto. Solo “ve” lo que entra en la **ventana de trabajo** de esa conversación o de esa tarea: instrucciones, archivos pegados, fragmentos del repo que el asistente lee, mensajes anteriores, etc.
- Esa ventana tiene un **límite de tamaño** (se mide en *tokens*, trozos de texto). Si metes demasiado de golpe —docenas de artículos largos a la vez— lo más antiguo o lo más periférico **cae fuera** o el sistema tiene que resumir; entonces bajan la **fidelidad** y suben los **errores de detalle** (nombres, enlaces, tono, coherencia entre capítulos).
- Por eso “**context overflow**” (desbordar el contexto) no es un meme técnico: es **perder calidad** porque la máquina literalmente deja de tener a mano todo el texto que creías que estaba “ahí”.

No es lo mismo que “contexto humano” (situación, intención). Aquí **contexto = material que cabe en la ventana** que el modelo puede usar en esa pasada.

## Por qué partimos la migración en lotes (batches)

Los motivos encajan con lo anterior y con el blog como producto:

1. **Límite de contexto** — traducir el archivo entero en un solo bloque forzaría a meter muchos `.md` a la vez; el modelo deja de “tener presente” el principio cuando va por el final. Los lotes acotan el trabajo por **PR revisable**.
2. **Serie completa en el mismo lote** — evita que un lector en inglés encuentre el Día 4 en inglés y el Día 5 solo en español; el orden narrativo se mantiene.
3. **Glosario y tono** — cada lote puede cerrar decisiones (“discoverability”, “Recent posts”, nombres de series) antes de abrir el siguiente tema.
4. **Revisiones humanas** — un PR gigante es difícil de revisar; varios PRs por lote mantienen trazabilidad.

Traducir solo páginas sueltas “donde pille” suele romper la experiencia: serie a medias, nombres cambiantes y estilo inconsistente. Los lotes temáticos y el glosario vivo atajan eso.

## Cuántos lotes hay y línea temporal (plan a abril de 2026)

Definimos **8 batches**. El detalle vive en el tracker del repo; aquí va la **línea temporal prevista** (fechas de cierre son objetivos de planificación; se irán tachando al cerrar cada PR):

| Lote | Contenido | Estado (abril 2026) |
|------|-----------|---------------------|
| **1** | *Reviviendo Praderas* (Día 1–9, serie completa) | **Hecho** — 30 abr |
| **2** | *Control de Tiempo Desacoplado* (13 capítulos, serie completa) | **Hecho** — 30 abr (PR batch 2) |
| **3** | Ciberseguridad, privacidad, geolocalización | Pendiente |
| **4** | IA, tendencias, salud/entretenimiento con IA | Pendiente |
| **5** | Productividad y herramientas (Emacs, Taskwarrior, colaboración, etc.) | Pendiente |
| **6** | Desarrollo móvil (guías, frameworks, UX, pruebas) | Pendiente |
| **7** | Cripto y blockchain | Pendiente |
| **8** | Cola larga (educación, sistemas, posts sueltos) | Pendiente |

Orden deliberado: primero las **series** que dan forma al sitio y a la reconstrucción; luego **clusters temáticos** para que el glosario y el tono no luchen entre sí.

## Estimación de tiempos (reloj humano vs localización tradicional)

Para **este batch** (plan + traducciones del lote 1 + artículo de cierre + PR), el tiempo **real de calendario del lado humano** fue del orden de **~20 minutos** (revisión, creación del PR, coordinación con el asistente). Eso **no** es “lo que tarda un modelo por dentro”: la IA no mide tiempo como tú; lo relevante es **cuánto te quitó el flujo a ti** en esa sesión.

Como referencia aparte, un **especialista de localización** más circuito editorial clásico para un paquete equivalente (misma cobertura, consistencia terminológica y metadatos) suele estar en un rango del orden de **8 a 14 horas** según profundidad de revisión y herramientas — no es una ley universal, sirve para situar órdenes de magnitud.

La lección para quien quiere **entender la IA** no es “la IA acaba en 20 minutos todo el archivo del blog”, sino que **acotar el trabajo** (lotes, repo documentado, series enteras) hace que el **contexto** siga siendo útil y que **tu** tiempo de calendario pueda ser muy bajo si el alcance del PR está bien acotado.

## Qué sigue

El **Batch 2** (serie **Control de Tiempo Desacoplado** + hubs `/en/series` y `/en/categorias`) quedó en un PR aparte. El siguiente bloque natural es **Batch 3**: cluster de **ciberseguridad, privacidad y geolocalización**.
