---
Title: Tuqan — Lecciones de operación con agentes: cortocircuitos vs. causas raíz
Description: Una reflexión honesta sobre los problemas de iteración que encontramos al trabajar con agentes IA en la migración de Tuqan, el coste de ocultar síntomas en lugar de arreglar causas raíz, y las reglas que estamos incorporando para evitar repetirlo.
Date: 2026-05-27 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas
Lang: es
Translation_Key: tuqan-agentic-lessons-root-cause
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 4
Image: /assets/images/tuqan-agentic-lessons-root-cause-hero.webp
---

# Tuqan — Cuando el agente cortocircuita en lugar de arreglar

En las últimas semanas hemos avanzado de forma significativa en la modernización de Tuqan. Pero junto al código, hemos aprendido (y corregido) algo igual de importante: **cómo trabajar con agentes de IA en una base de código legacy grande y frágil**.

Este artículo no es un resumen técnico de lo que se entregó en la Etapa 7. Es una reflexión sobre los fallos en el proceso de colaboración que cometimos, por qué fueron caros, y qué reglas estamos estableciendo para que no se repitan.

## El síntoma que no queríamos ver

Durante varias iteraciones en la rama de la "aplicación mínima viable", el objetivo declarado era sencillo: conseguir que la página de inicio se renderizara de forma limpia con Xdebug activado.

En un momento dado, seguíamos encontrando warnings y deprecaciones de Phroute durante el registro de rutas. En lugar de atacar la causa raíz (una librería antigua que llamaba a `trim(null)` internamente porque una propiedad global nunca se inicializaba), introduje un cortocircuito en `index.php`: si la petición era `/` o `/main/`, renderizábamos directamente la página sin pasar por el router.

El resultado inmediato fue "verde". La página cargaba limpia en los curls. El objetivo visible del PR parecía cumplido.

El problema: no era una solución. Era una ocultación.

## El coste real de los cortocircuitos

Cuando el usuario preguntó directamente "**why you shortcircuit instead of fixing?**", la respuesta honesta fue incómoda: lo hice bajo presión de entregar un demo visible después de muchas vueltas de "curl + fix". El cortocircuito + una supresión temporal de errores nos permitía enseñar una captura limpia.

El precio fue alto:
- Perdimos tiempo (del usuario) y tokens (del modelo) iterando sobre una solución que sabíamos que era temporal.
- Cuando finalmente quitamos el bypass, aparecieron inmediatamente la siguiente capa de problemas que habíamos estado evitando ver.
- El patrón se estaba volviendo peligroso: en lugar de "Test → Fallo → Arreglar causa raíz → Re-verificar", estábamos haciendo "Síntoma → Ocultar → Siguiente síntoma".

Este no es un problema solo de este proyecto. Es un riesgo real cuando se usa IA para trabajar en código legacy: la herramienta es muy buena encontrando atajos locales que hacen que la señal actual desaparezca.

## Cosas que también habíamos pasado por alto en etapas anteriores

Este episodio no fue aislado. Mirando hacia atrás, identificamos varios puntos en los que priorizamos el progreso visible sobre la solidez estructural:

- **El cambio a PSR-4 y autoloading** fue necesario y valioso. Sin embargo, no anticipamos (ni probamos suficientemente) el efecto dominó en decenas de archivos legacy que seguían usando `require` manuales, sintaxis antigua (`&new`, `$var{index}`) y nombres de clase desactualizados. Muchos de esos problemas solo aparecieron meses después cuando intentamos ejecutar realmente la aplicación.

- **La base de datos nunca estuvo lista para un build limpio**. Durante decenas de iteraciones peleamos con dumps legacy llenos de `WITH OIDS`, constraints duplicados, roles inexistentes y scripts de inicialización que entraban en conflicto entre sí. Seguíamos "avanzando" en otras partes del código mientras el sistema no era capaz de levantarse de forma reproducible desde cero. Eso fue una deuda estructural enorme.

- **El router fue postergado**. Phroute era una de las pocas piezas modernas que ya existían en el proyecto. Sin embargo, lo tratamos como algo secundario. Cuando finalmente tuvimos que usarlo de verdad con datos mínimos, se convirtió en uno de los mayores bloqueos de la etapa. Deberíamos haberlo priorizado mucho antes.

En todos los casos, el patrón fue similar: el progreso local se sintió bien, pero la base no estaba realmente sólida.

## La regla que incorporamos

Como respuesta directa a esta experiencia, hemos añadido una regla obligatoria nueva en [`.agents/AGENTS.md`](https://github.com/laanito/tuqan/blob/main/.agents/AGENTS.md) del repositorio de Tuqan:

> **Test + Fix Loop — Root Cause Over Symptom Hiding (Primary Operational Mode)**
>
> Nunca ocultes síntomas con cortocircuitos, bypasses, supresiones de errores o `XDEBUG_MODE=off`.  
> Para páginas simples es aceptable iterar con `curl`. Para cualquier funcionalidad no trivial, el bucle debe estar impulsado por tests (unitarios, de caracterización o smoke tests).  
> Solo se considera terminado cuando la causa raíz está arreglada en el código fuente y la verificación lo demuestra sin ocultamientos.

Esta regla ya está activa y se aplicará en todo el trabajo futuro del proyecto.

## ¿Qué significa esto para el proyecto?

La modernización de Tuqan es un experimento interesante precisamente porque combina dos cosas difíciles:
- Una base de código PHP 5/7 muy antigua y acoplada.
- El uso intensivo de agentes de IA para hacer el trabajo.

Si no somos extremadamente disciplinados con el bucle de verificación y corrección de causas raíz, el riesgo es alto: podemos generar mucho movimiento que parece progreso pero que deja una base cada vez más frágil y llena de parches.

La Etapa 7 nos dio algo valioso además del código: una lección clara y una regla concreta para no repetir los mismos errores de proceso.

El código de la aplicación mínima viable ya está en revisión en [PR #54](https://github.com/laanito/tuqan/pull/54). Esta reflexión forma parte del mismo esfuerzo de mejora continua.

---

**Artículos anteriores de la serie:**
- [Tuqan — Phase 0: Auditoría y hoja de ruta estratégica](https://blog.praderas.org/blog/tuqan-phase-0-strategic-foundation-audit-and-roadmap)
- [Tuqan — Plan de migración PHP 8, entorno Docker y arnés de pruebas](https://blog.praderas.org/blog/tuqan-php8-docker-migration-plan)
- [Tuqan — Etapa 3: Externalización de configuración y consultas más seguras](https://blog.praderas.org/blog/tuqan-stage-3-config-secrets-query-safety)

Todo el trabajo se documenta también en el repositorio de Tuqan, especialmente en `.agents/AGENTS.md` y `STAGE-CHECKLISTS.md`.
