---
Title: "Remaining tables for the time-tracking database"
Description: SQL for roles, projects, tasks, worked hours, and permission grants across DBA, admin, web, and anonymous roles.
Author: Luis Amigo
Date: 2023-09-14 03:47PM
Template: post
Tags: Desarrollo Web, Sistemas
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 8
Lang: en
Translation_Key: praderas-ctd-08
Image: /assets/images/ctd-08-database-extra-tables-hero.webp

---

With `control_tiempo.usuarios` in place, we add the rest of the relational core: **roles**, **projects**, **tasks**, and **worked hours**, then wire **GRANT**s for each database role.

## Roles table

```sql
CREATE TABLE control_tiempo.roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50),
    descripcion TEXT
);
```

## Projects

```sql
CREATE TABLE control_tiempo.proyectos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE
);
```

## Tasks

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

## Worked hours

```sql
CREATE TABLE control_tiempo.horas_trabajadas (
    id SERIAL PRIMARY KEY,
    horas DECIMAL(5, 2),
    fecha DATE,
    id_tarea INT REFERENCES control_tiempo.tareas(id),
    id_usuario INT REFERENCES control_tiempo.usuarios(id)
);
```

## Grants (abbreviated labels)

```sql
GRANT ALL PRIVILEGES ON TABLE control_tiempo.roles TO dba_user;
GRANT ALL PRIVILEGES ON TABLE control_tiempo.proyectos TO dba_user;
GRANT ALL PRIVILEGES ON TABLE control_tiempo.tareas TO dba_user;
GRANT ALL PRIVILEGES ON TABLE control_tiempo.horas_trabajadas TO dba_user;

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.roles TO admin_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.proyectos TO admin_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.tareas TO admin_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.horas_trabajadas TO admin_user;

GRANT SELECT ON TABLE control_tiempo.roles TO web_user;
GRANT SELECT ON TABLE control_tiempo.proyectos TO web_user;
GRANT SELECT ON TABLE control_tiempo.tareas TO web_user;
GRANT SELECT ON TABLE control_tiempo.horas_trabajadas TO web_user;

GRANT SELECT ON TABLE control_tiempo.usuarios TO anon_user;
```

## Next

With tables and grants aligned, we talk to the API from clients—read [Calling the REST API of the time-tracking app](%base_url%/blog/en/rest-api-client-time-tracking-app).
