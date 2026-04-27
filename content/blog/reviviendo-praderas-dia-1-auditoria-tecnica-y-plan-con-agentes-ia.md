---
Title: Reviviendo Praderas (Día 1): auditoría técnica y plan de mejoras con agentes de IA
Description: En este primer día de revitalización analizamos el código, la estructura del sitio y la experiencia real del blog para construir una base sólida que permita mejorar navegación, usabilidad y evolución técnica.
Date: 2026-04-22 01:10PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
Series: Reviviendo Praderas
Series_Slug: reviviendo-praderas
Series_Order: 1
---

# Reviviendo Praderas (Día 1): auditoría técnica y plan de mejoras con agentes de IA

Hay proyectos que no necesitan nacer de cero: necesitan una segunda vida.

Hoy arrancó oficialmente esa segunda vida para **Praderas**. El objetivo no es solo "retocar" un blog, sino convertirlo en un proyecto vivo, mantenible y cada vez más útil para quienes lo leen. Y sí: una parte importante del proceso será apoyarnos en agentes de IA para iterar más rápido y con mejor criterio.

## Qué hicimos hoy (y por qué importa)

Antes de tocar código, hicimos una auditoría completa en tres frentes:

1. **Repositorio y arquitectura interna**  
   Revisamos configuración de PicoCMS, temas, plugins y estructura de contenidos para entender cómo está ensamblado todo.

2. **Estructura real del sitio publicado**  
   Contrastamos lo que el código "dice" con lo que realmente ve un visitante en producción.

3. **Calidad de contenido y metadatos**  
   Evaluamos consistencia editorial (fechas, tags, plantillas y organización) para preparar mejoras sostenibles.

Este paso es clave: cuando no hay contexto claro, cualquier mejora técnica termina siendo frágil.

## Hallazgos más relevantes

Durante la revisión aparecieron varios puntos importantes:

- Existe una **desalineación de dominio canónico** entre lo esperado y lo que está activo.
- Hay una plantilla principal del listado de blog con señales de **marcado malformado** en el render final.
- La interfaz mezcla textos en español e inglés en zonas clave de navegación.
- El buscador y la paginación tienen margen de mejora para una navegación más clara.
- La base de contenidos está bien encaminada, pero aún hay espacio para estandarizar metadatos.

Nada de esto es dramático; de hecho, es el tipo de deuda normal en proyectos que han evolucionado por etapas.

## Lo que dejamos listo para acelerar próximas iteraciones

Como último cambio de este primer PR, creamos dos documentos dentro de `.agents` para que futuros agentes (y también humanos) partan de una base común:

- **`repo-context.md`**: mapa del proyecto, arquitectura, plugins, convenciones y riesgos actuales.
- **`proposed-improvements.md`**: backlog priorizado de mejoras en estructura, navegación y usabilidad, con fases y métricas.

Traducido a lenguaje práctico: ahora tenemos brújula, no solo entusiasmo.

## Lo que viene: una serie paralela muy especial

Además de mejorar el blog, iniciaremos una bitácora técnica paralela sobre un reto que muchos equipos conocen bien:

**cómo un agente puede ayudar a transformar iterativamente una app antigua en PHP5 hacia PHP moderno, sin romperlo todo en el intento.**

No será teoría. Vamos a documentar decisiones reales, trade-offs, errores, validaciones y resultados.

Si te interesan la modernización progresiva, la deuda técnica bien gestionada y el uso práctico de IA en software legacy, esta serie será para ti.

## Cierre del Día 1

Hoy no buscamos "cambios vistosos". Buscamos algo más valioso: **claridad estructural para poder mejorar con método**.

Mañana ya no partimos de cero. Partimos con contexto, prioridades y un plan.

Y eso, en cualquier proyecto técnico, cambia todo.
