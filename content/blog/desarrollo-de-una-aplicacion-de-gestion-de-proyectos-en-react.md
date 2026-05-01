---
Title: Desarrollo de una Aplicación de Gestión de Proyectos en React
Description: En este artículo hablamos de Desarrollo de una Aplicación de Gestión de Proyectos en React
Author: Luis Amigo
Date: 2023-09-24 11:28AM
Tags: Desarrollo Web, Productividad
Series: Control de Tiempo Desacoplado
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 12
Template: post
Lang: es
Translation_Key: praderas-ctd-12
---

En nuestra serie de tutoriales sobre el desarrollo de una aplicación de control de tiempo, llegamos a la etapa de gestionar proyectos. Vamos a utilizar React para crear una interfaz de usuario que permita a los usuarios listar, crear y editar proyectos. A continuación, exploraremos el proceso de desarrollo y el código de React necesario.

## Creación de la Tabla de Proyectos en la Base de Datos

Antes de comenzar con el desarrollo en React, asegurémonos de que tengamos la tabla de proyectos definida en nuestra base de datos. Utilizamos SQL para crear la tabla:

```sql
CREATE TABLE control_tiempo.proyectos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE
);
```

## Listar Proyectos en React

Vamos a crear un componente React llamado `ListProjects.js` que mostrará la lista de proyectos. Aquí tienes el código:

```jsx
import React, { useState, useEffect } from 'react';

function ListProjects() {
  const [projects, setProjects] = useState([]);

  useEffect(() => {
    // Realiza una solicitud HTTP para obtener la lista de proyectos desde tu API.
    fetch('/api/proyectos') // Asegúrate de utilizar la URL correcta de tu API
      .then((response) => response.json())
      .then((data) => setProjects(data))
      .catch((error) => console.error('Error:', error));
  }, []);

  return (
    <div>
      <h1>Proyectos</h1>
      <ul>
        {projects.map((project) => (
          <li key={project.id}>
            <h2>{project.nombre}</h2>
            <p>{project.descripcion}</p>
            <p>Fecha de Inicio: {project.fecha_inicio}</p>
            <p>Fecha de Fin: {project.fecha_fin}</p>
          </li>
        ))}
      </ul>
    </div>
  );
}

export default ListProjects;
```

Este componente utiliza `useState` y `useEffect` para realizar una solicitud HTTP a nuestra API y obtener la lista de proyectos. Luego, muestra los proyectos en la interfaz de usuario.

## Crear y Editar Proyectos en React

Para crear y editar proyectos, creamos un componente `EditProject.js`. Aquí está el código:

```jsx
import React, { useState } from 'react';

function EditProject() {
  const [nombre, setNombre] = useState('');
  const [descripcion, setDescripcion] = useState('');
  const [fechaInicio, setFechaInicio] = useState('');
  const [fechaFin, setFechaFin] = useState('');

  const handleNombreChange = (event) => {
    setNombre(event.target.value);
  };

  const handleDescripcionChange = (event) => {
    setDescripcion(event.target.value);
  };

  const handleFechaInicioChange = (event) => {
    setFechaInicio(event.target.value);
  };

  const handleFechaFinChange = (event) => {
    setFechaFin(event.target.value);
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    // Realiza una solicitud HTTP para crear/editar un proyecto en tu API.
    fetch('/api/proyectos', {
      method: 'POST', // Cambia el método según corresponda para crear/editar.
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ nombre, descripcion, fecha_inicio: fechaInicio, fecha_fin: fechaFin }),
    })
      .then((response) => response.json())
      .then((data) => {
        // Maneja la respuesta o realiza redirección a la lista de proyectos.
        console.log('Respuesta del servidor:', data);
      })
      .catch((error) => console.error('Error:', error));
  };

  return (
    <div>
      <h1>Crear/Editar Proyecto</h1>
      <form onSubmit={handleSubmit}>
        <div>
          <label htmlFor="nombre">Nombre:</label>
          <input type="text" id="nombre" value={nombre} onChange={handleNombreChange} />
        </div>
        <div>
          <label htmlFor="descripcion">Descripción:</label>
          <textarea id="descripcion" value={descripcion} onChange={handleDescripcionChange} />
        </div>
        <div>
          <label htmlFor="fechaInicio">Fecha de Inicio:</label>
          <input type="date" id="fechaInicio" value={fechaInicio} onChange={handleFechaInicioChange} />
        </div>
        <div>
          <label htmlFor="fechaFin">Fecha de Fin:</label>
          <input type="date" id="fechaFin" value={fechaFin} onChange={handleFechaFinChange} />
        </div>
        <button type="submit">Guardar</button>
      </form>
    </div>
  );
}

export default EditProject;
```

Este componente permite al usuario crear o editar un proyecto proporcionando información como el nombre, la descripción y las fechas de inicio y fin. Al hacer clic en "Guardar", se realiza una solicitud HTTP a la API para crear o editar el proyecto.

¡Ahora tienes las herramientas necesarias para listar, crear y editar proyectos en tu aplicación de control de tiempo en React! En el siguiente artículo, exploraremos cómo conectar estos componentes a tu API para una funcionalidad completa.


