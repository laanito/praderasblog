---
Title: "Designing the database for a time-tracking app"
Description: Conceptual tables for users, roles, projects, tasks, and worked hours—the backbone of the decoupled series.
Author: Luis Amigo
Date: 2023-09-12 02:07PM
Template: post
Tags: Desarrollo Web, Sistemas
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 6
Lang: en
Translation_Key: praderas-ctd-06
---

Solid applications start with a coherent **data model**. For **time tracking** we need to connect **users**, **roles**, **projects**, **tasks**, and **hours logged**.

## Users

Core identity records:

- **id** — primary key.
- **nombre / apellidos** — in the Spanish originals; translate to `first_name` / `last_name` in greenfield schemas.
- **email** — login identifier.
- **password hash** — never store raw passwords.
- **created_at** — audit trail.
- **active** — soft-disable accounts without deleting history.

## Roles

Describe permissions bundles:

- **id**
- **name** — e.g. admin, manager, worker.
- **description** — human-readable scope.

## Projects

Containers for work:

- **id**
- **name**
- **description**
- **start_date** / **end_date** — nullable while work is ongoing.

## Tasks

Assignable units inside a project:

- **id**, **name**, **description**
- **start_date** / **end_date**
- **project_id** — FK to projects.
- **assignee_id** — FK to users (or a join table if many assignees).

## Time entries

The operational ledger:

- **id**
- **hours** — numeric precision suited to payroll rules.
- **date** — business day of the entry.
- **task_id** / **user_id** — who logged what against which task.

## Conclusion

This schema is intentionally pragmatic: it supports the workflows we implement later with PostgREST and React. Security—authentication, authorization, and least-privilege DB roles—remains critical as you harden the system.

Next: [Database configuration: roles and first tables](%base_url%/blog/en/database-roles-permissions-time-tracking).
