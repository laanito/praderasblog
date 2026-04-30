---
Title: Reviviendo Praderas (Día 3) — Fase 2: navegación, categorías, migas y posts relacionados (en equipo con un agente de IA)
Description: Cómo pasamos de un listado de páginas a una navegación fija, una página de categorías con conteos, migas de pan, entradas relacionadas por tag y ancla anterior/siguiente en el tiempo, documentado mientras un agente de IA implementa y un humano decide.
Date: 2026-04-24 09:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 3
Lang: es
Translation_Key: praderas-day-3-phase-2-navigation-categories-breadcrumbs-related
---

# Reviviendo Praderas (Día 3) — Fase 2: que el sitio te diga dónde estás (con IA haciendo el pesado)

Fase 1 dejó el cimiento estable: listado, búsqueda, paginación, lateral con sentido. **Fase 2** toca otra liga: **encontrar cosas** sin chapotear en un menú infinito y, cuando entras a una entrada, entender *de qué va el blog* y a dónde saltar después.

Esto es trabajo de producto, no solo de CSS. Y, como en los días anteriores, la coreografía es la misma: **el agente de IA propone y ejecuta** sobre el repositorio; el humano valida, prioriza y pone en producción. Sin ese segundo paso, las cosas rechinan o quedan “correctas a medias”.

## Qué significaba la Fase 2 en términos concretos

1. **Menú principal a cuatro brazos** — Inicio, Blog, Categorías, Acerca. Nada de “cada .md con título pasa a la barra” : así evitamos que vuelva el ruido de mezclar *pages* poco frecuentes con lo que de verdad usas cada día.
2. **Página de categorías** — Un mapa: cada tag del blog, una descripción en castellano, conteo aproximado de entradas, enlace a la lista filtrada (`/tags/?tag=...`). La lista alfabética y el buscador del lateral no desaparecen: se complementan.
3. **Migaja de pan** en blog, búsqueda, etiquetas, entradas y (cuando aplica) pasando por Categorías : para que nunca tengas que *adivinar* el camino hacia arriba.
4. **Descubrimiento al final de cada post** (solo entradas bajo `blog/`) : **entradas relacionadas** (misma etiqueta) y un par **anterior / siguiente** ordenado por *tiempo* de publicación, no alfabéticamente por fichero, que eso sería fácil y poco lector.

Eso último implica un poco de lógica en el servidor, no solo plantillas, porque juntar y ordenar decenas de `.md` en pure Twig *se puede*, pero a costa de fragilidad y horas de debug.

## El truco técnico que lo hace mantenible

Añadimos un plugin mínimo (`50-BlogNeighbors.php` en `plugins/`) : calcula, para el artículo que estás leyendo, el vecino más viejo, el más reciente, la lista de relacionados, y (solo al renderizar el índice de categorías) un mapa *tag → número de posts*. Poco código, cero base de datos, fácil de reemplazar o extender luego (por ejemplo ajustes de relevancia en “relacionados”).

Eso encaja con la promesa de Pico: **plano, legible, sin magia** ; la magia está en que el *markdown* y las *etiquetas* sigan bien cuidados.

## Qué hacemos hoy y qué dejamos para otra tanda (sin dramatizar)

Hoy tocamos estructura y comprensión, no aún toda la *Priority 2* (unidad de idioma al 100 %, imágenes reales, tipografía de lectura larga). Cada fase paga su factura, y hoy se pagó la de **navegación e información** ; lo demás sigue en el backlog, en claro, en `proposed-improvements.md`.

## Cierre del Día 3

El blog, otra vez, deja de ser un conjunto de ficheros y empieza a leerse como un **lugar** : sabes dónde estás, a qué tema pertenece lo que lees, y a qué otra cosa *probablemente* quieres ir.

Mañana habrá cosas en las que pincelar y opinar, como siempre. Hoy, la Fase 2 ya hizo el trabajo sucio, con método y con bitácora.

### Actualización (mismo día, pincel final)

Aún el mismo día, retocamos la **página de inicio** (`content/index.md`): hacía falta quitar un relato antiguo centrado en *ChatGPT* como “socio digital” del lector, que ya no reflejaba el proyecto (hoy el foco es el **blog y el repositorio**, asistido cuando toca con **agentes y herramientas** tipo Cursor, con humano al mando). También dejamos anclados en **`.agents/phase-5-6-plan.md`** los borradores de **Fase 5 (multilingüe)** y **Fase 6 (JSON para agentes)** para no perder el hilo cuando toque abrir otro capítulo. No es glamour; es alinear discurso y pizarra.
