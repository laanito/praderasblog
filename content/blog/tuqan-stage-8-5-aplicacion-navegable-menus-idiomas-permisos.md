---
Title: Tuqan — Lecciones de operación con agentes (11): Aplicación completamente navegable (Perfiles, Empresas, Menús, Idiomas y Permisos)
Description: Cierre de la vertical slice de la sección Aplicación bajo Administración. Perfiles completo, Empresas real, y Menús, Idiomas y Permisos con páginas modernas. Todo el submenú ahora es navegable antes de entrar en la fase de POST.
Date: 2026-06-11 10:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-stage-8-5-aplicacion-navegable
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 11
Image: /assets/images/tuqan-stage-8.5-aplicacion-hero.webp
---

En la última entrega cerramos un ciclo importante dentro de la rama **Administración → Aplicación**.

Después de tener Perfiles y Empresas funcionando con el nuevo layout moderno, el siguiente paso natural era terminar el resto de las entradas que ya existían en el menú legacy:

- **Menús**: ahora muestra la estructura real (`menu_nuevo` + traducciones) de forma legible.
- **Idiomas**: listado simple + alta/edición básica (siguiendo el alcance limitado que definimos: solo disponibilidad y selección).
- **Permisos**: vista de perfiles con la puerta abierta a la futura matriz de asignación de menús.

El resultado es que **todo el submenú de Aplicación** es ahora completamente navegable con el sidebar colapsable, la cabecera roja y el patrón de páginas modernas que venimos usando desde Usuarios.

### Por qué importaba terminar esto antes de los POST

Una de las lecciones que más se repite en este proyecto es que **navegar importa más de lo que parece al principio**.

Mientras las páginas seguían cayendo en Placeholder, era muy difícil sentir el verdadero peso y la jerarquía del menú legacy. Al tener páginas reales (aunque sean solo GET), de repente:

- El flujo de trabajo de "abrir el menú y llegar a donde quiero" funciona de verdad.
- Los ajustes de UX que hicimos en el sidebar (estado persistente de los acordeones + colapsado por defecto) se volvieron inmediatamente útiles para probar estas nuevas pantallas.
- Queda claro qué partes del sistema legacy aún dependen de la vieja lógica de formularios y cuáles ya podemos empezar a reemplazar de forma ordenada.

Terminar esta vertical slice también nos dio un muy buen punto de corte natural antes de meternos de lleno en la lógica de POST, validaciones y flujos de creación/edición.

### Pequeños aprendizajes laterales

- El refactor del entrypoint de Docker para compilar automáticamente los `.mo` a partir de los `.po` en cada arranque del contenedor fue una mejora que surgió orgánicamente mientras trabajábamos en Idiomas.
- La persistencia del estado del menú (que arreglamos hace poco) demostró su valor real en esta tanda: poder expandir "Aplicación", navegar entre Perfiles, Empresas, Menús, etc. y volver sin que todo se colapse es sorprendentemente cómodo cuando estás explorando varias pantallas seguidas.

### Próximos pasos

Con Aplicación completamente navegable, el siguiente gran bloque será activar los formularios (POST + validación) de estos mismos módulos. Perfiles y Empresas son los candidatos más naturales para empezar, ya que desbloquean trabajo real en Usuarios.

Como siempre, el menú sigue siendo la brújula.

---

Este artículo complementa el trabajo de la rama `feat/stage-8.5-profiles-empresas-personalizacion` del repositorio de Tuqan y la documentación actualizada en:

- [.agents/STAGE-CHECKLISTS.md](https://github.com/laanito/tuqan/blob/master/.agents/STAGE-CHECKLISTS.md) (sección Stage 8.5)
- [.agents/MIGRATION-PLAN.md](https://github.com/laanito/tuqan/blob/master/.agents/MIGRATION-PLAN.md)

El PR correspondiente en Tuqan se abrirá/mergeará próximamente con todo el conjunto de cambios de esta etapa.