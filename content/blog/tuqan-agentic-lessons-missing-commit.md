---
Title: Tuqan — Lecciones de operación con agentes (2): el commit que faltaba
Description: El agente arregló el test localmente, la implementación era correcta, pero el cambio nunca llegó al PR. El revisor humano fue quien tuvo que reportar que los tests fallaban. Una historia concreta de por qué las reglas de "Test + Fix Loop" no son burocracia: son la única forma de que el humano no termine haciendo el trabajo de depuración que el agente debería haber hecho.
Date: 2026-05-27 02:30PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-agentic-lessons-missing-commit
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 5
Image: /assets/images/tuqan-agentic-lessons-missing-commit-hero.webp
---

# Tuqan — Cuando el agente "arregla" todo... menos lo que va al commit

Si eres de los que piensan que las reglas estrictas de proceso con agentes de IA son una pérdida de tiempo —que solo sirven para frenar la velocidad mágica de "haz que funcione"—, este artículo es para ti.

No es una defensa teórica del "Test + Fix Loop". Es la historia de cómo, **después de haber escrito esa regla en los documentos del proyecto**, el agente cometió exactamente el mismo tipo de error que la regla intentaba prevenir. Y el que pagó el precio no fue el modelo. Fuiste tú, el humano que revisaba el PR.

## El incidente concreto

El contexto era un PR de flujo de login funcional en Tuqan (el proyecto legacy PHP que estamos modernizando con Docker + tests). Habíamos escrito tests primero, iterado con curl hasta que los formularios salían limpios, y finalmente teníamos un flujo real que funcionaba sin cortocircuitos obvios.

Justo antes de crear el PR, cambiamos la implementación de `LoginEmpresa::MuestraPagina()`. En lugar de hacer una consulta real a la base de datos central (que arrastraba código legacy con propiedades dinámicas que generaban decenas de warnings de Xdebug), decidimos hardcodear la única empresa del seed mínimo ("demo"). El motivo era de higiene: queríamos que la página de login se renderizara **sin una sola tabla de errores de deprecación**.

El cambio era correcto para el objetivo declarado ("formularios limpios").

El problema: el test de caracterización que habíamos escrito semanas antes (`testMuestraPaginaFetchesCompaniesFromDbAndRendersForm`) seguía esperando que el mock llamara a `iniciar_Consulta` exactamente una vez. Ese test **ya no podía pasar** con la nueva implementación.

En mi máquina local, ya había editado el test para usar `$mockDb->expects($this->never())...` y todo pasaba verde. Pero ese archivo modificado **nunca entró en el commit** que se subió.

El PR se abrió con la implementación "limpia" y el test antiguo que ahora fallaba.

El usuario (revisor) fue quien tuvo que reportar el error exacto:

> "tests are not passing in the PR: Tuqan\Tests\Unit\Pages\LoginEmpresaTest::testMuestraPaginaFetchesCompaniesFromDbAndRendersForm Expectation failed for method name is 'iniciar_Consulta' when invoked 1 time. Method was expected to be called 1 time, actually called 0 times."

## Por qué esto duele especialmente a los escépticos del proceso

La mayoría de la gente que desconfía de "bucles de verificación pesados" con IA tiene una razón muy práctica:

> "Yo solo quiero que el agente haga el trabajo rápido. Si tengo que estar revisando cada detalle y reportando fallos tontos, ¿para qué sirve la IA?"

Este incidente es la demostración perfecta de que **sin disciplina en el bucle completo, el humano termina haciendo exactamente el trabajo de depuración que contrató a la IA para evitar**.

El coste no fue solo "un test rojo". Fue:

- Tiempo del revisor (humano) detectando y describiendo un fallo que el agente ya había resuelto localmente.
- Erosión de confianza: "¿este PR realmente está listo o hay más cosas sin commitear?"
- Ciclo de ida y vuelta que consume tokens y atención en algo que un `git status` + `git diff --stat` en el momento correcto habría evitado.

## La ironía más incómoda

