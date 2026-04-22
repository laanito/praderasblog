---
Title: Internet no es un lugar seguro (II) - Privacidad
Description: El artículo habla sobre como nuestra privacidad está en riesgo si no tenemos cuidado al navegar por internet
Author: Luis Amigo
Date: 2020-12-10
Template: post
Tags: Ciberseguridad, Privacidad
---
La seguridad no necesariamente debe ser nuestra única preocupación en Internet, de un tiempo a esta parte hemos visto cómo la privacidad se ha convertido en un nuevo caballo de batalla en internet. Diversas compañías se dedican a acumular datos sobre los usuarios y utilizarlos en su propio beneficio, o incluso venderlos a terceros para su uso de forma casi descontrolada.

### ¿Qué datos se obtienen y de qué forma?
La forma más habitual de obtener datos de un usuario es mediante cookies de rastreo, las cookies son fragmentos de información que se almacenan en el navegador del usuario, de esta forma, guardando un ID único por usuario siempre se podrán relacionar las visitas del usuario accediendo al ID en la cookie.

Esto no es tan sencillo como pueda parecer, ya que, por seguridad, las cookies solo son accesibles desde el dominio que las ha creado. Por ello, en teoría, Google, Facebook, Twitter o Amazon (4 de los mayores rastreadores de internet) solo podrán acceder al ID del usuario en los sitios de su dominio, es decir, Google podrá leer esa cookie desde cualquier dominio incluido en google.com, pero no en cualquier otra página.

Si esto se quedara así las cookies de rastreo serían una de las cosas más inútiles del planeta y es por eso por lo que todas estas compañías ofrecen “cosas” para poner en páginas de terceros. ¿Qué significa poner cosas en páginas de terceros? Pues significa que si en la página de un tercero consigues que ese tercero lea un javascript o un iframe que venga del dominio de la cookie original, este código sí podrá acceder a la cookie, así como al modelo de datos global de la página actual, con esto ya pueden obtener datos del modelo de datos principal y relacionarlo con la ID de rastreo, incluso enviar datos telemétricos a su dominio de origen.

### ¿Qué tipo de cosas ponen en sitios de terceros? 
El rey del imperio de Google es Google Analytics, se estima que el 70% de todos los sitios web existentes hacen uso de Google Analytics, también que el 70% de todas las búsquedas del mundo se realizan en Google y que el 75% de todos los dispositivos móviles del mundo utilizan el sistema operativo de Google.

Esto quiere decir básicamente que los rastreadores de Google están presentes en al menos el 70% de todas las páginas web del mundo (algunos estudios apuntan al 85%) y al menos en el 75% de todos los dispositivos móviles del mundo vinculando el 70% de los resultados de búsquedas del mundo con perfiles individualizados de usuarios de internet que incluyen sitios que visitan, horario de búsqueda, tipos de dispositivos e incluso vinculación con historial de compras (sí, si usas Gmail, Google rastrea tus correos) o con tus datos personales o medios de pago.

### ¿Qué hacen con tantos datos?
Perfiles, perfiles y más perfiles. En general estas compañías obtienen datos de dos maneras:

Utilizar los perfiles para orientar el negocio. La mayor fuente de ingresos de Google y de Facebook es la publicidad orientada, con este tipo de publicidad los anuncios que se muestran lo hacen porque el perfil del usuario encaja con el target del anuncio. En esta línea también funcionan los rastreadores de Amazon o Ebay que ofrecen productos relacionados con intereses anteriores del usuario en internet.
Venta de datos. Esta venta de datos teóricamente se hace de forma agregada y anonimizada, pero casos como el de Cambridge Analytica han demostrado que este tipo de ventas no se hace de forma tan agregada ni tan anonimizada.
Aun en el caso de que los datos se ofrezcan anonimizados, diversos estudios han demostrado que los datos son fácilmente desanonimizables en muchos casos y en otros casos son fácilmente cruzables con las ubicaciones a través de la dirección IP. Esto genera un problema adicional para aquellos que no viven en grandes urbes ya que en su caso los datos se pueden desanonimizar con mayor facilidad.

### ¿Cómo protegerse de esto?
Como vimos con anterioridad, en cualquier caso, siempre es recomendable utilizar una VPN para PC y así evitar que se pueda utilizar la IP para desanonimizar los datos.

En general la recomendación más sencilla sería no utilizar ningún servicio de estas compañías, pero como eso es algo prácticamente imposible vamos a ir con unos sencillos trucos.

- Activar todas las funcionalidades de privacidad que nos ofrezca el sitio en cuestión, por ejemplo, si vamos a los ajustes de la cuenta de Google podremos pedirle que no rastree la actividad web, el historial de ubicaciones o el historial de YouTube e incluso pedirle que no personalice los anuncios.
- Utilizar un bloqueador de rastreadores. – Afortunadamente los rastreadores no son necesarios para el buen funcionamiento de internet, diversas extensiones y navegadores, como DuckDuckGo ofrecen bloqueadores muy interesante.
- No utilizar el navegador Chrome, ya que Google lo utiliza para rastrear aun en modo incógnito.
- Utilizar un buscador alternativo a Google. Existen diversas opciones como Bing, DuckDuckGo, Ecosia, …
- Evitar el uso de Facebook o Twitter y hacerlo únicamente en ventanas privadas del navegador.
- Utilizar Amazon y Ebay solo en ventanas privadas del navegador.
- En dispositivos móviles revocar aquellos permisos que no sean imprescindibles a las aplicaciones de Facebook, Twitter, Google, Amazon o Ebay. Por ejemplo, no necesitan acceder a tu ubicación ni a datos en segundo plano.
- Parece una tontería, pero siguiendo estas pequeñas indicaciones veremos cómo los anuncios dejan de perseguirnos como si hubiera micrófonos en nuestra casa.
