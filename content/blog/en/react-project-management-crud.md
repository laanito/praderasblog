---
Title: "Project management screens in React"
Description: ListProjects and EditProject examples—fetching projects, rendering cards, and submitting create/update payloads to the API.
Author: Luis Amigo
Date: 2023-09-24 11:28AM
Template: post
Tags: Desarrollo Web, Productividad
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 12
Lang: en
Translation_Key: praderas-ctd-12
---

We continue the **time-tracking** UI by managing **projects**: listing them, showing metadata, and creating or editing records through the REST API.

## SQL reminder (projects table)

```sql
CREATE TABLE control_tiempo.proyectos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE
);
```

## `ListProjects.js`

```jsx
import React, { useState, useEffect } from 'react';

function ListProjects() {
  const [projects, setProjects] = useState([]);

  useEffect(() => {
    fetch('/api/proyectos')
      .then((response) => response.json())
      .then((data) => setProjects(data))
      .catch((error) => console.error('Error:', error));
  }, []);

  return (
    <div>
      <h1>Projects</h1>
      <ul>
        {projects.map((project) => (
          <li key={project.id}>
            <h2>{project.nombre}</h2>
            <p>{project.descripcion}</p>
            <p>Start date: {project.fecha_inicio}</p>
            <p>End date: {project.fecha_fin}</p>
          </li>
        ))}
      </ul>
    </div>
  );
}

export default ListProjects;
```

## `EditProject.js`

```jsx
import React, { useState } from 'react';

function EditProject() {
  const [nombre, setNombre] = useState('');
  const [descripcion, setDescripcion] = useState('');
  const [fechaInicio, setFechaInicio] = useState('');
  const [fechaFin, setFechaFin] = useState('');

  const handleSubmit = (event) => {
    event.preventDefault();
    fetch('/api/proyectos', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nombre,
        descripcion,
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
      }),
    })
      .then((response) => response.json())
      .then((data) => console.log('Server response:', data))
      .catch((error) => console.error('Error:', error));
  };

  return (
    <div>
      <h1>Create / edit project</h1>
      <form onSubmit={handleSubmit}>
        <div>
          <label htmlFor="nombre">Name</label>
          <input type="text" id="nombre" value={nombre} onChange={(e) => setNombre(e.target.value)} />
        </div>
        <div>
          <label htmlFor="descripcion">Description</label>
          <textarea id="descripcion" value={descripcion} onChange={(e) => setDescripcion(e.target.value)} />
        </div>
        <div>
          <label htmlFor="fechaInicio">Start date</label>
          <input type="date" id="fechaInicio" value={fechaInicio} onChange={(e) => setFechaInicio(e.target.value)} />
        </div>
        <div>
          <label htmlFor="fechaFin">End date</label>
          <input type="date" id="fechaFin" value={fechaFin} onChange={(e) => setFechaFin(e.target.value)} />
        </div>
        <button type="submit">Save</button>
      </form>
    </div>
  );
}

export default EditProject;
```

Hook these routes into your router and add validation before trusting client payloads.

## Next

We close the loop with **user creation**—[Create users in the time-tracking app with React](%base_url%/blog/en/react-create-user-time-tracking).
