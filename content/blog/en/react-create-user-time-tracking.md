---
Title: "Create users in the time-tracking app with React"
Description: A CreateUser component sketch that posts JSON to your API to insert rows into control_tiempo.usuarios.
Author: Luis Amigo
Date: 2023-09-25 02:00PM
Template: post
Tags: Desarrollo Web, Productividad
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 13
Lang: en
Translation_Key: praderas-ctd-13
---

Roles and projects are in motion—now we add **user onboarding** from the React app by posting to `/api/usuarios` (adjust to your real PostgREST path).

## `CreateUser` sketch

```jsx
import React, { useState } from 'react';

function CreateUser() {
  const [nombre, setNombre] = useState('');
  const [apellidos, setApellidos] = useState('');
  const [email, setEmail] = useState('');
  const [clave, setClave] = useState('');
  const [activo, setActivo] = useState(true);

  const handleSubmit = (event) => {
    event.preventDefault();
    fetch('/api/usuarios', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre, apellidos, email, clave, activo }),
    })
      .then((response) => response.json())
      .then((data) => console.log('Server response:', data))
      .catch((error) => console.error('Error:', error));
  };

  return (
    <div>
      <h1>Create user</h1>
      <form onSubmit={handleSubmit}>
        {/* Wire inputs to the state setters; never ship demo passwords to production */}
        <button type="submit">Save</button>
      </form>
    </div>
  );
}

export default CreateUser;
```

## Integration notes

- Hash passwords **server-side** (or via database triggers) before persistence—this sample mirrors the pedagogy of the Spanish series, not production hardening.
- Surface validation errors and optimistic UI states for better UX.
- After insert, navigate to a detail page or refresh a shared user directory component.

## Closing

With users, roles, projects, tasks, hours, and API wiring described end-to-end, you have a coherent blueprint for a decoupled time tracker. Iterate on security, testing, and deployment practices next—and thanks for following the series.
