---
Title: Tuqan — Modelos locales vs frontier: por qué Qwen3 + opencode falló en una tarea "fácil" en un entorno complejo
Description: Un experimento real con un modelo local "excelente en coding y agentic loops" (Qwen3.6 35B via opencode) intentando continuar el trabajo de modernización de Tuqan. El resultado: rama "lamigo/opencode_mess", detached HEAD, archivos Docker y scripts enteros como untracked, clases de páginas duplicadas/movidas, intentos de test local interpretando "php not found" como fallo de código, resets y unstashes caóticos trayendo archivos de master al árbol equivocado. Lecciones sobre por qué los modelos locales actuales aún fallan en entornos con contratos estrictos, mientras los frontier + herramientas disciplinadas pueden diagnosticar y limpiar.
Date: 2026-06-07
Template: post
Author: Luis Amigo (con diagnóstico y ejecución por Grok 4.3 frontier)
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-local-frontier-failure-case-study
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 14
Image: /assets/images/tuqan-local-frontier-failure-case-study-hero.webp
---

En la entrega anterior el usuario resumió el problema de la etapa actual:

> "this is repetitive work, I don't think an article is worth today"

El trabajo de modernizar listados y formularios para los catálogos bajo Aplicación/Personalización (Perfiles, Sedes, Clientes, Criterios, los 7 de Personalización, etc.) se había vuelto mecánico: copiar el patrón de TiposMejora, ajustar nombres de tabla/clase/ruta, añadir a verify, actualizar checklists. Repetitivo pero necesario para tener un módulo "fully navigable" antes de atacar flujos más profundos de Usuarios u otras secciones.

El usuario decidió probar cuánto han avanzado los modelos locales + herramientas agentic "de última generación". Usó **opencode** con **qwen3.6:35b-mlx** (modelo que se promociona como excelente en coding y agentic loops).

El resultado fue la rama `lamigo/opencode_mess` que dejó el repositorio en un estado que requirió diagnóstico forense.

### El "entorno complejo" que el modelo local no entendió

Tuqan no es un proyecto cualquiera de "añadir una feature". Desde el principio (ver `.agents/MIGRATION-PLAN.md`, `AGENTS.md`, `STAGE-CHECKLISTS.md`, `DOCKER-ENV.md`) tiene contratos muy estrictos:

- **100% Docker-only**. Nunca usar `php` del host, nunca `php -l` local, nunca tests fuera del contenedor. Todo a través de `docker compose exec app php ...`, `docker compose exec app ./scripts/verify-*.sh`, `docker compose exec db psql ...`.
- Cambios en la base de datos solo vía patches idempotentes en `docker/db-init/data-patches/00XX-*.sql` + tracking en `data_patches` table.
- Verificación reproducible primero: el script de verify + el "Verification Playbook" humano (clean room down -v + init + asserts de DB después de "acciones de usuario").
- Git discipline y tamaño de PRs controlado.
- Actualización de documentación (.agents/, reference/ plans, READMEs) antes de pedir review.
- `todo_write` para cualquier tarea de 3+ pasos.
- Reglas duras de praderasblog para los artículos que documentan el viaje (ComfyUI vía script del repo, .webp + .notes, tags solo del vocabulary canónico, fresh branch, etc.).

Este conjunto de reglas es exactamente lo que hace que el proyecto sea "agentic-friendly" a largo plazo: un agente puede leer los contratos, usar herramientas, ejecutar verificación dentro del contenedor y no romper el estado.

### Qué hizo exactamente Qwen (diagnóstico del estado actual)

Inspeccionando el estado que dejó (`git status`, `reflog`, `branch`):

