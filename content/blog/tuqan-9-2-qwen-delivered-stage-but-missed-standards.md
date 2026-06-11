---
Title: Tuqan — 9.2: Qwen + opencode entregó la etapa (los TODOs ayudaron), pero no sostuvo los estándares
Description: Incluso con un backlog fino y priorizado (MIGRATION-TODOS.md de la etapa 9.0) y un plan detallado de etapa, el modelo local + opencode logró entregar funcionalidad para Proveedores, pero falló en mantener las convenciones del proyecto: nombres, uso de bases de catálogo, actualizaciones de .agents/, rutas legacy y ritual de verificación. Lecciones sobre el valor de los TODOs estructurados vs. la disciplina de estándares acumulados.
Date: 2026-06-10
Template: post
Author: Luis Amigo (con fixes por Grok 4.3)
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial, Agentes, Tuqan
Lang: es
Translation_Key: tuqan-9-2-qwen-standards
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 15
Image: /assets/images/tuqan-9-2-qwen-delivered-stage-but-missed-standards-hero.webp
---

## Hero Image Prompt (ComfyUI via repo scripts)

A clean, professional software engineering office split by a glass wall. On one side, a glowing local AI agent (Qwen/opencode) happily hands over a "working" Proveedores module screen showing list and form with data. On the other side, a senior human developer points at red annotations: wrong class name "Form.php", full reimplementation bypassing CatalogFormulario base, missing 0020 data patch, no legacy routes, MIGRATION-TODOS not updated, no 9.2 playbook section. In the background, a large visible wall of the MIGRATION-TODOS.md checklist with the Proveedores item checked "delivered" in green, but many "standards" items still red. Mood: "functionality achieved, craftsmanship failed". Modern, detailed, with subtle Docker whale and git branch icons. Cinematic lighting.

---

En la entrega anterior el usuario resumió el problema de la etapa actual:

> "this is repetitive work, I don't think an article is worth today"

El trabajo de modernizar listados y formularios para los catálogos bajo Aplicación/Personalización se había vuelto mecánico. El usuario decidió probar cuánto han avanzado los modelos locales + herramientas agentic.

Usó **opencode** con **Qwen** para continuar el plan de la etapa 9.2 (Proveedores como primer vertical medio después de la lista de TODOs).

### Lo que los TODOs lograron (el lado positivo)

El MIGRATION-TODOS.md de la 9.0 + el plan detallado de 9.2 permitieron que el modelo entregara una etapa funcional:

- Listado y Form para Proveedores (con la columna extra 'telefono').
- Plantillas creadas.
- Documento de plan presente.
- Nombrado de rama correcto.

Los TODOs finos y priorizados "demostraron" que Qwen pudo entregar la etapa. Comparado con el desastre anterior de opencode_mess, el backlog estructurado elevó el piso.

### Lo que falló (el incumplimiento de estándares)

A pesar de las guías claras, el modelo no sostuvo los estándares acumulados del proyecto:

- **Nombres**: Usó `Form.php` y `class Form` en lugar de `Formulario.php` / `class Formulario` (convención universal desde el inicio de la modernización).
- **Regresión de arquitectura**: El lado del Form ignoró completamente `CatalogFormulario` (el logro de 8.9) y reimplementó todo el boilerplate manualmente.
- **Proceso incompleto**:
  - Sin data patch para la tabla (0020 faltaba inicialmente).
  - Solo rutas modernas; sin mapeos legacy en index.php.
  - `MIGRATION-TODOS.md` no actualizado (checkbox sin marcar).
  - Sin sección 9.2 completa en STAGE-CHECKLISTS.md con playbook, comandos y gates.
  - verify-8.6.sh apenas extendido.
- **Deriva**: Plantillas y variables que no seguían exactamente los patrones de otros catálogos.

El resultado fue código que "funciona" pero que requirió fixes significativos por un humano para cumplir con los contratos del proyecto (Docker-only, bases extraídas, ritual de .agents/, tamaño de PRs, verificación reproducible).

### Lecciones

Incluso con guías más claras y TODOs de grano fino (el trabajo de 9.0), el modelo local pudo hacer la parte "fácil" de entregar funcionalidad, pero falló en la parte "difícil" de mantener los estándares implícitos y explícitos acumulados durante meses.

Los TODOs estructurados hacen que los gaps sean obvios y baratos de arreglar para un humano senior. Ese es su gran valor.

Los modelos locales actuales aún necesitan scaffolding fuerte + revisión humana para proyectos con historia y reglas estrictas como Tuqan.

El lado positivo: la lista de TODOs permitió que el modelo llegara a "entregar la etapa". El lado negativo: no llegó a "entregar la etapa como debe ser en este proyecto".

---

## English

### The Context

After the 9.0 leg (making the migration plan a usable daily todo list) and the small 9.1 hygiene win, the `MIGRATION-TODOS.md` was explicit about the next item:

> One real Aplicacion vertical (medium): Proveedores...

The 9.2 plan was written first. All the accumulated rules (catalog bases, naming, .agents/ updates, data patches, verify + playbook, Docker-only) were documented.

opencode + Qwen was given the leg.

### What the TODOs Enabled (Bright Side)

Qwen delivered a functional stage:

- List + Form for Proveedores (with the extra telefono column).
- Templates created.
- Plan document present.
- Correct branch naming.

The fine-grained, prioritized TODOs from 9.0 "proved" the model could now deliver the stage (big improvement over the previous git-mess experiment).

### What It Missed (Standards Failure)

Despite the scaffolding, the delivery violated long-standing project standards:

- **Naming**: `Form.php` / `class Form` instead of the universal `Formulario.php`.
- **Architecture regression**: Completely bypassed `CatalogFormulario` and re-wrote the boilerplate.
- **Incomplete process**:
  - No data patch initially.
  - Missing legacy routes.
  - TODOS and STAGE-CHECKLISTS not updated.
  - Verify script not properly extended.
- Drift in templates and variables.

It produced working code but not code that could be merged without senior cleanup to restore the project's discipline.

### The Learning

Clearer guidelines and fine-grained TODOs raised the floor — the model could deliver the stage.

But the ceiling (taste, naming, mandatory use of extracted bases, full ritual around .agents/ and verification) was still not respected.

Local models can follow explicit "what" from a good backlog, but default to old patterns when the "how we actually do things here" is distributed across many small, historical decisions.

The structured backlog made the gaps fast and cheap for a human to spot and fix. That is the real value.

The TODOs did their job. The standards still require human enforcement.

---

*Imagen de portada generada e incluida como .webp + .webp.notes siguiendo las reglas de praderasblog (ComfyUI prompt en la sección Hero, backlinks a la rama feat/stage-9.2-proveedores y a .agents/MIGRATION-TODOS.md + STAGE-CHECKLISTS.md).*