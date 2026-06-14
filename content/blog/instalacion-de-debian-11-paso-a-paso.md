---

Title: Instalación de Debian 11 paso a paso
Description: Pasos básicos para instalar Debian 11 "Bullseye" en tu equipo.
Date: 2023-08-09
Template: post
Author: Luis Amigo
Tags: Sistemas
Lang: es
Translation_Key: praderas-b8-debian-11-install
Image: /assets/images/b8-debian-11-install-walkthrough-hero.webp

---

## Introducción

Debian 11, también conocido como "Bullseye", es la última versión estable de esta popular distribución de Linux. Conocida por su enfoque en la estabilidad, seguridad y software de código abierto, Debian es una elección sólida para usuarios que buscan un sistema operativo confiable y versátil. En esta guía, te proporcionaremos un detallado paso a paso sobre cómo instalar Debian 11 en tu sistema, asegurándote de tener un proceso fluido y exitoso.

## Preparativos

Antes de sumergirnos en la instalación, hay algunos preparativos importantes que debes realizar:

1. **Descarga de la imagen ISO:**
   Dirígete al [sitio web oficial de Debian](https://www.debian.org) y descarga la imagen ISO correspondiente a la arquitectura de tu sistema (por ejemplo, 32 bits o 64 bits). También puedes optar por las imágenes de instalación en red o las imágenes de pequeño tamaño si prefieres una descarga más rápida y solo deseas instalar los paquetes necesarios.

2. **Crear un medio de instalación:**
   Graba la imagen ISO en un DVD o crea una unidad USB de arranque utilizando herramientas como Etcher o Rufus. Esto te permitirá arrancar desde el medio de instalación y comenzar el proceso de instalación de Debian 11.

## Proceso de Instalación

Una vez que hayas creado el medio de instalación, estás listo para iniciar el proceso de instalación de Debian 11:

1. **Arranque desde el medio de instalación:**
   Inserta el DVD o la unidad USB en tu sistema y reinicia la computadora. Asegúrate de que la configuración de arranque esté configurada para arrancar desde el medio que has creado. El sistema debería arrancar desde el medio de instalación y presentarte el menú de inicio de Debian.

2. **Selecciona la opción de instalación:**
   En el menú de inicio, elige la opción "Install" para comenzar el proceso de instalación.

3. **Selecciona el idioma y la ubicación:**
   Selecciona tu idioma preferido y tu ubicación. Esto configurará el sistema en el idioma y la zona horaria correctos.

4. **Configura el teclado:**
   Elige el diseño de teclado que corresponda al tuyo. Puedes probar la detección automática del teclado o seleccionar manualmente de la lista si es necesario.

5. **Configura la red y el hostname:**
   Configura la red de acuerdo a tus preferencias. Si estás utilizando una conexión por cable, el sistema intentará detectar la configuración automáticamente. Si prefieres configurarla manualmente, puedes hacerlo en esta etapa. También puedes establecer el nombre de host de tu sistema en esta sección.

6. **Configuración de la cuenta de usuario:**
   Proporciona tu nombre completo, nombre de usuario y contraseña para la cuenta de usuario principal. Esta cuenta tendrá privilegios administrativos mediante el uso del comando "sudo".

7. **Particionamiento del disco:**
   Llegamos a uno de los pasos más críticos. Puedes elegir entre el particionamiento guiado o manual. El particionamiento guiado es más adecuado para usuarios nuevos, ya que configura automáticamente las particiones. Si eres un usuario avanzado y deseas control total, elige la opción de particionamiento manual.
   
   Si optas por el particionamiento manual, asegúrate de crear al menos una partición raíz ("/") y, si lo deseas, una partición de intercambio ("swap") y una partición "/home" para tus archivos personales.

8. **Confirmación de los cambios en el disco:**
   Una vez que hayas establecido las particiones, el instalador te mostrará un resumen de los cambios en el disco. Verifica cuidadosamente que todo esté configurado según tus preferencias antes de continuar.

9. **Instalación del sistema base:**
   La instalación comenzará en esta etapa. El instalador copiará los archivos al disco y configurará el sistema base de Debian 11.

10. **Configuración de paquetes adicionales:**
    Durante la instalación, se te pedirá que elijas qué paquetes adicionales deseas instalar. Puedes optar por instalar el entorno de escritorio GNOME, KDE Plasma, Xfce u otros entornos según tus preferencias. También puedes elegir paquetes adicionales como servidores web, utilidades y aplicaciones.

11. **Instalación del cargador de arranque:**
    El instalador te dará la opción de instalar el cargador de arranque GRUB. Asegúrate de que esté marcado para que puedas arrancar en Debian 11 después de la instalación.

12. **Finalización de la instalación:**
    Una vez que todos los pasos anteriores estén completos, el instalador te notificará que la instalación ha finalizado con éxito. En este punto, puedes optar por reiniciar el sistema y arrancar en Debian 11.

## Conclusión

La instalación de Debian 11 "Bullseye" es un proceso relativamente sencillo, pero requiere atención a los detalles, especialmente en lo que respecta al particionamiento del disco. Siguiendo los pasos descritos en esta guía, estarás bien encaminado para tener un sistema Debian 11 funcional y estable. Desde aquí, puedes personalizar tu sistema, instalar software adicional y comenzar a disfrutar de todas las ventajas que Debian tiene para ofrecer en términos de seguridad, estabilidad y versatilidad. ¡Bienvenido a la comunidad de usuarios de Debian 11!

