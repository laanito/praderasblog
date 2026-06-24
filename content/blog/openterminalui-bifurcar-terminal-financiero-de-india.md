---
Title: OpenTerminalUI — Bifurcar un terminal financiero para que funcione fuera de India
Description: Por qué bifurcamos un terminal financiero de código abierto al estilo Bloomberg y reconstruimos su capa de datos centrada en India para cubrir mercados de EE. UU., Europa y cripto desde cualquier lugar.
Date: 2026-06-24 06:00PM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Economia
Series: OpenTerminalUI — Bifurcando un terminal financiero
Series_Slug: openterminalui
Series_Order: 1
Lang: es
Translation_Key: openterminalui-intro-de-india
Image: /assets/images/openterminalui-01-de-india-intro-hero.webp

---

# OpenTerminalUI — Bifurcar un terminal financiero para que funcione fuera de India

Hay un tipo de software del que solo te das cuenta cuando, sin decirlo, da por hecho que vives en otro sitio. **OpenTerminalUI** es uno de ellos: un terminal financiero de código abierto y autoalojado —gráficos en vivo, *screeners*, carteras, analítica de opciones, una consola de comandos al estilo Bloomberg— que es excelente y está pensado, ante todo, para el **mercado indio**. Esta serie va de bifurcarlo para que sirva al resto. Esta primera entrega plantea la premisa y el trabajo más grande hasta ahora: quitarle la "India" a la capa de datos sin tirar por la borda lo que ya funciona.

> Esta es la entrega **1** de la serie *OpenTerminalUI*. Las siguientes entran a fondo en dos historias concretas; aquí va el panorama general y por qué existe el proyecto.

---

## Contexto: qué es OpenTerminalUI

OpenTerminalUI es un terminal financiero completo que ejecutas en tu propia máquina: un *backend* en Python/FastAPI, un *frontend* en React, cotizaciones en tiempo real, más de 70 indicadores técnicos, gráficos multipanel, un *screener*, analítica de cartera e *insights* asistidos por IA. Apunta a muchos mercados, pero sus valores por defecto y sus tuberías crecieron alrededor de India.

Unos cuantos términos, definidos una vez para que el resto se lea con soltura:

- **NSE / BSE** — las dos grandes bolsas de India (la Nacional y la de Bombay).
- **NIFTY / SENSEX** — los índices de referencia de India, el equivalente local al S&P 500 o al Dow.
- **Ticker** — el símbolo corto de un instrumento (`AAPL`, `RELIANCE`).
- **F&O** — *futuros y opciones*, la parte de derivados del terminal.

Nada de esto es un defecto. Es, sencillamente, un proyecto con una casa, y esa casa es India.

---

## El problema: desde fuera de India, la mitad se apaga

El problema empieza en cuanto lo ejecutas desde, por ejemplo, Europa. Buena parte de los datos pasa en silencio por fuentes **solo de India** —una integración con un bróker doméstico (Zerodha Kite) y *scrapers* que leen páginas de la bolsa india— y esas llamadas devuelven **404/403** desde el extranjero. Los valores por defecto lo agravan: la pantalla de inicio sigue a NIFTY y SENSEX, el ticker de ejemplo es RELIANCE, los precios se formatean en **₹ (INR)** y los *benchmarks* preconfigurados apuntan a índices indios. Una persona usuaria de EE. UU. o Europa abre el terminal y se encuentra un muro de paneles vacíos y símbolos de rupia sobre acciones cotizadas en dólares.

Así que el objetivo de la bifurcación es estrecho y concreto: **que el terminal se sienta nativo en mercados de EE. UU., Europa y cripto**, a poder ser sin reescribirlo y sin pagar por los datos.

---

## Qué hicimos: reconstruir la capa de datos sobre fuentes globales y gratuitas

El corazón del trabajo fue el **universo de símbolos** —la lista de instrumentos que puedes buscar y cargar—. En el proyecto original se sembraba desde un CSV de la bolsa india; lo sustituimos por una única tabla de instrumentos que se auto-siembra y se nutre de fuentes **gratuitas**:

| Mercado | Fuente gratuita |
|---------|-----------------|
| Acciones y ETFs de EE. UU. | ficheros públicos de listados de Nasdaq Trader |
| Acciones de Europa / Reino Unido | un paquete pip con los componentes de los índices (ISIN + símbolo de Yahoo + divisa) |
| Cripto | la API sin clave de CoinGecko (universo real ordenado por capitalización) |

