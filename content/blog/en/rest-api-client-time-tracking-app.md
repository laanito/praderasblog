---
Title: "Calling the REST API of the time-tracking app"
Description: REST shapes for users, roles, projects, tasks, and hours; OpenAPI documentation; curl examples.
Author: Luis Amigo
Date: 2023-09-18 02:07PM
Template: post
Tags: Desarrollo Web, Sistemas
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 9
Lang: en
Translation_Key: praderas-ctd-09
---

The database is ready—now we interact through the **REST API** exposed by PostgREST (or your gateway prefix).

## Example resource paths

The Spanish originals used illustrative `/api/v1/...` prefixes. Map these to your real PostgREST routes (often schema-qualified, e.g. `/control_tiempo/usuarios`).

### Users

```
GET /api/v1/usuarios
POST /api/v1/usuarios
```

### Roles

```
GET /api/v1/roles
```

### Projects

```
GET /api/v1/proyectos
POST /api/v1/proyectos
```

### Tasks

```
GET /api/v1/tareas
POST /api/v1/tareas
```

### Worked hours

```
GET /api/v1/horas_trabajadas
POST /api/v1/horas_trabajadas
```

## OpenAPI documentation

Machine-readable specs help teammates and automated clients stay aligned. Export or hand-write OpenAPI as your API stabilizes.

## Client tooling

- **Postman** — collections, environments, and collaboration.
- **Insomnia** — lightweight request building.
- **curl** — reproducible snippets for CI and docs.

## curl examples

```bash
curl -X GET http://your-host/api/v1/usuarios
```

```bash
curl -X POST -H "Content-Type: application/json" -d '{
  "nombre": "My project",
  "descripcion": "Project description"
}' http://your-host/api/v1/proyectos
```

## Next

We move to the React side for **role management**—start with [Scaffolding a React frontend for roles](%base_url%/blog/en/react-frontend-role-management-setup).
