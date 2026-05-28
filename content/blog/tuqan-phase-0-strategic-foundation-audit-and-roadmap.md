---
Title: Tuqan Phase 0 — Fundación estratégica, auditoría y roadmap inicial
Description: Arranque de la modernización agentic de Tuqan con documentación viva, plan de auditoría y roadmap priorizado. Primer artículo de la serie Tuqan.
Date: 2026-05-06 10:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas
Lang: es
Translation_Key: tuqan-phase-0-strategic-foundation
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 1
Image: /assets/images/tuqan-phase-0-strategic-foundation-hero.webp

---

# Tuqan Phase 0 — fundación estratégica, auditoría y roadmap inicial

**«Hay proyectos que no necesitan nacer de cero: necesitan una segunda vida.»**

Marcamos el inicio de **Phase 0** de la modernización de Tuqan, como segunda gran línea de trabajo dentro del ecosistema Praderas.

## Contexto del proyecto

Tuqan es una aplicación legacy de gestión ISO 9001 / ISO 14001 (origen ~2005): PHP 5.1 y PEAR junto con modernizaciones parciales (Composer, PSR-4, Phroute, Bootstrap 5, PDO, etc.). En su estado actual la aplicación **no es funcional**.

El objetivo no es reescribirla desde cero, sino **evolucionarla preservando la lógica de negocio** y aplicando estándares actuales.

## Enfoque agentic y «documentation first»

- Documentación orientada a agentes en `.agents/`, siguiendo el mismo patrón que ya describimos en este blog (por ejemplo `repo-context.md`, `proposed-improvements.md`, `phase-5-6-plan.md`).
- Cada sesión en una rama nueva; cambios revisados mediante PR.
- Sin código de aplicación hasta tener cerrados el alcance y el roadmap de Phase 0.

## Este blog frente al repositorio de Tuqan

Los entregables específicos de auditoría Tuqan (informes, checklist y roadmap técnico detallado) viven en el **repositorio de la aplicación**. Aquí publicamos **hitos** y decisiones en formato artículo, en línea con la política de transparencia del proyecto.

## Fricciones con herramientas (GitHub / agentes)

Durante la preparación aparecieron limitaciones habituales de integraciones (respuestas JSON incompletas, desajustes de SHA al actualizar archivos, necesidad de pushes secuenciales). Lo contamos tal cual: el flujo agentic aún tiene roce operativo.

## Plan de auditoría (Phase 0)

- Estructura de carpetas y archivos legacy.
- Dependencias Composer y paquetes obsoletos.
- Separación (o mezcla) de lógica de negocio y presentación.
- Superficie de seguridad (restos PEAR, prácticas PHP antiguas).
- Estado real de funcionalidad.

## Roadmap de alto nivel

- **Phase 0:** Fundación estratégica y auditoría (actual).
- **Phase 1:** Limpieza de dependencias y cumplimiento PSR.
- **Phase 2:** Modernización de arquitectura (routing, DI, plantillas).
- **Phase 3:** Extracción de lógica de negocio y pruebas.
- **Phase 4:** Renovación UI/UX y móvil.
- **Phase 5:** Despliegue y monitorización.

## Próximos pasos

Integrar esta entrada en la ruta canónica del blog, ejecutar la auditoría en el repo Tuqan y volver con hallazgos en un siguiente post.
