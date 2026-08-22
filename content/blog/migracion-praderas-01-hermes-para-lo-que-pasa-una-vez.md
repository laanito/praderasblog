---
Title: Migración Praderas (1) — Hermes para lo que pasa una vez
Description: Empezamos a mover el servidor de Praderas a un VPS nuevo en Time4VPS. Por qué un agente como Hermes encaja mejor que invertir en Ansible cuando la tarea es infrecuente, y qué dejamos deliberadamente para el siguiente capítulo.
Date: 2026-08-22 09:00PM
Template: post
Author: Luis Amigo
Tags: Sistemas, Productividad, Inteligencia Artificial
Lang: es
Translation_Key: migracion-praderas-01-hermes-once
Series: Migración Praderas
Series_Slug: migracion-praderas
Series_Order: 1
Image: /assets/images/migracion-praderas-01-hermes-once-hero.webp
---

Hay tareas de infraestructura que no se repiten lo bastante como para merecer un proyecto de automatización “de verdad”. Mover un servidor personal —blog, correo, nube de archivos, un par de sitios estáticos— es de esas: ocurre cada pocos años, cambia el mapa cada vez, y el coste de montar un inventario perfecto suele superar el beneficio.

Este es el **primer capítulo** de una serie corta sobre la migración del hosting de Praderas. No es un tutorial de comandos ni un inventario de servicios. Es la historia de **por qué estamos usando un agente (Hermes) como herramienta principal** en lugar de invertir en Ansible u otra capa de infraestructura-as-code pensada para flotas y repetición.

### El problema no era “no saber cómo”

El servidor anterior cumplía. Funcionaba. También era un producto de años de decisiones acumuladas: un sistema operativo que se quedaba atrás, límites para contenedores modernos, y la sensación de que cada mejora seria empezaba con “primero habría que…” y nunca empezaba.

La migración no nace de un incidente dramático. Nace de querer un sitio donde el futuro —contenedores para la nube de archivos, un agente de operaciones en el propio servidor, menos fricción al crecer— no choque con la caja de hoy. Eso se puede planear en una hoja y ejecutar a mano. También se puede convertir en un **proyecto Ansible** con roles, vaults y playbooks… para algo que, si sale bien, no volverás a hacer igual en mucho tiempo.

### Ansible brilla cuando repites; aquí el valor está en el juicio

No hay nada de malo en Ansible. En un equipo con decenas de hosts idénticos, o en un producto que se despliega cada semana, la inversión se amortiza sola: tests, idempotencia, onboarding de gente nueva.

En un **box personal** la aritmética es otra:

- El mapa de servicios **no es estable**: parte del trabajo es decidir qué se queda, qué se retira y qué se renombra (por ejemplo, un hostname de correo nuevo mientras el viejo sigue recibiendo).
- El camino **no es lineal**: a veces conviene mover primero la web y dejar el correo en la máquina antigua meses; a veces no se puede reutilizar la IP y hay que convivir con dos cajas.
- El conocimiento que importa es **contextual** —privacidad, hoster de confianza, “¿qué quiero en un año?”— no solo la lista de paquetes.

Un playbook te obliga a **congelar** esas decisiones demasiado pronto. Un agente te permite **explorar, proponer, corregir y dejar rastro** mientras el criterio humano se reserva para las horquillas que sí importan: dónde hospedar, si el correo se queda en casa, qué se migra primero.

### Hermes como herramienta, no como magia

En la práctica, Hermes ha hecho el trabajo de un ayudante de operaciones muy paciente: inventariar lo que realmente corre (no lo que creías que corría), redactar un plan compartido, preparar la máquina nueva, llevar blog y sitios estáticos al destino y dejar el correo y la nube de archivos para cuando toque. Tú sigues al mando —DNS, proveedor, “sí / no / espera”—; el agente se come el trabajo repetible y el borrador de decisiones.

Eso **no sustituye** el criterio. Sustituye la tentación de montar un mini-proyecto de plataforma para un evento que ocurre una vez. El ROI de aprender a hablar con el agente sobre *este* servidor es alto; el ROI de un inventario Ansible perfecto para *un* VPS es bajo.

### Dónde hospedamos: Time4VPS

Para la caja nueva elegimos **[Time4VPS](https://billing.time4vps.com/?affid=8565)**: proveedor europeo con el que ya teníamos relación, buena relación calidad-precio en VPS con suficiente RAM y disco, y la posibilidad de mantener el correo en la máquina antigua el tiempo que haga falta mientras la nueva se gana la confianza. No es un anuncio disfrazado de artículo: es el dato concreto de *dónde* está corriendo ya el blog y los sitios estáticos, por si a alguien le sirve el mismo camino.

### Qué ya se movió (sin el manual)

En este primer tramo la web “ligera” ya vive en la máquina nueva: el blog en Pico y un par de sitios estáticos, con HTTPS y la costumbre de publicar con un `git pull` en el directorio correcto. El resto del mundo —correo, nube de archivos— sigue en la caja antigua a propósito. Dual-box no es un fallo; es un colchón.

### Qué no es este artículo

No encontrarás aquí listas de puertos, copias de configuración ni recetas de endurecimiento. Eso se queda fuera a propósito: la serie habla de **proceso y criterio**, no de abrir un mapa de ataque ni de clonar un setup ajeno sin entenderlo.

### Próximo capítulo

Cuando toque la **nube de archivos (Nextcloud)** —el trozo gordo de datos y el que más se beneficia de un host con Docker de verdad— escribiremos el segundo artículo de la serie: qué implica moverla, qué dejamos en paralelo y qué aprendimos con Hermes en un trabajo que sí duele si se hace mal.

Hasta entonces, el blog ya respira en casa nueva. El resto del mudanza, a su ritmo.
