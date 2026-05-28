---
Title: "Decoupled Architectures: Building a Time-Tracking App"
Description: Overview of a decoupled stack (Debian, PostgREST, Nginx, React, PostgreSQL) for a small-company time-tracking product.
Author: Luis Amigo
Date: 2023-09-06 11:21AM
Template: post
Tags: Desarrollo Web, Sistemas
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 1
Lang: en
Translation_Key: praderas-ctd-01
Image: /assets/images/ctd-01-decoupled-time-tracking-hero.webp

---

In this article series we walk through building **decoupled architectures**. The end goal is a **time-tracking application** for a small company. Along the way we break down each component—from server setup to a **React** user interface.

## Project overview

The app will help a company manage time spent on different projects. It is shaped around three roles:

1. **Administrator:** Full control—create users and projects, and assign roles per project.
2. **Manager:** Create tasks inside projects, assign them to users, and review time logged on tasks.
3. **Worker:** Log hours on tasks they are assigned to.

Users can hold different roles in different projects, so responsibilities and access stay flexible.

## Core tools

We rely on a small but powerful stack (with common alternatives called out in the originals):

1. **Debian 11 server** — open OS baseline for infrastructure (alternatives: Ubuntu Server, CentOS).
2. **PostgREST** — auto-generates a REST API from PostgreSQL (alternatives: Express, Django REST).
3. **Nginx** — web server and reverse proxy (alternatives: Apache, Caddy).
4. **React** — UI library for an interactive frontend (alternatives: Vue, Angular).
5. **PostgreSQL** — relational database engine (alternatives: MySQL, SQLite).

## How the series is structured

Each instalment focuses on one slice of the system. A preview of what follows:

1. **[Debian 11 server setup](%base_url%/blog/en/debian-11-install-step-by-step)** — paired English walkthrough of the Spanish install guide.
2. **[PostgREST implementation](%base_url%/blog/en/postgrest-rest-api-setup)** — standing up the auto-generated REST API.
3. **Nginx** — reverse proxy, TLS, and safer exposure of PostgREST.
4. **React frontend** — screens and flows for the product.
5. **Users and roles** — admin workflows for membership and permissions.
6. **Tasks** — managers create and assign work.
7. **Time entries** — workers log hours against tasks.

We are excited to keep publishing this thread—and we hope it helps you reason about decoupled stacks in your own projects. Questions and comments welcome as we go.
