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
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard - Autos Costa Sol</title>

  <!-- Fuente moderna -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --bg:#eef2ff;
      --card:#fff;
      --muted:#6b7280;
      --accent:#6c5ce7;
      --accent-2:#7c3aed;
      --success:#16a34a;
      --danger:#ef4444;
      --border:#e6e9f2;
    }
    *{box-sizing:border-box}
    body{
      font-family:'Montserrat',sans-serif;
      margin:0;
      background: linear-gradient(180deg,var(--bg), #f3e8ff 100%);
      color:#111827;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }
    .shell{max-width:1200px; margin:18px auto; padding:18px;}
    /* NAV */
    .header{
      background: #fff;
      border-radius:12px;
      padding:12px 18px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      box-shadow:0 8px 24px rgba(16,24,40,0.06);
      margin-bottom:18px;
    }
    .logo{display:flex; align-items:center; gap:12px; font-weight:700; font-size:20px; color:var(--accent-2);}
    .logo img{width:44px;height:44px;border-radius:10px; object-fit:cover}
    .nav-right{display:flex;align-items:center;gap:12px}
    .badge{display:inline-block;padding:6px 10px;border-radius:999px;font-weight:600;font-size:13px}
    .btn-logout{background:linear-gradient(90deg,#ff7a7a,#ff6b6b); color:#fff; padding:8px 12px; border-radius:10px; text-decoration:none; font-weight:600}
    .nav-link{color:#4338ca;text-decoration:none;font-weight:600}

    /* HERO */
    .hero{
      background-image: url('img/costa.jpg'); /* o sol.jpg, según tengas */
      background-size: cover;
      background-position: center;
      height:300px;
      border-radius:14px;
      position:relative;
      overflow:hidden;
      box-shadow:0 10px 30px rgba(16,24,40,0.08);
      margin-bottom:20px;
    }
    .hero::before{content:""; position:absolute; inset:0; background: linear-gradient(180deg, rgba(12,12,30,0.25), rgba(12,12,30,0.45));}
    .hero-content{position:relative; z-index:2; color:#fff; text-align:center; top:50%; transform:translateY(-50%);}
    .hero-card{
      display:inline-block;
      background: rgba(0,0,0,0.35);
      padding:18px 26px;
      border-radius:12px;
      backdrop-filter: blur(6px);
      text-shadow: 0 6px 20px rgba(0,0,0,0.6);
    }
    .hero-card h1{margin:0; font-size:34px; font-weight:700; letter-spacing:0.2px}
    .hero-card p{margin:8px 0 0; font-weight:500; opacity:0.95}

    /* GRID DE TARJETAS */
    .grid{display:grid; grid-template-columns: repeat(3,1fr); gap:18px; margin-top:18px}
    .card{background:var(--card); border-radius:12px; padding:20px; border:1px solid var(--border); box-shadow:0 8px 18px rgba(12,18,30,0.04); display:flex; flex-direction:column; gap:12px; min-height:120px}
    .card h3{margin:0; font-size:18px; color:#1f2937}
    .card p{margin:0; color:var(--muted)}
    .card a{margin-top:auto; display:inline-block; padding:8px 12px; border-radius:8px; text-decoration:none; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; font-weight:700}

    /* responsive */
    @media (max-width:960px){
      .grid{grid-template-columns:repeat(2,1fr)}
    }
    @media (max-width:640px){
      .grid{grid-template-columns:1fr}
      .hero{height:220px}
      .logo span{display:none}
    }

    a:focus, button:focus{outline:3px solid rgba(99,102,241,0.18); outline-offset:3px}
  </style>
</head>
<body class="dashboard">
  <div class="shell">

    <!-- NAV -->
    <header class="header">
      <div class="logo">
        <img src="img/sol.jpg" alt="logo">
        Autos Costa Sol
      </div>

      <div class="nav-links">
    <span>Bienvenido, <strong><?php echo $usuario_nombre; ?></strong></span>

    <span class="badge" style="background: #3498db; color: white;">
        <?php echo $usuario_rol == 'cliente' ? '👤 Cliente' : '👨‍💼 Empleado'; ?>
    </span>

    <a href="perfil.php" class="nav-link">👤 Mi Perfil</a>

    <?php if ($usuario_rol === 'empleado'): ?>
        <a href="generar_informe.php" class="nav-link">📄 Informe PDF</a>
    <?php endif; ?>

    <a href="logout.php" class="btn-logout">🚪 Cerrar Sesión</a>
</div>

    </header>

    <!-- HERO -->
    <section class="hero" role="img" aria-label="Playa Costa Sol">
      <div class="hero-content">
        <div class="hero-card">
          <h1>¡Bienvenido a Autos Costa Sol! 🌅</h1>
          <p>
            <?php if ($usuario_rol === 'cliente'): ?>
              Reservar el vehículo para tus vacaciones nunca fue más fácil
            <?php else: ?>
              Panel de gestión de reservas, clientes y vehículos
            <?php endif; ?>
          </p>
        </div>
      </div>
    </section>

    <!-- GRID con accesos rápidos -->
    <div class="grid">
      <?php if ($usuario_rol === 'cliente'): ?>
        <!-- Vista para CLIENTE -->
        <div class="card">
          <h3>📅 Mis reservas</h3>
          <p>Consulta y gestiona tus reservas activas y pasadas.</p>
          <a href="mis_reservas.php">Ir a Mis reservas</a>
        </div>

        <div class="card">
          <h3>➕ Crear nueva reserva</h3>
          <p>Elige vehículo, fechas y ubicación para tu próximo viaje.</p>
          <a href="crear_reserva.php">Crear reserva</a>
        </div>

        <div class="card">
          <h3>👤 Mi perfil</h3>
          <p>Modifica tus datos personales y contraseña.</p>
          <a href="perfil.php">Ver mi perfil</a>
        </div>

      <?php else: ?>
        <!-- Vista para EMPLEADO -->
        <div class="card">
          <h3>🗓️ Gestión Reservas</h3>
          <p>Administra todas las reservas del sistema.</p>
          <a href="gestion_reservas.php">Ir a reservas</a>
        </div>

        <div class="card">
          <h3>🚗 Gestión Vehículos</h3>
          <p>Controla la flota y disponibilidad de vehículos.</p>
          <a href="vehiculos.php">Ir a vehículos</a>
        </div>

        <div class="card">
          <h3>👥 Gestión Clientes</h3>
          <p>Consulta y administra datos de clientes.</p>
          <a href="gestion_clientes.php">Ir a clientes</a>
        </div>

       
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
