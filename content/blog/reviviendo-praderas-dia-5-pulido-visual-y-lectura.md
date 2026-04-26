---
Title: Reviviendo Praderas (Día 5) — Pulido visual: espacio, legibilidad y un tono de marca sin frameworks nuevos
Description: Capa de diseño (tokens CSS, tipografía de lectura, tarjetas, barra lateral y pie) para salir de la sensación "Bootstrap de serie" y acercar el sitio a una bitácora tranquila.
Date: 2026-04-26 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Inteligencia Artificial
---

# Reviviendo Praderas (Día 5) — Cuando toca cuidar la forma, no solo la función

Después de metadatos, migas, posts relacionados y búsqueda, el sitio **funcionaba**. Lo que aún **hacía falta** era el aspecto: demasiada sensación de plantilla genérica y poco **ritmo vertical** para leer con calma.

Ese es el criterio del *Day 5* aprobado: **pulido visual y de uso** antes de meter piezas gordas como fases multilíngües o series. Aquí vamos, con un agente de **copiloto** en el repo, como en los días anteriores.

## Qué tocamos (a alto nivel)

- **Fichero nuevo** `praderas-theme.css` encima de `styles.css`: **variables** (color, sombras, radios, interlineado de cuerpo ~1,75) sin arrastrar otro framework.
- **Plantillas** (`index`, `post`, `blog`, `sidebar`, `breadcrumbs`…): clases y marcas mínimas para títulos, cuerpo del post, *Te puede interesar*, lateral y pie, alineado con el feedback del consultor.
- **Acento** coherente (verde bosque) integrado vía **tokens de Bootstrap 5** (`--bs-primary`, etc.) para no pelear con botones y enlaces.
- **Pie** unificado: texto de atribución en **castellano** y con menos ruido visual.

## Por qué importa (y un abuso honesto con la IA)

La parte “bonita” se puede menospreciar; en un blog, **leer cinco minutos** sin notar fricción con el ojo importa. Aquí el LLM aporta sobre todo **volumen de prueba y consistencia** en el CSS, pero las decisiones (tono, contraste, no meter dependencias gordas) vienen de criterio humano y de lo que **ya** habíamos escrito en `.agents/day5-consultant-feedback.md`.

## Qué queda (sin inventarnos cierre falso)

- **Series / colecciones** sigue en cola, con el lenguaje visual de esta fase.
- Más afinado fino: tipografía web opcional, modo oscuro si algún día lo pide alguien de verdad.

Hasta el siguiente día, en el repositorio.
