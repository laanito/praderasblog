---
Title: Interactuando con la API REST de tu Aplicación de Control de Tiempo
Description: En este artículo hablamos de Interactuando con la API REST de tu Aplicación de Control de Tiempo
Author: Luis Amigo
Date: 2023-09-18 02:07PM
Tags: Desarrollo Web, Sistemas
Series: Control de Tiempo Desacoplado
Series_Slug: control-de-tiempo-desacoplado
Series_Order: 9
Template: post
Lang: es
Translation_Key: praderas-ctd-09
Image: /assets/images/ctd-09-rest-api-client-hero.webp

---


En nuestra serie de desarrollo de una aplicación de control de tiempo, hemos configurado la base de datos y creado tablas esenciales para el seguimiento de proyectos, tareas y horas trabajadas. Ahora, es el momento de aprender cómo interactuar con el sistema a través de su API REST.

## Realizando Peticiones REST

### Tabla de Usuarios

Para acceder a la información de usuarios, puedes realizar peticiones GET a la siguiente URL:

```
GET /api/v1/usuarios
```

También puedes crear nuevos usuarios utilizando peticiones POST:

```
POST /api/v1/usuarios
```

### Tabla de Roles

Para obtener una lista de roles, realiza una solicitud GET a:

```
GET /api/v1/roles
```

### Tabla de Proyectos

Las solicitudes GET permiten acceder a la información de proyectos:

```
GET /api/v1/proyectos
```

Para crear un nuevo proyecto, realiza una solicitud POST:

```
POST /api/v1/proyectos
```

### Tabla de Tareas

Las tareas pueden consultarse mediante solicitudes GET:

```
GET /api/v1/tareas
```

Utiliza solicitudes POST para crear nuevas tareas:

```
POST /api/v1/tareas
```

### Tabla de Horas Trabajadas

Las horas trabajadas se pueden obtener con solicitudes GET:

```
GET /api/v1/horas_trabajadas
```

Para registrar nuevas horas trabajadas, realiza solicitudes POST:

```
POST /api/v1/horas_trabajadas
```

## Documentación del API con OpenAPI

La documentación de un API es esencial para que los desarrolladores comprendan cómo utilizarlo. Puedes utilizar OpenAPI para crear documentación detallada de tu API. OpenAPI te permite describir los endpoints, los parámetros, las respuestas y más.

## Herramientas para Probar APIs

Para probar y depurar tus solicitudes REST, puedes utilizar diversas herramientas. Algunas opciones populares incluyen:

- **Postman:** Una potente herramienta para probar y documentar APIs.
- **Insomnia:** Otra aplicación que simplifica la creación y ejecución de solicitudes REST.
- **Curl:** Una herramienta de línea de comandos para realizar solicitudes HTTP y obtener respuestas.

## Ejemplos de Llamadas a Endpoints

A continuación, te proporcionamos ejemplos de llamadas a endpoints utilizando la herramienta `curl`.

### Ejemplo de Solicitud GET

Para obtener una lista de usuarios:

```bash
curl -X GET http://tu-servidor/api/v1/usuarios
```

### Ejemplo de Solicitud POST

Para crear un nuevo proyecto:

```bash
curl -X POST -H "Content-Type: application/json" -d '{
    "nombre": "Mi Proyecto",
    "descripcion": "Descripción de mi proyecto"
}' http://tu-servidor/api/v1/proyectos
```

Estos ejemplos te brindan una idea de cómo interactuar con tu API. A medida que avances en el desarrollo de tu aplicación, explorarás más solicitudes y respuestas específicas para cada tabla y funcionalidad.

## Conclusión

Con este conocimiento, estás preparado para interactuar con la API REST de tu aplicación de control de tiempo. Las peticiones, la documentación y las herramientas de prueba son herramientas esenciales para cualquier desarrollador que trabaje con APIs. En los próximos artículos, profundizaremos en cómo implementar la lógica de la aplicación utilizando estas capacidades.

