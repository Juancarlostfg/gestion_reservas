
🚗 Autos Costa Sol – Gestor de Reservas de Vehículos

Proyecto final de Grado Superior en Administración de Sistemas Informáticos en Red (ASIR).
Aplicación web para la gestión integral de reservas de vehículos, con control de usuarios, seguridad, generación de informes en PDF y servicios de red.

📌 Descripción del proyecto

Autos Costa Sol es una aplicación web desarrollada en PHP y MySQL que permite gestionar reservas de vehículos de forma segura y centralizada.

El sistema diferencia claramente entre clientes y empleados, aplicando control de accesos, validación de credenciales y protección frente a ataques comunes.
Incluye además un módulo en Python para generar informes PDF con estadísticas del sistema, cumpliendo la exigencia de uso de múltiples lenguajes.

👥 Roles del sistema
👤 Cliente

Registro e inicio de sesión

Crear nuevas reservas

Consultar sus reservas

Ver vehículos disponibles

Gestión de su perfil

Recepción de email de confirmación de reserva

👨‍💼 Empleado

Acceso completo al sistema

Gestión de reservas de todos los clientes

Gestión de clientes

Gestión de vehículos

Generación de informes PDF

Acceso restringido a funcionalidades administrativas

🧰 Tecnologías utilizadas
Backend

PHP 8

MySQL

Python 3

Frontend

HTML5

CSS3 (diseño responsive)

JavaScript básico

Librerías y herramientas

PHPMailer (envío de correos)

ReportLab (generación de PDF en Python)

MySQL Connector Python

Red y sistemas

Servidor Apache (XAMPP)

DNS local (VirtualHost: rentacar.local)

DHCP para asignación dinámica de IP

Git y GitHub para control de versiones

🔐 Seguridad implementada

Contraseñas cifradas con password_hash()

Verificación de sesión y roles en cada página

Protección frente a:

Inyección SQL

XSS (Cross-Site Scripting)

Acceso restringido según rol

Validación de datos de entrada

Separación de credenciales en archivos de configuración

📧 Sistema de correo

Envío automático de email de confirmación al crear una reserva

Implementado con PHPMailer

Probado con Mailtrap (entorno seguro de pruebas)

Configuración SMTP externa

📄 Informe PDF en Python

El sistema incluye un script en Python que genera un informe PDF automático con:

Número total de reservas

Ingresos totales

Reservas agrupadas por:

Estado

Tipo de vehículo

Mes

Fecha y hora de generación

El informe:

Se genera directamente desde la base de datos

Se descarga desde el dashboard del empleado

Demuestra integración entre PHP y Python

🌐 Servicios de red

Resolución del dominio local rentacar.local mediante DNS

Asignación de IP mediante DHCP

Acceso al sistema desde navegador web

Despliegue local preparado para presentación en aula

⚠️ Plan de riesgos

El proyecto incluye un plan de riesgos donde se identifican:

Activos del sistema

Amenazas técnicas y organizativas

Vulnerabilidades

Impacto y probabilidad

Medidas de mitigación aplicadas

📁 Documentado en la carpeta Documentos/.

📂 Estructura del proyecto (resumen)
gestion_reservas/
├── css/
├── img/
├── includes/
│   ├── conexion.php
│   ├── mail_config.php
├── PHPMailer/
├── Documentos/
│   └── plan_riesgos.pdf, Video explicativo del funcionamiento e informe PDF con todo el proyecto.
├── informe_reservas.py
├── generar_informe.php
├── dashboard.php
├── crear_reserva.php
├── gestion_reservas.php
├── vehiculos.php
├── mis_reservas.php
├── login.php
├── register.php
└── README.md

🚀 Instalación básica

Clonar el repositorio

Copiar el proyecto en htdocs

Crear la base de datos gestion_reservas

Importar las tablas necesarias

Configurar:

includes/conexion.php

includes/mail_config.php

Configurar VirtualHost (rentacar.local)

Acceder desde el navegador

📈 Posibles mejoras futuras

Pasarela de pago online

API REST

Panel de estadísticas gráficas

Control de disponibilidad real de vehículos

Despliegue en servidor externo

Autenticación multifactor

👨‍🎓 Autor

Juan Carlos García Calvo
Proyecto Final – ASIR
Repositorio desarrollado y versionado con GitHub

✅ Estado del proyecto

✔ Funcional
✔ Probado
✔ Documentado
✔ Listo para defensa