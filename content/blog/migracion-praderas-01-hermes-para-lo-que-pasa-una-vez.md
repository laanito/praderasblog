---
Title: Migración Praderas (1) — Hermes para lo que pasa una vez
Description: Empezamos a mudarnos a un VPS nuevo en Time4VPS. Por qué un agente como Hermes encaja mejor que montar Ansible cuando la tarea es infrecuente, y qué dejamos a propósito para el siguiente capítulo.
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

Hay trabajos de infraestructura que no se repiten lo bastante como para merecer un proyecto de automatización “de libro”. Mudar un servidor personal —blog, correo, nube de archivos, un par de sitios estáticos— es uno de ellos: ocurre cada pocos años, el mapa cambia cada vez, y el esfuerzo de dejar un inventario impecable suele costar más de lo que luego ahorra.

Este es el **primer capítulo** de una serie corta sobre la migración del hosting de Praderas. No es un tutorial ni un catálogo de servicios. Es la historia de **por qué estamos usando un agente (Hermes) como herramienta principal**, en lugar de invertir en Ansible u otra capa de infraestructura como código pensada para flotas y para repetir el mismo despliegue una y otra vez.

### El problema no era “no saber cómo”

El servidor anterior cumplía. Funcionaba. También era el resultado de años de decisiones apiladas: un sistema que se iba quedando atrás, techos para usar contenedores con soltura, y la sensación de que cada mejora seria empezaba con un “primero habría que…” y se quedaba ahí.

La mudanza no nace de un apagón ni de un susto. Nace de querer un sitio donde lo que viene después —contenedores de verdad para la nube de archivos, un agente de operaciones en la propia máquina, menos fricción al crecer— no choque con la caja de hoy. Eso se puede apuntar en una hoja y hacer a mano. También se puede convertir en un **proyecto Ansible** con roles, secretos y playbooks… para algo que, si sale bien, no vas a repetir igual en mucho tiempo.

### Ansible brilla cuando repites; aquí lo que pesa es el criterio

No hay nada de malo en Ansible. En un equipo con decenas de máquinas parecidas, o en un producto que se publica cada semana, la inversión se paga sola: pruebas, idempotencia, gente nueva que hereda un procedimiento claro.

En un **servidor de casa** la cuenta es otra:

- El mapa de servicios **no está quieto**: parte del trabajo es decidir qué se queda, qué se retira y qué se renombra —por ejemplo un nombre nuevo para el correo mientras el viejo sigue recibiendo.
- El camino **no es recto**: a veces conviene mover primero la web y dejar el correo en la máquina antigua meses; a veces no se puede reutilizar la dirección y hay que convivir con dos cajas a la vez.
- Lo que importa es **contexto** —privacidad, un proveedor de confianza, “¿qué quiero dentro de un año?”— no solo la lista de paquetes.

Un playbook te empuja a **cerrar** esas decisiones demasiado pronto. Un agente te deja **explorar, proponer, corregir y dejar rastro**, mientras el criterio humano se guarda para las horquillas de verdad: dónde hospedar, si el correo se queda en casa, qué se mueve primero.

### Hermes como herramienta, no como magia

En la práctica, Hermes ha hecho de ayudante de operaciones muy paciente: mirar qué corre de verdad (no lo que creías que corría), redactar un plan compartido, dejar lista la máquina nueva, llevar el blog y los sitios estáticos al destino, y aparcar el correo y la nube de archivos para cuando toque. Tú sigues al mando —DNS, proveedor, “sí / no / espera”—. El agente se come el trabajo repetible y el primer borrador de las decisiones.

Eso **no sustituye** el criterio. Sustituye la tentación de montar un mini-proyecto de plataforma para un acontecimiento que pasa una vez. Aprender a trabajar con el agente sobre *este* servidor se nota enseguida; un inventario Ansible perfecto para *un solo* VPS casi nunca se amortiza.

### Dónde hospedamos: Time4VPS

Para la caja nueva elegimos **[Time4VPS](https://billing.time4vps.com/?affid=8565)**: proveedor europeo con el que ya teníamos trato, buena relación calidad-precio en VPS con RAM y disco de sobra para lo que necesitamos, y margen para mantener el correo en la máquina antigua el tiempo que haga falta mientras la nueva se gana la confianza. No es un anuncio disfrazado de artículo: es el dato concreto de *dónde* corren ya el blog y los sitios estáticos, por si a alguien le sirve el mismo camino.

### Qué ya se movió (sin el manual)

En este primer tramo la web “ligera” ya vive en la máquina nueva: el blog en Pico y un par de sitios estáticos, con HTTPS y la costumbre de publicar con un `git pull` en el directorio correcto. El resto —correo, nube de archivos— sigue en la caja antigua a propósito. Convivir con dos máquinas no es un fallo; es un colchón.

### Lo que no vas a encontrar aquí

No hay listas de puertos, copias de configuración ni recetas de endurecimiento. Queda fuera a propósito: la serie habla de **proceso y criterio**, no de regalar un mapa de ataque ni de invitar a clonar un setup ajeno sin entenderlo.

### Próximo capítulo

Cuando toque la **nube de archivos (Nextcloud)** —el trozo gordo de datos y el que más se beneficia de un host con Docker de verdad— escribiremos el segundo artículo: qué implica moverla, qué dejamos en paralelo y qué aprendimos con Hermes en un trabajo que sí duele si se hace mal.

Hasta entonces, el blog ya respira en casa nueva. El resto de la mudanza, a su ritmo.
