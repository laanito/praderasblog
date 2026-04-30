---
Title: Reviviendo Praderas (Día 2) — Fase 1: arreglamos el listado, la búsqueda y la paginación (con un agente de IA)
Description: Fase 1: listado, búsqueda, paginación, bloque de artículos recientes, y un repaso con feedback humano sobre el estilo del lateral. Proceso apoyado en agentes de IA, revisión y despliegue a cargo de humanos.
Date: 2026-04-23 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 2
Lang: es
Translation_Key: praderas-day-2-phase-1-listing-search-pagination
---

# Reviviendo Praderas (Día 2) — Fase 1: listado, búsqueda, paginación (con IA de copiloto)

Ayer dejamos el mapa. Hoy bajamos al barro, pero con guantes: **Fase 1** del plan en `.agents` — arreglar la plantilla del listado, unificar el buscador, enseñar de nuevo la paginación y sustituir el *widget* lateral que era puro relleno de demostración.

Y sí, otra vez con la misma tesis: **un agente de IA puede ejecutar el plan con criterio** si le das contexto en el repo, prioridades claras, y luego un humano revisa y despliega. No es magia; es iteración con menos fricción.

## Qué estaba roto (o flojo) y lo notabas al inspeccionar

En el listado del blog, el HTML se “escapaba” hacia el final: restos de script y hasta un segundo `<!DOCTYPE` colándose en la página. Eso no es solo feo: es señal de que la plantilla estaba **corrupta o truncada** y el navegador hacía lo que podía.

Mientras tanto, en el lateral, el buscador del listado no compartía el mismo *wiring* que en otras pantallas, y el bloque “Side Widget” seguía diciéndote que “puedes poner lo que quieras” — genial en un tutorial de Bootstrap, poco serio en un blog que vuelve a publicar.

## Qué hicimos en Fase 1 (resumen operativo)

1. **Reconstruimos** `blog.twig` con un cierre de documento limpio: sin basura al final, sin sorpresas al ver el código fuente.
2. **Unificamos** el buscador: mismos `id` (`search_input` / `search_submit`) y el mismo script de comportamiento en un parcial, para que **clic y tecla Enter** te lleven a la ruta canónica de búsqueda.
3. **Volvimos a mostrar** la paginación en `/blog` usando lo que el plugin de paginación ya exponía, con etiquetas en castellano (“Entradas anteriores / recientes”) y estado de “página X de Y”.
4. **Sustituimos** el placeholder del lateral por un bloque de **artículos recientes** (las cinco entradas más nuevas bajo `blog/`), alineado con el espíritu de “ayudar a leer en dos clics”.
5. **Centralizamos** el lateral en un `sidebar.twig` reutilizable para el layout base, entradas y listado, para no volver a divergir en silencio.

También dejamos anotado en `config/config.yml` el texto que el plugin de paginación puede usar por si en el futuro alguien consume sus cadenas directamente.

## El bloque de “Artículos recientes” y el feedback de carne y hueso

Esto merece un párrafo aparte, porque **aquí se notó** que un agente puede cerrar tareas técnicas y, aun así, **fallar de forma sutil en el detalle** que tú sientes con el cursor encima: la primera versión del bloque hacía bien su trabajo, pero con enlaces *tan* básicos que daban tristeza. No era un bug: era poca capa de presentación.

Bajé feedback humano, un segundo PR afinó el markup (lista tipo “list group” y un poco de CSS aislado) para que tuviera jerarquía, hover y foco decente. Al día de hoy la valoración en casa es honesta: **está bastante mejor, no lo llamaríamos un diseño redondo, y asumimos vivir con ello** hasta que tengamos ganas de otra vuelta de mimo visual. A veces el plan es “*good enough* y seguir.”

Eso queda anotado también en `.agents` para que un agente futuro no tome un “*mejor*” por “*hecho para siempre*”.

## El meta-hilo: por qué documentar el proceso

Cada mejora que toca plantillas y plugins en un sitio *flat-file* es fácil de reventar en el siguiente rediseño si no dejas **rastro para el siguiente par** (humano o agente). Por eso actualizamos el contexto en `.agents` con lo hecho en Fase 1: el próximo “tú” —en una semana o en un PR— sabe qué tocar y qué no.

## Qué sigue (sin spoilear demasiado)

Fase 2 apunta a navegación e información: categorías con más mimo, *related posts*, *breadcrumbs*… Hoy hemos dejado la escena lista para eso: listado sano, búsqueda fiable, paginación visible, lateral útil.

Si lees esto y el sitio se siente un poco más “de verdad” que ayer, la Fase 1 ya cumplió. Lo demás es seguir con método —y seguir contándolo.
