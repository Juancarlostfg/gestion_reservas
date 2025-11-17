<?php
session_start();
include 'includes/conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 'empleado') {
    header("Location: login.php");
    exit();
}

// Estadísticas para reportes
$sql_total_reservas = "SELECT COUNT(*) as total FROM reservas";
$sql_reservas_hoy = "SELECT COUNT(*) as hoy FROM reservas WHERE DATE(fecha_creacion) = CURDATE()";
$sql_ingresos_totales = "SELECT SUM(precio_total) as ingresos FROM reservas WHERE estado_reserva IN ('confirmada', 'completada')";
$sql_clientes_totales = "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'cliente'";

$total_reservas = $conexion->query($sql_total_reservas)->fetch_assoc()['total'];
$reservas_hoy = $conexion->query($sql_reservas_hoy)->fetch_assoc()['hoy'];
$ingresos_totales = $conexion->query($sql_ingresos_totales)->fetch_assoc()['ingresos'] ?? 0;
$clientes_totales = $conexion->query($sql_clientes_totales)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin: 10px 0;
        }
        .ingresos { color: #28a745; }
        .reservas { color: #007bff; }
        .clientes { color: #6f42c1; }
        .hoy { color: #fd7e14; }
    </style>
</head>
<body>
    <div class="container">
        <header style="background: #343a40; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h1 style="margin: 0;">📈 Reportes y Estadísticas</h1>
            <a href="dashboard.php" style="color: white;">← Volver al Dashboard</a>
        </header>

       
        <div class="stats-grid">
            <div class="stat-card">
                <h3>💰 Ingresos Totales</h3>
                <div class="stat-number ingresos">€<?php echo number_format($ingresos_totales, 2); ?></div>
                <p>Reservas confirmadas/completadas</p>
            </div>
            <div class="stat-card">
                <h3>🚗 Total Reservas</h3>
                <div class="stat-number reservas"><?php echo $total_reservas; ?></div>
                <p>Reservas en el sistema</p>
            </div>
            <div class="stat-card">
                <h3>👥 Total Clientes</h3>
                <div class="stat-number clientes"><?php echo $clientes_totales; ?></div>
                <p>Clientes registrados</p>
            </div>
            <div class="stat-card">
                <h3>📅 Reservas Hoy</h3>
                <div class="stat-number hoy"><?php echo $reservas_hoy; ?></div>
                <p>Reservas creadas hoy</p>
            </div>
        </div>

       
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 20px;">
            <h3>📊 Reportes Disponibles</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h4>🚗 Reservas por Tipo</h4>
                    <p>Distribución de reservas por categoría de vehículo</p>
                </div>
                <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h4>📅 Reservas por Mes</h4>
                    <p>Evolución mensual de reservas</p>
                </div>
                <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h4>👥 Clientes Activos</h4>
                    <p>Clientes con más reservas</p>
                </div>
                <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h4>💰 Ingresos Mensuales</h4>
                    <p>Proyección de ingresos</p>
                </div>
            </div>
            
            <div style="margin-top: 25px; padding: 20px; background: #e7f3ff; border-radius: 8px;">
                <h4>💡 Próximamente</h4>
                <p>Estamos trabajando en reportes más detallados con gráficos interactivos.</p>
            </div>
        </div>
    </div>
</body>
</html>