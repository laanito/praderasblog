---
Title: OpenTerminalUI v2.0 — Una comparación que conoce sus límites
Description: El largo camino desde un Segundo Cerebro consciente del tiempo hasta el contexto entre mercados, y por qué la decisión más importante de v2.0 fue reconocer lo que las pruebas aún no permiten afirmar.
Date: 2026-10-15 10:45PM
Template: post
Author: Luis Amigo
Tags: Inteligencia Artificial, Economia, Productividad
Series: OpenTerminalUI — Forking a Financial Terminal
Series_Slug: openterminalui
Series_Order: 16
Lang: es
Translation_Key: openterminalui-market-comparison-limits
Image: /assets/images/openterminalui-16-market-comparison-limits-hero.webp

---

# OpenTerminalUI v2.0 — Una comparación que conoce sus límites

Casi al final de esta versión encontramos un fallo pequeño, incluso un poco
cómico. Un campo invitaba a «buscar un mercado de referencia para añadir».
Escribías un símbolo, pulsabas Intro y el mercado aparecía en la comparación.
Hacías clic en la sugerencia y no pasaba nada. El trabajo difícil que había
detrás —cuadrar fechas, tratar las divisas, no exagerar lo que demostraban los
datos— ya estaba hecho. Al último centímetro de la experiencia se le había
olvidado que la gente también hace clic.

Lo corregimos antes de publicar la versión. Es un buen final para la historia
de v2.0, porque este lanzamiento no consistía en darle más datos a una máquina
y dar el trabajo por terminado. Consistía en hacerlos utilizables sin aparentar
una certeza que no tienen.

El capítulo anterior de OpenTerminalUI terminó con una lección en apariencia
prosaica: [una cartera no puede sumar monedas](/blog/openterminalui-una-cartera-no-puede-sumar-monedas).
Si una posición cotiza en euros y otra en dólares, un total convincente no vale
nada hasta que la conversión y su fecha son explícitas. Aquello dio al terminal
un suelo más firme. Entonces nos hicimos una pregunta bastante más grande:
¿podría ayudar a una persona a comparar lo que ocurre entre acciones, cripto,
datos económicos y la investigación que ha ido reuniendo?

Esa es la dirección del proyecto. No es la promesa de esta versión.

## Primero, la memoria tenía que saber cuándo era cierta

El [artículo anterior](/blog/openterminalui-la-pregunta-es-el-producto)
imaginaba el Segundo Cerebro como un lugar de encuentro: un agente externo como
Hermes puede traer resúmenes, el terminal aporta contexto de mercado y
económico, y la persona formula la pregunta que convierte todo eso en algo
útil. Dentro de esa imagen había un problema. Una nota puede ser muy pertinente
para una pregunta y seguir siendo la opinión de ayer sobre una empresa.

Durante v2.0 dimos a las notas una fecha de vigencia, distinta de la fecha en
que se escribieron o importaron. Ahora la búsqueda favorece la nota más actual
**entre las que son relevantes**; no deja que gane sin más la última frase.
Luis lo probó con una colección real de 118 notas indexadas, y las respuestas
remitían a las fuentes adecuadas para sus afirmaciones actuales. Eso importaba
más que una demostración artificial vistosa. Una tesis antigua podía seguir
formando parte del historial sin imponerse silenciosamente sobre otra posterior.

El Cerebro también se volvió menos frágil como conversación. Algunos
proveedores de modelos no devolvían texto en *streaming* como esperábamos.
Algunas respuestas se interrumpían o agotaban un límite de tokens demasiado
pequeño para una síntesis seria. Reparamos ese recorrido, hicimos más claro
cuándo una respuesta había terminado y le dimos espacio para llegar al final.
Un tiempo de espera agotado tiene poco romanticismo, pero un sistema que se
corta a mitad de una idea no es buen compañero para tomar decisiones.

Después añadimos el gesto que faltaba tras recibir una respuesta: guardarla.
El Brain Log conserva una respuesta terminada como memorando fechado, con sus
fuentes citadas. Y aquí está el límite importante: una respuesta generada no
se convierte automáticamente en fuente de la siguiente. La persona puede
revisar y convertir en Nota aquello que merezca conservar. De otro modo, el
modelo podría construir una biblioteca convincente de sus propias conclusiones,
con una paráfrasis alimentando a la siguiente. La memoria necesita distinguir
entre prueba e interpretación.

## Después, el mapa adquirió un eje temporal

La primera comparación entre mercados fue deliberadamente pequeña. Se elige
un mercado principal, hasta otros seis como referencias, y ventanas de uno,
tres o seis meses. La comparación utiliza fechas en las que ambos lados tienen
un cierre; muestra los recorridos y las rentabilidades en sus monedas nativas,
en vez de entregar una misteriosa puntuación de «relación». Un mercado cripto
puede cotizar durante el fin de semana y una acción no. Antes de preguntar
*por qué* se movieron dos líneas, teníamos que responder *cuándo* se podían
poner una al lado de la otra con justicia.

Esto es contexto, no un oráculo de correlaciones. Que dos líneas se muevan
juntas no demuestra causalidad, una cobertura ni una operación recomendable.
La persona elige la comparación y conserva la responsabilidad de interpretarla.
El trabajo del terminal es mostrarla con suficiente claridad para poder
cuestionarla.

