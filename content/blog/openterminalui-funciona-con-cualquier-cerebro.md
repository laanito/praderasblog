---
Title: OpenTerminalUI — Un terminal que funciona con cualquier cerebro
Description: Las funciones de IA de OpenTerminalUI no dependen del modelo que ejecutes. Un cliente pequeño habla una única especificación — así el mismo código funciona con un Ollama local, LM Studio o una API alojada, y degrada con honestidad cuando no hay modelo.
Date: 2026-07-08 06:00PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Privacidad, Sistemas
Series: OpenTerminalUI — Bifurcando un terminal financiero
Series_Slug: openterminalui
Series_Order: 3
Lang: es
Translation_Key: openterminalui-run-on-any-brain
Image: /assets/images/openterminalui-03-any-brain-hero.webp

---

# OpenTerminalUI — Un terminal que funciona con cualquier cerebro

Gran parte de la historia de OpenTerminalUI hasta ahora ha ido sobre los *datos* — ser honesto con las cifras de mercado, hacer privada la cartera. Esta va sobre el *cerebro*: el modelo de lenguaje detrás de las funciones de IA del terminal. El objetivo de diseño era rotundo y algo a contracorriente para 2026 — **el terminal no debe depender del modelo que ejecutes.** Local o alojado, pesos abiertos o una gran API, tu portátil o la nube de alguien: el mismo código, las mismas funciones, y si no hay ningún modelo, un honesto "no disponible" en vez de una respuesta inventada.

Este es el análisis en profundidad que debía desde la [entrega 1](/blog/openterminalui-bifurcar-terminal-financiero-de-india) como #3 — llega después de los hitos, pero es la pieza que hace que las funciones de IA de la [1.0](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) y del trabajo de cartera sean de verdad portables.

---

## Contexto: qué significan aquí las "funciones de IA"

Varias pantallas del terminal entregan al modelo unos datos estructurados y le piden un análisis breve y por secciones: un informe de inversión para una acción, una lectura en lenguaje claro de un backtest, una narrativa de riesgo para tu cartera. Aparte, el "segundo cerebro" privado necesita **embeddings** — huellas numéricas del texto para encontrar tus notas por significado, no por palabra clave. (*Un LLM es un gran modelo de lenguaje — lo que escribe la prosa. Los embeddings son otra llamada al mismo tipo de servidor que convierte texto en vectores para buscar.*)

Todo eso necesita hablar con *algún* servidor de modelo. La pregunta es **cuál**, y la respuesta en la que insiste este fork es: **aquel al que lo apuntes.**

---

## El problema: una integración por proveedor es una trampa

El linaje previo al fork había seguido la ruta habitual — código específico por proveedor. Había un cliente de LM Studio, y la suposición de que ejecutarías *ese*. Funciona hasta que ya no quieres LM Studio: ahora escribes una segunda integración para Ollama, una tercera para una API alojada, cada una con sus manías, y las funciones se bifurcan en "va con X pero no con Y". Para un terminal autoalojado y **con la privacidad por delante** eso es justo al revés — el propósito es que *tú* elijas dónde ocurre la inferencia, idealmente en tu propia máquina.

Hay un accidente afortunado que evita la trampa: casi todo habla ya la **especificación de chat-completions de OpenAI**. Ollama, LM Studio, vLLM, llama.cpp, la propia OpenAI, OpenRouter, Groq, Together — todos exponen la misma forma `POST /chat/completions`. Si apuntas a la *especificación* en vez de a un *producto*, "qué proveedor" se reduce a una línea de configuración.

---

## Qué hicimos

El fork sustituyó las rutas específicas por proveedor por **un único cliente asíncrono pequeño** que habla esa especificación compartida — chat-completions y su hermano de embeddings — y nada más. Unas pocas decisiones le dan su carácter:

- **Apúntalo a cualquier sitio con una URL base.** Por defecto es un Ollama local (`http://localhost:11434/v1`); cambia un ajuste y es LM Studio, o un endpoint alojado. Los servidores locales ignoran la clave de API; los alojados la reciben como token bearer — así el *mismo* cliente cubre ambos sin una bifurcación.
- **Una escalera de salida estructurada.** Algunas pantallas necesitan JSON de vuelta, no prosa. Pero los proveedores discrepan en cómo se pide JSON estricto: los endpoints de nivel OpenAI aceptan un `json_schema` completo; otros solo un "modo JSON" laxo; algunos, nada. Así que el cliente prueba primero la forma más estricta y **baja peldaños** — esquema estricto → JSON laxo → texto plano — cada vez que un endpoint rechaza el anterior. Obtienes toda la estructura que ese cerebro admita, nunca un fallo duro porque no soportara la opción más sofisticada.
- **Degradación honesta, no invención.** Antes de preguntar nada, la función comprueba que el endpoint es alcanzable; si el modelo está apagado o inalcanzable, la respuesta es un *"El análisis con IA no está disponible — arranca tu modelo local"* etiquetado. Nunca uno inventado. Es la misma regla de integridad que sigue el resto del terminal, aplicada al cerebro.
- **Un cliente, muchas funciones.** Los informes, la narrativa de riesgo, la lectura de sentimiento de noticias y los embeddings del segundo cerebro pasan todos por este único cliente y un esquema de salida compartido — así una sola tarjeta del frontend renderiza cada tipo de análisis, y hay exactamente un sitio donde razonar sobre tiempos de espera, claves y salidas de emergencia.

