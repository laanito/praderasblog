---
Title: OpenTerminalUI — Hacer real la cartera (sin mentir sobre ella)
Description: La versión 1.1 convirtió una cartera de demostración en una de verdad — efectivo que no se puede falsear, beneficio realizado que dejó de inflarse y una cartera "heredada" que resultó estar compartida en secreto entre usuarios.
Date: 2026-07-02 07:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Economia, Privacidad
Series: OpenTerminalUI — Bifurcando un terminal financiero
Series_Slug: openterminalui
Series_Order: 5
Lang: es
Translation_Key: openterminalui-portfolio-real
Image: /assets/images/openterminalui-05-portfolio-real-hero.webp

---

# OpenTerminalUI — Hacer real la cartera (sin mentir sobre ella)

La [versión 1.0](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) iba de que el terminal nunca mostrara un dato *de mercado* inventado como si fuera real. La **1.1** giró esa misma lente hacia dentro, hacia la única parte de la aplicación que se supone enteramente *tuya*: tu cartera. El titular es "la cartera se vuelve real". La historia más interesante son las tres maneras en que, en silencio, no era honesta — y cómo arreglarlas rimó con todo lo que este fork defiende.

> Esta es la entrega **5** de la serie *OpenTerminalUI*. Los dos análisis en profundidad prometidos en la entrega 1 (la auditoría del cableado y la historia del "corre sobre cualquier IA") siguen reservados como #2 y #3; esta se adelanta al hito actual.

---

## Contexto: una cartera que solo se podía borrar

OpenTerminalUI es un terminal financiero autoalojado que ejecutas en tu propia máquina. Entregas anteriores contaron [por qué lo bifurcamos y reconstruimos su capa de datos centrada en India](/blog/openterminalui-bifurcar-terminal-financiero-de-india), y [por qué la 1.0 gastó todo su presupuesto en integridad](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion).

La estrella polar del fork es un terminal abierto y **privado** que ayuda a una persona a invertir **sin dejarse engañar** — tampoco por la propia herramienta. Se divide en dos mitades: *que no te engañen* (la investigación) y *crecer en privado* (tu cartera). La 1.0 endureció la primera. La cartera — la segunda mitad — seguía siendo una demo. Podías añadir una posición y podías borrarla. Nada más. Sin efectivo, sin registro de una operación, sin idea de lo que realmente habías ganado o perdido. Una cartera que solo admite añadir y borrar no es una cartera; es una lista de deseos.

Unos términos, definidos una vez:

- **Libro mayor** (*ledger*) — la lista continua de cada transacción (compra, venta, dividendo, ingreso, retirada). La fuente de verdad.
- **P&L realizado frente a no realizado** — *P&L* es pérdidas y ganancias. **No realizado** son las plusvalías sobre el papel de lo que aún tienes; **realizado** es lo que de verdad te embolsaste al vender.
- **Coste de adquisición** — lo que pagaste originalmente por una posición. Lo necesitas para saber una *ganancia*, y no solo unos ingresos.

---

## Problema uno: efectivo del que no te podías fiar

Para que comprar y vender signifiquen algo, la cartera necesita efectivo. El diseño tentador es un campo `cash_balance` que sumas y restas según ocurren las operaciones. También es una trampa: en cuanto un saldo almacenado puede desviarse de las transacciones que lo produjeron, tienes dos fuentes de verdad y ninguna forma de saber cuál miente.

Así que no almacenamos el efectivo en absoluto. **El efectivo se *deriva* del libro mayor** — saldo inicial, más cada abono, menos cada cargo, calculado de nuevo cada vez. Una compra carga efectivo; una venta, un dividendo o un ingreso lo abonan; una retirada lo carga, y las comisiones siempre cuestan. Hay exactamente una fuente de verdad: la lista de lo que pasó.

Un efecto secundario simpático, muy en el espíritu de la serie: si registras posiciones sin financiarlas, tu efectivo se vuelve **negativo** — y lo mostramos, en negativo. No es un error que tapar; es la señal honesta que dice "registra el ingreso que de verdad hiciste". La alternativa — recortar el efectivo a cero en silencio, o inventar un saldo — sería otra mentira educada.

---

## Problema dos: un beneficio que se inflaba

Este es primo directo de la historia de 1.0 sobre "el software que miente con educación", escondido en las analíticas desde el principio.

El terminal informaba de tu **beneficio realizado** sumando lo que *recibías* de las ventas: acciones por precio de venta, menos comisiones. Eso no es beneficio — son **ingresos**. Si compraste una acción por 990 $ y la vendiste por 1.000 $, el código anotaba **1.000 $** de "ganancias realizadas" en lugar de **10 $**. El número era preciso, seguro y erróneo por todo el coste de adquisición — exactamente el modo de fallo que todo el proyecto existe para evitar, salvo que esta vez el invento era sobre *tu propio dinero*.

