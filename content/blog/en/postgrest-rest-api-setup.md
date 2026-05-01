---
Title: "PostgREST in Practice: Building a Strong REST API"
Description: What a REST API is, why it helps, and how PostgREST exposes PostgreSQL as HTTP resources—with install and first SQL steps.
Author: Luis Amigo
Date: 2023-09-07 10:41AM
Template: post
Tags: Desarrollo Web, Sistemas
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 2
Lang: en
Translation_Key: praderas-ctd-02
---

In our decoupled-architecture series we reach a pivotal step: a **REST API** that talks to **PostgreSQL** efficiently. Here we outline what REST means on the wire, why teams adopt it, and how **PostgREST** turns schema and tables into HTTP endpoints.

## What is a REST API?

**REST** (Representational State Transfer) is an architectural style for distributed systems. It leans on standard HTTP verbs and resource-oriented URLs. Core ideas:

- **Resources** — data or objects, usually addressable by URL.
- **HTTP methods** — `GET` (read), `POST` (create), `PUT`/`PATCH` (update), `DELETE` (remove).
- **Representations** — payloads exchanged as JSON (or other formats) that capture resource state at a point in time.

## Why REST helps

- **Simplicity** — familiar verbs and URLs lower the learning curve.
- **Cross-platform clients** — browsers, mobile apps, and services can all consume the same API.
- **Scalability** — stateless requests map cleanly to horizontal scaling patterns.

## PostgREST in one paragraph

**PostgREST** reads your PostgreSQL schema and publishes a **RESTful** surface automatically. It is a fast path from tables to HTTP without hand-writing CRUD for every entity.

## Security knobs in PostgREST

- **Authentication and authorization** — leverage PostgreSQL roles and `GRANT`s.
- **Row-level concerns** — policies and views can narrow what each role sees.

## Installing PostgREST and PostgreSQL (Debian 11)

```bash
sudo apt update
sudo apt install postgresql postgresql-client postgrest -y
```

## Initial configuration

1. **Database roles** (example names from the original Spanish article):

   - `dba_user` — owns the database and schema work.
   - `admin_user` — application admin for creating entities.
   - `web_user` — role used by the API layer.
   - `anon_user` — used for unauthenticated bootstrap flows (e.g. login).

   ```sql
   CREATE USER dba_user WITH PASSWORD 'dba_password';
   CREATE USER admin_user WITH PASSWORD 'admin_password';
   CREATE USER web_user WITH PASSWORD 'web_password';
   CREATE USER anon_user WITH PASSWORD 'anon_password';
   ```

2. **Application schema**

   ```sql
   CREATE SCHEMA app_schema;
   ```

3. **Starter table** (`users`) — illustrative only; tighten types and secrets in real deployments.

   ```sql
   CREATE TABLE app_schema.users (
       id SERIAL PRIMARY KEY,
       login TEXT NOT NULL,
       name TEXT NOT NULL,
       last_name TEXT NOT NULL,
       password TEXT NOT NULL,
       creation_date TIMESTAMP,
       is_active BOOLEAN
   );
   ```

4. **PostgREST config + systemd** — point PostgREST at the right database URL, role, and schema; run it as a supervised service.

## Quick `curl` checks

```bash
curl http://localhost:3000/app_schema/users
```

```bash
curl -X POST -H "Content-Type: application/json" \
  -d '{"login":"johndoe","name":"John","last_name":"Doe","password":"password","is_active":true}' \
  http://localhost:3000/app_schema/users
```

```bash
curl 'http://localhost:3000/app_schema/users?id=eq.1'
```

This instalment lays the foundation for the API. Next we add **Nginx** in front and tighten access with **JWT**—see [Nginx hardening for PostgREST](%base_url%/blog/en/nginx-postgrest-https-hardening).
