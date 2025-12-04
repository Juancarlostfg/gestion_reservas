<?php
session_start();
include 'includes/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_rol = $_SESSION['usuario_rol'];

// Cargar vehículos disponibles desde la BD
$sql = "SELECT * FROM vehiculos WHERE disponible = 1 ORDER BY tipo_vehiculo, marca, modelo";
$result = $conexion->query($sql);
$vehiculos = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #3498db;
            background: transparent;
            color: #3498db;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .filter-btn:hover,
        .filter-btn.active {
            background: #3498db;
            color: white;
            transform: translateY(-2px);
        }
        .vehicle-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .feature-icon {
            width: 20px;
            text-align: center;
        }
        .vehiculo-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 12px 12px 0 0;
        }
        .vehiculo-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .vehiculo-card:hover .vehiculo-image img {
            transform: scale(1.05);
        }
        .vehiculos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }
        .vehiculo-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(15,23,42,0.08);
            padding-bottom: 16px;
        }
        .vehiculo-content {
            padding: 14px 16px;
        }
        .precio {
            font-size: 1.2em;
            font-weight: 700;
            color: #27ae60;
            margin: 8px 0 10px;
        }
    </style>
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
                    <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
                    <?php if ($usuario_rol == 'cliente'): ?>
                        <a href="crear_reserva.php" class="nav-link">🚘 Nueva Reserva</a>
                    <?php else: ?>
                        <a href="gestion_vehiculos.php" class="nav-link">🚘 Gestión Vehículos</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-logout">🚪 Salir</a>
                </div>
            </nav>
        </header>

        <!-- Hero -->
        <section class="hero" 
            style="
                background-image: url('img/manos.jpg');
                background-size: cover;
                background-position: center;
                border-radius: 14px;
                height: 260px;
                position: relative;
                overflow: hidden;
            "
        >
            <div style="
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    rgba(0,0,0,0.25),
                    rgba(0,0,0,0.45)
                );
            "></div>

            <div style="
                position: relative;
                z-index: 2;
                text-align: center;
                top: 50%;
                transform: translateY(-50%);
            ">
                <div style="
                    display: inline-block;
                    padding: 14px 26px;
                    border-radius: 18px;
                    background: rgba(0,0,0,0.32);
                    backdrop-filter: blur(4px);
                ">
                    <h1 style="
                        margin:0;
                        font-size:28px;
                        color:#ffffff;
                        text-shadow:0 6px 18px rgba(0,0,0,0.9);
                    ">
                        Vehículos 🚗
                    </h1>
                    <p style="
                        margin:6px 0 0;
                        color:#f9fafb;
                        text-shadow:0 4px 10px rgba(0,0,0,0.8);
                    ">
                        Consulta nuestra flota disponible
                    </p>
                </div>
            </div>
        </section>

        <!-- Filtros -->
        <div class="filter-buttons">
            <button class="filter-btn active" onclick="filterVehicles('all', this)">Todos</button>
            <button class="filter-btn" onclick="filterVehicles('economico', this)">Económicos</button>
            <button class="filter-btn" onclick="filterVehicles('compacto', this)">Compactos</button>
            <button class="filter-btn" onclick="filterVehicles('sedan', this)">Sedanes</button>
            <button class="filter-btn" onclick="filterVehicles('suv', this)">SUVs</button>
            <button class="filter-btn" onclick="filterVehicles('lujo', this)">Lujo</button>
            <button class="filter-btn" onclick="filterVehicles('deportivo', this)">Deportivos</button>
        </div>

        <!-- Grid de Vehículos -->
        <div class="vehiculos-grid">
            <?php if (count($vehiculos) === 0): ?>
                <p style="grid-column: 1 / -1; text-align:center; padding:30px; background:#f9fafb; border-radius:12px;">
                    No hay vehículos registrados o disponibles en este momento.
                </p>
            <?php else: ?>
                <?php
                // Colores para las etiquetas por tipo
                $coloresTipo = [
                    'economico' => '#27ae60',
                    'compacto'  => '#9b59b6',
                    'sedan'     => '#e74c3c',
                    'suv'       => '#f39c12',
                    'lujo'      => '#34495e',
                    'deportivo' => '#e74c3c'
                ];
                $nombresTipo = [
                    'economico' => 'Económico',
                    'compacto'  => 'Compacto',
                    'sedan'     => 'Sedán',
                    'suv'       => 'SUV',
                    'lujo'      => 'Lujo',
                    'deportivo' => 'Deportivo'
                ];
                ?>
                <?php foreach ($vehiculos as $v): ?>
                    <?php
                    $tipo = $v['tipo_vehiculo'];
                    $badgeColor = $coloresTipo[$tipo] ?? '#3498db';
                    $nombreTipo = $nombresTipo[$tipo] ?? ucfirst($tipo);

                    $imgFile = !empty($v['imagen']) ? $v['imagen'] : 'coche-placeholder.jpg';
                    $imgPath = 'img/' . $imgFile;
                    ?>
                    <div class="vehiculo-card" data-category="<?php echo htmlspecialchars($tipo); ?>">
                        <div class="vehiculo-image">
                            <img src="<?php echo $imgPath; ?>" 
                                 alt="<?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?>">
                        </div>
                        <div class="vehiculo-content">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                <h3 style="margin:0;">
                                    <?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?>
                                </h3>
                                <span class="badge" style="background: <?php echo $badgeColor; ?>; color: white;">
                                    <?php echo $nombreTipo; ?>
                                </span>
                            </div>

                            <div class="precio">€<?php echo number_format($v['precio_dia'], 2, ',', '.'); ?>/día</div>

                            <div class="vehicle-feature">
                                <span class="feature-icon">👥</span>
                                <span><?php echo (int)$v['plazas']; ?> plazas</span>
                            </div>
                            <div class="vehicle-feature">
                                <span class="feature-icon">⛽</span>
                                <span><?php echo ucfirst($v['combustible']); ?></span>
                            </div>
                            <div class="vehicle-feature">
                                <span class="feature-icon">⚙️</span>
                                <span><?php echo ucfirst($v['transmision']); ?></span>
                            </div>

                            <?php if (!empty($v['descripcion'])): ?>
                                <p style="color: #7f8c8d; font-style: italic; margin: 12px 0;">
                                    <?php echo htmlspecialchars($v['descripcion']); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($usuario_rol == 'cliente'): ?>
                                <button class="btn btn-primary" style="width: 100%; margin-top:8px;"
                                    onclick="reservarVehiculo('<?php echo $tipo; ?>', '<?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?>')">
                                    🚗 Reservar Este Vehículo
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Información adicional -->
        <div class="form-container">
            <h3 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">💡 Información Importante</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div>
                    <h4 style="color: #3498db; margin-bottom: 15px;">🛡️ Incluido en Todas las Reservas</h4>
                    <div class="vehicle-feature"><span class="feature-icon">✅</span><span>Seguro a todo riesgo</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">✅</span><span>Asistencia en carretera 24h</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">✅</span><span>Kilometraje ilimitado</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">✅</span><span>Limpieza final incluida</span></div>
                </div>
                <div>
                    <h4 style="color: #3498db; margin-bottom: 15px;">📋 Requisitos de Alquiler</h4>
                    <div class="vehicle-feature"><span class="feature-icon">📝</span><span>Carnet de conducir vigente</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">🎂</span><span>Mínimo 21 años de edad</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">💳</span><span>Tarjeta de crédito</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">🆔</span><span>Documento de identidad</span></div>
                </div>
                <div>
                    <h4 style="color: #3498db; margin-bottom: 15px;">📍 Servicios Adicionales</h4>
                    <div class="vehicle-feature"><span class="feature-icon">👶</span><span>Silla infantil: €5/día</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">🧭</span><span>GPS: €3/día</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">🚗</span><span>Conductor adicional: €10</span></div>
                    <div class="vehicle-feature"><span class="feature-icon">🔄</span><span>Entrega en otra sede</span></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2024 Autos Costa Sol - Encuentra tu vehículo ideal</p>
        </footer>
    </div>

    <script>
        function filterVehicles(category, btn) {
            // actualizar botones activos
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            // filtrar tarjetas
            const vehicles = document.querySelectorAll('.vehiculo-card');
            vehicles.forEach(vehicle => {
                if (category === 'all' || vehicle.dataset.category === category) {
                    vehicle.style.display = 'block';
                } else {
                    vehicle.style.display = 'none';
                }
            });
        }

        function reservarVehiculo(tipo, modelo) {
            if (confirm(`¿Quieres reservar el ${modelo} (${tipo})?`)) {
                window.location.href = `crear_reserva.php?tipo=${tipo}&modelo=${encodeURIComponent(modelo)}`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.vehiculo-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>
