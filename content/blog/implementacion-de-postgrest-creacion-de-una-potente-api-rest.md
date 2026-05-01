---
Title: Implementación de PostgREST: Creación de una Potente API REST
Description: En este artículo, exploraremos qué es una API REST, cuáles son sus ventajas y cómo podemos lograr esto utilizando PostgREST
Author: Luis Amigo
Date: 2023-09-07 10:41AM
Template: post
Tags: Desarrollo Web, Sistemas
Series: Control de Tiempo Desacoplado
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 2
Lang: es
Translation_Key: praderas-ctd-02
---

En nuestra serie de desarrollo de arquitecturas desacopladas, llegamos a un paso crucial: la implementación de una API REST que nos permitirá interactuar con nuestra base de datos PostgreSQL de manera eficiente. En este artículo, exploraremos qué es una API REST, cuáles son sus ventajas y cómo podemos lograr esto utilizando PostgREST.

## ¿Qué es una API REST?

**API REST** (Transferencia de Estado Representacional) es un enfoque arquitectónico para diseñar sistemas de software distribuidos. Se basa en principios simples y utiliza métodos HTTP estándar para realizar operaciones en datos, recursos o servicios. Algunos de los conceptos clave de una API REST incluyen:

- **Recursos:** Representaciones de objetos o datos, a menudo accesibles a través de URL.

- **Métodos HTTP:** Acciones que se realizan en los recursos, como GET (obtener datos), POST (crear datos), PUT (actualizar datos) y DELETE (eliminar datos).

- **Estado Representacional:** Los datos transmitidos entre el cliente y el servidor representan el estado del recurso en un momento dado.

## Ventajas de una API REST

Las API REST ofrecen varias ventajas:

- **Simplicidad:** Utilizan métodos HTTP familiares y URL para interactuar con recursos, lo que facilita la comprensión y el uso.

- **Independencia de Plataforma:** Las API REST pueden ser consumidas por una amplia variedad de clientes, independientemente de la plataforma o tecnología que utilicen.

- **Escalabilidad:** Los servicios REST pueden escalar de manera eficiente, ya que cada solicitud es independiente y no requiere estado del servidor.

## Introducción a PostgREST

**PostgREST** es una herramienta poderosa que genera automáticamente una API RESTful a partir de una base de datos PostgreSQL. Es una forma efectiva de exponer rápidamente los datos almacenados en PostgreSQL como servicios web RESTful.

## Control de Seguridad en PostgREST

PostgREST incluye características de seguridad esenciales:

- **Autenticación y Autorización:** Puedes controlar quién tiene acceso a tus recursos a través de roles y permisos en PostgreSQL.

- **Filtrado de Filas:** PostgREST permite configurar restricciones en las filas que los usuarios pueden acceder.

## Instalación de PostgREST y PostgreSQL

Para comenzar con PostgREST, primero debemos instalar tanto PostgREST como PostgreSQL en nuestro servidor Debian 11. Esto se puede hacer utilizando los siguientes comandos:

```bash
# Actualiza los repositorios y instala PostgreSQL y PostgREST
sudo apt update
sudo apt install postgresql postgresql-client postgrest -y
```

## Configuración Inicial

Una vez instalados, necesitamos realizar algunas configuraciones iniciales:

1. **Creación de Usuarios de PostgreSQL:**

   - `dba_user`: El usuario de administración de base de datos que será el propietario de la base de datos.
   - `admin_user`: El usuario de administración que utilizará nuestra aplicación para crear nuevos elementos.
   - `web_user`: El usuario que utilizará el API.
   - `anon_user`: El usuario que utilizaremos para autenticación.

   ```sql
   CREATE USER dba_user WITH PASSWORD 'dba_password';
   CREATE USER admin_user WITH PASSWORD 'admin_password';
   CREATE USER web_user WITH PASSWORD 'web_password';
   CREATE USER anon_user WITH PASSWORD 'anon_password';
   ```

2. **Creación del Esquema de la Aplicación:**

   Crearemos un esquema llamado `app_schema` que contendrá nuestras tablas de aplicación.

   ```sql
   CREATE SCHEMA app_schema;
   ```

3. **Creación de Tabla de Prueba:**

   Comenzaremos con una tabla básica de usuarios para nuestro API. Esto es solo un ejemplo inicial.

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

4. **Configuración Básica de PostgREST:**

   Para que el API se exponga públicamente, debemos crear un archivo de configuración básico. Asegúrate de ajustar la configuración según tus necesidades específicas.

5. **Configuración de PostgREST como Servicio:**

   Configura PostgREST para ejecutarse como un servicio del sistema para que esté disponible de manera continua.

## Ejemplos de Solicitudes al API

Finalmente, para verificar que todo esté configurado correctamente, puedes realizar algunas solicitudes al API utilizando herramientas como `curl`. Aquí hay algunos ejemplos:

- Obtener todos los usuarios:

   ```bash
   curl http://localhost:3000/app_schema/users
   ```

- Crear un nuevo usuario:

   ```bash
   curl -X POST -H "Content-Type: application/json" -d '{"login":"johndoe","name":"John","last_name":"Doe","password":"password","is_active":true}' http://localhost:3000/app_schema/users
   ```

- Obtener un usuario específico por ID:

   ```bash
   curl http://localhost:3000/app_schema/users?id=eq.1
   ```

Este artículo sienta las bases para nuestra API REST utilizando PostgREST. En el siguiente artículo, agregaremos Nginx y seguridad mediante tokens JWT para proteger y mejorar aún más nuestro API. ¡Continúa siguiendo nuestra serie de desarrollo de arquitecturas desacopladas para obtener más información emocionante!
