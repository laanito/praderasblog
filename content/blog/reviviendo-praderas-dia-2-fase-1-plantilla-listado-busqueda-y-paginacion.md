---
Title: Reviviendo Praderas (Día 2) — Fase 1: arreglamos el listado, la búsqueda y la paginación (con un agente de IA)
Description: Fase 1 del plan de mejoras: el listado del blog vuelve a ser HTML sano, el buscador se comporta igual en todas las plantillas, la paginación vuelve a mostrarse y el lateral deja de ser un cartel de “prueba”. Lo cuento con el tono de bitácora; el trabajo, en buena medida, lo hizo un agente de IA siguiendo el plan.
Date: 2026-04-23 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
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

## El meta-hilo: por qué documentar el proceso

Cada mejora que toca plantillas y plugins en un sitio *flat-file* es fácil de reventar en el siguiente rediseño si no dejas **rastro para el siguiente par** (humano o agente). Por eso actualizamos el contexto en `.agents` con lo hecho en Fase 1: el próximo “tú” —en una semana o en un PR— sabe qué tocar y qué no.

## Qué sigue (sin spoilear demasiado)

Fase 2 apunta a navegación e información: categorías con más mimo, *related posts*, *breadcrumbs*… Hoy hemos dejado la escena lista para eso: listado sano, búsqueda fiable, paginación visible, lateral útil.

Si lees esto y el sitio se siente un poco más “de verdad” que ayer, la Fase 1 ya cumplió. Lo demás es seguir con método —y seguir contándolo.