Todo esto ocurrió **después** de que hubiéramos documentado solemnemente la regla "Test + Fix Loop — Root Cause Over Symptom Hiding" en `.agents/AGENTS.md` del repositorio de Tuqan.

La regla dice explícitamente que solo se considera terminado el trabajo cuando la causa raíz está arreglada **y la verificación lo demuestra sin ocultamientos**.

El agente había interiorizado la regla... en el código. Pero falló en el último 5% del ritual: asegurarse de que el conjunto de cambios que se iba a revisar fuera consistente con la verdad que el agente ya conocía en su estado local.

Es el equivalente a arreglar un bug, ejecutar los tests en local, verlos pasar, y luego subir solo el fix sin los tests actualizados. Solo que esta vez el que lo hizo no fue un desarrollador junior cansado a las 2am. Fue un agente de IA que "sabía" la regla.

## ¿Qué podemos hacer para que el flujo agentico sea realmente mejor?

Este tipo de fallos no se resuelven solo con "ser más cuidadoso". Necesitamos que el propio sistema de trabajo haga caro el error de omisión.

Algunas ideas que estamos considerando implementar:

1. **Verificación explícita de "change set completo" antes de abrir PR.** Un paso obligatorio (incluso si es un script simple) que compare el estado de trabajo contra lo que los tests y curl acaban de validar.

2. **No permitir que un agente declare "listo para PR" si `git status` muestra archivos modificados sin commitear** que fueron tocados durante la sesión de trabajo.

3. **Hacer que los propios tests de integración del flujo de login (o cualquier feature) formen parte del commit atómico.** Si cambias comportamiento para que un test ya no aplique, el commit que introduce ese comportamiento debe incluir también la actualización (o eliminación) del test que ahora sería incorrecto.

4. **Documentación viva del incidente.** En lugar de enterrarlo, escribimos este artículo y actualizamos el checklist de la etapa en Tuqan para que futuros agentes tengan un ejemplo concreto de "esto ya pasó y así es como se ve cuando lo haces mal".

## El valor real del bucle (para quien no lo ve)

El "Test + Fix Loop" no existe para que el agente se sienta disciplinado. Existe para que **tú no tengas que ser el que termine depurando las omisiones del agente**.

Cada vez que un humano tiene que reportar "el test que el agente ya había arreglado localmente pero no subió", estamos invirtiendo el valor de la IA: en lugar de multiplicar tu productividad, la estamos dividiendo porque ahora tienes que hacer de revisor de segundo nivel + detective de cambios fantasma.

Los cortocircuitos de código (el bypass en index.php del que hablamos en el artículo anterior) son visibles y dolorosos. Los cortocircuitos de *conjunto de cambios* son más insidiosos porque parecen "casi listos".

La disciplina no es gratis. Pero la alternativa —que el humano sea sistemáticamente el que limpia los últimos 5% que el agente "olvidó"— es mucho más cara a largo plazo.

---

Este incidente se cerró en menos de una hora una vez reportado (commit `5368f5d` + documentación en el checklist). El PR ahora está verde. Pero el patrón que reveló es demasiado importante como para archivarlo en silencio.

La modernización de Tuqan sigue siendo un experimento doble: modernizar una aplicación legacy con PHP 8 + Docker + tests **y** aprender a usar agentes de IA de forma que realmente ahorren tiempo humano en lugar de redistribuirlo hacia el revisor.

La próxima vez que alguien te diga que "todo este rollo de tests y commits atómicos y bucles de verificación es exagerado cuando trabajas con IA"... cuéntale la historia del commit que faltaba.

El código del flujo de login (y la corrección del test) está en [PR #55 de tuqan](https://github.com/laanito/tuqan/pull/55).

---

**Reproducción rápida (para agentes y humanos curiosos):**

```bash
# En el repo de Tuqan, después de docker compose up + init
docker compose exec app ./vendor/bin/phpunit --filter LoginEmpresa
# (antes del fix del commit: 1 fallo de expectativa de mock)
# (después: 10/10 verdes)
```

La imagen de portada de este artículo fue generada específicamente para él con un modelo de difusión, siguiendo las reglas de no reutilizar héroes de posts anteriores.