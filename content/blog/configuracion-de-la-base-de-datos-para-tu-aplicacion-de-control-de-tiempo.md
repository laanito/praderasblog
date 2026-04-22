---
Title: Configuración de la Base de Datos para tu Aplicación de Control de Tiempo
Description: En este artículo, te guiaremos a través del proceso de creación de la estructura de la base de datos y la configuración de los usuarios y permisos necesarios
Author: Luis Amigo
Date: 2023-09-13 12:31PM
Template: post
---


En el desarrollo de tu aplicación de control de tiempo, una parte fundamental es la configuración de la base de datos que almacenará todos los datos relevantes. En este artículo, te guiaremos a través del proceso de creación de la estructura de la base de datos y la configuración de los usuarios y permisos necesarios.

## Usuarios de la Base de Datos

Primero, necesitamos crear los usuarios de la base de datos que se encargarán de administrar y utilizar la aplicación. Utilizaremos cuatro usuarios diferentes con roles específicos: el usuario DBA, el usuario de administración, el usuario web y el usuario anónimo.

### Usuario DBA (Administrador de la Base de Datos)

Este usuario será el propietario de la base de datos y se utilizará para crear los esquemas y las tablas necesarias.

```sql
CREATE ROLE dba_user WITH LOGIN PASSWORD 'tu_contraseña';
ALTER ROLE dba_user CREATEDB;
```

### Usuario de Administración

El usuario de administración será responsable de crear nuevos elementos en el esquema de la aplicación.

```sql
CREATE ROLE admin_user WITH LOGIN PASSWORD 'tu_contraseña';
```

### Usuario Web (Para el API)

Este usuario se utilizará para el acceso al API de la aplicación.

```sql
CREATE ROLE web_user WITH LOGIN PASSWORD 'tu_contraseña';
```

### Usuario Anónimo (Para Autenticación)

El usuario anónimo se utiliza para autenticar y proporcionar tokens JWT.

```sql
CREATE ROLE anon_user WITH LOGIN PASSWORD 'tu_contraseña';
```

## Creación de Esquema y Tablas

Ahora que hemos configurado los usuarios, crearemos el esquema y las tablas necesarios para nuestra aplicación de control de tiempo. Comenzaremos con una tabla básica de usuarios como ejemplo.

### Creación de Esquema

```sql
CREATE SCHEMA control_tiempo;
```

### Creación de Tabla de Usuarios

La tabla de usuarios almacenará la información de los usuarios de la aplicación.

```sql
CREATE TABLE control_tiempo.usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    email VARCHAR(100),
    clave VARCHAR(100),
    fecha_creacion TIMESTAMP,
    activo BOOLEAN
);
```

## Asignación de Permisos

Finalmente, es importante asignar los permisos adecuados a los usuarios para garantizar que puedan acceder y manipular los datos según sus roles.

### Asignación de Permisos a los Usuarios

```sql
GRANT ALL PRIVILEGES ON SCHEMA control_tiempo TO dba_user;
GRANT USAGE, CREATE ON SCHEMA control_tiempo TO admin_user;
GRANT USAGE, CREATE ON SCHEMA control_tiempo TO web_user;
```

### Asignación de Permisos a las Tablas

```sql
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.usuarios TO admin_user;
GRANT SELECT ON TABLE control_tiempo.usuarios TO web_user;
```

## Conclusión

Con estos comandos SQL, has configurado la base de datos para tu aplicación de control de tiempo. Los usuarios y las tablas son fundamentales para el funcionamiento de la aplicación y su seguridad. A medida que avances en el desarrollo, podrás agregar más tablas y relaciones según las necesidades específicas de tu proyecto.

En los próximos artículos, exploraremos cómo interactuar con esta base de datos y cómo implementar la lógica de tu aplicación.

