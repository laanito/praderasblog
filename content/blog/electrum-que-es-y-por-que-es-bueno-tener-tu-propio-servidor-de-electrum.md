---

Title: Electrum: qué es y por qué es bueno tener tu propio servidor de Electrum
Description: En este artículo hablamos de Electrum: qué es y por qué es bueno tener tu propio servidor de Electrum
Author: Luis Amigo
Date: 2024-03-18 03:29PM
Tags: Sistemas, Crypto, Privacidad
Template: post
Lang: es
Translation_Key: praderas-b7-electrum-server
Image: /assets/images/b7-electrum-server-wallet-hero.webp

---

## Introducción

En este artículo vamos a ver qué es un wallet de bitcoin, qué es electrum y por qué un servidor de electrum ayudará a mantener tu privacidad.

Los bitcoins y las criptomonedas se han vuelto cada vez más populares en los últimos años. Sin embargo, para muchas personas el mundo de las criptomonedas puede parecer complejo y lleno de tecnicismos. Uno de los conceptos más importantes para entender es el de las carteras criptográficas, también conocidas como "wallets". 

Un wallet de bitcoin es un programa o aplicación que te permite almacenar, enviar y recibir bitcoins de forma segura. Existen diferentes tipos de wallets, como los wallets en línea, los wallets de escritorio y los wallets móviles. Una de las opciones más populares es Electrum, un wallet de escritorio de código abierto y gratuito.

Electrum se diferencia de otros wallets porque utiliza una arquitectura cliente-servidor. Esto significa que aunque Electrum se instala localmente en tu ordenador, se conecta a servidores remotos que le proporcionan información sobre la cadena de bloques de Bitcoin. Estos servidores son operados por terceros en los que debes confiar. 

Sin embargo, existe la posibilidad de ejecutar tu propio nodo Electrum en lugar de depender de servidores de terceros. Esto te otorga mayor privacidad y seguridad, ya que tienes el control total sobre tus datos y no dependes de servidores que podrían dejar de estar disponibles o ser atacados. 

En este artículo veremos en detalle qué es Electrum, cómo funciona su arquitectura cliente-servidor y cómo puedes configurar y ejecutar tu propio servidor Electrum de forma fácil.

## ¿Qué es un wallet de bitcoin?
Un wallet de bitcoin es una aplicación que permite a los usuarios almacenar, recibir y enviar bitcoins de forma segura. Los wallets generan parejas de claves públicas y privadas vinculadas a una dirección bitcoin, y usan la clave privada para firmar las transacciones salientes demostrando la propiedad sobre los fondos. Los principales tipos de wallets son:

* Wallets online: Almacenan las claves en servidores en la nube de terceros. Son fáciles de usar pero ofrecen menos privacidad y control sobre las claves.
* Wallets de escritorio: Se descargan e instalan en el ordenador local. Ofrecen mayor control y privacidad que los wallets online al almacenar las claves localmente.
* Wallets móviles: Aplicaciones para teléfonos inteligentes que permiten el uso de criptomonedas de forma portable. Suelen tener funciones más limitadas que los wallets de escritorio.
* Wallets hardware: Dispositivos hardware como llaves USB donde se almacenan las claves de forma aislada del ordenador. Ofrecen uno de los niveles de seguridad más altos.
Factores como la seguridad, privacidad, portabilidad y control de claves son algunas de las características más valoradas por los usuarios al elegir un wallet.

## ¿Qué es Electrum?

Electrum es un wallet de escritorio de código abierto muy popular entre los usuarios de bitcoins. Se caracteriza por utilizar una arquitectura cliente-servidor donde el cliente ligero se conecta a nodos Electrum en la red para obtener información. Esto lo hace más ligero que otros wallets como Bitcoin Core, sin necesidad de descargar toda la cadena de bloques.

Algunas de sus funcionalidades clave son: soporte para múltiples monedas, recuperación de wallets mediante palabras semilla, creación de direcciones vanity, firma de transacciones offline para mayor seguridad, soporte para wallets multisig, gestión de diferentes cuentas, envío programado de pagos y más.

