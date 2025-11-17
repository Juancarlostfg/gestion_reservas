<?php
session_start();
include 'includes/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_rol = $_SESSION['usuario_rol'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = $usuario_id";
$usuario = $conexion->query($sql)->fetch_assoc();

// Contar reservas del usuario
$sql_reservas = "SELECT COUNT(*) as total FROM reservas WHERE usuario_id = $usuario_id";
$total_reservas = $conexion->query($sql_reservas)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <header style="background: #343a40; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h1 style="margin: 0;">👤 Mi Perfil</h1>
            <a href="dashboard.php" style="color: white;">← Volver al Dashboard</a>
        </header>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            <!-- Información del usuario -->
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>📊 Mis Datos</h3>
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="width: 80px; height: 80px; background: #007bff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 2em;">
                        <?php echo strtoupper(substr($usuario_nombre, 0, 1)); ?>
                    </div>
                </div>
                
                <p><strong>👤 Nombre:</strong> <?php echo $usuario_nombre; ?></p>
                <p><strong>📧 Email:</strong> <?php echo $usuario['email']; ?></p>
                <p><strong>🎯 Rol:</strong> <?php echo $usuario_rol == 'cliente' ? 'Cliente' : 'Empleado'; ?></p>
                <p><strong>📅 Miembro desde:</strong> <?php echo $usuario['fecha_creacion']; ?></p>
                <p><strong>🚗 Reservas totales:</strong> <?php echo $total_reservas; ?></p>
            </div>

            <!-- Estadísticas y acciones -->
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>🚀 Acciones Rápidas</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0;">
                    <a href="crear_reserva.php" style="background: #007bff; color: white; padding: 15px; border-radius: 8px; text-decoration: none; text-align: center;">
                        🚗 Nueva Reserva
                    </a>
                    <a href="mis_reservas.php" style="background: #28a745; color: white; padding: 15px; border-radius: 8px; text-decoration: none; text-align: center;">
                        📋 Mis Reservas
                    </a>
                    <a href="dashboard.php" style="background: #6c757d; color: white; padding: 15px; border-radius: 8px; text-decoration: none; text-align: center;">
                        📊 Dashboard
                    </a>
                    <a href="logout.php" style="background: #dc3545; color: white; padding: 15px; border-radius: 8px; text-decoration: none; text-align: center;">
                        🚪 Cerrar Sesión
                    </a>
                </div>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                    <h4>💡 Próximas Funcionalidades</h4>
                    <ul>
                        <li>Editar perfil y datos personales</li>
                        <li>Cambiar contraseña</li>
                        <li>Historial completo de reservas</li>
                        <li>Preferencias de vehículos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>