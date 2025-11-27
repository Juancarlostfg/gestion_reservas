<?php
session_start();
include 'includes/conexion.php';


if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM usuarios WHERE email='$email'";
    $result = $conexion->query($sql);
    
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        var_dump($usuario['password']); // muestra el hash que llegó desde BD
        var_dump(password_verify($password, $usuario['password'])); // true/false

        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
        }
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 25px 0;
        }
        .option-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        .option-card:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }
        .demo-accounts {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <header style="text-align: center; margin-bottom: 30px;">
                <h1>🚗 Autos Costa Sol</h1>
                <p>Iniciar Sesión en el Sistema</p>
            </header>

            <?php if (isset($error)): ?>
                <div style="color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin-bottom: 20px;">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="margin-bottom: 20px;">
                    <label>📧 Correo Electrónico:</label>
                    <input type="email" name="email" placeholder="tu@email.com" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label>🔒 Contraseña:</label>
                    <input type="password" name="password" placeholder="Tu contraseña" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <button type="submit" style="width: 100%; background: #007bff; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    🚀 Iniciar Sesión
                </button>
            </form>

            <!-- Cuentas de demostración -->
            <div class="demo-accounts">
                <h4>👨‍💼 Cuentas de Demo:</h4>
                <p><strong>Empleado:</strong> empleado@rentacar.com / password</p>
                <p><strong>Cliente:</strong> Regístrate gratis</p>
            </div>

            <!-- Opciones de navegación -->
            <div class="options-grid">
                <a href="register.php" class="option-card">
                    <h4>📝 Registrarse</h4>
                    <p>Nuevo cliente</p>
                </a>
                
                <a href="index.php" class="option-card">
                    <h4>🏠 Inicio</h4>
                    <p>Página principal</p>
                </a>
                
                <a href="dashboard.php" class="option-card">
                    <h4>📊 Dashboard</h4>
                    <p>Ir al panel</p>
                </a>
                
                <a href="crear_reserva.php" class="option-card">
                    <h4>🚗 Reservar</h4>
                    <p>Nueva reserva</p>
                </a>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p>¿Problemas para acceder? <a href="index.php">Volver al inicio</a></p>
            </div>
        </div>
    </div>
</body>
</html>