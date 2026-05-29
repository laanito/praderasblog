---
Title: Tuqan — Lecciones de operación con agentes (4): el primer paso real del stepping stone
Description: Ejecutamos el primer corte concreto de la fase de modernización de funcionalidades centrales. Actualizamos Twig de la 1.44 a la 3.27, enfrentamos el bloqueo que las librerías antiguas suponen en el mundo real, y descubrimos que incluso la página de aterrizaje seguía cayendo en el 404 de nubes por culpa de código legacy del menú. Añadimos el fallback defensivo que se pidió. Todo con la misma disciplina de Test + Fix Loop y cero ruido de Xdebug.
Date: 2026-05-29 10:30AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Productividad, Sistemas, Inteligencia Artificial
Lang: es
Translation_Key: tuqan-agentic-lessons-twig-upgrade-stepping-stone-execution
Series: Tuqan — Modernización
Series_Slug: tuqan-modernization
Series_Order: 7
Image: /assets/images/tuqan-agentic-lessons-twig-upgrade-stepping-stone-execution-hero.webp
---

# Tuqan — Cuando el stepping stone deja de ser teoría y se pone a trabajar

En el artículo anterior de esta serie contábamos cómo, tras varios meses aplicando parches de compatibilidad con buenos resultados locales, el usuario nos obligó a reconocer que ese patrón tenía un coste acumulado demasiado alto. La decisión fue clara: tratar la modernización de las funcionalidades centrales como un **stepping stone obligatorio** antes de seguir avanzando con lógica de negocio.

Este artículo es la crónica del primer paso real de esa fase.

## El primer corte: Twig

Elegimos Twig como primer objetivo por varias razones prácticas:

- Era la librería que más recientemente nos había obligado a aplicar un parche (`#[ReturnTypeWillChange]` en `Node.php`).
- Su superficie de uso en la aplicación mínima viable era muy pequeña: solo cuatro puntos de renderizado y tres plantillas muy sencillas.
- Actualizarla era una forma concreta de empezar a pagar la deuda que habíamos acumulado con parches.

El cambio fue mecánico pero significativo:

- De `~1.35` (v1.44.8) a `^3.8` (v3.27.0).
- Reemplazo de las clases antiguas sin namespace (`Twig_Loader_Filesystem`, `Twig_Environment`) por sus equivalentes modernos (`Twig\Loader\FilesystemLoader`, `Twig\Environment`) en las cuatro páginas que renderizan.
- Limpieza del parche anterior (ya no era necesario).
- Borrado de la caché de plantillas compiladas (el formato entre versiones mayores es incompatible).

Todo dentro de Docker, como siempre.

## La fricción que apareció de inmediato

Aquí llegó la primera lección práctica del stepping stone.

Cuando intentamos hacer `composer require twig/twig:^3.8`, el resolvedor nos devolvió un error duro:

La librería `anahkiasen/former` (4.1.7, con sus dependencias de Illuminate 5.x) sigue declarando compatibilidad solo con PHP ^7.1.3. Nuestro `platform.php` declarado en composer.json es 8.2.0. El resolvedor se negaba a continuar.

No era un problema de Twig. Era la prueba viviente de por qué la fase de modernización de funcionalidades centrales era necesaria.

La decisión que tomamos fue quirúrgica y deliberada: usar `--ignore-platform-reqs` **solo para esta actualización estrecha**. No tocamos Former. No hicimos una migración grande. Simplemente permitimos que Twig avanzara mientras documentábamos el bloqueo real que las dependencias antiguas suponen.

Esta es la diferencia entre "parchear cuando duele" y "reconocer el tamaño del problema antes de seguir".

## La segunda lección llegó durante la verificación

Con Twig 3 en su sitio, ejecutamos la verificación completa de flujo (login de empresa → login de usuario → `/main/` → logout) con Xdebug encendido y el escaneo estricto de malas cadenas que venimos usando desde hace semanas.

Todo volvió limpio: cero deprecaciones, cero warnings, cero ruido de Xdebug.

