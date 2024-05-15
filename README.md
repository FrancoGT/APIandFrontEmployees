# Guía de instalación de APIandFrontEmployees

BACKEND
=======
En la carpeta: entry_exit_employees esta la API REST implementada en Laravel.
Esta carpeta debe ser enviada a un directorio donde se trabaje un entorno de Apache.
En mi caso uso Laragon y mis proyectos en Laravel los tengo en el directorio: C:\laragon\www
El archivo .env.example se debe modificar por .env
En el archivo .env hay variables de entorno por defecto, puede cambiarlas dependendiendo de la configuración de la base de datos MySQL que tenga en su equipo.
Se debe abrir una terminal en la carpeta raíz entry_exit_employees
Se debe instalar las dependencias con el comando: composer install
Se debe crear una base de datos llamada entry_exit_employees en el gestor MySQL que tenga disponible como HeidySQL, MYSQL Workbench, etc
Se debe ejecutar el comando: php artisan migrate para crear las tablas
Se debe levantar el servidor con el comando: php artisan serve.

Nota: La API REST está en Laravel 8.83.27

FRONTEND
========
En la carpeta: crudemployees esta la app FrontEnd en Angular que consumirá la API REST implementada en Laravel.
Cabe recalcar que la API REST tiene que estar levantada.
Se debe abrir una terminal en la carpeta raíz crudemployees
Se debe ejecutar el comando: npm install
Para levantar el proyecto se debe ejecutar el comando: ng serve
EN la terminal se puede visualizar una url, en mi caso por defecto es http://localhost:4200/
Se pone la url en un navegador y se prueba las funcionalidades.

Nota: La app de Angular está en 16.2.0
