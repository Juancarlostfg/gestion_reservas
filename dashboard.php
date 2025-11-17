<?php
session_start();


$host = "localhost";
$user = "root";
$password = "";
$database = "gestion_reservas";

$conexion = new mysqli($host, $user, $password, $database);
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}


$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT * FROM usuarios WHERE id = $usuario_id";
$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    
    $usuario_nombre = $usuario['nombre'];
    $usuario_rol = $usuario['rol'];
} else {
    
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container fade-in">
        <!-- Header -->
        <header class="header">
            <nav class="navbar">
                <div class="logo">
                    <span class="logo-icon">🚗</span>
                    <span>Autos Costa Sol</span>
                </div>
                <div class="nav-links">
                    <span style="color: #7f8c8d;">Bienvenido, <strong><?php echo $usuario_nombre; ?></strong></span>
                    <span class="badge" style="background: #3498db; color: white;">
                        <?php echo $usuario_rol == 'cliente' ? '👤 Cliente' : '👨‍💼 Empleado'; ?>
                    </span>
                    <a href="perfil.php" class="nav-link">👤 Mi Perfil</a>
                    <a href="logout.php" class="btn-logout">🚪 Cerrar Sesión</a>
                </div>
            </nav>
        </header>

        
        <section class="hero">
            <h1>¡Bienvenido a Autos Costa Sol! 🌅</h1>
            <p>Tu viaje perfecto comienza con el vehículo ideal</p>
            <div style="margin-top: 20px;">
                <span style="background: rgba(52, 152, 219, 0.2); color: #3498db; padding: 8px 16px; border-radius: 20px; font-size: 0.9em;">
                    💫 Calidad y confianza en cada kilómetro
                </span>
            </div>
        </section>

       
        
<div class="quick-actions">
    <?php if ($usuario_rol == 'cliente'): ?>
        <!-- VISTA CLIENTE -->
        <a href="crear_reserva.php" class="action-card">
            <h3>🚘 Nueva Reserva</h3>
            <p>Reserva tu vehículo ideal</p>
        </a>
        <a href="mis_reservas.php" class="action-card">
            <h3>📋 Mis Reservas</h3>
            <p>Gestiona tus reservas activas</p>
        </a>
        <a href="vehiculos.php" class="action-card">
            <h3>🔍 Ver Vehículos</h3>
            <p>Descubre nuestra flota</p>
        </a>
        <a href="perfil.php" class="action-card">
            <h3>👤 Mi Perfil</h3>
            <p>Actualiza tus datos</p>
        </a>
    <?php else: ?>
        <!-- VISTA EMPLEADO -->
        <a href="gestion_reservas.php" class="action-card">
            <h3>⚙️ Gestión Reservas</h3>
            <p>Administra todas las reservas</p>
        </a>
        <a href="vehiculos.php" class="action-card">
            <h3>🚙 Gestión Vehículos</h3>
            <p>Controla la flota disponible</p>
        </a>
        <a href="gestion_clientes.php" class="action-card">
            <h3>👥 Gestión Clientes</h3>
            <p>Gestiona información de clientes</p>
        </a>
        <a href="reportes.php" class="action-card">
            <h3>📈 Reportes</h3>
            <p>Estadísticas y análisis</p>
        </a>
    <?php endif; ?>
</div>

<!-- Stats Section -->
<div class="stats">
    <?php if ($usuario_rol == 'cliente'): ?>
        <?php
        // Estadísticas para CLIENTE
        $sql_reservas = "SELECT COUNT(*) as total FROM reservas WHERE usuario_id = $usuario_id";
        $result_reservas = $conexion->query($sql_reservas);
        $total_reservas = $result_reservas ? $result_reservas->fetch_assoc()['total'] : 0;
        
        $sql_proxima = "SELECT fecha_inicio FROM reservas WHERE usuario_id = $usuario_id AND estado_reserva IN ('pendiente', 'confirmada') ORDER BY fecha_inicio ASC LIMIT 1";
        $result_proxima = $conexion->query($sql_proxima);
        $proxima_reserva = $result_proxima && $result_proxima->num_rows > 0 ? date('d/m/Y', strtotime($result_proxima->fetch_assoc()['fecha_inicio'])) : 'No hay';
        
        $sql_activas = "SELECT COUNT(*) as activas FROM reservas WHERE usuario_id = $usuario_id AND estado_reserva IN ('pendiente', 'confirmada')";
        $reservas_activas = $conexion->query($sql_activas) ? $conexion->query($sql_activas)->fetch_assoc()['activas'] : 0;
        ?>
        <div class="stat-card">
            <h3>Total Reservas</h3>
            <div class="stat-number reservas"><?php echo $total_reservas; ?></div>
            <p>Reservas realizadas</p>
        </div>
        <div class="stat-card">
            <h3>Próxima Reserva</h3>
            <div style="font-size: 1.3em; color: #27ae60; font-weight: bold; margin: 10px 0;">
                <?php echo $proxima_reserva; ?>
            </div>
            <p>Tu próxima aventura</p>
        </div>
        <div class="stat-card">
            <h3>Reservas Activas</h3>
            <div class="stat-number hoy"><?php echo $reservas_activas; ?></div>
            <p>En proceso</p>
        </div>
    <?php else: ?>
        <?php
        // Estadísticas para EMPLEADO
        $sql_total_reservas = "SELECT COUNT(*) as total FROM reservas";
        $sql_reservas_hoy = "SELECT COUNT(*) as hoy FROM reservas WHERE DATE(fecha_creacion) = CURDATE()";
        $sql_reservas_pendientes = "SELECT COUNT(*) as pendientes FROM reservas WHERE estado_reserva = 'pendiente'";
        $sql_ingresos = "SELECT SUM(precio_total) as ingresos FROM reservas WHERE estado_reserva IN ('confirmada', 'completada')";
        
        $total_reservas = $conexion->query($sql_total_reservas) ? $conexion->query($sql_total_reservas)->fetch_assoc()['total'] : 0;
        $reservas_hoy = $conexion->query($sql_reservas_hoy) ? $conexion->query($sql_reservas_hoy)->fetch_assoc()['hoy'] : 0;
        $reservas_pendientes = $conexion->query($sql_reservas_pendientes) ? $conexion->query($sql_reservas_pendientes)->fetch_assoc()['pendientes'] : 0;
        $ingresos_totales = $conexion->query($sql_ingresos) ? ($conexion->query($sql_ingresos)->fetch_assoc()['ingresos'] ?? 0) : 0;
        ?>
        <div class="stat-card">
            <h3>Total Reservas</h3>
            <div class="stat-number reservas"><?php echo $total_reservas; ?></div>
            <p>En el sistema</p>
        </div>
        <div class="stat-card">
            <h3>Reservas Hoy</h3>
            <div class="stat-number hoy"><?php echo $reservas_hoy; ?></div>
            <p>Nuevas hoy</p>
        </div>
        <div class="stat-card">
            <h3>Pendientes</h3>
            <div class="stat-number" style="color: #e74c3c;"><?php echo $reservas_pendientes; ?></div>
            <p>Por confirmar</p>
        </div>
        <div class="stat-card">
            <h3>Ingresos</h3>
            <div class="stat-number ingresos">€<?php echo number_format($ingresos_totales, 0); ?></div>
            <p>Totales</p>
        </div>
    <?php endif; ?>
</div>
            
            
        </div>

        <!-- Stats Section -->
        <div class="stats">
            <?php if ($usuario_rol == 'cliente'): ?>
                <?php
                // Contar reservas del cliente
                $sql_reservas = "SELECT COUNT(*) as total FROM reservas WHERE usuario_id = $usuario_id";
                $result_reservas = $conexion->query($sql_reservas);
                $total_reservas = $result_reservas ? $result_reservas->fetch_assoc()['total'] : 0;
                
                // Próxima reserva
                $sql_proxima = "SELECT fecha_inicio FROM reservas WHERE usuario_id = $usuario_id AND estado_reserva IN ('pendiente', 'confirmada') ORDER BY fecha_inicio ASC LIMIT 1";
                $result_proxima = $conexion->query($sql_proxima);
                $proxima_reserva = $result_proxima && $result_proxima->num_rows > 0 ? date('d/m/Y', strtotime($result_proxima->fetch_assoc()['fecha_inicio'])) : 'No hay';
                
                // Reservas activas
                $sql_activas = "SELECT COUNT(*) as activas FROM reservas WHERE usuario_id = $usuario_id AND estado_reserva IN ('pendiente', 'confirmada')";
                $reservas_activas = $conexion->query($sql_activas) ? $conexion->query($sql_activas)->fetch_assoc()['activas'] : 0;
                ?>
                <div class="stat-card">
                    <h3>Total Reservas</h3>
                    <div class="stat-number reservas"><?php echo $total_reservas; ?></div>
                    <p>Reservas realizadas</p>
                </div>
                <div class="stat-card">
                    <h3>Próxima Reserva</h3>
                    <div style="font-size: 1.3em; color: #27ae60; font-weight: bold; margin: 10px 0;">
                        <?php echo $proxima_reserva; ?>
                    </div>
                    <p>Tu próxima aventura</p>
                </div>
                <div class="stat-card">
                    <h3>Reservas Activas</h3>
                    <div class="stat-number hoy"><?php echo $reservas_activas; ?></div>
                    <p>En proceso</p>
                </div>
            <?php else: ?>
                <?php
                // Estadísticas para empleados
                $sql_total_reservas = "SELECT COUNT(*) as total FROM reservas";
                $sql_reservas_hoy = "SELECT COUNT(*) as hoy FROM reservas WHERE DATE(fecha_creacion) = CURDATE()";
                $sql_reservas_pendientes = "SELECT COUNT(*) as pendientes FROM reservas WHERE estado_reserva = 'pendiente'";
                $sql_ingresos = "SELECT SUM(precio_total) as ingresos FROM reservas WHERE estado_reserva IN ('confirmada', 'completada')";
                
                $total_reservas = $conexion->query($sql_total_reservas) ? $conexion->query($sql_total_reservas)->fetch_assoc()['total'] : 0;
                $reservas_hoy = $conexion->query($sql_reservas_hoy) ? $conexion->query($sql_reservas_hoy)->fetch_assoc()['hoy'] : 0;
                $reservas_pendientes = $conexion->query($sql_reservas_pendientes) ? $conexion->query($sql_reservas_pendientes)->fetch_assoc()['pendientes'] : 0;
                $ingresos_totales = $conexion->query($sql_ingresos) ? ($conexion->query($sql_ingresos)->fetch_assoc()['ingresos'] ?? 0) : 0;
                ?>
                <div class="stat-card">
                    <h3>Total Reservas</h3>
                    <div class="stat-number reservas"><?php echo $total_reservas; ?></div>
                    <p>En el sistema</p>
                </div>
                <div class="stat-card">
                    <h3>Reservas Hoy</h3>
                    <div class="stat-number hoy"><?php echo $reservas_hoy; ?></div>
                    <p>Nuevas hoy</p>
                </div>
                <div class="stat-card">
                    <h3>Pendientes</h3>
                    <div class="stat-number" style="color: #e74c3c;"><?php echo $reservas_pendientes; ?></div>
                    <p>Por confirmar</p>
                </div>
                <div class="stat-card">
                    <h3>Ingresos</h3>
                    <div class="stat-number ingresos">€<?php echo number_format($ingresos_totales, 0); ?></div>
                    <p>Totales</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información adicional -->
        <div class="form-container">
            <h3 style="text-align: center; margin-bottom: 25px; color: #2c3e50;">💡 ¿Cómo funciona el sistema?</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px;">
                <?php if ($usuario_rol == 'cliente'): ?>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5em; margin-bottom: 15px;">1️⃣</div>
                        <h4 style="color: #3498db; margin-bottom: 10px;">Explora Vehículos</h4>
                        <p style="color: #7f8c8d;">Descubre nuestra amplia flota y encuentra el vehículo perfecto para tu viaje.</p>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5em; margin-bottom: 15px;">2️⃣</div>
                        <h4 style="color: #3498db; margin-bottom: 10px;">Realiza tu Reserva</h4>
                        <p style="color: #7f8c8d;">Selecciona fechas, vehículo y completa tus datos de forma sencilla.</p>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5em; margin-bottom: 15px;">3️⃣</div>
                        <h4 style="color: #3498db; margin-bottom: 10px;">Gestiona Reservas</h4>
                        <p style="color: #7f8c8d;">Consulta el estado de tus reservas y recibe confirmación al instante.</p>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5em; margin-bottom: 15px;">📊</div>
                        <h4 style="color: #3498db; margin-bottom: 10px;">Gestión Completa</h4>
                        <p style="color: #7f8c8d;">Administra reservas, vehículos y clientes desde un solo panel.</p>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5em; margin-bottom: 15px;">⚡</div>
                        <h4 style="color: #3498db; margin-bottom: 10px;">Confirmación Rápida</h4>
                        <p style="color: #7f8c8d;">Gestiona las reservas pendientes y confírmalas fácilmente.</p>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5em; margin-bottom: 15px;">📈</div>
                        <h4 style="color: #3498db; margin-bottom: 10px;">Reportes Detallados</h4>
                        <p style="color: #7f8c8d;">Accede a estadísticas e informes del negocio en tiempo real.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2024 Autos Costa Sol - Sistema de Gestión de Reservas</p>
            <p style="margin-top: 10px; font-size: 0.9em;">
                Desarrollado con ❤️ para tu experiencia perfecta en la carretera
            </p>
        </footer>
    </div>
</body>
</html>