# 🚗 Autos Costa Sol - Central de Reservas (TFG ASIR)

Proyecto de **Central de Reservas para un Rent a Car** desarrollado como **Trabajo de Fin de Grado (TFG)** del ciclo **ASIR (Administración de Sistemas Informáticos en Red)**.

La aplicación permite la gestión de reservas de vehículos con dos tipos de usuarios:

* **Cliente**
* **Empleado (Administrador)**

---

## 🧑‍💻 Tecnologías Utilizadas

* PHP 8.x
* MySQL / MariaDB
* Apache (XAMPP)
* HTML5
* CSS3
* JavaScript
* Git & GitHub
* phpMyAdmin

---

## 📦 Estructura del Proyecto

```
/gestion_reservas
│── css/
│── img/
│── includes/
│── dashboard.php
│── crear_reserva.php
│── mis_reservas.php
│── gestion_reservas.php
│── gestion_clientes.php
│── vehiculos.php
│── perfil.php
│── login.php
│── register.php
│── logout.php
│── index.php
```

---

## 👥 Tipos de Usuario

### 👤 Cliente

* Registro e inicio de sesión
* Crear reservas
* Consultar sus reservas
* Editar perfil

### 👨‍💼 Empleado

* Gestión completa de reservas
* Gestión de clientes
* Gestión de vehículos
* Crear reservas para clientes

---

## ⚙️ Instalación en Local (XAMPP)

1. Descargar e instalar **XAMPP**.
2. Copiar el proyecto en:

   ```
   C:\xampp\htdocs\gestion_reservas
   ```
3. Iniciar **Apache** y **MySQL** desde XAMPP.
4. Crear la base de datos desde **phpMyAdmin**.
5. Importar la estructura SQL.
6. Configurar la conexión en:

   ```
   includes/conexion.php
   ```
7. Abrir en el navegador:

   ```
   http://localhost/gestion_reservas
   ```

---

## 🗃️ Base de Datos

Base de datos utilizada:

```
gestion_reservas
```

Tablas principales:

* usuarios
* reservas

---

## 🔐 Seguridad

* Contraseñas almacenadas mediante `password_hash()`
* Control de sesión para cliente y empleado
* Validación de acceso por rol

---

## 📸 Capturas

(Las capturas de pantalla se añadirán aquí)

---

## 📚 Autor

Juan Carlos García Calvo.

---

## ✅ Estado del Proyecto

✔ Proyecto funcional
✔ Subido a GitHub
✔ En fase final de documentación

---

## 📄 Licencia

Proyecto educativo sin fines comerciales.