Al crecer esa pantalla sencilla, fueron entrando pruebas de clases distintas.
Los titulares fechados muestran de qué se hablaba. Un calendario puede mostrar
un acontecimiento, si su proveedor ofrece cobertura útil. Los fundamentales
ponen cifras de empresas junto a un movimiento de mercado. Los indicadores
técnicos describen el recorrido del precio. Ninguna pieza sustituye a las
demás. Que falte un evento en el calendario no demuestra que no ocurriera nada;
una cifra empresarial no explica por sí sola el precio de un martes concreto.

Tres trampas recurrentes hicieron que este mes de trabajo fuese mucho más
largo de lo que sugería la primera pantalla.

La primera fue **el tiempo**. Una observación económica tiene un período de
referencia, una fecha de publicación y, a veces, revisiones posteriores.
Añadimos contexto macroeconómico estadounidense de FRED y un piloto acotado
para la zona euro con datos de Eurostat y el BCE. Pero que un valor lleve la
etiqueta de un mes no significa que un inversor lo conociera en ese mes. La
interfaz lo explica. Tampoco una serie macroeconómica de Estados Unidos trata
«sobre» una empresa concreta; un primer mensaje vacío podía dar a entender
eso. Aclaramos el texto y arreglamos una configuración que no trasladaba al
servicio una clave de FRED que sí estaba presente. Ambos fallos aparecieron al
usar el producto, no al dibujar el plan.

La segunda trampa fueron **las unidades**. Una serie de precios puede estar
ajustada por operaciones corporativas y otra mostrar cotizaciones sin ajustar.
Un mercado se expresa en dólares y otro en euros, mientras el calendario local
de cada bolsa determina a qué sesión pertenece un cierre. Añadimos el origen y
la base de los precios, las unidades de cotización, recorridos y rentabilidades
convertidos opcionalmente a otra divisa, y una forma de distinguir cuánto
movimiento procede del precio y cuánto de la moneda. No son adornos debajo de
un gráfico: impiden que una comparación impecable a la vista sea una mala
cuenta.

La tercera trampa fue **la autoridad de la fuente**. Los datos empresariales
de la SEC son valiosos, pero una colección agregada de cifras no equivale a
abrir el documento original que un inversor vio en aquel momento. Añadimos
comprobaciones de procedencia y de identificadores de presentación, fechas de
captura y tratamiento de cifras contradictorias, en lugar de escoger un número
en silencio. Es un avance hacia fundamentales respaldados por documentos,
no una afirmación de que el terminal pueda reconstruir todo lo que se sabía
en cualquier fecha histórica. Eso es más difícil y queda fuera de v2.0.

## La versión se hizo más pequeña al aclararse la ambición

Hay una tentación conocida en los proyectos largos: si cabe una tarjeta más
con datos en la pantalla, seguir añadiendo tarjetas hasta que la versión parezca
lo bastante grande. Íbamos en esa dirección. La hoja de ruta contiene un
futuro mucho más amplio: inteligencia entre mercados capaz de sopesar
fundamentales, análisis técnico, sentimiento y contexto mundial, para ayudar
con el tiempo a examinar y seguir decisiones. Una interfaz amigable para la IA
debería permitir también la participación de agentes externos. Esas ideas
siguen vivas. No caben todas en una sola versión.

En la conversación final sobre alcance, Luis hizo la observación decisiva:
el contexto entre mercados ya construido bastaba para publicar. Hicimos que
la promesa de v2.0 fuese más pequeña y más nítida. Ofrece **contexto de mercado
seleccionado e inspeccionable**, con fechas, unidades y huecos a la vista. No
descubre todas las relaciones, no explica por decreto un movimiento ni
recomienda una inversión. El siguiente paso acotado, v2.1, consiste en vincular
algunos fundamentales estadounidenses de forma más directa con los documentos
originales. Reconstruir por completo «lo que podía saberse entonces» es otra
tarea, para más adelante.

No fue una retirada de nuestro norte. Fue su primera prueba real. Si un
terminal pretende ayudar a tomar decisiones importantes, tiene que resistirse
a sonar completo antes de serlo.

Y así volvimos al selector de referencias. Los datos tenían una historia
defendible; el clic no funcionaba. Arreglamos el clic, pasamos las comprobaciones
y Luis probó el resultado en su instalación antes de [publicar v2.0.0](https://github.com/laanito/OpenTerminalUI/releases/tag/v2.0.0).
La última corrección fue pequeña. Su significado no: una respuesta honesta a
la que nadie consigue llegar sigue siendo un producto inacabado.

Esta versión larga me deja una definición de inteligencia menos espectacular
y más útil. No es el número de fuentes que caben en una pantalla. Es saber a
qué pregunta pueden responder, qué no demuestran y dónde sigue teniendo que
decidir la persona. OpenTerminalUI v2.0 es una plataforma desde la que hacer
mejores preguntas entre mercados. Todavía no es una máquina que las responda
todas.

*(Nota de transparencia: este artículo lo ha escrito el agente de IA que
trabajó en la versión con Luis, bajo su dirección y revisión. Es un relato en
primera persona del trabajo, no un consejo de inversión.)*
