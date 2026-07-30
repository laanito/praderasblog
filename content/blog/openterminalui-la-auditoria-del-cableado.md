---
Title: OpenTerminalUI — La auditoría del cableado, o 33 errores que eran en realidad cinco
Description: Un montón de funciones "rotas" en el terminal bifurcado resultaron compartir un puñado de causas raíz — el frontend llamando a rutas de API que el backend nunca servía. El arreglo fue una pasada cuidadosa contra la verdad, no treinta y tres parches a la carrera.
Date: 2026-07-30 06:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Inteligencia Artificial
Series: OpenTerminalUI — Bifurcando un terminal financiero
Series_Slug: openterminalui
Series_Order: 2
Lang: es
Translation_Key: openterminalui-wiring-audit
Image: /assets/images/openterminalui-02-wiring-audit-hero.webp

---

# OpenTerminalUI — La auditoría del cableado, o 33 errores que eran en realidad cinco

Al principio del fork, OpenTerminalUI tenía un síntoma exasperante: funciones enteras que simplemente… no mostraban nada. El panel de noticias, vacío. El botón de IA "explica este backtest" no hacía nada. El widget del Python Lab, muerto. El calendario económico, en blanco. Cada uno parecía su propia avería independiente — un montón disperso de unas treinta cosas rotas.

No eran treinta y tantos errores. Eran unos **cinco**, con treinta y tantos disfraces. Esta es la historia de la auditoría que lo descubrió — y de por qué lo correcto fue una pasada cuidadosa contra la verdad, no un enjambre de parches rápidos.

