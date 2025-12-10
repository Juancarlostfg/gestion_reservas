<?php
// Configuración SMTP

// EJEMPLO con Gmail:
// - Tienes que activar "Contraseñas de aplicación" en tu cuenta
// - SMTP_USER = tu correo Gmail
// - SMTP_PASS = contraseña de aplicación (NO la normal)

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'TU_CORREO@gmail.com');      // ← CAMBIA ESTO
define('SMTP_PASS', 'TU_CONTRASEÑA_APP');        // ← CAMBIA ESTO
define('SMTP_FROM', 'TU_CORREO@gmail.com');      // Desde
define('SMTP_FROM_NAME', 'Autos Costa Sol');     // Nombre que verá el cliente
