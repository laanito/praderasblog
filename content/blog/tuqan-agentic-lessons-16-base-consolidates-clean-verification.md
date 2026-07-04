---
Title: Tuqan — Lecciones agentic (16): La base se consolida y la verificación humana sale limpia
Description: Después de varias etapas autónomas desde la 9.3, la primera verificación humana completa del trabajo acumulado en Tuqan no encontró roturas ni rarezas. Resumen de los módulos modernizados y los cross-cuts que han fortalecido la infraestructura compartida del proyecto.
Date: 2026-07-02 09:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Inteligencia Artificial, Productividad, Sistemas
Lang: es
Translation_Key: tuqan-agentic-lessons-16-base-consolidates-clean-verification
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 16
Image: /assets/images/tuqan-agentic-lessons-16-base-consolidates-clean-verification-hero.webp

---

Después de varias etapas de trabajo autónomo desde la etapa 9.3, el usuario detuvo el ciclo para hacer la primera verificación humana completa del trabajo acumulado. El resultado: todo se ve genial, sin roturas, sin rarezas.

### Contexto

Desde que Equipos entró en 9.3, el proyecto ha seguido el ritual estricto documentado en `.agents/MIGRATION-TODOS.md`, `MIGRATION-PLAN.md` y `STAGE-CHECKLISTS.md`: leer los TODOs primero, plan detallado commitado primero, solo Docker, actualización de docs vivos, verificación limpia y PR.

El modo autónomo (el agente ejecuta legs completos, el humano solo hace merge) ha continuado sin intervención constante, tal como se exploró en posts anteriores de la serie.

### Lo que se construyó desde 9.3

**Verticales modernizadas (Pages + templates + rutas modernas /admin/*):**

- 9.4: Mejora (Acciones de Mejora) — CRUD básico con campos personalizados (descripción, fecha, cerrada, coste, análisis).
- 9.5: Formación (Planes) + shell inicial de Documentación.
- 9.6: Auditorías (Programa de auditoría anual) — campos núcleo nombre/vigente/revisión/activo.
- 9.7: Aspectos Ambientales — CRUD básico con scores (magnitud, gravedad, frecuencia) + área y tipo.
- 9.9: Indicadores — CRUD con definición, valores, responsables y frecuencias.
- 9.10: Procesos básico — catálogo núcleo (nombre, código, revisión, padre, activo).
- 9.11: Procesos más profundo — vista de Árbol con jerarquía por padre + resúmenes de contenido_procesos; la ruta legacy de arbol ahora sirve la versión moderna.
- 9.12: Documentación — primera slice del árbol (vista agrupada por tipo, página dedicada `/admin/documentacion/arbol`).
- 9.14: Aspectos — vista de matriz moderna (agrupada por área con todos los scores de evaluación).

**Cross-cuts (la parte que más paga a largo plazo):**

- 9.8: Extracción de base de catálogo (CatalogListado + CatalogFormulario con helpers para getDb, fetchItems, build variables, load/persist, etc.). Todo lo posterior se construyó sobre esto.
- 9.13: Helpers específicos para árboles (resolveParentNames, groupItems, initTwig, buildCommonVariables).
- 9.15: Filtros en listas (`getFilterParams`, `fetchFilteredItems` con soporte para activo y otros) + relaciones en formularios (`loadRelated`, `getRelatedLabel`).
- 9.16: Base completa para árboles (`CatalogTree` abstracta). Los dos Árbol actuales (Procesos y Documentación) ahora extienden esta base y son más pequeños y consistentes.

### Por qué estos cross-cuts importan

Sin ellos, cada vertical nuevo habría duplicado mucha lógica de DB, variables de Twig, manejo de flashes y sidebar. Con la base madura, agregar una nueva vista (matriz, árbol, lista filtrada) es mucho más barato y menos propenso a inconsistencias.

El patrón ha sido alternar verticales con cross-cuts para no acumular deuda técnica mientras se avanza.

### Impacto de la verificación humana

El usuario revisó el estado acumulado después de las etapas autónomas. Resultado: sin breakages, sin oddities. Los flujos modernos (listados, formularios, árboles, matriz) funcionan como se espera, las rutas legacy siguen respondiendo donde corresponde, y la infraestructura compartida (helpers de catálogo, filtros, relaciones, base de árboles) se siente sólida.

Esto valida que el modo autónomo + verificación no-interactiva (verify-8.6.sh + playbooks) + gate humano al final de varios legs funciona bien para generar masa verificable.

### Alcance y lo que no está hecho

Quedan partes importantes en legacy:
- Workflows completos (cierre de acciones de Mejora, hallazgos de auditoría, aprobaciones).
- Motor de cuestionarios (usado por Aspectos y Auditorías).
- Generación de PDF/Excel (GenPDF, crearExcel).
- Edición avanzada de árboles y otras entidades profundas.

El enfoque deliberado ha sido shells + cross-cuts primero, workflows completos después.

### Próximos pasos

El TODOs actual sugiere mezclar:
- Más cross-cuts (pulido de relaciones, full tree base ya entregado).
- O verticales más profundas: integración completa de Mejora, subs de Formación, ejecución de Auditorías, etc.

La verificación limpia da confianza para continuar el ciclo o pausar según convenga.

### Lecturas relacionadas

- [Tuqan — Lecciones agentic (13): 100% Autonomous Work Without Babysitting](https://blog.praderas.org/blog/en/tuqan-agentic-autonomous-legs-cron)
- Posts anteriores de la serie Tuqan — Modernización (ver Series en el sidebar).
- `.agents/MIGRATION-TODOS.md` en el repo de Tuqan para el estado actual y los próximos slices sugeridos.

Todo el trabajo sigue el ritual estricto: plan primero, solo Docker, actualización de docs vivos, verificación reproducible.

---

*Series: Tuqan — Modernización*