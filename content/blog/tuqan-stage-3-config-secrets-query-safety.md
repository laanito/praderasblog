---
Title: Tuqan — Etapa 3: Externalización de configuración y consultas más seguras
Description: Cómo en la Etapa 3 de la modernización de Tuqan eliminamos credenciales hardcodeadas y empezamos a reemplazar consultas SQL construidas con concatenación de strings por consultas preparadas en los caminos críticos de la aplicación.
Date: 2026-05-26 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad, Ciberseguridad
Lang: es
Translation_Key: tuqan-stage-3-config-secrets-query-safety
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 3
Image: /assets/images/tuqan-stage-3-config-secrets-hero.webp
---

# Tuqan — Etapa 3: De las credenciales hardcodeadas a consultas preparadas

En las etapas anteriores hablamos de la auditoría del proyecto y de la decisión de crear un entorno de desarrollo 100% basado en Docker. En esta Etapa 3 dimos un paso más concreto y de alto impacto: **empezamos a sacar los secretos del código y a reducir los riesgos de inyección SQL** en los flujos más sensibles de la aplicación.

Este artículo no es un resumen técnico de diffs. Es una explicación de **por qué** estos cambios importan en una aplicación real que lleva casi 20 años gestionando sistemas de calidad y medio ambiente.

## El problema que heredamos

Tuqan es una herramienta que organizaciones usan para cumplir con normas ISO 9001 e ISO 14001. Eso significa que maneja información sensible durante muchos años: documentos controlados, registros de auditoría, firmas, permisos de usuarios, etc.

El código original (de 2005) tenía varios patrones muy comunes en esa época:

- Credenciales de base de datos escritas directamente en archivos PHP.
- Construcción de consultas SQL mediante concatenación de strings.
- Múltiples puntos de entrada que repetían la misma lógica de conexión y consulta.

Aunque en su momento estos patrones eran aceptables, hoy representan riesgos reales:

- Cualquier persona con acceso al repositorio puede ver las credenciales.
- Las consultas construidas con concatenación son vulnerables a inyección SQL si algún valor no está correctamente controlado.
- Es muy difícil escribir pruebas automáticas fiables cuando la base de datos está acoplada en todas partes.

## Qué hicimos en la Etapa 3

El trabajo de esta etapa se centró en dos frentes principales:

### 1. Externalización de la configuración

Movimos la lectura de credenciales y algunos parámetros de configuración para que se carguen desde variables de entorno en lugar de estar escritas en el código.

Esto tiene varias ventajas inmediatas:

- Las credenciales ya no viajan dentro del repositorio.
- En el entorno de desarrollo (Docker) podemos tener un archivo `.env.docker` local que nunca se sube.
- En producción se pueden inyectar las variables de forma segura a través del orquestador o del servidor.

Todavía queda trabajo por hacer para limpiar todos los puntos legacy que leen la configuración antigua, pero el camino correcto ya está establecido.

### 2. Introducción de consultas preparadas en los caminos críticos

Creamos un nuevo método `consultaPreparada()` en la capa de acceso a datos que permite ejecutar consultas con parámetros de forma segura.

Posteriormente fuimos reemplazando, en los flujos más sensibles, las antiguas construcciones de consultas por este nuevo método:

- La carga de datos de usuario en el proceso de autenticación.
- La lógica de login de empresa.
- Actualizaciones de documentos en el editor.
- Lectura de mensajes internos.

No se trata de una migración masiva de golpe (eso sería muy riesgoso). Se trata de ir protegiendo los puntos de mayor exposición mientras mantenemos la compatibilidad con el resto del código antiguo.

## La importancia de la testabilidad

Uno de los aprendizajes más claros de esta etapa es que **no se puede mejorar la seguridad de forma sostenible sin mejorar la testabilidad**.

Para poder refactorizar con confianza los accesos a base de datos, introdujimos puntos de inyección (`setDbHandler()`) en las clases más críticas. Esto nos permite escribir pruebas unitarias que verifican que se están usando las consultas preparadas correctamente, sin necesidad de tener una base de datos real levantada en cada ejecución.

Este tipo de cambios, aunque parecen pequeños, son los que permiten que una aplicación de 20 años siga evolucionando de forma segura en lugar de quedar congelada por miedo a romper algo.

## ¿Qué significa esto para quien usa Tuqan?

Para las organizaciones que dependen de Tuqan para sus sistemas de gestión de calidad y medio ambiente, estos cambios tienen un impacto directo:

- Menor superficie de ataque en una de las herramientas que más datos sensibles maneja.
- Mayor capacidad de evolucionar el sistema sin miedo a introducir vulnerabilidades clásicas.
- Un camino más claro hacia poder añadir pruebas automáticas que protejan el comportamiento actual.

No es un cambio que se vea desde la interfaz. Es un cambio que se nota en la tranquilidad con la que se puede seguir manteniendo y mejorando el sistema durante los próximos años.

## Próximos pasos

La Etapa 3 nos deja con una base mucho más sólida en cuanto a manejo de secretos y consultas seguras. Las siguientes etapas probablemente se centrarán en:

- Seguir reemplazando usos del antiguo constructor de consultas en más partes del sistema.
- Mejorar la cobertura de pruebas de los flujos críticos.
- Avanzar en la limpieza de dependencias legacy que complican el mantenimiento.

Como siempre, todo el trabajo queda registrado tanto en el repositorio de Tuqan como en esta serie de artículos.

---

Este artículo forma parte de la serie **Tuqan — Modernización**. Puedes leer los anteriores aquí:

- [Tuqan — Phase 0: Auditoría y hoja de ruta estratégica](https://blog.praderas.org/blog/tuqan-phase-0-strategic-foundation-audit-and-roadmap)
- [Tuqan — Plan de migración PHP 8, entorno Docker y arnés de pruebas](https://blog.praderas.org/blog/tuqan-php8-docker-migration-plan)

El código y los documentos de la migración están disponibles en [github.com/laanito/tuqan](https://github.com/laanito/tuqan).