---
Title: Tuqan — Lecciones de operación con agentes (5): modernizando las dependencias del composer (sin saltos de fe)
Description: Ejecutamos la gran ronda de modernización de dependencias del composer dentro de la fase de stepping stone. Actualizamos Monolog a 2.x, Phroute a 2.2, Former a 5.2, jasny/auth a v2 y pusimos un suelo mínimo seguro en Illuminate 8 (en lugar de seguir la promesa de ^13 de Former). Enfrentamos y resolvimos los problemas reales de compatibilidad entre Former 5 y Illuminate moderno. Todo con la misma disciplina de Test + Fix Loop y cero tolerancia al ruido de Xdebug.
Date: 2026-05-29 09:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-agentic-lessons-composer-deps-modernization
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 8
Image: /assets/images/tuqan-agentic-lessons-composer-deps-modernization-hero.webp
---

# Tuqan — Cuando las dependencias antiguas dejan de ser un atajo

En el artículo anterior contábamos cómo ejecutamos el primer corte concreto del stepping stone: la actualización de Twig y el fallback defensivo en el menú. Ese trabajo fue importante, pero era todavía un paso quirúrgico y contenido.

Este artículo es la crónica de algo mucho más ambicioso y, en cierto sentido, más arriesgado: la gran ronda coordinada de modernización de dependencias del composer.

## El contexto

Durante meses habíamos ido aplicando parches de compatibilidad (`#[ReturnTypeWillChange]`) cada vez que una librería antigua nos generaba ruido en PHP 8.3. Funcionaba. Era pragmático. Pero el usuario nos había pedido explícitamente que, antes de seguir invirtiendo en funcionalidad, hiciéramos una pasada seria sobre las dependencias.

Las librerías que más nos importaban en ese momento eran:

- Monolog (todavía en 1.x)
- Phroute (2.1.0 con nuestros parches propios)
- Former (4.1.7, el principal dolor de cabeza en resoluciones de composer)
- jasny/auth (v1.0.1)

Y por debajo de todo, el elefante en la habitación: las versiones antiguas de Illuminate que Former seguía arrastrando.

## La estrategia: minimal que resuelva el problema

No queríamos hacer un salto heroico a las últimas versiones de todo. Queríamos ser conservadores pero efectivos.

La decisión clave fue poner un suelo explícito en el root:

```json
"illuminate/support": "^8.0"
```

No seguimos la promesa de Former 5.2 de soportar hasta Illuminate 13. Elegimos la versión más baja que, en la práctica, eliminaba completamente el ruido de ReturnType deprecations en Container, Collection, Request y compañía.

El resultado fue Illuminate 8.83.27.

## Lo que realmente se movió

- **monolog**: 1.27 → 2.11 (cambio de API en TuqanLogger)
- **phroute**: 2.1.0 → 2.2.0 + eliminación de nuestros dos parches manuales de trim(null)
- **former**: 4.1.7 → 5.2.0
- **jasny/auth**: v1.0.1 → v2.2.1
- **fpdf**: pequeño bump

El cambio de Former a 5.2 fue el más delicado. Aunque la librería declaraba soporte para versiones modernas de Illuminate, en la práctica seguía resolviendo a componentes muy antiguos (Symfony 3.4 y Illuminate 5.5). Esto generó nuevos problemas de compatibilidad, especialmente con la resolución de `UrlGenerator` y `RouteCollectionInterface`.

## El problema que no esperábamos (y cómo lo resolvimos)

Al pasar a Illuminate 8, `UrlGenerator` se volvió más estricto con sus dependencias. Former 5.2, internamente, hacía esto:

```php
$app->bindIf('url', 'Illuminate\Routing\UrlGenerator');
```

Y `UrlGenerator` ahora exigía `RouteCollectionInterface`, que nadie había vinculado.

La solución no fue mágica. Tuvimos que:

1. Añadir bindings mínimos tempranos en `index.php`.
2. Parchear `FormerServiceProvider::make()` para que prefiriera `Container::getInstance()` en vez de crear siempre un contenedor nuevo vacío.

Fue un recordatorio claro de que "actualizar la versión del paquete" no siempre es suficiente cuando el paquete en cuestión no ha sido diseñado pensando en entornos que no son Laravel completo.

## Lo que aprendimos (otra vez)

Este ciclo reforzó varias lecciones que venimos repitiendo en esta serie:

- Los rangos amplios en `composer.json` ("^5.0") pueden ser engañosos. No significan que la librería esté realmente modernizada.
- A veces el "minimal que resuelve el problema" (Illuminate 8) es mucho más sensato que seguir la máxima versión que permite una dependencia antigua (Illuminate 13).
- Los problemas más duros no suelen estar en la librería que actualizas, sino en todo lo que esa actualización arrastra consigo.

## Estado actual

El flujo completo de login (empresa → usuario) → `/main/` → logout funciona de forma limpia, sin warnings fatales y con un nivel de ruido de deprecaciones muy bajo considerando el estado de partida.

Todavía queda trabajo por delante (especialmente en torno a Former y su relación con el ecosistema Illuminate/Symfony), pero la base de dependencias es ahora significativamente más sostenible que hace unas semanas.

Este fue, sin duda, uno de los tramos más densos y delicados de todo el proceso de modernización hasta la fecha.

---

**Reproducción rápida:**

Todo el trabajo está en la rama `feat/stage-8-composer-deps-modernization` del repositorio de Tuqan (PR #58).

La imagen de portada fue generada específicamente para reflejar el trabajo de reemplazar, de forma ordenada y sin dramatismo, varios soportes estructurales antiguos de la casa mientras el prado sigue en calma.

---

**Artículos relacionados de la serie:**

- [Tuqan — Lecciones de operación con agentes (4): el primer paso real del stepping stone](/blog/tuqan-agentic-lessons-twig-upgrade-stepping-stone-execution)
- [Tuqan — Lecciones de operación con agentes (3): cuando los parches dejan de ser atajos](/blog/tuqan-agentic-lessons-core-modernization-stepping-stone)