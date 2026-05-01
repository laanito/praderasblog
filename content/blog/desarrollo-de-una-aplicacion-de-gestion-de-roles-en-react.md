---
Title: Desarrollo de una Aplicación de Gestión de Roles en React
Description: En este artículo hablamos de Desarrollo de una Aplicación de Gestión de Roles en React
Author: Luis Amigo
Date: 2023-09-20 11:44AM
Tags: Desarrollo Web
Series: Control de Tiempo Desacoplado
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 11
Template: post
Lang: es
Translation_Key: praderas-ctd-11
---


En esta serie de tutoriales, estamos desarrollando una aplicación de gestión de roles utilizando una arquitectura desacoplada. Hoy nos centraremos en el frontend de la aplicación, específicamente en la parte de listar y crear/editar roles. Utilizaremos React como nuestra biblioteca de interfaz de usuario y Bootstrap para la apariencia. Los componentes clave que vamos a desarrollar son `ListRoles.js` y `EditRole.js`.

## ListRoles.js

`ListRoles.js` es el componente encargado de listar los roles en nuestra aplicación. Aquí está el código:

```jsx
import React, { useState, useEffect } from 'react';

function ListRoles() {
  const [roles, setRoles] = useState([]);

  useEffect(() => {
    // Realiza una solicitud HTTP para obtener la lista de roles desde tu API.
    fetch('/api/roles') // Reemplaza con la URL correcta de tu API
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

En este componente, utilizamos `useState` para mantener un estado local que almacenará la lista de roles. Usamos `useEffect` para realizar una solicitud HTTP a nuestra API y obtener la lista de roles. Luego, renderizamos la lista de roles en la interfaz de usuario.

## EditRole.js

`EditRole.js` es el componente responsable de crear y editar roles en nuestra aplicación. Aquí está el código:

```jsx
import React, { useState } from 'react';

function EditRole() {
  const [nombre, setNombre] = useState('');

  const handleNombreChange = (event) => {
    setNombre(event.target.value);
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    // Realiza una solicitud HTTP para crear/editar un rol en tu API.
    fetch('/api/roles', {
      method: 'POST', // Cambia el método según corresponda para crear/editar.
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ nombre }), // Puedes agregar más campos según tus necesidades.
    })
      .then((response) => response.json())
      .then((data) => {
        // Maneja la respuesta o realiza redirección a la lista de roles.
        console.log('Respuesta del servidor:', data);
      })
      .catch((error) => console.error('Error:', error));
  };

  return (
    <div>
      <h1>Crear/Editar Rol</h1>
      <form onSubmit={handleSubmit}>
        <div>
          <label htmlFor="nombre">Nombre:</label>
          <input
            type="text"
            id="nombre"
            value={nombre}
            onChange={handleNombreChange}
          />
        </div>
        <button type="submit">Guardar</button>
      </form>
    </div>
  );
}

export default EditRole;
```

En este componente, utilizamos el estado local para controlar el nombre del rol que estamos creando o editando. Cuando el usuario envía el formulario, realizamos una solicitud HTTP a nuestra API para crear o editar el rol.

Estos componentes proporcionan un punto de partida para desarrollar la gestión de roles en nuestra aplicación frontend en React. Puedes personalizarlos y agregar más funcionalidad según tus necesidades específicas.

En el próximo tutorial, continuaremos desarrollando nuestra aplicación y abordaremos más características y funcionalidades.

¡Hasta la próxima!