El viejo código específico de LM Studio no tuvo un funeral dramático; sus ajustes siguen vivos como alias silenciosos de retrocompatibilidad para que a nadie se le rompa su configuración, y ahora todo pasa por el único cliente.

---

## Por qué este camino

- **Apunta a la especificación, no al proveedor.** Apostar por la forma compatible con OpenAI (en vez de por un producto) es lo que convierte "soportar otro proveedor" en "cambiar una URL". Es la clase más barata de a prueba de futuro: el ecosistema ya convergió; solo nos apoyamos en ello.
- **Degrada por peldaños, no por precipicios.** Suponer que todo endpoint soporta esquemas JSON estrictos rompería la mitad de los servidores locales que la gente ejecuta de verdad. La escalera hace que la función vaya en un modelo local modesto *y* obtenga garantías más estrictas en uno capaz — el mismo código.
- **Local por defecto es una postura de privacidad, no un atajo de demo.** Poner Ollama en `localhost` por defecto significa que la experiencia recién instalada envía tus posiciones y tus notas a **tu propia máquina**, no a un tercero. Apuntar a una API alojada es una decisión deliberada que tomas tú, no el valor por defecto del que hay que escapar.

---

## Impacto

La recompensa práctica: puedes ejecutar la IA del terminal en un portátil con Ollama y no enviar ni un byte de datos de cartera fuera del dispositivo — o apuntarlo a un gran modelo alojado para un análisis más afilado — y **nada en el código de las funciones cambia**. Añadir un proveedor nuevo es configuración, no desarrollo. Y como los embeddings van sobre el mismo cliente, la búsqueda del segundo cerebro privado es tan portable como las funciones de prosa.

También mantiene las funciones de IA *honestas por construcción*: sin modelo configurado lo dicen, con claridad, en todas partes — la narrativa de riesgo, los informes, la búsqueda. Ninguna pantalla inventa un análisis en silencio para parecer ocupada.

---

## Alcance: lo que esto *no* es

- **Aún sin streaming.** Las respuestas llegan enteras, no token a token. Bien para análisis breves por secciones; una mejora futura para generaciones largas.
- **Estructura de mejor esfuerzo, no una garantía.** La escalera maximiza la estructura por proveedor, pero un modelo local pequeño aún puede devolver algo que el esquema no honre del todo — así que el parseo tolera bloques de código y prosa suelta, y las funciones sanean lo que reciben en vez de confiar a ciegas.
- **No es un recomendador de modelos.** El terminal ejecuta *tu* elección de cerebro; no clasifica modelos ni gestiona su ciclo de vida. Eso es cosa tuya (o de Ollama).

---

## Qué viene después

Este cliente es el cimiento silencioso sobre el que se apoya con fuerza la próxima versión: **v1.2 — investigación que interroga**, una capa de "¿esto es puro humo?" que usa tu modelo local y tus propias notas de forma *adversaria*, no como animadora. Cuando el cerebro es intercambiable y privado por defecto, una función de interrogación es algo en lo que de verdad puedes confiar que se quede de tu lado de la pantalla. Aún pendiente de la #1: la historia detectivesca de la auditoría del cableado (#2).

---

## Lecturas relacionadas

- [OpenTerminalUI — Bifurcar un terminal financiero para trabajar más allá de India](/blog/openterminalui-bifurcar-terminal-financiero-de-india) — #1: la premisa y la reconstrucción de la capa de datos.
- [OpenTerminalUI — Lanzar la 1.0, cuando la integridad es la función](/blog/openterminalui-lanzar-1-0-cuando-la-integridad-es-la-funcion) — #4: por qué la primera versión estable se negó a mostrar datos inventados como reales.
- [OpenTerminalUI — Retirar la cartera que estaba compartida en secreto](/blog/openterminalui-retirar-la-cartera-compartida) — #6: la consolidación de la cartera privada, donde aparece la narrativa de IA local.
- **Código:** el fork vive en [github.com/laanito/OpenTerminalUI](https://github.com/laanito/OpenTerminalUI).

*(Una nota de transparencia, en el espíritu de este blog: este artículo lo escribió un agente de IA bajo dirección humana — el mismo agente que hizo la ingeniería que describe. Apropiado, para un artículo sobre qué cerebro dirige el cotarro.)*
