<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autos Costal Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 80px 40px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 40px;
        }
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }
        .btn {
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            transition: transform 0.3s;
        }
        .btn-primary {
            background: #28a745;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            transform: translateY(-3px);
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }
        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <h1 style="font-size: 3em; margin: 0 0 20px 0;">🚗 Autos Costa Sol</h1>
            <p style="font-size: 1.5em; margin: 0; opacity: 0.9;">Tu solución perfecta para tus vacaciones</p>
            
            <div class="cta-buttons">
                <a href="login.php" class="btn btn-primary">🚀 Iniciar Sesión</a>
                <a href="register.php" class="btn btn-secondary">📝 Registrarse</a>
            </div>
        </div>

        <!-- Features -->
        <div class="features">
            <div class="feature-card">
                <h3>🚘 Amplia Flota</h3>
                <p>Vehículos económicos, compactos, sedanes, SUV y de lujo</p>
            </div>
            <div class="feature-card">
                <h3>⚡ Reserva Rápida</h3>
                <p>Sistema online sencillo e intuitivo</p>
            </div>
            <div class="feature-card">
                <h3>💰 Mejor Precio</h3>
                <p>Precios competitivos y transparentes</p>
            </div>
            <div class="feature-card">
                <h3>📱 Fácil Gestión</h3>
                <p>Controla tus reservas desde cualquier dispositivo</p>
            </div>
        </div>

        <!-- Demo Info -->
        <div style="text-align: center; background: #f8f9fa; padding: 30px; border-radius: 10px;">
            <h2>💡 ¿Primera vez por aquí?</h2>
            <p><strong>Demo del sistema:</strong></p>
            <div style="display: inline-block; text-align: left; background: white; padding: 20px; border-radius: 8px; margin: 15px 0;">
                <p><strong>👤 Como Cliente:</strong></p>
                <ol>
                    <li>Regístrate como nuevo usuario</li>
                    <li>Inicia sesión y crea tu primera reserva</li>
                    <li>Gestiona tus reservas desde el dashboard</li>
                </ol>
                
                <p><strong>👨‍💼 Como Empleado:</strong></p>
                <ul>
                    <li>Email: <code>empleado@rentacar.com</code></li>
                    <li>Contraseña: <code>password</code></li>
                    <li>Gestiona todas las reservas del sistema</li>
                </ul>
            </div>
            <br>
            <a href="register.php" class="btn btn-primary" style="display: inline-block;">🎯 Comenzar Ahora</a>
        </div>
    </div>
</body>
</html>