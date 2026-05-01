---
Title: "React primer: ES5, ES6, and how apps run"
Description: What React is, how ES5 and ES6 differ, common runtimes (browser vs Node), and a minimal Hello World in modern syntax.
Author: Luis Amigo
Date: 2023-09-11 11:15AM
Template: post
Tags: Desarrollo Web
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 5
Lang: en
Translation_Key: praderas-ctd-05
---

We have covered API layers with **PostgREST** and **JWT**. Now we turn to the **frontend** using **React**.

## What is React?

React is an open-source JavaScript library for building interactive UIs. Created inside Meta’s ecosystem, it is now a mainstream choice for component-driven web apps.

## ES5 vs ES6

- **ES5** — older JavaScript baseline with broad legacy-browser support.
- **ES6 (ES2015)** — adds classes, arrow functions, modules, `let`/`const`, and more ergonomic syntax. Most teams author ES6+ and transpile where needed.

## React compared to full frameworks

React focuses on the **view layer**. You can pair it with your own routing, data fetching, and state libraries—unlike batteries-included frameworks that prescribe more of the stack.

## How React apps execute

1. **In the browser** — simplest for demos; larger apps usually add bundlers.
2. **On the server with Node** — SSR/SSG improves first paint and SEO for content-heavy routes.
3. **Modern browsers with native ES modules** — skip transpilation when your target matrix allows.

Each option trades setup complexity for performance and SEO characteristics.

## Hello World (ES6 + JSX)

```jsx
import React from 'react';
import ReactDOM from 'react-dom';

const App = () => {
  return (
    <div>
      <h1>Hello, world!</h1>
    </div>
  );
};

ReactDOM.render(<App />, document.getElementById('root'));
```

This shows a tiny component mounted into the DOM—your starting point before routing and data layers.

## Closing

Understanding ES5 vs ES6 and where code executes sets you up for productive React work. Next we model persistence—see [Database design for the time-tracking app](%base_url%/blog/en/database-schema-time-tracking-app).

---

You should now have a concise map of React’s role, how it compares to heavier frameworks, and a minimal runnable snippet to experiment with locally.
