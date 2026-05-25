---
Title: Tuqan — plan de migración PHP 8, entorno Docker y arnés de pruebas
Description: Tras fusionar el plan ejecutable en el repo Tuqan (PR #44): desarrollo 100% en Docker, PHP 8.3, PHPUnit en contenedor y roadmap en ocho etapas con checklists para agentes.
Date: 2026-05-25 11:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad
Lang: es
Translation_Key: tuqan-php8-docker-migration-plan
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 2
Image: /assets/images/tuqan-comfyui-sdxl-php8-docker-migration-plan-hero.webp

---

# Tuqan — de la auditoría al plan ejecutable

En [Phase 0](https://blog.praderas.org/blog/tuqan-phase-0-strategic-foundation-audit-and-roadmap) dejamos claro el contexto: Tuqan es una aplicación legacy ISO 9001/14001 que hoy **no arranca** y necesita evolución sin tirar la lógica de negocio. Hoy documentamos el siguiente hito en el **repositorio de la aplicación**, no en este blog como sustituto del código.

## Qué se fusionó en Tuqan (PR #44)

El equipo cerró en `master` un paquete de documentación **accionable** para humanos y agentes:

| Documento | Rol |
|-----------|-----|
| `AGENTS.md` | Reglas operativas estrictas (solo Docker, doc-first, checklists) |
| `MIGRATION-PLAN.md` | Auditoría del estado actual + plan 5.1/5.2/5.3 y roadmap de **8 etapas** |
| `DOCKER-ENV.md` | Dockerfile, compose, nginx y flujos listos para copiar |
| `TESTING-HARNESS.md` | PHPUnit (y PHPStan) **dentro** de Docker |
| `STAGE-CHECKLISTS.md` | Listas por etapa con comandos de validación |

Referencia: [github.com/laanito/tuqan/pull/44](https://github.com/laanito/tuqan/pull/44) — *docs(agents): PHP 8 + Docker-only dev environment + testing migration plan*.

## Por qué Docker-only y PHP 8.3

Tuqan nació en PHP 5.1 con PEAR; después hubo parches (Composer, PSR-4, Phroute, PDO…). Mezclar PHP del host con esa historia **reproduce bugs que no existen en producción** y hace imposible auditar de forma repetible.

**Decisión:** el entorno de desarrollo y las pruebas automatizadas corren **solo en contenedores** (PHP **8.3**, nginx, PostgreSQL). Nada de “funciona en mi máquina” con servicios locales sueltos: un `docker compose up` debe ser la puerta de entrada para cualquier agente o persona.

## Por qué pruebas antes de reescribir pantallas

La migración no empieza por maquetar Bootstrap otra vez. Empieza por **saber qué se rompe** cuando el intérprete sube de versión y cuando el árbol de dependencias se ordena. PHPUnit en Docker es el cinturón de seguridad para las etapas que toquen autenticación, informes ISO y rutas Phroute.

## Relación con este blog

- **Aquí:** hitos, decisiones y enlaces (transparencia Praderas).
- **En github.com/laanito/tuqan:** informes, checklists y el plan que los agentes deben seguir al pie de la letra.

## Próximo paso en Tuqan

**Etapa 1 — fundación Docker** en una rama feature: levantar el stack del `DOCKER-ENV.md`, verificar que el contenedor PHP responde y dejar anotado el baseline en `STAGE-CHECKLISTS.md`. Volveremos con un post cuando esa etapa cierre con evidencia (comandos y aprendizajes, no solo ticks).