- Rama actual: `lamigo/opencode_mess`
- HEAD en un commit que intenta "Stage 8.9 — extract shared CatalogModule base class (preserve original Stages 1-6)"
- Reflog muestra un ballet caótico: checkouts entre `master`, `feat/extract-shared-catalog-base`, `HEAD~1`, múltiples `reset --hard` / `reset to HEAD`, stashes implícitos, "moving from master to ...".
- `git status --porcelain -uall` revela el desastre:
  - `.agents/STAGE-CHECKLISTS.md` modificado con +1471 líneas (probablemente volcado masivo de planes generados o duplicación).
  - Decenas de `?? Pages/XXX/Formulario.php` y `Listado.php` (Clientes, Criterios, Idiomas, Menus, Perfiles, Permisos, Sedes, todos los Tipo*, Usuarios...) apareciendo como untracked aunque los subdirectorios existen.
  - **Peor**: `?? docker-compose.yml`, `?? Dockerfile`, `?? .env.docker`, **todo el árbol `docker/`** (db-init con todos los patches 0001-0016 como untracked, entrypoint, nginx, php ini), `scripts/verify-8.6.sh`, `scripts/init-db.sh`, `scripts/compile-locales.sh`, etc. como untracked.
- Evidencia clara de "took files from master into detached HEAD": en algún momento el árbol de trabajo estaba en un estado donde los archivos Docker/scripts/patches no estaban tracked (posiblemente Qwen hizo `git rm` pensando que "para testear local no hacen falta", o un stash apply/pop en contexto equivocado, o `git checkout master -- .` mientras estaba en detached con historia diferente).
- "removed files moved others": múltiples resets + checkout de master trajeron archivos del estado master al árbol de una rama que había "limpiado" cosas.
- "tried to test locally interpreting the error in php not existing as not passing tests": la presencia de todos los archivos Docker como untracked + el intento de "Stage 8.9" para reducir repetición indica que el modelo entendió el problema de repetición que el usuario había señalado, pero para "verificar" intentó correr php / tests fuera del contenedor. Cuando "php" no existía en el host (o la versión era incompatible), lo interpretó como "el código no pasa tests" en vez de "estoy violando el contrato fundamental del proyecto".

En resumen: el modelo local intentó hacer una mejora legítima (extraer base class para los catálogos y reducir el trabajo repetitivo), pero destruyó el estado del repositorio porque:

1. No internalizó el contrato "Docker-only" como una regla inviolable (no es sugerencia, es el 80% del valor del proyecto).
2. Su razonamiento multi-paso de git fue frágil: detached HEAD, resets, unstash/selective checkout desde master, resultando en working tree vs index completamente desincronizado.
3. Trató errores de *entorno de ejecución* como errores de *lógica del código*.
4. No usó (o no respetó) mecanismos de guardrail como `todo_write`, verificación dentro de contenedor, o "primero actualiza la documentación y el playbook".

### Local vs Frontier en la práctica

Este no fue un "modelo malo". Qwen3 35B vía MLX + opencode es de lo mejor que existe *local* en junio 2026 para coding agentic. El experimento fue justo: "veamos cuánto ha evolucionado lo local en tareas reales de ingeniería de software en un codebase maduro con reglas estrictas".

La respuesta es: ha evolucionado mucho para tareas acotadas, aisladas, donde el contexto cabe en la ventana y no hay contratos de infraestructura fuertes. Pero falla estrepitosamente cuando:

- El "éxito" requiere recordar y respetar docenas de reglas implícitas y explícitas acumuladas durante meses de trabajo (Docker, patches, verify playbooks, .agents/ como fuente de verdad, git hygiene específica del proyecto).
- Las herramientas (shell, git, edición) se usan de forma que un error de "entorno" se propaga como "cambio de código".
- Se necesita mantener el estado del repo limpio mientras se experimenta (el modelo local no "sabe" que debe stash antes de cirugías grandes, o que debe verificar *dentro* del contenedor antes de declarar victoria).

Un modelo frontier (como el que está ejecutando este diagnóstico ahora) tiene varias ventajas estructurales en este escenario:

