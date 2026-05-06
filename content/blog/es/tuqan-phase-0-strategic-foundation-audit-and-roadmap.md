---
Title: Tuqan Phase 0: Fundación Estratégica – Auditoría y Roadmap Inicial
Description: Iniciamos la modernización agentic de Tuqan con documentación viva, plan de auditoría completo y roadmap priorizado. Primer artículo de la serie.
Date: 2026-05-06 22:00
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas
Lang: es
Translation_Key: tuqan-phase-0-strategic-foundation
---

**“Hay proyectos que no necesitan nacer de cero: necesitan una segunda vida.”**

Hoy marcamos el inicio oficial de **Phase 0** de la modernización de Tuqan, la segunda gran fase del ecosistema Praderas.

### El contexto del proyecto
Tuqan es una aplicación legacy de gestión ISO 9001 / ISO 14001 nacida alrededor de 2005. Tiene código PHP 5.1 + PEAR mezclado con modernizaciones parciales (Composer, PSR-4, Phroute, Bootstrap 5, PDO, etc.). La aplicación actualmente no es funcional.

Nuestro objetivo no es reescribirla desde cero, sino **evolucionarla preservando la lógica de negocio** mientras aplicamos estándares modernos.

### El enfoque agentic y “documentation first”
Todo el trabajo se hace de forma transparente:
- Documentación primero en la carpeta `.agents/` (living documents).
- Cada sesión empieza en un branch nuevo.
- Cambios solo vía PRs.
- Cero código de aplicación hasta que el roadmap completo esté aprobado.

### Los living documents creados
- `.agents/grok-consultant-context.md`
- `.agents/repo-context.md`
- `.agents/phase-0-audit.md` (con plan detallado de auditoría)
- `.agents/proposed-improvements.md` (roadmap priorizado con riesgos y métricas)

### Los problemas reales con el GitHub connector
Durante este proceso hemos descubierto las limitaciones del conector de GitHub que uso:
- Errores frecuentes de "unexpected end of JSON input"
- Problemas de SHA mismatch al actualizar archivos existentes
- Necesidad de pushes uno por uno en lugar de batch

Esto ha obligado a hacer varios intentos manuales y a pausar temporalmente. Es una lección valiosa de transparencia: las herramientas agentic todavía tienen fricciones.

### Plan de auditoría (Phase 0)
El plan incluye una revisión completa de:
- Estructura de archivos y carpetas legacy
- Dependencias Composer y paquetes obsoletos
- Mezcla de lógica de negocio con presentación
- Problemas de seguridad (PEAR remnants, old PHP practices)
- Estado actual de funcionalidad

### Roadmap de alto nivel
Fases principales:
- **Phase 0**: Strategic Foundation & Audit (actual)
- **Phase 1**: Dependency Cleanup & PSR enforcement
- **Phase 2**: Architecture modernization (routing, DI, templates)
- **Phase 3**: Business logic extraction & testing
- **Phase 4**: UI/UX refresh + mobile
- **Phase 5**: Deployment & monitoring

**Próximos pasos**
- Mergear este artículo.
- Ejecutar la auditoría completa.
- Publicar hallazgos en el siguiente post.

Seguimos paso a paso, sin prisa, documentando todo.

*(Versión en inglés disponible con la misma Translation_Key)*
