---
Title: Mejora de Seguridad y Acceso con Nginx en PostgREST
Description: En este artículo hablamos de Mejora de Seguridad y Acceso con Nginx en PostgREST
Author: Luis Amigo
Date: 2023-09-08 10:29AM
Template: post
Tags: Sistemas
---

En nuestra serie de desarrollo de arquitecturas desacopladas, estamos construyendo una sólida aplicación de control de horas. En el artículo anterior, configuramos con éxito nuestra API REST utilizando PostgREST. Ahora, es el momento de mejorar la seguridad y la accesibilidad utilizando Nginx como servidor proxy y Certbot para la seguridad HTTPS.

## Instalación de Nginx

Comenzaremos instalando Nginx en nuestro servidor Debian 11:

```bash
sudo apt update
sudo apt install nginx -y
```

## Configuración de Nginx como Proxy para PostgREST

Nginx actuará como un servidor proxy inverso que enruta las solicitudes HTTP entrantes a nuestro servicio PostgREST. Esto se logra mediante la configuración del archivo de host virtual de Nginx. Asegúrate de ajustar las rutas y los puertos según tu configuración:

```nginx
server {
    listen 80;
    server_name tu-dominio.com;

    location /api {
        proxy_pass http://localhost:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location / {
        # Configura la ruta a tu frontend React
        root /ruta/a/tu/frontend;
        index index.html;
    }

    # ... Configuración adicional ...
}
```

## Configuración Básica de Seguridad en Nginx

Para una mayor seguridad, es importante configurar Nginx adecuadamente. Esto incluye:

- Restringir el acceso a rutas específicas.
- Ocultar información sensible del servidor en las respuestas HTTP.
- Configurar límites de velocidad y límites de conexiones para evitar ataques de denegación de servicio.

## Generación de Certificado con Certbot y Redirección a HTTPS

Para cifrar las comunicaciones entre el cliente y el servidor, habilitaremos HTTPS mediante un certificado SSL. Utilizaremos Certbot, una herramienta que simplifica la obtención y renovación de certificados SSL de Let's Encrypt:

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx
```

Certbot configurará automáticamente Nginx para utilizar HTTPS y redirigir todas las solicitudes HTTP a HTTPS.

## Uso de Usuario Anónimo para Autenticación y Devolución de Token JWT

Para autenticar a los usuarios en nuestra API, configuraremos un usuario anónimo que se utilizará para obtener un token JWT. A través de esta autenticación, los usuarios podrán acceder a la API de manera segura y obtener un token de autenticación.

En el siguiente artículo, enseñaremos cómo generar el token JWT y asignarlo a un rol específico. Con estas medidas, mejoraremos significativamente la seguridad y la accesibilidad de nuestra aplicación. ¡Mantente atento para obtener más información emocionante sobre el desarrollo de arquitecturas desacopladas!
