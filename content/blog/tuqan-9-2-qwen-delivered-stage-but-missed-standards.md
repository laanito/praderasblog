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
Image: /assets/images/tuqan-9-2-qwen-standards-hero.webp
---

## Hero Image Prompt (ComfyUI via repo scripts)

A clean, professional software engineering office split by a glass wall. On one side, a glowing local AI agent (Qwen/opencode) happily hands over a "working" Proveedores module screen showing list and form with data. On the other side, a senior human developer points at red annotations: wrong class name "Form.php", full reimplementation bypassing CatalogFormulario base, missing 0020 data patch, no legacy routes, MIGRATION-TODOS not updated, no 9.2 playbook section. In the background, a large visible wall of the MIGRATION-TODOS.md checklist with the Proveedores item checked "delivered" in green, but many "standards" items still red. Mood: "functionality achieved, craftsmanship failed". Modern, detailed, with subtle Docker whale and git branch icons. Cinematic lighting.

---

## English

### The Context

After 9.0 (the "make the migration plan usable as a daily todo list" leg) and the small 9.1 hygiene win (finally giving "Criterios Ambientales" its proper direct action under Personalizacion), the `MIGRATION-TODOS.md` was very clear about the next step:

> One real Aplicacion vertical (medium): Proveedores (listado + nuevo/editar ...). Follow 8.6-8.8 pattern (table in patch if new, Pages/ + templates/, full routes modern+legacy, POST Procesar, flashes, verify extension, playbook, update this TODOS + checklists).

The `stage-9.2-proveedores-plan.md` was written first on the branch (discipline followed). The catalog bases, naming conventions (Formulario.php everywhere), .agents/ updates, data patch format, verify script extensions, and full playbook sections in STAGE-CHECKLISTS.md were all well documented and recent.

A "new developer" (in practice: opencode + Qwen) was tasked with the leg.

### What Worked (the Bright Side the TODOs Proved)

Qwen + opencode **delivered a functional stage**.

- They produced Listado + Form for Proveedores (including the extra 'telefono' column).
- Templates were created.
- A plan document was present.
- The branch naming was correct (stage-9.2-...).
- The structured, fine-grained, prioritized TODO list from 9.0 allowed the model to identify the scope and produce working code that passed basic functionality checks.

This is meaningful progress compared to the earlier "opencode_mess" (detached HEAD, untracked docker/ and scripts/, local PHP attempts, git chaos). The clear backlog + plan raised the floor. The TODOs literally "proved" that the model could now deliver the stage.

### What Failed (the Standards)

Despite the improved scaffolding, the initial delivery violated core, long-standing project invariants:

- **Naming**: `Pages/Proveedores/Form.php` + `class Form` instead of the consistent `Formulario.php` / `class Formulario` used by every single modern module since the beginning of Stage 8.
- **Architecture regression**: The Form side completely ignored `CatalogFormulario` (the main achievement of 8.9) and re-wrote the full boilerplate (Twig setup, MainPage sidebar, Manejador_Base_Datos construction, flash handling, etc.). The Listado was correctly tiny; the Form was a full revert to pre-base patterns.
- **Process & ritual incomplete**:
  - No data patch for the `proveedores` table (the plan itself called for 0021/0020-style patch).
  - Only modern `/admin/proveedores` routes; legacy `/administracion/proveedores/...` mappings missing.
  - `MIGRATION-TODOS.md` not updated (the checkbox remained unchecked).
  - No 9.2 section in `STAGE-CHECKLISTS.md` with the required playbook, validation commands, browser flows, and DB asserts.
  - `verify-8.6.sh` barely extended for the new table/patch.
- **Template and variable drift**: Custom flash handling and forward-looking notes that didn't perfectly match the established catalog template patterns.

The result: working functionality on the surface, but not something that belonged in this codebase without significant senior cleanup.

### The Real Learning

Clearer guidelines + fine-grained daily TODOs (exactly what 9.0 was built for) are powerful. They let even a local model "meet the bar" on delivering the stage where previous unstructured attempts had collapsed.

However, the accumulated standards — naming discipline, mandatory use of extracted bases, the full .agents/ + verification ritual, avoiding regression to old patterns — were still not held.

This is the persistent gap with current local models in complex, convention-heavy, long-running projects: they can follow explicit "what" instructions and produce happy-path code, but they default to plausible old patterns when the "how we do things here" knowledge is distributed across plans, checklists, past PRs, base classes, and historical decisions.

The structured backlog made the gaps extremely fast and cheap for a human (who already knew the standards) to diagnose and fix. That is the real value delivered by the 9.0 leg.

### Implications

- The TODO list + "plan first" + "update the living documents" ritual is necessary, but still requires strong review against the full body of conventions.
- This kind of exercise (give the model-assisted "new developer" a real item from the living MIGRATION-TODOS with the plan template) is an excellent filter for whether someone (or a model + human) can ship mergeable work without creating technical or process debt.
- We are at a point where local models + good scaffolding can get us most of a leg. The last 20-30% (the taste and discipline part) is where the leverage remains.

