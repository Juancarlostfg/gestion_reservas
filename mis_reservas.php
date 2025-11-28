<?php
session_start();
include 'includes/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_rol = $_SESSION['usuario_rol'];

// Clientes ven solo sus reservas, empleados ven todas
if ($usuario_rol == 'cliente') {
    $sql = "SELECT * FROM reservas WHERE usuario_id = $usuario_id ORDER BY fecha_creacion DESC";
} else {
    $sql = "SELECT r.*, u.nombre as cliente_nombre FROM reservas r JOIN usuarios u ON r.usuario_id = u.id ORDER BY r.fecha_creacion DESC";
}

$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .reserva-card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(214, 212, 212, 0.66);
            border-left: 4px solid #007bff;
        }
        .estado-pendiente { border-left-color: #ffc107; }
        .estado-confirmada { border-left-color: #28a745; }
        .estado-completada { border-left-color: #6c757d; }
        .estado-cancelada { border-left-color: #dc3545; }

        .btn { 
            padding: 8px 15px; 
            border-radius: 5px; 
            text-decoration: none; 
            margin-right: 10px; 
            display: inline-block;
            font-size: 14px;
        }
        .btn-confirmar { background: #28a745; color: white; }
        .btn-cancelar { background: #dc3545; color: white; }

        .header-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- HERO con reservas.jpg -->
        <section class="hero" 
            style="
                background-image: url('img/reservas.jpg');
                background-size: cover;
                background-position: center;
                border-radius: 14px;
                height: 260px;
                position: relative;
                overflow: hidden;
            "
        >
            <!-- Capa oscura -->
            <div style="
    position: absolute;
    inset: 0;
    background: linear-gradient(
        rgba(0,0,0,0.15),
        rgba(0,0,0,0.35)
    );
"></div>


            <!-- Contenido centrado con cajita -->
            <div style="
                position: relative;
                z-index: 2;
                text-align: center;
                top: 50%;
                transform: translateY(-50%);
                ">
            "<div style="
    display: inline-block;
    padding: 12px 24px;
    border-radius: 16px;
    background: rgba(236, 236, 236, 0.45);
    backdrop-filter: blur(4px);
">
    <h1 style="margin:0; font-size:28px; color:#ffffff; text-shadow:0 4px 12px rgba(0,0,0,0.9);">
        Mis Reservas 
    </h1>
    <p style="margin:6px 0 0; color:#e5e7eb; text-shadow:0 3px 8px rgba(0,0,0,0.8);">
        Consulta el estado de todas tus reservas
    </p>
</div>

            </div>
        </section>

        <!-- Botones de navegación -->
        <div class="header-actions">
            <a href="dashboard.php" 
               style="background: #6c757d; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
                ← Dashboard
            </a>

            <?php if ($usuario_rol == 'cliente'): ?>
                <a href="crear_reserva.php" 
                   style="background: #007bff; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
                    🚗 Nueva Reserva
                </a>
            <?php else: ?>
                <a href="gestion_reservas.php" 
                   style="background: #007bff; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
                    ⚙️ Gestión Avanzada
                </a>
            <?php endif; ?>
        </div>

        <!-- Listado de reservas -->
        <?php if ($result->num_rows > 0): ?>
            <div style="margin-top: 10px;">
                <?php while($reserva = $result->fetch_assoc()): ?>
                    <div class="reserva-card estado-<?php echo $reserva['estado_reserva']; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 10px 0; color: #333;">🚗 <?php echo $reserva['modelo_vehiculo']; ?></h3>
                                <p><strong>📅 Fechas:</strong> <?php echo $reserva['fecha_inicio']; ?> al <?php echo $reserva['fecha_fin']; ?></p>
                                <p><strong>Tipo:</strong> 
                                    <?php 
                                    $tipos = [
                                        'economico' => 'Económico',
                                        'compacto' => 'Compacto', 
                                        'sedan' => 'Sedán',
                                        'suv' => 'SUV',
                                        'lujo' => 'Lujo',
                                        'deportivo' => 'Deportivo'
                                    ];
                                    echo $tipos[$reserva['tipo_vehiculo']] ?? ucfirst($reserva['tipo_vehiculo']);
                                    ?>
                                </p>
                                <p><strong>📍 Ubicación:</strong> <?php echo ucfirst($reserva['ubicacion']); ?></p>
                                <p><strong>💰 Precio Total:</strong> 
                                    <span style="color: #28a745; font-weight: bold;">€<?php echo $reserva['precio_total']; ?></span>
                                </p>
                                <p><strong>📊 Estado:</strong> 
                                    <span style="padding: 5px 12px; border-radius: 15px; font-weight: bold; color: #000; background: 
                                        <?php 
                                        switch($reserva['estado_reserva']) {
                                            case 'pendiente': echo '#fff3cd'; break;
                                            case 'confirmada': echo '#d4edda'; break;
                                            case 'en_curso': echo '#cce7ff'; break;
                                            case 'completada': echo '#e2e3e5'; break;
                                            case 'cancelada': echo '#f8d7da'; break;
                                            default: echo '#fff3cd';
                                        }
                                        ?>">
                                        <?php echo ucfirst($reserva['estado_reserva']); ?>
                                    </span>
                                </p>

                                <?php if ($usuario_rol == 'empleado' && isset($reserva['cliente_nombre'])): ?>
                                    <p><strong>👤 Cliente:</strong> <?php echo $reserva['cliente_nombre']; ?></p>
                                <?php endif; ?>

                                <p><strong>🕒 Creado:</strong> <?php echo $reserva['fecha_creacion']; ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($reserva['observaciones'])): ?>
                            <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                <strong>📝 Observaciones:</strong> <?php echo $reserva['observaciones']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($usuario_rol == 'empleado' && $reserva['estado_reserva'] == 'pendiente'): ?>
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                                <strong>Acciones:</strong>
                                <a href="gestion_reservas.php?accion=confirmar&id=<?php echo $reserva['id']; ?>" class="btn btn-confirmar">
                                    ✅ Confirmar Reserva
                                </a>
                                <a href="gestion_reservas.php?accion=cancelar&id=<?php echo $reserva['id']; ?>" class="btn btn-cancelar">
                                    ❌ Cancelar Reserva
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 10px; margin-top: 20px;">
                <h3 style="color: #6c757d; margin-bottom: 15px;">📭 No hay reservas</h3>
                <p style="color: #6c757d; margin-bottom: 25px;">
                    <?php echo $usuario_rol == 'cliente' ? '¡Aún no has hecho ninguna reserva!' : 'No hay reservas en el sistema'; ?>
                </p>
                <?php if ($usuario_rol == 'cliente'): ?>
                    <a href="crear_reserva.php" style="background: #007bff; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-size: 16px;">
                        🚗 Hacer mi primera reserva
                    </a>
                <?php else: ?>
                    <p style="color: #6c757d;">Las reservas de los clientes aparecerán aquí.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
