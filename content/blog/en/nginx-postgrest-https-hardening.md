---
Title: "Hardening PostgREST with Nginx and HTTPS"
Description: Put Nginx in front of PostgREST, add TLS with Certbot, and outline anonymous login flows that return JWTs.
Author: Luis Amigo
Date: 2023-09-08 10:29AM
Template: post
Tags: Sistemas
Series: Decoupled time tracking
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 3
Lang: en
Translation_Key: praderas-ctd-03
---

We are assembling a decoupled **time-tracking** stack. After standing up **PostgREST**, the next move is safer exposure: **Nginx** as a reverse proxy and **Certbot** for **HTTPS**.

## Install Nginx

```bash
sudo apt update
sudo apt install nginx -y
```

## Proxy PostgREST through Nginx

Nginx terminates HTTP(S) and forwards `/api` (or your chosen prefix) to PostgREST on `localhost:3000`. Adjust hostnames, paths, and TLS settings for your environment:

```nginx
server {
    listen 80;
    server_name your-domain.example;

    location /api {
        proxy_pass http://localhost:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location / {
        root /path/to/your/react/build;
        index index.html;
    }
}
```

## Baseline hardening ideas

- Restrict sensitive paths and admin surfaces.
- Strip or normalize verbose `Server` headers.
- Add rate limits and connection caps to mitigate abuse.

## TLS with Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx
```

Certbot can rewrite your server block for HTTPS redirects.

## Anonymous role and JWT handoff

For authenticated access we configure an **anonymous** database role that can only perform the narrow steps required to mint a **JWT**. The following article dives into token issuance and validation—see [JWT authentication with PostgREST](%base_url%/blog/en/postgrest-jwt-authentication).
