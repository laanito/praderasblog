---
Title: Creación de Tablas Restantes en la Base de Datos para tu Aplicación de Control de Tiempo
Description: En este artículo hablamos de Creación de Tablas Restantes en la Base de Datos para tu Aplicación de Control de Tiempo
Author: Luis Amigo
Date: 2023-09-14 03:47PM
Tags: Desarrollo Web, Sistemas
Series: Control de Tiempo Desacoplado
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 8
Template: post
Lang: es
Translation_Key: praderas-ctd-08
Image: /assets/images/ctd-08-database-extra-tables-hero.webp

---


En nuestro proyecto de desarrollo de una aplicación de control de tiempo, hemos definido previamente la estructura de la base de datos, incluyendo la tabla de usuarios. Ahora, avanzaremos en la creación de las tablas restantes y asignaremos los permisos necesarios. Estas tablas son fundamentales para gestionar proyectos, tareas y horas trabajadas de manera eficiente.

## Tabla de Roles

Los roles desempeñan un papel crucial en la definición de las responsabilidades y permisos de los usuarios en la aplicación. A continuación, presentamos la creación de la tabla de roles:

```sql
CREATE TABLE control_tiempo.roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50),
    descripcion TEXT
);
```

## Tabla de Proyectos

Los proyectos son la base de nuestra aplicación de control de tiempo. Cada registro en esta tabla representa un proyecto y contiene detalles como su nombre, descripción y fechas importantes.

```sql
CREATE TABLE control_tiempo.proyectos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE
);
```

## Tabla de Tareas

La gestión de tareas es esencial para el seguimiento de proyectos. La tabla de tareas almacena información detallada sobre cada tarea, incluyendo su nombre, descripción y fechas clave.

```sql
CREATE TABLE control_tiempo.tareas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    id_proyecto INT REFERENCES control_tiempo.proyectos(id),
    id_usuario INT REFERENCES control_tiempo.usuarios(id)
);
```

## Tabla de Horas Trabajadas

La tabla de horas trabajadas es crucial para el control de tiempo preciso. Cada registro en esta tabla representa las horas trabajadas por un usuario en una tarea específica.

```sql
CREATE TABLE control_tiempo.horas_trabajadas (
    id SERIAL PRIMARY KEY,
    horas DECIMAL(5, 2),
    fecha DATE,
    id_tarea INT REFERENCES control_tiempo.tareas(id),
    id_usuario INT REFERENCES control_tiempo.usuarios(id)
);
```

## Asignación de Permisos

A continuación, asignaremos los permisos necesarios a los usuarios para que puedan acceder y manipular los datos en estas tablas. Los permisos varían según el tipo de usuario y su rol en la aplicación.

```sql
-- Permisos para el usuario DBA
GRANT ALL PRIVILEGES ON TABLE control_tiempo.roles TO dba_user;
GRANT ALL PRIVILEGES ON TABLE control_tiempo.proyectos TO dba_user;
GRANT ALL PRIVILEGES ON TABLE control_tiempo.tareas TO dba_user;
GRANT ALL PRIVILEGES ON TABLE control_tiempo.horas_trabajadas TO dba_user;

-- Permisos para el usuario de administración
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.roles TO admin_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.proyectos TO admin_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.tareas TO admin_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.horas_trabajadas TO admin_user;

-- Permisos para el usuario web (API)
GRANT SELECT ON TABLE control_tiempo.roles TO web_user;
GRANT SELECT ON TABLE control_tiempo.proyectos TO web_user;
GRANT SELECT ON TABLE control_tiempo.tareas TO web_user;
GRANT SELECT ON TABLE control_tiempo.horas_trabajadas TO web_user;

-- Permisos para el usuario anónimo (autenticación)
GRANT SELECT ON TABLE control_tiempo.usuarios TO anon_user;
```

## Conclusión

Con la creación de estas tablas y la asignación de permisos, hemos completado la configuración de la base de datos para nuestra aplicación de control de tiempo. Estas tablas son esenciales para el funcionamiento de la aplicación y el seguimiento de proyectos, tareas y horas trabajadas. En los próximos artículos, exploraremos cómo interactuar con la base de datos y cómo implementar la lógica de la aplicación.


