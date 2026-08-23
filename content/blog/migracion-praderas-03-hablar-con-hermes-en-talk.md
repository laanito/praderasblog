---
Title: Migración Praderas (3) — Hablar con Hermes en Talk
Description: Cómo dejar un Hermes de verdad contestando en Nextcloud Talk: no el envoltorio en un contenedor, sino el agente de siempre, con Docker solo para aislar lo que ejecuta.
Date: 2026-08-23 11:00PM
Template: post
Author: Luis Amigo
Tags: Sistemas, Productividad, Inteligencia Artificial
Lang: es
Translation_Key: migracion-praderas-03-hermes-talk
Series: Migración Praderas
Series_Slug: migracion-praderas
Series_Order: 3
Image: /assets/images/migracion-praderas-03-hermes-talk-hero.webp
---

En el [capítulo anterior](/blog/migracion-praderas-02-la-nube-en-casa-nueva) la nube ya estaba en el VPS nuevo. El siguiente impulso era obvio: **hablar con un agente en el mismo sitio donde ya hablamos entre nosotros**. No otro Telegram, no otra pestaña. Nextcloud Talk.

Eso suena a integración de media hora. No lo es. El fallo no está en el chat. Está en **qué programa crees que estás llamando** cuando escribes `hermes`.

### Lo que queríamos

Hay un Hermes de cabecera —el de siempre, en el portátil— y hace falta otro que viva **encendido**. El de casa entiende el contexto largo. El de servidor responde a las tres de la mañana, mira un servicio, deja un archivo, vuelve a Talk. Mismo apellido, distinto trabajo.

Nextcloud ya sabe de bots: un secreto compartido, un webhook, un mensaje firmado de vuelta a la sala. El puente que une eso con Hermes Agent existe y es de código abierto. La receta pública que encontramos —un post en el foro de Nextcloud— acierta en el dibujo: **Talk llama al puente; el puente llama al Hermes de verdad; Hermes contesta**.

Nosotros, por ganas de “dejarlo todo en Docker”, metimos el agente *dentro* del mismo contenedor que el puente. Quedó limpio en el `compose`. Y dejó de parecerse a una conversación.

### El desvío: el envoltorio no es el agente

El paquete pip que se llama como el CLI instala **una porción**. Arranca. Tiene versión. Acepta `chat -q`. Pero no es la instalación de siempre: le faltan el árbol de skills, la casa (`~/.hermes`) como la espera el agente, y el mismo criterio sobre qué imprimir cuando razona.

El síntoma es casi cómico si no te está pasando a ti. El webhook responde 202. El bot está en la sala. Escribes “hola” y te devuelve **el monólogo interno**: bloques de *explanation*, el turn entero, a veces un error genérico del puente. No es que Talk esté roto. Es que el puente está haciendo su trabajo: **reenvía lo que el CLI escupió a stdout**.

Parchear el puente para que use otro flag y recorte etiquetas es tentador. Es también admitir que estás negociando con un sustituto. Lo dejamos. El arreglo de verdad fue **sacar a Hermes del contenedor**.

### La forma que sí funciona

Docker no sobra. Sobran **las cosas equivocadas dentro de Docker**.

- El **CLI oficial** se instala en el servidor como en cualquier máquina: el instalador, el binario en el `PATH` del usuario, un **perfil** aparte para no mezclarlo con el Hermes del portátil.
- El **puente** es un proceso pequeño en el host (venv + servicio de usuario). Llama a ese binario, no a un `hermes` inventado en una imagen.
- **Docker** queda para el backend de terminal: lo que el agente *ejecuta* va a un contenedor. Eso reduce el radio de daño. No reduce al agente a una librería pip.
- Talk sigue siendo un **bot**, no un usuario con contraseña. El secreto del bot no es la clave de nadie.

Eso es más cerca del tutorial de la comunidad —sin redes privadas de por medio— y más honesto con lo que pedíamos: un Hermes de automatización con **alcance reducido**, no un Hermes recortado.

El ExApp de la tienda de Nextcloud es el mismo puente con otro envoltorio. Pide AppAPI y un demonio que habla con el socket de Docker. Para este tramo no lo necesitamos. El camino standalone basta y no abre esa puerta.

### Cómo se siente cuando encaja

Dejas un mensaje en una sala. El bot no te suelta un volcado. Contesta como si estuvieras en el terminal, solo que el hilo queda en Talk, con el resto de la casa (archivos, avisos) a un clic. El Hermes del portátil sigue siendo el de siempre. Este otro no pretende sustituirlo.

Todavía hay que pulirlo: menos herramientas de las que tiene el de casa, un perfil más seco, avisos que no deberían llegar al chat. Eso es trabajo de después. Hoy cierra el día **haber encontrado la pieza correcta**.

### Reproducción (anónima)

Nada de rutas reales ni secretos. Si copias esto, cambia nombres y genera tus propias claves.

1. Instala Hermes Agent **en el host** (el instalador oficial, no `pip install hermes-agent` como único runtime).
2. Crea un **perfil** solo para Talk. Modelo y proveedor los tuyos. `terminal.backend` en `docker` si quieres aislar la shell. `display.show_reasoning` en falso: Talk no es un TUI.
3. Clona [nextcloud-talk-hermes-bridge](https://github.com/robertlmann02/nextcloud-talk-hermes-bridge), `venv`, `pip install -e .`. No parchees el puente para “arreglar” el stdout: si el CLI es el de verdad, `hermes chat -q` basta.
4. En el `.env` del puente: URL pública de Nextcloud, secreto del bot, `HERMES_BIN` apuntando al binario del host, `HERMES_PROFILE` al perfil de automatización, `HERMES_HOME_DIR` al home del usuario que lo ejecuta. Skills vacías hasta que existan en esa casa. YOLO apagado.
5. Servicio de usuario (systemd) + linger, para que sobreviva al cierre de sesión. Health en `http://127.0.0.1:8788/health`.
6. Si Nextcloud está detrás de un proxy, un `location` que reenvíe `/hook` a ese puerto. El bot se instala con `occ talk:bot:install` (webhook + response) y se añade a **una** sala con `talk:bot:setup`.
7. El secreto del bot se genera una vez y se comparte solo entre Nextcloud y el puente. No uses la contraseña de un humano.

Hay una guía en el [foro de Nextcloud](https://help.nextcloud.com/t/connecting-hermes-agent-to-nextcloud-talk-using-nextcloud-talk-hermes-bridge/246436) que dibuja este mismo flujo. La lección que nos faltaba allí —y que pagamos en una tarde— es una sola frase: **el puente espera al Hermes de verdad, no a un homónimo empaquetado**.

El correo sigue en el servidor viejo. El VPS nuevo de [Time4VPS](https://billing.time4vps.com/?affid=8565) ya tiene nube, llamadas y un agente al que se le puede hablar. Mañana se afina. Hoy alcanza.
