---
Title: "Role management components in React"
Description: Example ListRoles and EditRole components using hooks, fetch, and minimal forms wired to your REST API.
Author: Luis Amigo
Date: 2023-09-20 11:44AM
Template: post
Tags: Desarrollo Web
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 11
Lang: en
Translation_Key: praderas-ctd-11
---

This instalment sketches two components from the Spanish originals: **`ListRoles`** and **`EditRole`**. Treat URLs and JSON field names as placeholders—align them with your PostgREST routes and schema (`nombre`, etc.).

## `ListRoles.js`

```jsx
import React, { useState, useEffect } from 'react';

function ListRoles() {
  const [roles, setRoles] = useState([]);

  useEffect(() => {
    fetch('/api/roles')
      .then((response) => response.json())
      .then((data) => setRoles(data))
      .catch((error) => console.error('Error:', error));
  }, []);

  return (
    <div>
      <h1>Roles</h1>
      <ul>
        {roles.map((role) => (
          <li key={role.id}>{role.nombre}</li>
        ))}
      </ul>
    </div>
  );
}

export default ListRoles;
```

`useEffect` loads data once on mount; extend with refresh controls, error banners, and pagination as needed.

## `EditRole.js`

```jsx
import React, { useState } from 'react';

function EditRole() {
  const [nombre, setNombre] = useState('');

  const handleSubmit = (event) => {
    event.preventDefault();
    fetch('/api/roles', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre }),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log('Server response:', data);
      })
      .catch((error) => console.error('Error:', error));
  };

  return (
    <div>
      <h1>Create / edit role</h1>
      <form onSubmit={handleSubmit}>
        <label htmlFor="nombre">Name</label>
        <input
          type="text"
          id="nombre"
          value={nombre}
          onChange={(e) => setNombre(e.target.value)}
        />
        <button type="submit">Save</button>
      </form>
    </div>
  );
}

export default EditRole;
```

## Next

We extend the same pattern to **projects**—see [Project management screens in React](%base_url%/blog/en/react-project-management-crud).
