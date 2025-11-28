<?php
session_start();
include 'includes/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_rol = $_SESSION['usuario_rol'];
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
                        <a href="gestion_reservas.php" class="nav-link">⚙️ Gestión</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-logout">🚪 Salir</a>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
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
    <!-- Capa oscura encima de la foto -->
    <div style="
        position: absolute;
        inset: 0;
        background: linear-gradient(
            rgba(0,0,0,0.35),
            rgba(0,0,0,0.6)
        );
    "></div>

    <!-- Contenido centrado -->
    <div style="
        position: relative;
        z-index: 2;
        text-align: center;
        top: 50%;
        transform: translateY(-50%);
    ">
        <!-- Cajita translúcida para que el texto destaque -->
        <div style="
            display: inline-block;
            padding: 12px 24px;
            border-radius: 16px;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(4px);
        ">
            <h1 style="margin:0; font-size:28px; color:#ffffff; text-shadow:0 4px 12px rgba(0,0,0,0.9);">
                Vehículos 🚗
            </h1>
            <p style="margin:6px 0 0; color:#e5e7eb; text-shadow:0 3px 8px rgba(0,0,0,0.8);">
                Consulta nuestra flota disponible
            </p>
        </div>
    </div>
</section>



        <!-- Filtros -->
        <div class="filter-buttons">
            <button class="filter-btn active" onclick="filterVehicles('all')">Todos</button>
            <button class="filter-btn" onclick="filterVehicles('economico')">Económicos</button>
            <button class="filter-btn" onclick="filterVehicles('compacto')">Compactos</button>
            <button class="filter-btn" onclick="filterVehicles('sedan')">Sedanes</button>
            <button class="filter-btn" onclick="filterVehicles('suv')">SUVs</button>
            <button class="filter-btn" onclick="filterVehicles('lujo')">Lujo</button>
        </div>

        <!-- Grid de Vehículos -->
        <div class="vehiculos-grid">
          <!-- Vehículo Económico - Fiat Panda -->
<div class="vehiculo-card" data-category="economico">
    <div class="vehiculo-image">
        <img src="img/fiat-panda.jpg" 
             alt="Fiat Panda o similar" 
             style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="vehiculo-content">
        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
            <h3>Fiat Panda o similar </h3>
            <span class="badge" style="background: #27ae60; color: white;">Económico</span>
        </div>
        
        <div class="precio">€30/día</div>
        
        <div class="vehicle-feature">
            <span class="feature-icon">👥</span>
            <span>4-5 plazas</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎒</span>
            <span>Maletero: 220L</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">⛽</span>
            <span>Consumo: 4.8L/100km</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎛️</span>
            <span>Aire acondicionado</span>
        </div>
        
        <p style="color: #7f8c8d; font-style: italic; margin: 15px 0;">
            Perfecto para trayectos urbanos y viajes cortos
        </p>
        
        <?php if ($usuario_rol == 'cliente'): ?>
            <button class="btn btn-primary" style="width: 100%;" onclick="reservarVehiculo('economico', 'Fiat Panda')">
                🚗 Reservar Este Vehículo
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Vehículo Compacto - Volkswagen Golf -->
<div class="vehiculo-card" data-category="compacto">
    <div class="vehiculo-image">
        <img src="img/golf.jpg" 
             alt="Volkswagen Golf o similar" 
             style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="vehiculo-content">
        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
            <h3>Volkswagen Golf o similar </h3>
            <span class="badge" style="background: #9b59b6; color: white;">Compacto</span>
        </div>
        
        <div class="precio">€40/día</div>
        
        <div class="vehicle-feature">
            <span class="feature-icon">👥</span>
            <span>5 plazas</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎒</span>
            <span>Maletero: 380L</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">⛽</span>
            <span>Consumo: 5.2L/100km</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">📱</span>
            <span>Bluetooth & Pantalla</span>
        </div>
        
        <p style="color: #7f8c8d; font-style: italic; margin: 15px 0;">
            El equilibrio perfecto entre espacio y eficiencia
        </p>
        
        <?php if ($usuario_rol == 'cliente'): ?>
            <button class="btn btn-primary" style="width: 100%;" onclick="reservarVehiculo('compacto', 'Volkswagen Golf')">
                🚗 Reservar Este Vehículo
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Vehículo Sedán - Toyota Corolla -->
<div class="vehiculo-card" data-category="sedan">
    <div class="vehiculo-image">
        <img src="img/corolla.jpg" 
             alt="Toyota Corolla o similar" 
             style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="vehiculo-content">
        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
            <h3>Toyota Corolla o similar </h3>
            <span class="badge" style="background: #e74c3c; color: white;">Sedán</span>
        </div>
        
        <div class="precio">€50/día</div>
        
        <div class="vehicle-feature">
            <span class="feature-icon">👥</span>
            <span>5 plazas comfort</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎒</span>
            <span>Maletero: 480L</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">⛽</span>
            <span>Consumo: 5.5L/100km</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🛡️</span>
            <span>Asistente conducción</span>
        </div>
        
        <p style="color: #7f8c8d; font-style: italic; margin: 15px 0;">
            Confort superior para viajes largos y negocios
        </p>
        
        <?php if ($usuario_rol == 'cliente'): ?>
            <button class="btn btn-primary" style="width: 100%;" onclick="reservarVehiculo('sedan', 'Toyota Corolla')">
                🚗 Reservar Este Vehículo
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Vehículo SUV - Nissan Qashqai -->
<div class="vehiculo-card" data-category="suv">
    <div class="vehiculo-image">
        <img src="img/qashqai.jpg" 
             alt="Nissan Qashqai o similar" 
             style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="vehiculo-content">
        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
            <h3>Nissan Qashqai o similar </h3>
            <span class="badge" style="background: #f39c12; color: white;">SUV</span>
        </div>
        
        <div class="precio">€70/día</div>
        
        <div class="vehicle-feature">
            <span class="feature-icon">👥</span>
            <span>5-7 plazas</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎒</span>
            <span>Maletero: 550L</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">⛽</span>
            <span>Consumo: 6.2L/100km</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🌐</span>
            <span>4x4 Disponible</span>
        </div>
        
        <p style="color: #7f8c8d; font-style: italic; margin: 15px 0;">
            Espacio amplio para familias y aventuras todoterreno
        </p>
        
        <?php if ($usuario_rol == 'cliente'): ?>
            <button class="btn btn-primary" style="width: 100%;" onclick="reservarVehiculo('suv', 'Nissan Qashqai')">
                🚗 Reservar Este Vehículo
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Vehículo Lujo - BMW Serie 3 -->
<div class="vehiculo-card" data-category="lujo">
    <div class="vehiculo-image">
        <img src="img/bmw-serie3.jpg" 
             alt="BMW Serie 3 o similar" 
             style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="vehiculo-content">
        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
            <h3>BMW Serie 3 o similar </h3>
            <span class="badge" style="background: #34495e; color: white;">Lujo</span>
        </div>
        
        <div class="precio">€100/día</div>
        
        <div class="vehicle-feature">
            <span class="feature-icon">👥</span>
            <span>5 plazas premium</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎒</span>
            <span>Maletero: 480L</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">⛽</span>
            <span>Consumo: 6.8L/100km</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎛️</span>
            <span>Pantalla 10.25"</span>
        </div>
        
        <p style="color: #7f8c8d; font-style: italic; margin: 15px 0;">
            Experiencia premium para eventos especiales
        </p>
        
        <?php if ($usuario_rol == 'cliente'): ?>
            <button class="btn btn-primary" style="width: 100%;" onclick="reservarVehiculo('lujo', 'BMW Serie 3')">
                🚗 Reservar Este Vehículo
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Vehículo Deportivo - Audi TT -->
<div class="vehiculo-card" data-category="lujo">
    <div class="vehiculo-image">
        <img src="img/audi-tt.jpg" 
             alt="Audi TT o similar" 
             style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="vehiculo-content">
        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
            <h3>Audi TT o similar </h3>
            <span class="badge" style="background: #e74c3c; color: white;">Deportivo</span>
        </div>
        
        <div class="precio">€120/día</div>
        
        <div class="vehicle-feature">
            <span class="feature-icon">👥</span>
            <span>2-4 plazas</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🎒</span>
            <span>Maletero: 280L</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">⛽</span>
            <span>Consumo: 7.5L/100km</span>
        </div>
        <div class="vehicle-feature">
            <span class="feature-icon">🌅</span>
            <span>Techo descapotable</span>
        </div>
        
        <p style="color: #7f8c8d; font-style: italic; margin: 15px 0;">
            Para una experiencia de conducción inolvidable
        </p>
        
        <?php if ($usuario_rol == 'cliente'): ?>
            <button class="btn btn-primary" style="width: 100%;" onclick="reservarVehiculo('deportivo', 'Audi TT')">
                🚗 Reservar Este Vehículo
            </button>
        <?php endif; ?>
    </div>
