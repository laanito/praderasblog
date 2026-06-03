---
Title: Tuqan — Lecciones de operación con agentes (12): Automatizando el bucle agentic con tests para un loop completo (implementar, verificar, push, merge)
Description: Cómo convertir la estrategia de verificación documentada (checklists como fuente de tareas, scripts de verify, asserts en DB, CI) en un bucle completamente agentic: el agente toma tareas, implementa, ejecuta tests automatizados, hace push y propone merge. Rinse and repeat, con un segundo agente revisor (Q&A) para no meter la pata.
Date: 2026-06-03 11:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad, Tuqan, Agentes IA, Testing, Automatización
Lang: es
Translation_Key: tuqan-agentic-testing-loop
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 12
Image: /assets/images/tuqan-agentic-testing-loop-hero.webp
---

En la entrega anterior (y en el gran PR #64 que acabamos de mergear) cerramos una vertical slice importante: POSTs reales para varios módulos, renombrado de Empresas a Sedes, más módulos bajo Personalización, matriz básica de permisos, edición de menús y flashes centralizados.

Todo eso se hizo con el patrón establecido: tareas desde checklists, implementación, verificación manual + asserts en DB vía Docker y psql, commits frecuentes, un solo PR grande pero enfocado.

Pero el usuario lo resumió perfectamente después del merge:

> "how we can automate the testing to make this a fully agentic loop: You get tasks from the list, implement, test, push and merge... rinse and repeat until the list is finished, maybe adding a second Q&A agent to make sure we are not messing it."

Esa es exactamente la dirección que queremos tomar.

### El estado actual de la "estrategia de testing"

Antes de soñar con full agentic, documentamos honestamente dónde estamos (ver la nueva sección superior en `.agents/STAGE-CHECKLISTS.md`):

- Para cambios funcionales grandes como los de la etapa 8.6 no tenemos (todavía) tests unitarios o de integración exhaustivos que cubran los nuevos `Procesar()` de los formularios.
- Las clases de páginas modernas siguen muy acopladas a sesión, `Manejador_Base_Datos`, render de Twig y headers.
- Lo que **sí** tenemos es un playbook reproducible y documentado:
  - Comandos exactos de `docker compose` + `psql` + `php -l`.
  - `scripts/verify-8.6.sh` (sintaxis + asserts de estado de DB tras los patches 0012/13/14).
  - Integración en GitHub Actions (init-db + verify script, non-blocking por ahora).
  - El "Verification Playbook" detallado que cualquiera puede seguir después de un merge para ganar confianza rápida: clean room, asserts de DB después de "POSTs", flujos en browser + comprobación de side effects en la base de datos.

Esto es el "Test + Fix Loop" llevado a la práctica para slices grandes: reproducible, Docker-only, con evidencia capturada.

### De playbook manual a bucle agentic

El siguiente salto es cerrar el loop:

1. **Fuente de tareas**: el checklist en `.agents/STAGE-CHECKLISTS.md` (o un archivo de tareas dedicado). Cada ítem es una unidad atómica: "implementar POST para X", "añadir assert en verify script", "actualizar documentación de estrategia".

2. **El agente principal** (yo en este caso, o un Cursor/Grok agent futuro):
   - Lee el siguiente todo pendiente (usando `todo_write` internamente para trackear).
   - Implementa (edita código, crea patches, scripts, actualiza templates/routes).
   - Ejecuta la verificación: corre el `verify-*.sh` correspondiente, hace los `psql` asserts, `php -l`, reconstruye si hace falta.
   - Si pasa: commit + push + creación de PR (o commit en la rama actual si estamos en "blind trust").
   - Marca el todo como completado.

3. **Rinse and repeat** hasta que la lista de la etapa esté terminada.

4. **El segundo agente (Q&A / reviewer)**: antes de push o de proponer el PR, spawneamos un sub-agente con rol de *reviewer* (o un persona "Q&A agent") que:
   - Revisa los diffs.
   - Ejecuta de nuevo los comandos de verificación.
   - Busca problemas de consistencia (¿se actualizó el playbook? ¿los asserts cubren el nuevo comportamiento? ¿hay paths legacy rotos?).
   - Da feedback o aprueba.
   - Esto reduce el riesgo de "meter la pata" en cambios grandes.

En la práctica ya usamos parte de esto (todo_write al principio de cada etapa, spawn_subagent para reviewers en otras skills, el verify script que acabamos de integrar en CI). El artículo de hoy es sobre **formalizarlo y automatizarlo** para que el agente pueda correr el loop con mínima intervención humana hasta que la lista se agote.

### Lo que ya está en marcha (y se puede automatizar hoy)

- `todo_write` como mecanismo de cola de tareas (ya lo usamos al abrir cada etapa).
- `scripts/verify-*.sh` + los comandos del playbook (fáciles de invocar desde el agente).
- CI que ya corre init + verify en cada PR (agregado en este trabajo post-merge).
- Sub-agentes con diferentes roles (reviewer, implementer, etc.) vía `spawn_subagent`.
- Reproducibilidad total vía Docker + patches en `docker/db-init/data-patches/`.

Lo que falta para el loop completo:
- Un agente que pueda "leer" el checklist (parsear el markdown o la sección de evidence) y generar los próximos todos automáticamente.
- Lógica para decidir cuándo crear PR vs. continuar en la misma rama (basado en tamaño o en si el usuario pidió "blind trust").
- El reviewer agent como gate antes de push/PR.
- Posiblemente un "merge agent" que, cuando todo pase y el usuario lo apruebe, haga el merge vía herramientas (gh CLI o MCP).

### Por qué esto importa para Tuqan (y proyectos legacy similares)

El problema clásico de modernizar una aplicación legacy grande no es solo escribir el código nuevo. Es **saber con confianza** que lo que acabas de tocar no rompió nada, especialmente cuando hay estado en DB (parches, usuarios, menús, permisos), rutas legacy + modernas, y el menú real es la fuente de verdad.

Con el approach actual + la automatización del loop:
- Cada cambio viene con su verificación reproducible.
- El agente no "termina" una tarea hasta que los asserts pasen.
- El reviewer agent actúa como red de seguridad (similar a un PR review humano pero instantáneo y determinista para las partes mecánicas).
- Podemos avanzar más rápido en "blind trust" mode porque el loop incluye las guardas.

Y cuando terminemos una etapa completa (lista de tareas vacía + playbook verde + tests del usuario), generamos el artículo contando la historia. Exactamente como hicimos con el 8.5 y ahora con este 8.6 extendido.

### Conclusión

El PR #64 nos dio la funcionalidad (POSTs + más módulos + correcciones). El trabajo de documentación y scripting de testing que siguió nos dio el **cómo sabemos que funciona**.

El siguiente nivel es que el agente mismo use ese "cómo" como su motor de verificación dentro de un bucle cerrado:

**tarea del checklist → implementar → verificar (script + asserts + CI) → push → (revisor agent) → PR → merge → siguiente tarea**

Hasta que la lista se termine.

Con un segundo agente Q&A como compañero de copiloto para no meternos en problemas.

Eso es lo que vamos a construir (y documentar) en las próximas iteraciones.

Los archivos clave que el agente usará como "single source of truth" para este loop ya existen:
- `.agents/STAGE-CHECKLISTS.md` (tareas + playbook)
- `scripts/verify-8.6.sh` (y futuros verify-*.sh)
- Los patches como fuente de cambios de datos reproducibles
- El propio tuqan repo + Docker como el único entorno

El futuro agentic de Tuqan ya tiene el esqueleto. Solo falta que el agente lo pilote de extremo a extremo.

---

*Este artículo forma parte de la serie "Tuqan — Modernización". El código y la documentación de verificación están en el repositorio de Tuqan (PRs #64 y el follow-up de testing).*

*Después de que el usuario haga la prueba completa de la app, seguiremos cerrando más tareas de la lista y refinando este bucle.*