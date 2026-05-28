---
Title: Tuqan — Lecciones de operación con agentes (3): cuando los parches dejan de ser atajos
Description: El agente aplicó el parche de compatibilidad que ya había usado con éxito antes. Pero el usuario señaló que este patrón se repetiría con todas las librerías antiguas del proyecto. Esto provocó un cambio real en el plan de migración: tratar la modernización de las funcionalidades centrales como un paso obligatorio antes de seguir avanzando.
Date: 2026-05-28 12:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-agentic-lessons-core-modernization-stepping-stone
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 6
Image: /assets/images/tuqan-agentic-lessons-core-modernization-stepping-stone-hero.webp
---

# Tuqan — Cuando el agente descubre que los parches ya no son atajos

Si eres de los que creen que aplicar parches de compatibilidad una y otra vez es una forma pragmática de avanzar con agentes de IA en código legacy, este artículo también es para ti.

No es una crítica a la prudencia. Es la historia de cómo, después de varios meses aplicando el mismo patrón con buenos resultados, el usuario nos obligó a cuestionar si ese patrón seguía siendo sostenible.

## El incidente que cambió el rumbo

Estábamos cerrando los últimos detalles del PR de login real contra base de datos. Como mejora final antes de merge, añadimos una página de aterrizaje mínima y un logout funcional. Durante las pruebas apareció una nueva deprecación:

> Return type of Twig\Node\Node::count() should either be compatible with Countable::count(): int, or the #[\ReturnTypeWillChange] attribute should be used...

Era el patrón que ya habíamos usado con éxito varias veces: en Illuminate, en Phroute, y en otras partes de Twig. Aplicamos el parche mínimo (`#[ReturnTypeWillChange]`), documentamos que era temporal, y todo quedó limpio de nuevo.

El flujo completo (login → landing → logout) funcionaba sin una sola advertencia de Xdebug.

Entonces el usuario escribió:

> "we will be hitting twig issues for every step we take and will be fixing them to make it usable same for the other classes, starting clean might be a timeline accelerator"

No estaba pidiendo que parásemos todo. Estaba señalando algo más profundo: que el enfoque de "parcheamos cuando aparece" iba a repetirse con prácticamente todas las librerías antiguas que aún usamos (Twig, Former, el query builder legacy, etc.). Cada nuevo trozo de funcionalidad que tocáramos generaría más trabajo de este tipo.

## El cambio real en el plan

Como último paso antes de merge, actualizamos el plan de migración.

En lugar de seguir tratando estas actualizaciones de librerías como trabajo táctico que surge orgánicamente, se decidió formalizar una fase intermedia explícita:

**"Core Functionality Modernization" como stepping stone obligatorio.**

Esta fase no es un regreso al big-bang. Es el reconocimiento de que, después de haber construido una aplicación mínima viable usando parches pragmáticos sobre dependencias de 2010, llega un momento en el que es más eficiente (y más honesto) dedicar un esfuerzo concentrado a modernizar las bases sobre las que se va a construir todo lo demás.

El plan ahora refleja que esta etapa debe considerarse pre-requisito antes de entrar de lleno en modernización profunda de lógica de negocio.

## ¿Qué significa esto para el trabajo con agentes?

Hay una lección incómoda aquí.

El patrón de "aplicar el parche que ya funcionó antes" es extremadamente seductor para un agente. Es localmente eficiente, reduce ruido, permite seguir entregando valor visible. Y en muchos momentos es la decisión correcta.

El problema aparece cuando ese patrón se convierte en la estrategia por defecto durante demasiado tiempo. Cada parche que aplicamos hoy es una decisión que posponemos. Y las decisiones pospuestas tienden a acumularse.

El usuario no nos pidió que dejáramos de entregar. Nos pidió que reconociéramos cuándo el atajo deja de ser atajo y se convierte en el camino principal.

Esta es, probablemente, una de las formas más valiosas en las que un humano puede corregir a un agente: no solo pidiendo que se arregle algo concreto, sino señalando que la forma en la que estamos decidiendo qué arreglar (y cómo) tiene un coste acumulado que ya no compensa.

---

El PR #56 de Tuqan incluye ahora esta reflexión como parte de su documentación final. El cambio en el plan de migración es, paradójicamente, uno de los resultados más concretos y útiles de todo el trabajo de las últimas semanas.

A veces el mayor avance no es añadir una funcionalidad nueva. Es tener la claridad de dejar de aplicar el mismo parche de siempre.

---

**Reproducción rápida:**

Los cambios principales de este ciclo (landing page + logout + parche de Twig + actualización del plan) están en la rama `feat/real-db-auth-no-shortcuts` del repositorio de Tuqan.

La imagen de portada de este artículo fue generada específicamente para reflejar la tensión entre aplicar parches repetidamente y decidir fortalecer los cimientos. Sigue el mismo estilo editorial calmado de Praderas que usamos en el artículo anterior de esta serie.

---

**Artículos relacionados de la serie:**

- [Tuqan — Lecciones de operación con agentes: cortocircuitos vs. causas raíz](/blog/tuqan-agentic-lessons-root-cause)
- [Tuqan — Lecciones de operación con agentes (2): el commit que faltaba](/blog/tuqan-agentic-lessons-missing-commit)