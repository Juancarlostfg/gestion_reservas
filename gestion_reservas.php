<?php
session_start();

// CONEXIÓN DIRECTA
$host = "localhost";
$user = "root";
$password = "";
$database = "gestion_reservas";

$conexion = new mysqli($host, $user, $password, $database);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// VERIFICAR EMPLEADO
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 'empleado') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <nav class="navbar">
                <div class="logo">
                    <span class="logo-icon">🚗</span>
                    <span>Autos Costa Sol</span>
                </div>
                <div class="nav-links">
                    <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
                    <a href="logout.php" class="btn-logout">🚪 Salir</a>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <h1>⚙️ Gestión de Reservas</h1>
            <p>Administra y gestiona todas las reservas del sistema</p>
        </section>

        <div class="form-container">
            <div style="text-align: center; padding: 40px;">
                <h2 style="color: #27ae60;">✅ ¡GESTIÓN DE RESERVAS FUNCIONA!</h2>
                <p>Usuario: <strong><?php echo $_SESSION['usuario_nombre']; ?></strong> (<?php echo $_SESSION['usuario_rol']; ?>)</p>
                <p>Esta página demuestra que el sistema de empleados funciona correctamente.</p>
                
                <?php
                // Mostrar algunas reservas de prueba
                $sql = "SELECT COUNT(*) as total FROM reservas";
                $result = $conexion->query($sql);
                $total_reservas = $result ? $result->fetch_assoc()['total'] : 0;
                ?>
                
                <div style="background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <h3>📊 Resumen del Sistema</h3>
                    <p>Total de reservas en el sistema: <strong><?php echo $total_reservas; ?></strong></p>
                </div>
                
                <a href="dashboard.php" class="btn btn-primary">Volver al Dashboard</a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2024 Autos Costa Sol - Panel de Gestión</p>
        </footer>
    </div>
</body>
</html>