El arreglo es calcular una *ganancia*, lo que significa restar lo que pagaste. Reproducimos el libro mayor en orden cronológico, arrastrando un coste medio por posición, y en cada venta anotamos `acciones × (precio de venta − coste medio) − comisiones`. Los dividendos se cuentan como ingresos, aparte, en vez de colarse entre las plusvalías. Las dos tarjetas principales también se reetiquetaron: lo que se llamaba "P&L total" solo era nunca la cifra *no realizada*, así que ahora lo dice.

---

## Problema tres: una cartera "privada" que estaba compartida

Mientras cableábamos lo nuevo, surgió una pregunta justa: ¿qué *es* la cartera "heredada" más antigua que la aplicación todavía arrastraba junto a la nueva? La respuesta era incómoda.

La cartera heredada era una **única tabla global sin noción de usuario**. Cada ruta leía *todas* las posiciones, sin filtrar. En una instalación autoalojada de una sola persona eso es invisible. Pero en cuanto dos personas comparten una instancia, verían y editarían **la misma cartera** — las posiciones de la otra, una lista compartida. Para un terminal cuyo argumento incluye la palabra **privado**, una cartera compartida globalmente no es un error pequeño; es una contradicción de la premisa.

La cartera "Manager" más nueva es el modelo correcto: por usuario, varias carteras con nombre, respaldadas por el libro mayor que acabábamos de hacer honesto. Así que la 1.1 también inició la migración fuera del modelo heredado — hizo el Manager de verdad accesible (estaba oculto tras un parámetro de URL, sin ningún botón que llevara a él) y añadió un **Importar desde la heredada** de un clic para que nadie pierda las posiciones que había introducido. Retirar la tabla global por completo se programa a propósito como trabajo posterior, pero la rampa de acceso está construida y el rumbo, fijado.

---

## Por qué esta forma

- **Una fuente de verdad gana a dos que coinciden casi siempre.** Derivar el efectivo del libro mayor implica que no puede desviarse. Un saldo almacenado es una segunda verdad esperando a discrepar de la primera.
- **Un número erróneo sobre tu propio dinero es el peor.** Un beneficio realizado inflado no solo engaña — lo hace sobre exactamente lo que abriste la herramienta para entender. La precisión se lee como verdad, así que una respuesta precisa y equivocada es peor que un hueco en blanco.
- **Privado tiene que significar privado.** Una función que filtra la cartera de un usuario a otro no es una función con una salvedad; en este proyecto es un defecto de la tesis.

---

## Impacto

La cartera es ahora algo por lo que de verdad puedes hacer pasar una estrategia: financiarla, comprar, vender, cobrar dividendos, retirar — y leer un saldo de efectivo y un desglose realizado/no realizado que reflejan la realidad en lugar de adularla. Los próximos dividendos y eventos corporativos aparecen ahora junto a las posiciones que los sentirán. Y el camino del viejo modelo compartido a uno por usuario está abierto, sin abandonar los datos de nadie.

---

## Alcance: lo que la 1.1 *no* es

En el espíritu de la versión:

- **La cartera heredada aún no ha desaparecido.** La 1.1 hace accesible e importable la cartera moderna; borrar de verdad la tabla global (y trasladar sus analíticas más ricas) es trabajo futuro, registrado abiertamente.
- **Sin contabilidad de lotes fiscales.** El coste de adquisición a efectos fiscales es un agujero específico de cada jurisdicción donde un número erróneo es peor que ninguno, así que queda deliberadamente fuera de alcance.
- **La rentabilidad anualizada sigue siendo simple.** Mide el patrimonio actual frente al saldo inicial; todavía no hace rentabilidades ponderadas por tiempo con ingresos y retiradas intermedias. Honesta y útil, aún no sofisticada.

---

## Qué viene después

- **Terminar la consolidación:** retirar la cartera heredada global, llevando sus analíticas al modelo por usuario, y cerrar de una vez el hueco de la cartera compartida.
- **v1.2 — investigación que interroga:** apoyarse en la mitad de "que no te engañen" — una capa de "¿esto es puro humo?" que use la IA local y tus propias notas de forma adversaria, no como animadora.
- Y los dos análisis en profundidad aún pendientes de la entrega 1: la auditoría del cableado y la reconstrucción del "corre sobre cualquier IA".

---

## Lecturas relacionadas

- [OpenTerminalUI — Bifurcar un terminal financiero para trabajar más allá de India](/blog/openterminalui-bifurcar-terminal-financiero-de-india) — entrega 1: la premisa y la reconstrucción de la capa de datos.
- [OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) — entrega 4: por qué la primera versión estable lo gastó todo en no mostrar datos inventados como reales.
- **Código:** el fork vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(Una nota de transparencia, en el espíritu de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que hizo la ingeniería que describe.)*