</div>
                    
                    <p style="color: #7f8c8d; font-style: italic; margin: 15px 0;">
                        Para una experiencia de conducción inolvidable
                    </p>
                    
                    <?php if ($usuario_rol == 'cliente'): ?>
                        <button class="btn btn-primary" style="width: 100%;" onclick="reservarVehiculo('deportivo', 'Audi TT')">
                            🚗 Reservar Este Vehículo
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
o similar
        <!-- Información Adicional -->
        <div class="form-container">
            <h3 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">💡 Información Importante</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div>
                    <h4 style="color: #3498db; margin-bottom: 15px;">🛡️ Incluido en Todas las Reservas</h4>
                    <div class="vehicle-feature">
                        <span class="feature-icon">✅</span>
                        <span>Seguro a todo riesgo</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">✅</span>
                        <span>Asistencia en carretera 24h</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">✅</span>
                        <span>Kilometraje ilimitado</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">✅</span>
                        <span>Limpieza final incluida</span>
                    </div>
                </div>
                
                <div>
                    <h4 style="color: #3498db; margin-bottom: 15px;">📋 Requisitos de Alquiler</h4>
                    <div class="vehicle-feature">
                        <span class="feature-icon">📝</span>
                        <span>Carnet de conducir vigente</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">🎂</span>
                        <span>Mínimo 21 años de edad</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">💳</span>
                        <span>Tarjeta de crédito</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">🆔</span>
                        <span>Documento de identidad</span>
                    </div>
                </div>
                
                <div>
                    <h4 style="color: #3498db; margin-bottom: 15px;">📍 Servicios Adicionales</h4>
                    <div class="vehicle-feature">
                        <span class="feature-icon">👶</span>
                        <span>Silla infantil: €5/día</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">🧭</span>
                        <span>GPS: €3/día</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">🚂</span>
                        <span>Conductor adicional: €10</span>
                    </div>
                    <div class="vehicle-feature">
                        <span class="feature-icon">🔄</span>
                        <span>Entrega en otra sede</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2024 Autos Costa Sol - Encuentra tu vehículo ideal</p>
        </footer>
    </div>

    <script>
        function filterVehicles(category) {
            // Actualizar botones activos
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
           
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