The TODOs did their job. The standards still need (human) enforcement for now.

---

## Español

### El contexto

Después de la etapa 9.0 (hacer que el plan de migración sea usable como lista de tareas diaria) y la pequeña victoria de higiene de la 9.1 (finalmente dar a "Criterios Ambientales" su acción directa bajo Personalizacion), el `MIGRATION-TODOS.md` era muy claro sobre el siguiente paso:

> One real Aplicacion vertical (medium): Proveedores...

El plan `stage-9.2-proveedores-plan.md` se escribió primero en la rama (disciplina cumplida). Las bases de catálogo, las convenciones de nombres (Formulario.php en todos lados), las actualizaciones de .agents/, el formato de parches de datos, las extensiones del verify y las secciones completas de playbook en STAGE-CHECKLISTS.md estaban todas bien documentadas y recientes.

A un "nuevo desarrollador" (en la práctica: opencode + Qwen) se le asignó la pierna.

### Lo que funcionó (el lado positivo que los TODOs demostraron)

Qwen + opencode **entregó una etapa funcional**.

- Produjeron Listado + Form para Proveedores (incluyendo la columna extra 'telefono').
- Se crearon las plantillas.
- Existía un documento de plan.
- El nombrado de rama fue correcto (stage-9.2-...).
- La lista de tareas estructurada, de grano fino y priorizada de la 9.0 permitió al modelo identificar el alcance y producir código que funcionaba en las comprobaciones básicas.

Esto representa un progreso real comparado con el anterior "opencode_mess". El backlog claro + plan elevaron el suelo. Los TODOs literalmente "demostraron" que el modelo ahora podía entregar la etapa.

### Lo que falló (el incumplimiento de estándares)

A pesar del andamiaje mejorado, la entrega inicial violó invariantes centrales y de larga data del proyecto:

- **Nombres**: `Pages/Proveedores/Form.php` + `class Form` en lugar del consistente `Formulario.php` / `class Formulario` usado por todos los módulos modernos.
- **Regresión de arquitectura**: El lado del Form ignoró completamente `CatalogFormulario` (el logro principal de 8.9) y reescribió todo el boilerplate. El Listado estaba correctamente pequeño; el Form fue una vuelta atrás.
- **Artefactos de proceso incompletos**:
  - Sin data patch para la tabla `proveedores`.
  - Solo rutas modernas; faltaban los mapeos legacy.
  - `MIGRATION-TODOS.md` sin actualizar.
  - Sin sección 9.2 en STAGE-CHECKLISTS.md con playbook completo.
  - verify-8.6.sh apenas extendido.
- **Deriva en plantillas**: Manejo custom de flashes que no coincidía con los patrones establecidos.

Resultado: funcionalidad en la superficie, pero no algo que perteneciera a esta base de código sin limpieza significativa por parte de un senior.

### La lección real

Las guías más claras + los TODOs de grano fino son poderosos. Permiten que incluso un modelo local "llegue al mínimo" en la entrega de la etapa donde intentos anteriores sin estructura habían colapsado.

Sin embargo, los estándares acumulados — disciplina de nombres, uso obligatorio de las bases extraídas, el ritual completo de .agents/ + verificación, evitar regresiones a patrones antiguos — seguían sin sostenerse.

Este es el gap persistente de los modelos locales actuales en proyectos complejos y llenos de convenciones: pueden seguir instrucciones explícitas de "qué" y producir código para el happy path, pero por defecto vuelven a patrones antiguos plausibles cuando el "cómo hacemos las cosas aquí" está distribuido.

El backlog estructurado hizo que los gaps fueran extremadamente rápidos y baratos de diagnosticar y arreglar para un humano que ya conocía los estándares. Ese es el verdadero valor de la etapa 9.0.

### Implicaciones

- La lista de TODOs + "plan primero" + "actualizar los documentos vivos" es necesario, pero aún requiere revisión fuerte contra el cuerpo completo de convenciones.
- Este tipo de ejercicio es un filtro excelente para ver si alguien (o un modelo + humano) puede entregar trabajo mergeable sin crear deuda técnica o de proceso.
- Estamos en un punto donde los modelos locales + buen andamiaje pueden darnos la mayor parte de una pierna. El último 20-30% (la parte de gusto y disciplina) sigue siendo donde está la palanca.

Los TODOs cumplieron su función. Los estándares todavía necesitan enforcement humano (por ahora).

---

*Artículo escrito siguiendo las reglas de praderasblog (rama fresca, frontmatter con Series y Translation_Key, prompt para portada listo para scripts ComfyUI + .webp + .webp.notes). Backlinks a la rama feat/stage-9.2-proveedores (con fixes) y a los artefactos .agents/ relevantes.*