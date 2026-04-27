---
Title: Introducción a React: ES5, ES6 y su Ejecución
Description: En este artículo hablamos de Introducción a React: ES5, ES6 y su Ejecución
Author: Luis Amigo
Date: 2023-09-11 11:15AM
Tags: Desarrollo Web
Series: Control de Tiempo Desacoplado
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 5
Template: post
---


En nuestra serie de desarrollo de arquitecturas desacopladas, hemos abordado varias capas, desde la configuración de PostgREST hasta la seguridad y la autenticación con tokens JWT. Ahora, es el momento de adentrarnos en el desarrollo del frontend, y para ello, utilizaremos React.

## ¿Qué es React?

React es una biblioteca de JavaScript de código abierto utilizada para crear interfaces de usuario (UI) interactivas y dinámicas. Fue desarrollada por Facebook y se ha convertido en una de las herramientas más populares para construir aplicaciones web modernas.

## ES5 y ES6: ¿Qué son?

Antes de profundizar en React, es importante entender los conceptos de ES5 y ES6, que se refieren a las versiones de JavaScript.

- **ES5 (ECMAScript 5)**: Es una versión anterior de JavaScript, ampliamente compatible con la mayoría de los navegadores. Fue la base para muchas aplicaciones web en el pasado.

- **ES6 (ECMAScript 2015)**: Es una versión más reciente de JavaScript que introduce muchas características nuevas y mejoras en el lenguaje. ES6 ofrece una sintaxis más limpia y funcionalidades avanzadas que facilitan la escritura de código más eficiente y legible.

## React vs. Otros Frameworks

React se destaca entre otros marcos de trabajo debido a su enfoque en el uso de componentes reutilizables. A diferencia de frameworks como Angular o Vue, React se centra principalmente en la interfaz de usuario. Esto significa que puedes usar React en combinación con otras bibliotecas y herramientas según tus necesidades.

## Ejecución de Aplicaciones React

Una de las ventajas de React es su flexibilidad en cuanto a cómo puedes ejecutar una aplicación. Aquí hay tres formas comunes de hacerlo:

1. **En el Navegador**: Puedes cargar React directamente en el navegador. Esto es adecuado para aplicaciones más pequeñas o prototipos, pero puede no ser la opción más eficiente para aplicaciones complejas.

2. **En el Servidor con Node.js**: Utilizando Node.js, puedes renderizar las vistas de React en el servidor antes de enviarlas al navegador. Esto mejora la velocidad de carga y el SEO, ya que el contenido se entrega de inmediato.

3. **En Navegadores que Soportan ES6 de Manera Nativa**: Algunos navegadores modernos admiten ES6 de manera nativa. Puedes escribir tu código React en ES6 y ejecutarlo directamente en estos navegadores sin necesidad de transpilarlo a ES5.

## Ventajas e Inconvenientes

Cada método de ejecución tiene sus ventajas e inconvenientes. La elección depende de tus necesidades específicas. Renderizar en el servidor con Node.js es beneficioso para SEO y rendimiento, pero requiere configuración adicional. La ejecución en el navegador es más simple pero puede ser menos eficiente para aplicaciones grandes.

## ¡Hola Mundo en React!

Para comenzar con React, aquí tienes un ejemplo de cómo se vería una aplicación "Hola Mundo" en React utilizando ES6:

```jsx
import React from 'react';
import ReactDOM from 'react-dom';

const App = () => {
  return (
    <div>
      <h1>Hola, Mundo!</h1>
    </div>
  );
};

ReactDOM.render(<App />, document.getElementById('root'));
```

Este ejemplo muestra cómo crear un componente simple de React y renderizarlo en el DOM.

## Conclusión

React es una potente biblioteca de JavaScript que simplifica la creación de interfaces de usuario interactivas. Comprender las diferencias entre ES5 y ES6 y las diversas formas de ejecutar una aplicación React es fundamental para el desarrollo efectivo. En futuros artículos, profundizaremos en React y construiremos nuestra aplicación de control de horas.

¡Mantente atento para aprender más sobre cómo desarrollar aplicaciones desacopladas con React!

---

Con este artículo, deberías tener una comprensión sólida de qué es React, cómo se compara con otros frameworks y cómo se puede ejecutar una aplicación React en diferentes contextos. Además, hemos proporcionado un ejemplo simple de "Hola Mundo" en React para comenzar. ¡Espero que esta información te sea útil en tu viaje hacia el desarrollo de aplicaciones desacopladas!
