---
Title: "Scaffolding a React frontend for role management"
Description: Create React App (or equivalent), project layout, and the screens you need to list and edit roles against the REST API.
Author: Luis Amigo
Date: 2023-09-19 12:27PM
Template: post
Tags: Desarrollo Web
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 10
Lang: en
Translation_Key: praderas-ctd-10
---

We now build the **React** UI for the **time-tracking** product, starting with **role management**—list views and create/edit flows backed by the API you already exposed.

## Why React here?

React encourages **reusable components** that re-render when data changes—ideal for admin tables and forms that talk to PostgREST.

## Bootstrap a project

Install **Node.js** + **npm**, then:

```bash
npx create-react-app role-management-frontend
cd role-management-frontend
npm start
```

Key folders:

- `src/` — application code (`App.js`, components, hooks).
- `public/` — static assets and `index.html`.

## Pages to implement first

### List roles (`ListRoles.js`)

Fetch from your API (`fetch`, `axios`, or React Query), store results in component state, render a table or list with links to edit routes.

### Create / edit role (`EditRole.js`)

Controlled inputs for role fields, `POST`/`PATCH`/`PUT` to the API on submit, optimistic or pessimistic UI feedback.

Bootstrap (or your design system) speeds layout polish.

## Next

We drop in concrete component examples—see [Role management components in React](%base_url%/blog/en/react-role-management-list-edit).
