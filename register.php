<?php
session_start();
include 'includes/conexion.php';

$mensaje = "";

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    
    // Primero verificamos si el email ya existe
    $sql_check = "SELECT id FROM usuarios WHERE email='$email'";
    $result_check = $conexion->query($sql_check);
    
    if ($result_check->num_rows > 0) {
        $mensaje = "<p style='color: red;'>Este email ya está registrado. Usa otro o <a href='login.php'>inicia sesión</a>.</p>";
    } else {
        // Si no existe, lo registramos
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (email, password, nombre) VALUES ('$email', '$password_hash', '$nombre')";
        
        if ($conexion->query($sql) === TRUE) {
            $mensaje = "<p style='color: green;'>¡Registro exitoso! Ahora puedes <a href='login.php'>iniciar sesión</a>.</p>";
        } else {
            $mensaje = "<p style='color: red;'>Error: " . $conexion->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Incidencias</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>
        
        <?php echo $mensaje; ?>
        
        <form method="post">
            <input type="text" name="nombre" placeholder="Tu nombre completo" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>