Su código abierto permite que cualquier persona lo audite y contribuya al proyecto. Gracias a ello se ha convertido en uno de los wallets más confiables, ya que al estar su código disponible públicamente se puede verificar en todo momento que no existe ninguna puerta trasera u otro tipo de código malicioso. Electrum es por tanto una opción segura y completa para gestionar bitcoins de forma sencilla.

## ¿Para qué utiliza electrum un servidor?

Electrum se caracteriza por utilizar una arquitectura cliente-servidor. El cliente Electrum es ligero ya que no almacena localmente toda la cadena de bloques de Bitcoin, que actualmente supera los 500GB. En su lugar, se conecta a nodos Electrum en la red, que sí almacenan y mantienen actualizada una copia completa de la cadena de bloques.

Estos servidores, gestionados por voluntarios de todo el mundo, proveen información al cliente como los balances asociados a las direcciones, la confirmación de transacciones y la descarga de datos de bloques individuales cuando el usuario lo necesita. Sin embargo, la información sensible como las claves privadas siempre se mantienen exclusivamente en el dispositivo local del usuario, sin ser transmitidas a los servidores.

Gracias a esta arquitectura, Electrum puede ofrecer una experiencia ligera y de baja latencia, a cambio de tener que confiar en que los servidores no están manipulando la información que transmiten.

## ¿Qué información se comparte con el servidor y por qué puede poner tu privacidad en riesgo?

Para poder mostrar los saldos y confirmar las transacciones del usuario, el cliente Electrum debe compartir con el servidor todas las direcciones públicas asociadas a la cartera. Esto permite que el servidor verifique el estado de dichas direcciones en la blockchain y reporte los datos al usuario.

Sin embargo, esto también implica un riesgo para la privacidad, ya que un servidor malicioso podría vincular todas las direcciones, y por tanto los diferentes wallets de un usuario, con su dirección IP. De esta forma, se podría realizar un seguimiento de los fondos de una persona a lo largo del tiempo incluso usando diferentes carteras.

Además, al centralizar la información de muchos usuarios, los servidores Electrum se convierten en un objetivo de ciberataques que podrían comprometer la privacidad de quienes usan dichos servicios.

## ¿Cómo evitar ese riesgo?

Para evitar estos riesgos de privacidad, es posible ejecutar un nodo Electrum de forma local en lugar de depender de servidores de terceros. Existen diferentes alternativas de software para configurar fácilmente un nodo Electrum en una Raspberry Pi u otro ordenador:

* Electrum Personal Server: Software oficial que simplifica el proceso de configuración e incluye actualizaciones automáticas.
* ElectrumX: Servidor Electrum de código abierto más popular. Requiere un poco más de configuración pero es más ligero.
* Fulcrum: Implementación alternativa de ElectrumX con características adicionales.
* Electrs: Implementación en Rust de un servidor Electrum.
* Electrum Docker: Contenedor Docker con ElectrumX para facilitar el despliegue en VPS, servidores dedicados u otros sistemas.
Al ejecutar tu propio nodo, controlas totalmente tus datos y no dependes de terceros. Además, al no compartir tus direcciones públicas con otros usuarios se evita el riesgo de correlacionar tus transacciones y vincularlas a tu IP.

## Conclusión

En resumen, Electrum es una de las opciones más populares para gestionar bitcoins de forma sencilla y segura. Su arquitectura cliente-servidor permite una experiencia ligera al descargar solo la información necesaria de nodos en la red.

Sin embargo, al compartir las direcciones públicas con servidores de terceros también introduce algunos riesgos para la privacidad de los usuarios. Ejecutar tu propio nodo Electrum es una buena alternativa para eliminar esos riesgos, ya que tienes el control total sobre tus datos y no dependes de servidores externos.

Afortunadamente, existen diversas herramientas de código abierto que facilitan enormemente la configuración de un nodo Electrum personal. De esta forma cualquier usuario preocupado por su privacidad puede disfrutar de las ventajas de Electrum de forma segura y autónoma corriendo su propio servidor.

Esperamos que este artículo haya servido para comprender mejor el funcionamiento de Electrum y sus implicaciones de seguridad y privacidad. ¡Animamos a cualquiera interesado a probar a ejecutar su propio nodo!