Sobre esa tabla mejoramos lo que hace que un terminal se sienta bien:

- **Una búsqueda que encuentra lo que quieres decir** — insensible a acentos (para que *nestle* encuentre *Nestlé*) y ordenada por relevancia y por el mercado que tengas seleccionado, en vez de por el orden de inserción.
- **Enrutado de símbolos extranjeros** — los símbolos con sufijos europeos (`.L` Londres, `.DE` Fráncfort, `.PA` París…) ahora se clasifican de forma determinista y se piden a Yahoo, esquivando la ruta del bróker indio que antes daba 403.
- **Una cabina sin India** — los widgets de inicio, panel y cinta de cotización muestran ahora **S&P 500 / NASDAQ / DOW** (que el *backend* ya devolvía, sin usar) en lugar de NIFTY/SENSEX.
- **Cripto en vivo** — *ticks* en tiempo real vía el WebSocket público de Binance, ya que el cripto cotiza 24/7 y no hay un flujo gratuito de *ticks* para índices de EE. UU.

---

## Por qué este camino, y no una reescritura

Tres decisiones de criterio dieron forma a todo:

- **Gratuito y sin claves antes que de pago.** Los ficheros de Nasdaq Trader, un paquete de componentes, CoinGecko y Yahoo cubren EE. UU./Europa/cripto sin claves de API. Un terminal que alojas tú no debería exigir un contrato de datos para arrancar.
- **Híbrido antes que un extremo.** En vez de un volcado estático gigante *o* una llamada en vivo en cada pulsación, el universo se siembra en una base de datos que se auto-puebla en el primer arranque y se refresca periódicamente, con un **respaldo en vivo a Yahoo** para lo que falte en caché (y se reescribe de forma perezosa). Caso común rápido, sin callejones sin salida.
- **Reutilizar lo que ya funcionaba.** Yahoo ya servía cotizaciones y gráficos perfectamente para símbolos de EE. UU. y Europa; el arreglo fue, sobre todo, *dejar de enrutar esos símbolos por la ruta india*, no cambiar la fuente. El cambio más barato que funciona es el que se prefiere.

---

## Impacto

El terminal abre ahora, por defecto, a un mundo de EE. UU. / Europa / cripto y es realmente usable desde fuera de India: la búsqueda devuelve instrumentos globales, los *tickers* extranjeros cargan cotizaciones y gráficos, la vista general muestra índices occidentales y el cripto fluye en vivo. Las pantallas que antes daban 404 ahora tienen datos.

---

## Alcance: qué sigue, a propósito, con sabor a India

Honestidad sobre los bordes:

- **Las opciones (F&O)** siguen orientadas a India — ese módulo no formó parte de esta etapa.
- **La divisa de visualización en EUR** aún no está conectada. Verás todavía algo de formato `₹`/`INR` hasta que llegue la conversión real de divisas; reetiquetar los números a `$`/`€` *sin* convertirlos sería peor que dejarlos, así que espera.
- Sobreviven, **a propósito**, un puñado de valores por defecto indios — son la rama "India" de condicionales del tipo "si el mercado seleccionado es X", lo cual es comportamiento correcto, no resto olvidado.

---

## Qué viene después

Dos entregas siguientes, cada una una historia autocontenida:

- **El error que parecían 33 errores.** Toda una clase de llamadas del *frontend* apuntaban a rutas del *backend* que no existían —una fina capa de API escrita contra un contrato que el servidor nunca sirvió—. Auditarlo convirtió "33 cosas rotas" en un puñado de patrones con una sola causa raíz. Una pequeña historia de detectives sobre leer un código desde fuera.
- **Un terminal que funciona con cualquier cerebro.** Las funciones de IA estaban atadas a un servidor de modelo local concreto; hicimos que hablen el estándar compatible con OpenAI para que funcionen con **cualquier** proveedor — un [Ollama](https://ollama.com/) local, una API alojada, lo que le apuntes. Muy en la línea de este blog.

---

## Leer y explorar

- **Código:** la bifurcación vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI) (bifurcado del proyecto original OpenTerminalUI). Las notas de ingeniería del día a día están en la carpeta `.agents/` del repositorio, escritas para que las lean tanto personas como agentes.
- Esta es la entrega **1** de *OpenTerminalUI*. Las dos siguientes cuentan, desde dentro, la auditoría del cableado y la historia del LLM local.

*(Una nota de transparencia, en el espíritu de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que hizo la ingeniería que describe.)*
