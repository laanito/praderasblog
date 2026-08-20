---
Title: Tuqan — Plateau post-9.40: checklist de QA de usuario, tres P0 y por qué el verify script no basta
Description: Tras cerrar Proveedores homologación (9.40), corrimos un checklist humano sobre /admin/*: smoke, journeys y shells. El script non-interactive pasó con patches 0001–0050; el navegador encontró tres bloqueos P0 (Mejora, listado de ejecución, tabla areas). Cómo estructuramos el QA, qué arreglamos y qué queda fuera del merge.
Date: 2026-08-20 07:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-post-9-40-user-qa-plateau
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 17
Image: /assets/images/tuqan-post-9-40-user-qa-plateau-hero.webp
---

Tuqan es una aplicación legacy de gestión ISO 9001/14001 que estamos modernizando por **strangler fig**: rutas y páginas nuevas bajo `/admin/*`, base de datos en Postgres vía Docker, y un camino de **data-patches** numerados en lugar de un big-bang. Tras el merge de **Proveedores homologación (Stage 9.40 / PR #112)** y el checklist de usuario **post-9.40 (PR #113)**, tocó hacer lo que el propio plan pedía antes de invertir en GenPDF, WYSIWYG o verticales profundas: un **pase de QA humano** sobre la meseta actual.

Este artículo cuenta ese pase: cómo lo diseñamos, qué encontró el navegador que el script `verify-8.6.sh` no ve, los tres P0 arreglados en [PR #114](https://github.com/laanito/tuqan/pull/114), y qué dejamos documentado a propósito. Complementa la documentación viva en el repo Tuqan: `reference/USER-QA-CHECKLIST-post-9.40.md`, `.agents/STAGE-CHECKLISTS.md` y `.agents/BLOG-POSTING.md`.

### Contexto: qué es la “meseta” 9.40

Desde las etapas 9.x el trabajo diario se ha centrado en módulos **caminables de punta a punta** en UI moderna: Documentación (workflow + texto + adjunto), Auditorías (ejecución, hallazgos, horario, informe HTML), Mejora, Formación (planes ↔ cursos ↔ inscripciones), Equipos (revisiones, calendario, plan preventivo) y Proveedores (homologación, productos, criterios). Otros módulos (Aspectos, Indicadores, Procesos) están en **shell**: listan y editan lo básico, sin profundidad de producto.

Eso es intencional. Si la meseta no se sostiene en un pase de usuario de 45–90 minutos, añadir GenPDF o un editor WYSIWYG solo multiplica deuda sobre rutas que aún no se pueden confiar.

### Por qué un checklist de usuario además del verify script

En entregas anteriores formalizamos el **Test + Fix Loop** con Docker: `php -l`, `init-db` + **data-patches**, asserts de tablas y filas demo, y CI que corre el verify. Eso es necesario y no negociable. **No es suficiente** para un CMS de negocio:

- El script no inicia sesión ni hace POST de workflow.
- No detecta variables Twig mal nombradas (lista vacía con datos en DB).
- No detecta un `use` de PHP incorrecto que solo estalla en una acción POST.
- No detecta una tabla del schema legacy completo que el camino **minimal + patches** nunca creó.

Por eso el checklist post-9.40 separa **P0** (debe pasar o hay blocker), **P1** (se registra y prioriza) y shells que pueden ser *soft-pass* si abren y guardan lo mínimo. Criterio de meseta: **todos los P0 en Pass**; los P1 abiertos no impiden seguir si hay un plan de fixes pequeños.

### Cómo corrimos el pase (agente + browser + fetch autenticado)

1. **Prep de entorno** — contenedores arriba, app en `localhost:8080`, `verify-8.6.sh` verde, login demo (`demo` / `admin` → usuario `admin` / `admin`).
2. **Smoke** — GET de cada listado moderno (`/admin/documentacion`, ejecución, formación, equipos, proveedores, shells…). Fallo solo si pantalla blanca, 500 o “route not found”.
3. **Journeys A–E** — Documentación, Auditorías↔Mejora↔Informe, Formación, Equipos, Proveedores: filtros, prefills `?fk=`, crear filas, flashes, descargas, transiciones de estado.
4. **Shells + cross-cut** — editar un aspecto/indicador, validación de nombre vacío, filtro vacío amable.
5. **Clasificar y arreglar P0** — no solo informe: tres root causes en código + un patch de datos, re-verificación en browser, PR.

Detalle operativo: el menú lateral sigue apuntando a **rutas legacy** en muchos sitios; el QA de meseta se hace por URL `/admin/*` a propósito. Eso es P1 de producto (authz/menú), no fallo de smoke si el módulo es alcanzable.

### Hallazgo de migraciones: 50/50 y un hueco de schema

Antes de tocar el P0 de Documentación, comparamos **disco** (`docker/db-init/data-patches/*.sql`) con **`data_patches`** en Postgres. Resultado: **0001–0050 aplicados al 100%**. No había cola de migraciones pendientes.

La tabla **`areas`** sí aparece en el dump de schema completo (`00-schema.sql`) y en el legacy de producción antigua, pero el camino moderno (schema mínimo + patches) **nunca la creó**. Los documentos demo ya tenían `area = 1` y `2`. El formulario moderno hacía `getRelatedOptions('areas')` sin red de seguridad → excepción SQL al editar. Eso no se “arregla” reaplicando 0050; hace falta **crear el catálogo en el camino de patches** (y opcionalmente soft-fail si la tabla aún no existe mid-migration).

### Los tres P0 (root cause → fix)

| Síntoma | Causa raíz | Fix (PR #114) |
|---------|------------|----------------|
| Mejora **Verificar** / **Cerrar** → fatal PHP | `Config::initialize()` en namespace `Tuqan\Pages\Mejora` sin `use Tuqan\Classes\Config` | Import correcto |
| `/admin/auditorias/ejecucion` siempre vacío | `templateDir` anidado → clave Twig `auditorias/ejecucion`; la plantilla espera `auditoria` | Alias en `buildListVariables` (como Hallazgos/Cursos) |
| Editar documento → `relation "areas" does not exist` | Catálogo legacy ausente en camino minimal | Patch **0051** + soft-fail de options/labels |

Re-verificación post-fix (sesión autenticada): listado de ejecución con 3 filas y contadores; formulario de documentación con contenido y adjunto; guardar texto y reabrir; Verificar/Cerrar con flash de éxito. `verify-8.6.sh` extendido para afirmar `areas` y el patch 0051.

### Lo que pasó (y lo que no es “bug de producto” todavía)

- **Formación, Equipos, Proveedores homologación** y la mayoría de cross-cuts (flash, validación, prefills) se comportaron bien una vez limpia la **caché Twig** (`templates/cache/`). Plantillas compiladas viejas pueden ocultar UI ya mergeada: nota de ops, no de feature.
- Workflow de Documentación desde el **listado** (enviar a revisión → revisar → aprobar) y descarga binaria funcionaron **antes** de arreglar el formulario de edición: el P0 era el editor, no la máquina de estados.
- Informe de auditoría (editar + ficha HTML) usable; print CSS no se certificó a fondo (P1/opcional).
- **Deshomologar** sin diálogo de confirmación: UX, no crash.
- Menú lateral legacy: P1 consciente.

### Por qué importa para trabajo agentic

En artículos anteriores de esta serie hablamos de bucles agentic (checklist → implementar → verify → PR) y de lecciones cuando el agente “pasa” sin estándares. Este pase cierra el círculo desde el otro lado: **el humano (o el agente en rol QA) debe poder recorrer la meseta como usuario** antes de la siguiente vertical.

Un agente que solo corre `verify-8.6.sh` habría declarado la meseta verde con los tres P0 vivos. Un agente que además ejecuta el checklist (browser o fetch con cookies de sesión) encuentra lo que el usuario real encontraría el lunes por la mañana. Eso es parte del “segundo agente Q&A” del que ya hablamos: no solo review de diff, sino **smoke de producto**.

### Alcance y siguiente trabajo

**Dentro del hotfix #114:** los tres P0, patch 0051, asserts en verify.  
**Fuera (documentado en notas de QA del repo, no bloqueantes de meseta una vez P0 verdes):** menú → `/admin/*`, confirm en deshomologar, higiene de caché Twig en deploy, profundidad GenPDF / contactos / WYSIWYG.

Decisión de meseta tras el fix: **Go with fixes** (ya mergeados) y luego elegir **una** pata de roadmap, no tres a la vez.

### Reproducción (operadores)

Tras pull de #114 en un entorno ya inicializado:

```bash
# Aplicar 0051 si data_patches aún no lo tiene
docker compose --env-file .env.docker exec -T db psql -U qnova -d qnova \
  -v ON_ERROR_STOP=1 -f - < docker/db-init/data-patches/0051-areas-table-and-seed.sql

docker compose --env-file .env.docker exec app ./scripts/verify-8.6.sh
# Opcional tras deploys de templates:
# docker compose exec app sh -c 'rm -rf /var/www/html/templates/cache/*'
```

Checklist humano: `reference/USER-QA-CHECKLIST-post-9.40.md` en el repo Tuqan. Clean-room completo: `down -v`, `up`, `./scripts/init-db.sh` (aplica 0001…0051 en orden).

### Cierre

La meseta post-9.40 no se define solo por “hay código moderno”. Se define por **poder caminar Documentación, Auditorías, Mejora, Formación, Equipos y Proveedores sin white screen ni fatal**, con un script de DB verde **y** un checklist de usuario. El verify nos dijo que los patches estaban; el QA de usuario nos dijo dónde el producto aún mentía. Arreglar eso en un PR pequeño antes del siguiente feature es exactamente la disciplina que esta modernización necesita para no acumular teatro de progreso.
