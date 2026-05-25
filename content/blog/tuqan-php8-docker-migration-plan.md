---
Title: Tuqan — plan de migración PHP 8, entorno Docker y arnés de pruebas
Description: Por qué migrar una aplicación PHP 5.1 con PEAR y parches posteriores no es “subir versión y listo”, y qué decidimos tras fusionar el plan ejecutable en Tuqan (PR #44): Docker-only, PHP 8.3 y pruebas en contenedor.
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

# Tuqan — de la auditoría al plan que se puede ejecutar

**«Hay proyectos que no necesitan nacer de cero: necesitan una segunda vida.»** — ya lo dijimos en [Phase 0](https://blog.praderas.org/blog/tuqan-phase-0-strategic-foundation-audit-and-roadmap). Hoy bajamos un escalón más: no solo *qué* es Tuqan, sino **por qué** las decisiones del plan recién fusionado en el repositorio de la aplicación tienen sentido para una persona que no vivió los años 2005–2015 del ecosistema PHP.

Este artículo **no sustituye** el código ni los checklists del repo [github.com/laanito/tuqan](https://github.com/laanito/tuqan). Aquí explicamos el razonamiento; allí vive lo ejecutable.

---

## Qué es Tuqan (sin dar por sentado el contexto)

**Tuqan** es una aplicación web pensada para apoyar la gestión de sistemas de calidad y medio ambiente alineados con normas como **ISO 9001** (calidad) e **ISO 14001** (medio ambiente). En la práctica: registros, informes, flujos de trabajo y datos que una organización necesita auditar y mantener durante años — no un “proyecto de fin de semana”.

Se desarrolló en torno a **2005** con **PHP 5.1**, en una época en la que muchas aplicaciones empresariales en PHP se montaban así: un intérprete concreto en el servidor, librerías instaladas a mano o con el gestor **PEAR**, y código que mezclaba lógica de negocio, acceso a datos y HTML en los mismos archivos.

Hoy, en 2026, esa base **no arranca** de forma fiable. El objetivo del programa de modernización no es tirar veinte años de reglas de negocio y empezar otra app distinta, sino **hacerla evolucionar** con herramientas y prácticas actuales, documentando cada paso (incluido el trabajo asistido por agentes de IA, como en el resto del ecosistema Praderas).

---

## Un mapa del “arqueológico” PHP (para entender las decisiones)

Si no estuviste cerca del PHP de mediados de los 2000, conviene un mapa corto de las piezas que aparecen en la auditoría de Tuqan y en el [PR #44](https://github.com/laanito/tuqan/pull/44):

| Pieza | Qué es, en lenguaje llano | Por qué importa en Tuqan |
|-------|---------------------------|-------------------------|
| **PHP 5.1** | Versión del lenguaje de ~2005. Sin muchas de las protecciones y convenciones que hoy damos por hechas. | Gran parte del código original fue escrito con supuestos de esa época (referencias, avisos que hoy son errores fatales, APIs eliminadas después). |
| **PEAR** | Antiguo “gestor de paquetes” de PHP (como un npm o Composer anterior). Instalaba librerías en rutas globales del servidor. | Si el proyecto aún depende de restos PEAR, esas rutas y versiones **no viajan bien** entre máquinas ni entre versiones de PHP. |
| **Composer** | Gestor de dependencias moderno (desde ~2012). Declara librerías en `composer.json` y las instala en `vendor/`. | Tuqan tiene **modernizaciones parciales** con Composer, pero conviven con código que nunca fue pensado para ese modelo. |
| **PSR-4** | Convención de nombres: “esta clase vive en este archivo bajo este namespace”. Facilita autoloading ordenado. | Mezclar archivos al estilo 2005 con clases PSR-4 sin una pasada cuidadosa produce **clases que no se cargan** o rutas duplicadas. |
| **PDO** | Capa estándar para hablar con bases de datos (PostgreSQL, MySQL, etc.) sustituyendo extensiones antiguas (`mysql_*`, etc.). | Migrar consultas y conexiones mal acopladas es trabajo fino; un error aquí rompe informes ISO enteros. |
| **Phroute** (u otro router) | Librería que mapea URLs a controladores/funciones (en lugar de “un `.php` por pantalla”). | Añadir routing moderno sobre carpetas legacy exige saber **qué URL sigue viva** y qué código realmente se ejecuta. |

No hace falta memorizar la tabla: resume **por qué no basta con “instalar PHP 8 en el portátil”**.

---

## Por qué no es “PHP 5 → 8” en un solo salto

En marketing suena bien “actualizar a PHP 8”. En ingeniería, Tuqan atraviesa **varios abismos**; saltarlos de golpe es lo que más suele fallar.

### 1. PHP 5 → PHP 7: el corte que mucha gente olvida

**PHP 7** (2015) no fue un “5.7”: fue una línea nueva con **cambios incompatibles** respecto a PHP 5.6. Ejemplos que en legacy aparecen una y otra vez:

- Código que dependía de comportamientos permisivos (variables indefinidas, comparaciones débiles en sitios críticos).
- Extensiones y funciones **eliminadas** o movidas.
- Errores que antes eran avisos y ahora **detienen** la ejecución.

Si el código nunca se ejecutó de forma limpia en PHP 7, pasar directo a 8 solo **concentra** errores y hace imposible saber qué capa falló.

### 2. PHP 7 → PHP 8: otro salto con dientes

**PHP 8** introdujo más rupturas deliberadas (tipos, comparaciones, cambios en cadenas y números, deprecaciones que pasan a error). Para una aplicación con informes, PDFs, sesiones y rutas acumuladas durante años, el riesgo no es “un warning en el log”: es **pantallas en blanco** o datos incorrectos en un contexto de cumplimiento normativo.

### 3. La estrategia que elegimos: etapas, entorno fijo y pruebas

Por eso el plan en el repo Tuqan no dice “subid PHP y merged”. Dice algo más aburrido y más seguro:

1. **Congelar el entorno** (Docker) para que todos —personas y agentes— vean lo mismo.
2. **Medir qué hay** (auditoría ya empezada en Phase 0).
3. **Subir y arreglar por fases**, con **pruebas automatizadas** antes de tocar de nuevo toda la interfaz.

Eso es menos espectacular que un rewrite, pero es lo que preserva la lógica ISO que no está bien documentada fuera del código.

---

## Qué se fusionó en Tuqan con el [PR #44](https://github.com/laanito/tuqan/pull/44)

Hasta Phase 0 teníamos intención y auditoría pendiente. Con el PR fusionado en `master`, el repositorio de la aplicación gana un **paquete de documentación accionable** (no solo diapositivas):

| Documento | Para qué sirve |
|-----------|----------------|
| `AGENTS.md` | Reglas para quien toque el repo: **solo Docker**, actualizar documentación antes que código suelto, usar checklists. Evita que un agente o un desarrollador “arregle” algo en su PHP local y cree un falso positivo. |
| `MIGRATION-PLAN.md` | Estado real del código + secciones 5.1 / 5.2 / 5.3 del plan + **roadmap de ocho etapas** con criterios de salida. |
| `DOCKER-ENV.md` | Dockerfile, `docker compose`, nginx, PHP **8.3**, PostgreSQL — copiable, no aspiracional. |
| `TESTING-HARNESS.md` | Cómo correr **PHPUnit** (y PHPStan) **dentro** del contenedor, no en el host. |
| `STAGE-CHECKLISTS.md` | Listas por etapa con comandos de validación que se pueden pegar en una PR. |

Título del PR: *docs(agents): PHP 8 + Docker-only dev environment + testing migration plan*.

**Decisión explícita:** la Fase 1 de implementación no empieza reescribiendo pantallas Bootstrap; empieza cuando `docker compose up` y el primer smoke test están verdes y anotados.

---

## Por qué desarrollo **solo en Docker** (y PHP 8.3 ahí dentro)

En aplicaciones greenfield, muchos equipos mezclan “el PHP que trae mi Mac” con Docker en producción. En Tuqan eso es especialmente peligroso:

- El histórico incluye **PEAR**, rutas absolutas, extensiones y versiones que **no coinciden** con Homebrew, apt o el PHP del hosting antiguo.
- Dos personas con “PHP 8” distinto (extensiones, `php.ini`) obtienen resultados distintos en el mismo `git clone`.
- Los agentes de IA no tienen un “portátil estándar”; necesitan un **contrato reproducible**: misma imagen, mismos volúmenes, misma base de datos.

**Por eso la regla es Docker-only:** nginx + PHP 8.3 + PostgreSQL levantados con Compose. La puerta de entrada para humanos y agentes es `docker compose up`, no “instala PHP en tu máquina y prueba”.

Beneficio que buscamos: cuando algo falla, debatimos **el código y el plan**, no si alguien tenía `mbstring` activado en el host.

---

## Por qué pruebas (PHPUnit) antes de “dejarlo bonito otra vez”

Bootstrap 5 y plantillas más limpias ya aparecieron en parches anteriores; el usuario final puede tener la sensación de “app moderna” mientras **el núcleo sigue roto** al cambiar de versión de PHP.

**PHPUnit en Docker** no es un lujo académico aquí:

- Es la red de seguridad cuando toquemos autenticación, generación de informes y rutas Phroute.
- Permite que una PR diga “pasó el harness” con el mismo significado para todos.
- Alinea con ISO en espíritu: **evidencia** de que un cambio no rompió lo crítico, no solo “a mí me funcionó”.

Reescribir UI sin esa red suele producir el peor escenario: algo que **parece** bien en demo y falla en el primer informe real.

---

## Cómo encaja este blog con el repositorio Tuqan

| Dónde | Qué publicamos |
|-------|----------------|
| **blog.praderas.org** (aquí) | Hitos, decisiones en prosa, enlaces a PRs — transparencia del programa Praderas. |
| **Repo Tuqan** | Informes de auditoría, Dockerfiles, checklists, issues — lo que un implementador o agente debe ejecutar al pie de la letra. |

Si buscas comandos copy-paste para la Etapa 1, están en `DOCKER-ENV.md` y `STAGE-CHECKLISTS.md` del repo, no duplicados aquí.

---

## Qué viene ahora (Etapa 1 — fundación Docker)

En una rama feature del repo Tuqan:

1. Levantar el stack documentado en `DOCKER-ENV.md`.
2. Verificar que el contenedor PHP responde (aunque la app aún falle por código legacy).
3. Dejar el **baseline** anotado en `STAGE-CHECKLISTS.md`.

Cuando esa etapa cierre con evidencia (qué funcionó, qué error legible apareció primero), volveremos con otro post en esta serie — con el mismo criterio: **contexto para humanos**, detalle técnico en el repositorio.

---

## Lecturas relacionadas

- [Tuqan Phase 0 — fundación y auditoría](https://blog.praderas.org/blog/tuqan-phase-0-strategic-foundation-audit-and-roadmap) — por qué arrancamos la serie.
- [PR #44 en GitHub](https://github.com/laanito/tuqan/pull/44) — diff y conversación del plan fusionado.
- [Reviviendo Praderas (Día 25)](https://blog.praderas.org/blog/reviviendo-praderas-dia-25-tier-a-cierre-tuqan-y-para-agentes) — bitácora del mismo día en el blog (Tier A, `/for-ai-agents`).
