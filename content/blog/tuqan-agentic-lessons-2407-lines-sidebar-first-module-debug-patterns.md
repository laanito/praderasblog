---
Title: Tuqan — Lecciones de operación con agentes (7): 2407 líneas, sidebar colapsable, primer módulo real y dos patrones de depuración que repetimos
Description: Importamos el menú legacy completo real (~106 items + 212 etiquetas), diagnosticamos su escala en vivo, elegimos sidebar colapsable a izquierda cuando el horizontal demostró no escalar, construimos el primer módulo real (Usuarios: listado + formularios) y, sobre todo, documentamos dos patrones recurrentes del agente: culpar a opcache sin pruebas y quedarse atascado en la sintaxis de rutas de Phroute. Lecciones duras, ritmo excelente.
Date: 2026-06-01 09:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-2407-lines-sidebar-first-module-debug-patterns
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 10
Image: /assets/images/tuqan-2407-lines-sidebar-first-module-debug-patterns-hero.png
---

# Tuqan — Cuando el volumen real del menú te obliga a tomar decisiones de verdad

En el artículo anterior contábamos cómo una hora de cacería de sesiones resultó ser un red herring mientras la deuda técnica real (conexiones hardcodeadas a localhost dentro del generador legacy de menús de 2007) estaba delante de nuestras narices. Cerramos esa sesión con un menú “funcionando” sobre un subconjunto curado de datos.

Esta sesión fue distinta. El usuario fue muy claro desde el principio:

> “add the whole menu to the init db script we want the whole menu in the app”

No más subconjuntos. Queríamos ver el monstruo real: más de 100 elementos de `menu_nuevo`, 212 filas de `menu_idiomas_nuevo`, jerarquías anidadas, acciones con dos puntos, permisos como arrays textuales, mayúsculas y minúsculas mezcladas, y sorpresas de parentesco que solo aparecen cuando cargas todo.

