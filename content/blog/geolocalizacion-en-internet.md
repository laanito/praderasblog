---
Title: Geolocalización en Internet
Description: Explicamos cómo es posible mapear una dirección IP en una ubicación física
Date: 2020-06-23
Author: Luis Amigo
Template: post
Tags: Privacidad
Translation_Key: praderas-b3-geolocation
---

### ¿Cómo funciona Internet?
Para entender cómo funciona internet lo más fácil es hacernos la idea de que internet es como una red de carreteras, si queremos ir desde nuestra ciudad a una ciudad lejana tendremos que trazar una ruta hasta nuestro destino y, seguramente, parte del trayecto lo haríamos por grandes autovías y otras partes por carreteras convencionales. En esta ruta tendremos que cambiar de carreteras varias veces, habitualmente en nudos de comunicación (que en el caso de las carreteras suelen coincidir con ciudades)

Internet funciona de una forma muy parecida, la información viaja desde su origen al destino a través de una red de caminos, a veces por grandes autopistas de la información y otras veces por caminos locales de menor capacidad, unas redes se unen con otras en nudos de comunicación (conocidos como nodos).

### ¿Qué es una dirección IP?
Cada uno de estos puntos de red se identifica con un identificador único, este identificador único se conoce como dirección IP, hay dos formatos de direcciones IP, el de toda la vida - IPv4 - que es una combinación de cuatro enteros entre 1 y 255 (xxx.xxx.xxx.xxx) lo que nos da algo más de cuatro mil millones de posibles combinaciones de números. Este número ya se ha quedado pequeñito para la enorme cantidad de dispositivos que se conectan a internet, por lo que las organizaciones que marcan los estándares en internet crearon un nuevo formato (conocido como ipv6), esta vez decidieron no quedarse cortos y la dirección la forman 8 grupos de números entre 1 y 65535 en formato hexadecimal, lo que da la nada describible cantidad de 3.4 * 1038 direcciones. (algo así como 340 sextillones si utilizamos la escala numérica larga).

### ¿Qué dice la dirección IP sobre mí?
Para que los datos que solicitas puedan encontrar el camino de vuelta, en los mensajes que enviamos a internet se incluyen tanto la dirección IP de nuestro punto de inicio (nuestra dirección IP), como la dirección IP del último punto de salto. Nuestra dirección IP es nuestro dato más privado, y puede ser utilizada para muchas cosas, desde crear perfiles sobre usuarios hasta usarla para intentar acceder a nuestros dispositivos de forma remota, pero ahora vamos a centrarnos en una información que proporciona y que es ampliamente utilizada en internet: La geolocalización.

### ¿Cómo consigo una IP?
Las direcciones IP - salvo que contrates una dirección fija con tu operador - son propiedad de nuestro proveedor de acceso a internet, y se asignan de forma dinámica al conectarnos a internet con nuestro dispositivo (router o dispositivo móvil), por lo que pueden variar, aunque en la práctica, dado que no tenemos la costumbre de apagar los routers cuando no los usamos, no varían demasiado. De esta forma, mantendremos la misma dirección IP durante semanas o meses si mantememos el router encendido de forma constante.

### ¿Qué es eso de la geolocalización IP?
Los operadores tienen los grupos de direcciones IP asignados a usuarios de regiones específicas, y esas asignaciones han de comunicarse a los organismos gestores de internet. Hay empresas que se dedican a recabar esos datos de los operadores, de esa forma, si dispones de todas las asignaciones geográficas de los operadores y te dan una dirección IP podrás decir en qué población -o distrito- se encuentra esa dirección con un escaso margen de error. Hay multitud de sitios que darán esta información sobre tu conexión, por ejemplo el buscador DuckDuckGo.

### ¿Para qué se usa la Geolocalización IP?
El primer uso de esta geolocalización es la segmentación de usuarios, tanto para estadísticas (saber de dónde vienen las visitas a un sitio web), como para servicios más avanzados como pueda ser la publicidad (los anunciantes no quieren mostrar anuncios a usuarios que no pueden ser clientes potenciales) o los servicios locales (por ejemplo búsquedas en internet, si buscas un café cerca de ti el buscador te mostrará cafeterías en un radio de supuesta ubicación)
