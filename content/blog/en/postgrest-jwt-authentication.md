---
Title: "JWT Authentication with PostgREST"
Description: Why JSON Web Tokens help secure a PostgREST API, and how roles, headers, and PostgreSQL functions fit together.
Author: Luis Amigo
Date: 2023-09-10 08:05PM
Template: post
Tags: Desarrollo Web, Sistemas, Ciberseguridad
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 4
Lang: en
Translation_Key: praderas-ctd-04
Image: /assets/images/ctd-04-postgrest-jwt-authentication-hero.webp

---

We are building a decoupled **time-tracking** product on **PostgREST**. To protect the REST surface we adopt **JWT** (JSON Web Tokens) for authentication and authorization hints between client and API.

## What is a JWT?

A JWT is a compact, signed JSON payload exchanged between parties. It is typically split into **header**, **payload**, and **signature**. They are widely used to authenticate users and carry claims for downstream authorization.

## Implementing JWT with PostgREST (outline)

1. **Create database roles** that mirror how clients connect (`web`, `anon`, etc.).
2. **Configure PostgREST** so each connection uses the right role and JWT secret settings.
3. **Issue tokens** from PostgreSQL functions (or adjacent services) using your signing key.
4. **Authenticate users** by validating credentials, then returning a JWT on success.
5. **Return the token** to the client for storage (prefer secure, HTTP-only patterns where possible).
6. **Send `Authorization: Bearer …`** on subsequent requests.
7. **Validate** signatures and claims on every protected call.

Example role bootstrap (illustrative):

```sql
CREATE ROLE web_user LOGIN PASSWORD 'replace-me';
CREATE ROLE anon_user NOLOGIN;
```

Example `jwt_encode` style call (depends on your extensions and security model):

```sql
SELECT jwt_encode('{"role": "web_user"}'::json, 'replace-with-strong-secret');
```

## Why JWT fits here

- **Scalability** — validators can check signatures without hitting the database on every read (trade-offs apply—revocation needs a strategy).
- **Integrity** — signatures detect tampering.
- **Simplicity** — clients only need a header and a token lifecycle policy.
- **Stateless servers** — horizontal scaling is easier when sessions are not pinned to a single node.

JWTs are not a silver bullet—secret rotation, clock skew, and revocation still matter. Treat this article as a map, not a compliance checklist.

Next we pivot to the **React** frontend—start with [React, ES5, ES6, and how it runs](%base_url%/blog/en/react-es5-es6-intro).