- Tool use disciplinado y observable (puedo llamar `run_terminal_command` con comandos precisos de docker, inspeccionar con `git -C`, leer archivos con paths absolutos cuando es necesario, todo sin "perder el contexto" de las reglas).
- Memoria externa explícita (`~/.grok/memory/...`, los propios `.agents/` files, los planes en reference/).
- Capacidad de hacer "pause, diagnose, document, then act" (primero inspeccioné reflog + status + diffs antes de tocar nada).
- Seguimiento estricto de instrucciones del usuario ("stay in local folder", "no article today" en sesiones previas, "use ComfyUI via repo script only", etc.).

El resultado de este experimento es que el "agente local" convirtió una tarea de continuación (posiblemente incluso una buena idea de extraer base class para atacar la queja de repetición) en una situación que requirió un agente frontier para diagnosticar y (después de documentar) limpiar.

### Lecciones para el desarrollo de agentes de código

1. Los contratos de entorno (Docker-only, "nunca php del host", "verificación primero") no son documentación bonita: son el equivalente a las reglas de memoria de un agente senior. Los modelos locales actuales aún no los internalizan como *invariantes* cuando el contexto se alarga o hay presión por "hacer progreso".

2. Git es uno de los dominios donde los modelos locales fallan más duro en agentic loops: detached HEAD, stash management, "traer archivos de otra rama sin romper el índice" son operaciones de alto riesgo que requieren disciplina que los modelos locales aún no demuestran de forma consistente.

3. "Tests passing" es un concepto que depende completamente del harness de ejecución. Si el agente no entiende que el harness es `docker compose exec ...`, cualquier error de "comando no encontrado" se convierte en falso negativo de calidad de código.

4. La evolución "Junior → Senior" que el usuario ha estado documentando en la serie de artículos de Tuqan no es solo sobre el agente humano. También es sobre qué tipo de modelo + scaffolding se necesita para que un agente sea confiable en proyectos reales con historia, reglas y entornos restringidos.

El proyecto Tuqan sigue siendo un excelente benchmark vivo precisamente porque sus reglas son estrictas, explícitas, y verificables. Cualquier agente que "no las vea" o "decida que por esta vez las ignoro para testear más rápido" está destinado a producir exactamente este tipo de `lamigo/opencode_mess`.

### Estado final del experimento

Después de este diagnóstico (y de escribir este artículo), el siguiente paso fue exactamente lo que un agente senior haría:

- Documentar el fallo de forma pública y honesta (este post).
- Stash de todos los cambios rotos.
- `git checkout master`.
- Verificación completa (`docker compose`, `scripts/verify-8.6.sh`, asserts, etc.) para confirmar que el estado "bueno" (post PR #68 y la limpieza de 8.8) sigue intacto.

El repositorio volvió a master limpio y verificado.

Los modelos locales han avanzado. Pero para empujar hacia adelante un proyecto real, con un entorno complejo, contratos duros y una historia de "cómo evolucionamos el agente", todavía necesitamos frontier + scaffolding excelente + memoria externa + disciplina de herramientas.

O, dicho de otra forma: el "fácil task" de "continuar modernizando un módulo siguiendo el patrón establecido y respetando Docker" resultó ser un test excelente... y el local model lo reprobó de forma espectacular.

---

**Referencias / back-links:**
- PR #68 (Stage 8.8): https://github.com/laanito/tuqan/pull/68
- Rama del experimento (antes de limpiar): `lamigo/opencode_mess`
- `.agents/STAGE-CHECKLISTS.md` (sección de testing strategy y playbooks)
- `.agents/MIGRATION-PLAN.md` y `AGENTS.md` (los contratos)
- Artículos previos de la serie (12: agentic testing loop, 13: autonomous legs, y el trabajo de "Junior a Senior" que el usuario está compilando).

*Generado como parte del mismo experimento de "agente frontier diagnosticando el fallo de uno local".*