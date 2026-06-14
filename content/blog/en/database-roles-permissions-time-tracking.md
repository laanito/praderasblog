---
Title: "Database setup for the time-tracking app"
Description: Create DBA/admin/web/anon roles, a control_tiempo schema, the users table, and grants aligned with PostgREST access patterns.
Author: Luis Amigo
Date: 2023-09-13 12:31PM
Template: post
Tags: Desarrollo Web, Sistemas
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 7
Lang: en
Translation_Key: praderas-ctd-07
Image: /assets/images/ctd-07-database-roles-permissions-hero.webp

---

A working **time-tracking** backend needs explicit **database roles** and **grants**. The Spanish originals use the schema name `control_tiempo`; we keep it here so SQL snippets stay aligned with earlier posts.

## Roles

### DBA

```sql
CREATE ROLE dba_user WITH LOGIN PASSWORD 'replace-me';
ALTER ROLE dba_user CREATEDB;
```

### Admin (creates business rows)

```sql
CREATE ROLE admin_user WITH LOGIN PASSWORD 'replace-me';
```

### Web (API-facing)

```sql
CREATE ROLE web_user WITH LOGIN PASSWORD 'replace-me';
```

### Anonymous (login bootstrap)

```sql
CREATE ROLE anon_user WITH LOGIN PASSWORD 'replace-me';
```

## Schema and starter table

```sql
CREATE SCHEMA control_tiempo;
```

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

Column names stay Spanish to match the rest of the series’ SQL; translate at the ORM layer if you prefer English field names in application code.

## Grants

```sql
GRANT ALL PRIVILEGES ON SCHEMA control_tiempo TO dba_user;
GRANT USAGE, CREATE ON SCHEMA control_tiempo TO admin_user;
GRANT USAGE, CREATE ON SCHEMA control_tiempo TO web_user;
```

```sql
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE control_tiempo.usuarios TO admin_user;
GRANT SELECT ON TABLE control_tiempo.usuarios TO web_user;
```

## Next

We add the remaining tables for roles, projects, tasks, and hours—see [Remaining tables for the time-tracking database](%base_url%/blog/en/database-extra-tables-time-tracking).