Entonces el usuario escribió:

> "impressive, be aware main renders as 404, that was happening yesterday too, so not a fault of twig migration"

Y nos mandó los logs de nginx.

El GET a `/main/` devolvía HTTP 200. Pero el cuerpo contenía la animación de nubes del `NotFoundPage` ("THE PAGE WAS NOT FOUND"). El tamaño del cuerpo coincidía con la página de 404, no con la landing que habíamos construido.

El problema no era nuevo. Llevaba pasando desde el PR anterior.

## El menú que no quería rendirse

Tras reproducir el flujo desde el host (exactamente como lo hace un navegador en `:8080`), el diagnóstico fue claro.

En `MainPage::crea_Menu_Superior()`, una vez que el login de usuario establece `$_SESSION['idioma']`, el código dejaba de usar el guard defensivo que habíamos puesto y llamaba al viejo `arbol_listas`. Esa clase, construida para un mundo con tablas de menú completas, intentaba hacer una consulta compleja contra nuestra semilla mínima (que no tiene `menu_nuevo`, `menu_idiomas_nuevo` ni la columna de permisos que espera la query).

Cualquier excepción en ese punto era capturada por el gran `try/catch` de `index.php` y convertida en `NotFoundPage`... que se servía con HTTP 200.

Por eso el usuario veía "main renders as 404" aunque técnicamente era un 200.

## El fallback que se pidió

El usuario fue muy claro:

> "for this particular case I'd add a fallback in the menu if database is not present so this is not blocking the debug and will work once database is correctly populated"

Eso es exactamente lo que hicimos.

Envolvimos toda la construcción y ejecución del `arbol_listas` en un `try/catch` + una comprobación de resultado vacío. Si algo falla (por tablas ausentes, errores de la clase legacy, resultados vacíos...), devolvemos un pequeño fragmento de navegación limpio y sin warnings:

```html
<ul class="nav navbar-nav"><li><a href="#" title="Menú completo disponible cuando la base de datos esté poblada">(Menú)</a></li></ul>
```

El resto de la landing (`UserName`, el mensaje de bienvenida, el enlace de logout) se renderiza correctamente. El flujo completo sigue saliendo con cero ruido de Xdebug.

Y lo más importante: el código del menú real sigue intacto. Cuando llegue una base de datos con las tablas de menú pobladas, ese camino volverá a funcionar sin que nadie tenga que tocar nada más.

## Lo que esto significa para trabajar con agentes

Este ciclo ha sido especialmente ilustrativo.

- Actualizar una librería "sencilla" expuso un acoplamiento legacy profundo que afectaba a la experiencia después del login.
- El problema no era visible en las pruebas internas que hacíamos antes (usábamos cookies ya autenticadas y entornos que ya habían pasado por ciertos estados).
- Solo apareció cuando el usuario trajo los logs reales de su navegador.

El patrón de "si no lo veo en mis verificaciones, no existe" es peligrosamente fácil de caer para un agente. El usuario, una vez más, nos obligó a mirar el sistema desde fuera.

El stepping stone no es solo una lista de librerías que actualizar. Es un período en el que aceptamos que cada vez que toquemos algo "pequeño", es probable que aparezca algo más profundo. Y que la respuesta correcta no siempre es "arreglarlo todo ahora", sino "poner un fallback honesto que no bloquee el debug y que desaparezca solo cuando la base esté lista".

A veces el mayor avance en una migración legacy no es la librería que actualizaste. Es la claridad con la que decides qué vas a dejar sin romper mientras sigues avanzando.

---

**Reproducción rápida:**

Todo el trabajo está en la rama `feat/stage-8-twig-upgrade` del repositorio de Tuqan (PR #57).

La imagen de portada fue generada específicamente para este artículo, manteniendo el mismo estilo editorial calmado de Praderas (casa de madera al atardecer en el prado dorado) que usamos en el artículo anterior de la serie. Representa el momento de reforzar el primer pilar de verdad sin dramatismo, mientras el prado sigue en calma.