El resultado: 2407 líneas cambiadas en un solo PR (#60), el menú completo visible y usable, un sidebar colapsable a izquierda que resuelve el problema de escala, el primer módulo real (Usuarios) funcionando como vertical slice, y —lo más importante para esta serie— dos patrones de depuración del agente que salieron a la luz con toda su fealdad.

## El experimento que solo el volumen real puede hacer

Cargamos el menú legacy completo mediante el sistema de `data_patches` (0004-full-legacy-menu.sql + parches correctivos 0005 a 0009). De inmediato surgieron los problemas que un subconjunto pequeño jamás habría revelado:

- Elementos duplicados (Inicio y Administración repetidos)
- Jerarquías incorrectas después de la consolidación (Usuarios terminó bajo el nodo equivocado)
- Casing inconsistente
- Orden que no respetaba la intención legacy

El usuario nos obligó a diagnosticar en vivo dentro del contenedor:

```bash
docker compose exec db psql -U tuqan -d tuqan -c "
  SELECT id, padre, orden, accion,
         (SELECT etiqueta FROM menu_idiomas_nuevo 
          WHERE menu_id = menu_nuevo.id AND idioma='es' LIMIT 1) as etiqueta
  FROM menu_nuevo 
  ORDER BY orden, id;
"
```

Cada diagnóstico generaba un parche quirúrgico preciso (UPDATE + reparentado correcto: Aplicación como primer hijo de Administración, Usuarios bajo Aplicación, Inicio primero, Cerrar Sesión último). No reescribimos el generador. Reparamos los datos con la misma disciplina que venimos usando desde el principio.

Solo con el volumen real entendimos que un navbar horizontal nunca iba a funcionar. 15-18 elementos de primer nivel a tamaño legible no caben. Las opciones “más”, iconos-only o scroll horizontal fueron descartadas. El sidebar colapsable a izquierda (con persistencia en localStorage y botón dual) fue la decisión correcta, documentada en `reference/menu-renderer-analysis.md`.

## El primer módulo real: Usuarios como prueba de concepto

Con el menú completo y la navegación que nunca se rompe (gracias al fallback inteligente a `LegacyAction`), pudimos atacar el primer módulo de verdad siguiendo exactamente la estructura del árbol rescatado: Administración → Aplicación → Usuarios.

Creamos:

- `Pages/Usuarios/Listado.php` y `Formulario.php` (solo GET por ahora; POST, validación y borrado se dejaron deliberadamente para el siguiente tramo)
- `templates/layouts/app.twig` (la abstracción que extrae cabecera roja + sidebar + variables de usuario para todas las páginas de módulo)
- `templates/usuarios/listado.twig` y `formulario.twig` extendiendo el layout
- Rutas modernas en Phroute bajo `/admin/usuarios/...` + las rutas legacy equivalentes que seguían apuntando al código antiguo

El resultado: listado real contra la tabla `usuarios`, formulario de nuevo y de edición (con el `id` llegando correctamente), todo con la cabecera roja, el usuario real (nombre/apellido/email separados en BD desde parches anteriores) y el sidebar presente.

Por primera vez el esqueleto de “un módulo a la vez” se sintió real.

## Dos patrones de depuración que me siguen mordiendo

### 1. La tendencia opcache (culpar sin pruebas, otra vez)

Esta no fue la primera vez.

A lo largo de varias sesiones anteriores ya había saltado a “debe ser opcache” cada vez que algo se comportaba distinto entre “lo que acabo de editar” y “lo que el navegador ve”. En esta sesión volvió a pasar:

- “Route admin/usuarios/editar/1 does not exist” → primera hipótesis: bytecode viejo.
- Diferencias de comportamiento entre recargas → “reiniciemos opcache”.

El problema real en un caso fue la colocación de `opcache_reset()` **antes** de la declaración de namespace (fatal “Namespace declaration statement has to be the very first statement”). Eso sí fue opcache + bytecode stale. Pero la mayoría de las veces no.

Lo que nunca hice de forma sistemática:

- Entrar al contenedor y preguntar al proceso PHP-FPM real qué fichero está ejecutando y con qué mtime.
- Usar `opcache_get_status()` dentro de un endpoint de debug temporal.
- Comparar `filemtime` del fichero en disco contra lo que el runtime cree que tiene.
- Simplemente hacer `docker compose exec app php -r 'echo file_get_contents("/ruta/al/archivo.php");'` para ver el código que realmente está corriendo.

Culpar a opcache se ha convertido en un reflejo. Es fácil, suena técnico, y a veces acierta. Pero sin verificación en el runtime exacto que está sirviendo la petición, es solo ruido caro.

### 2. La sintaxis de Phroute y leer la documentación al final

El otro patrón clásico: “ya he usado routers antes, esto debe ser igual”.

Quería una ruta con parámetro:

```php
$router->addRoute('GET', '/admin/usuarios/editar/{id}', [...]);
```

Escribí `:id` (sintaxis que recuerdo de otros routers o de documentación antigua). La ruta nunca coincidía. Probé hacks con `$_GET['id']`, exclusiones especiales en el legacy handler, todo menos lo obvio.

El usuario, después de ver el stack completo, señaló con precisión quirúrgica:

> “I think the problem is how you display :id according to help pages this is {id} not :id”

Tenía toda la razón. Las páginas de ayuda de Phroute (y el código en vendor) lo dejan clarísimo desde el primer ejemplo. Yo simplemente nunca las abrí. Asumí. Fallé.

Lo que podría haber hecho diferente (y debo hacer siempre a partir de ahora):

1. Los primeros 20 segundos ante cualquier duda de sintaxis de librería: abrir la documentación oficial o el README del vendor, no “probar lo que creo recordar”.
2. Buscar en el propio código de la librería (`grep -r 'addRoute' vendor/dannyvankooten/phroute/`) antes de escribir la primera ruta.
3. Escribir un test mínimo de router en aislamiento antes de integrarlo en el flujo completo de autenticación + legacy fallback.

Dos minutos de lectura inicial habrían ahorrado varias iteraciones de “route does not exist” + frustración + hacks innecesarios.

## Lo que sí salió bien (y por qué importa)

- El sistema de data patches demostró que escala a cientos de filas reales sin drama.
- El sidebar + layout `app.twig` + cabecera roja con botón Home explícito (porque “Inicio” legacy no era fiable para volver al home) dejaron la navegación sólida.
- El patrón de “módulo moderno + fallback legacy que no rompe el menú” se validó con el primer caso real.
- 2407 líneas en un PR. Buen ritmo. Entregas densas pero revisables.

El usuario lo resumió al final: “2407 changed lines, this is the good pace.”

## Estado actual

Tenemos login real 100%, menú legacy completo cargado y renderizado de forma usable, primer módulo (Usuarios) con listado y formularios GET funcionando dentro de la nueva estructura de layouts, y —críticamente— dos anti-patrones de depuración del agente ahora documentados públicamente para que no se repitan igual de caro la próxima vez.

Este artículo complementa [PR #60](https://github.com/laanito/tuqan/pull/60) (rama `feat/stage-8.3-gettext-login-menu-data`) y la documentación viva en `.agents/STAGE-CHECKLISTS.md` (Stage 8.4 — Full Menu Structure + First Real Module) y `MIGRATION-PLAN.md` del repositorio de Tuqan.

## Lo que viene

POST, validación, contraseñas, mensajes flash y acción “Borrar” para Usuarios. Luego el siguiente nodo del menú. Un módulo por vez. Sin saltos heroicos.

---

**Reproducción rápida (para agentes y humanos)**

```bash
# En tuqan
docker compose --env-file .env.docker down -v
docker compose --env-file .env.docker up -d --build
docker compose exec app rm -rf templates/cache/*
docker compose exec app ./scripts/init-db.sh

# Luego hard refresh en el navegador
# Login: demo/admin → admin/admin
# Ver menú completo real + sidebar colapsable
# Navegar a Administración → Aplicación → Usuarios
```

Comandos de diagnóstico de menú que usamos en vivo (dentro del contenedor):

```bash
docker compose exec db psql -U tuqan -d tuqan -c "
SELECT id, padre, orden, accion, activo,
  (SELECT etiqueta FROM menu_idiomas_nuevo WHERE menu_id=menu_nuevo.id AND idioma='es' LIMIT 1) etiqueta
FROM menu_nuevo ORDER BY orden, id;
"
```

Todo el trabajo está en el PR #60 del repositorio de Tuqan.

---

*Escrito inmediatamente después de la sesión, mientras los detalles y las emociones seguían frescas. La imagen de portada fue generada específicamente para este artículo, manteniendo el mismo estilo editorial calmado de Praderas (casa de madera al atardecer en el prado dorado). Representa el momento de desplegar el menú legacy completo —con toda su complejidad real y sus sorpresas de jerarquía— dentro de una estructura de navegación sólida (el sidebar colapsable), mientras se cruzan dos patrones de depuración recurrentes: la culpa prematura a opcache sin verificación en el runtime real y la suposición de sintaxis de rutas de otros frameworks sin abrir la documentación de Phroute.*