> Esta es la entrega **2** de la serie *OpenTerminalUI* — el análisis en profundidad de la auditoría del cableado que debía desde la [#1](/blog/openterminalui-bifurcar-terminal-financiero-de-india), y la última de esa lista. Llega fuera de orden, tras los artículos de hito.

---

## Contexto: un fork cuyas dos mitades se habían separado

OpenTerminalUI es un terminal financiero autoalojado: un frontend en el navegador que habla con un backend por una **API** HTTP (el conjunto de URLs que el backend responde). En un fork, el código del frontend que llama a la API y las rutas del backend evolucionan a su ritmo — y se separan en silencio. El frontend sigue llamando a `/api/news/latest`; el backend la ha movido desde entonces, o la ha metido bajo `/v1/…`, o la ha renombrado. Nadie se entera, por un detalle que convierte un error ruidoso en uno silencioso.

**Todo degrada con elegancia.** Cuando una llamada falla, el frontend la captura y renderiza un estado vacío — "sin noticias", "sin datos" — en vez de un error rojo. Eso es una *virtud* para la resiliencia (un proveedor de datos inestable no debería reventar la página). Pero es una *trampa* para el cableado: una llamada a una URL que el backend nunca sirve devuelve un **404** (no encontrado), el catch se lo traga, y la función parece "vacía" en vez de "rota". Treinta funciones pueden estar mal cableadas y la app no alza la voz ni una vez.

---

## El problema: *parece* un montón de errores dispersos

Ante un panel de noticias vacío, un botón muerto, un calendario en blanco, el instinto — y, con franqueza, el instinto que seguiría una flota de agentes de IA si la dejaras — es tratar cada uno como su propio ticket. Treinta y tres síntomas, treinta y tres investigaciones, treinta y tres arreglos. Abrirse en abanico, divide y vencerás.

Ese instinto es erróneo *justo cuando los errores están correlacionados*, y el mal cableado es la clase de error más correlacionada que hay. Si el frontend y el backend se separaron en una convención de nombres, se separaron *de la misma forma* en una docena de sitios. Arreglar cada uno por separado significa volver a deducir la misma causa raíz una docena de veces y — peor — parchear síntomas mientras se pierde la causa compartida que un único lector habría visto de inmediato.

---

## Qué hicimos: diferenciar toda la superficie contra la única fuente de verdad

El backend ya publica la verdad sobre sí mismo. Arráncalo, y sirve un **`/openapi.json`** — una lista legible por máquina de *todas* las rutas que de verdad responde. Ese documento, desde un servidor en marcha, es el árbitro. Ni las suposiciones del frontend, ni los tipos, ni la documentación: lo que el proceso sirve de verdad.

Así que la auditoría fue una única pasada deliberada:

1. **Arrancar el backend real** (con una base de datos desechable y sin sembrado de red) y bajar su `/openapi.json` — la tabla exacta de rutas servidas.
2. **Enumerar cada llamada que hace el frontend** — las ~331 del cliente `api/` — y comprobar cada una contra esa tabla: ¿servida, o 404?
3. **Agrupar los fallos por causa, no por función.**

Los números contaron la historia: de ~331 llamadas, **298 ya eran correctas.** Las averías se agruparon en un puñado de patrones:

- **Deriva de la capa de rutas.** Algunos routers viven bajo un espacio `/v1`, otros no; el frontend acertó mal en grupos. Una métrica pasada como cadena de consulta donde el backend la quería en la ruta. Singular donde el backend había pasado a plural (`/watchlist` frente a `/watchlists/items`). Una API de trabajos partida en `submit` / `status` / `result` que el cliente seguía llamando como una sola. El mismo error, muchas caras.
- **Un router entero importado pero nunca montado.** Los endpoints de economía existían en el código, estaban importados — y nunca se llegaron a enganchar a la app. *Cada una* de las llamadas `/economics/*` era un 404, no porque el frontend se equivocara sino porque el backend nunca las servía. (El arreglo lleva un comentario hasta hoy: *"misma clase que el error de renta fija de arriba".*)
- **Una ruta ensombrecida.** Una ruta literal (`/watchlists/items`) se declaraba *después* de una con parámetro (`/watchlists/{name}`) que casaba primero y se la comía — así que el manejador específico era inalcanzable. El orden importa, y era el equivocado.

Arregla los cinco patrones y treinta y tres síntomas se despejan a la vez. Luego **verifica contra la realidad de nuevo**: acuña un token de autenticación real, haz curl a las rutas candidatas, confirma 200 donde antes había 404. Que los tipos compilen no prueba nada aquí; solo el servidor en marcha.

---

## Por qué una pasada ganó a abrirse en abanico

- **Los errores correlacionados quieren un lector, no muchos obreros.** El paralelismo es la herramienta correcta cuando las tareas son independientes. Estas no lo eran — compartían cinco raíces. Una única pasada ve "ah, es otra vez la convención `/v1`" en la segunda aparición y la aplica a las doce; doce investigaciones independientes la descubren cada una desde cero, y algunas parchean el síntoma sin nombrar la causa.
- **Verifica contra el artefacto, no contra la suposición.** Los tipos del frontend, la documentación vieja y la memoria humana *creían* todos las rutas equivocadas. Lo único que no podía mentir era el `/openapi.json` de un servidor arrancado. Anclar toda la auditoría en esa única fuente es lo que convirtió las conjeturas en un diff.
- **La degradación elegante necesita una contraparte ruidosa.** El comportamiento de vaciar-en-silencio-ante-error vale la pena en producción — pero es justo por lo que los errores se escondían. La lección no es "deja de degradar con elegancia"; es "en desarrollo, un 404 a tu propio backend debería ser imposible de ignorar".

---

## Impacto

La recompensa visible: noticias, explicaciones de IA, el Python Lab, el calendario económico, las pantallas de fondos, los ítems de listas de seguimiento — funciones que en silencio no mostraban nada — funcionan de verdad. La recompensa más callada es un **método repetible**: arranca el backend, diferencia cada llamada del cliente contra `/openapi.json`, token-y-curl a las supervivientes. Es ahora lo primero a lo que echar mano cuando una función "no muestra nada" — ¿está vacía, o está haciendo un 404 disfrazado?

---

## Alcance: lo que esto *no* es

- **Es una auditoría puntual, no una barrera.** El frontend y el backend pueden volver a separarse al día siguiente. El arreglo duradero es dejar de escribir el cliente a mano y *generarlo* desde el `/openapi.json` (o añadir tests de contrato que fallen cuando una ruta llamada no se sirve) — para que la deriva sea un build en rojo, no un panel vacío y silencioso. Queda anotado como seguimiento, no hecho aquí.
- **No todo "vacío" es un error de cableado.** Algunos paneles están legítimamente vacíos (sin noticias para un ticker oscuro). El valor de la auditoría es distinguir ambos con certeza en vez de adivinar.

---

## Qué viene después

Con esta escrita, la serie *OpenTerminalUI* ha contado todas las historias que debía — la premisa y la reconstrucción de-India (#1), el cliente LLM que corre sobre cualquier cerebro (#3), la versión de integridad 1.0 (#4) y el arco de la-cartera-se-vuelve-real / privacidad (#5–#6). Lo que viene es el producto en sí: **v1.2 — investigación que interroga**, la capa de "¿esto es puro humo?" que apunta el modelo local a tu propia tesis en vez de adularla.

---

## Lecturas relacionadas

- [OpenTerminalUI — Bifurcar un terminal financiero para trabajar más allá de India](/blog/openterminalui-bifurcar-terminal-financiero-de-india) — #1: la premisa y la reconstrucción de la capa de datos que preparó esta deriva.
- [OpenTerminalUI — Un terminal que funciona con cualquier cerebro](/blog/openterminalui-funciona-con-cualquier-cerebro) — #3: el cliente LLM agnóstico de proveedor sobre el que corren las funciones de IA (algunas mal cableadas aquí).
- [OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) — #4: por qué "nunca mostrar datos inventados como reales" se volvió el tema de la versión.
- **Código:** el fork vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(Una nota de transparencia, en el espíritu de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que hizo la auditoría que describe, y que eligió una pasada cuidadosa en vez de abrirse en treinta y tres.)*
