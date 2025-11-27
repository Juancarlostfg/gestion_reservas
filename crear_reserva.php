<?php
session_start();
include 'includes/conexion.php';

// SOLO se exige estar logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$es_empleado = ($_SESSION['usuario_rol'] === 'empleado');
$es_cliente  = ($_SESSION['usuario_rol'] === 'cliente');

// Preselección desde vehículos
$tipo_preseleccionado = $_GET['tipo'] ?? '';
$modelo_preseleccionado = $_GET['modelo'] ?? '';

$mensaje = "";

// 🔹 Si es empleado, cargamos la lista de clientes para el desplegable
$lista_clientes = [];
if ($es_empleado) {
    $sql_clientes = "SELECT id, nombre, email FROM usuarios WHERE rol='cliente' ORDER BY nombre ASC";
    $res = $conexion->query($sql_clientes);
    if ($res) $lista_clientes = $res->fetch_all(MYSQLI_ASSOC);
}

if ($_POST) {

    $modelo_vehiculo = $_POST['modelo_vehiculo'];
    $tipo_vehiculo   = $_POST['tipo_vehiculo'];
    $fecha_inicio    = $_POST['fecha_inicio'];
    $fecha_fin       = $_POST['fecha_fin'];
    $ubicacion       = $_POST['ubicacion'];
    $observaciones   = $_POST['observaciones'] ?? '';

    // 🔹 Cliente: usa su propio ID
    // 🔹 Empleado: elige un cliente desde el formulario
    $usuario_id = $es_empleado ? intval($_POST['cliente_id']) : $_SESSION['usuario_id'];

    // Calcular precio
    $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);

    $precios = [
        'economico' => 30, 
        'compacto'  => 40, 
        'sedan'     => 50, 
        'suv'       => 70, 
        'lujo'      => 100,
        'deportivo' => 120
    ];

    $precio_total = $dias * $precios[$tipo_vehiculo];

    // 🔹 Insert REAL a la tabla (usando los nombres correctos de tu BD)
    $sql = "INSERT INTO reservas (
                modelo_vehiculo,
                tipo_vehiculo,
                fecha_inicio,
                fecha_fin,
                ubicacion,
                observaciones,
                usuario_id,
                precio_total
            ) VALUES (
                '$modelo_vehiculo',
                '$tipo_vehiculo',
                '$fecha_inicio',
                '$fecha_fin',
                '$ubicacion',
                '$observaciones',
                $usuario_id,
                $precio_total
            )";

    if ($conexion->query($sql)) {
        $mensaje = "<div style='background:#d4edda;color:#155724;padding:15px;border-radius:6px;margin-bottom:20px;'>
                        ✅ <strong>¡Reserva creada exitosamente!</strong><br>
                        Precio total: <strong>€$precio_total</strong> para $dias días.
                    </div>";
    } else {
        $mensaje = "<div style='background:#f8d7da;color:#721c24;padding:15px;border-radius:6px;margin-bottom:20px;'>
                        ❌ Error: " . $conexion->error . "
                    </div>";
    }
}

$fecha_hoy = date('Y-m-d');
$fecha_manana = date('Y-m-d', strtotime('+1 day'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reserva - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container fade-in">

        <header class="header">
            <nav class="navbar">
                <div class="logo">
                    <span class="logo-icon">🚗</span>
                    <span>Autos Costa Sol</span>
                </div>
                <div class="nav-links">
                    <a href="dashboard.php" class="nav-link">📊 Inicio</a>
                    <a href="vehiculos.php" class="nav-link">🔍 Ver Vehículos</a>
                    <a href="logout.php" class="btn-logout">🚪 Salir</a>
                </div>
            </nav>
        </header>

        <!-- HERO -->
  <section style="
    background-image:url('img/flota.jpg');
    background-size:cover;
    background-position:center;
    height:260px;
    border-radius:14px;
    position:relative;
    overflow:hidden;
">
    <div style="
        position:absolute;
        inset:0;
        background:linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.45));
    "></div>

    <div style="
        position:relative;
        z-index:2;
        text-align:center;
        top:50%;
        transform:translateY(-50%);
        color:white;
        text-shadow:0 4px 12px rgba(0,0,0,0.7);
    ">
        <h1>Nueva Reserva 🚗</h1>
        <p>Completa los datos para reservar un vehículo</p>
    </div>
</section>


        <?php echo $mensaje; ?>

        <div class="form-container">
            <form method="post">

                <!-- 🔹 SOLO PARA EMPLEADOS: selector de cliente -->
                <?php if ($es_empleado): ?>
                    <div class="form-group">
                        <label for="cliente_id">👥 Cliente:</label>
                        <select name="cliente_id" id="cliente_id" class="form-control" required>
                            <option value="">Seleccione un cliente...</option>
                            <?php foreach($lista_clientes as $c): ?>
                                <option value="<?php echo $c['id']; ?>">
                                    <?php echo $c['nombre'] . " (" . $c['email'] . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="modelo_vehiculo">🚗 Modelo del Vehículo:</label>
                    <input type="text" name="modelo_vehiculo" required
                           value="<?php echo $modelo_preseleccionado; ?>"
                           placeholder="Ej: Toyota Corolla, Audi TT..."
                           class="form-control">
                </div>

                <div class="form-group">
                    <label for="tipo_vehiculo">🏷️ Tipo de Vehículo:</label>
                    <select name="tipo_vehiculo" id="tipo_vehiculo" class="form-control" onchange="calcularPrecio()">
                        <option value="economico">Económico (€30/día)</option>
                        <option value="compacto">Compacto (€40/día)</option>
                        <option value="sedan" selected>Sedán (€50/día)</option>
                        <option value="suv">SUV (€70/día)</option>
                        <option value="lujo">Lujo (€100/día)</option>
                        <option value="deportivo">Deportivo (€120/día)</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_inicio">📅 Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" min="<?php echo $fecha_hoy; ?>"
                               value="<?php echo $fecha_hoy; ?>" class="form-control"
                               onchange="calcularPrecio()" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_fin">📅 Fecha Fin:</label>
                        <input type="date" name="fecha_fin" min="<?php echo $fecha_manana; ?>"
                               value="<?php echo $fecha_manana; ?>" class="form-control"
                               onchange="calcularPrecio()" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ubicacion">📍 Ubicación:</label>
                    <select name="ubicacion" class="form-control">
                        <option value="malaga">Málaga Aeropuerto</option>
                        <option value="madrid">Madrid Centro</option>
                        <option value="barcelona">Barcelona Sants</option>
                        <option value="valencia">Valencia Aeropuerto</option>
                        <option value="sevilla">Sevilla Centro</option>
                        <option value="bilbao">Bilbao Centro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>📝 Observaciones:</label>
                    <textarea name="observaciones" rows="4" class="form-control"></textarea>
                </div>

                <div class="precio-box">
                    <h3>💰 Precio Estimado</h3>
                    <div id="precioTexto">€50</div>
                    <small id="detallePrecio">1 día x €50/día (Sedán)</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">
                    🚗 Confirmar Reserva
                </button>
            </form>
        </div>

        <footer class="footer">
            <p>© 2024 Autos Costa Sol - Tu viaje perfecto comienza aquí</p>
        </footer>
    </div>

<script>
function calcularPrecio() {
    const inicio = new Date(document.querySelector('[name="fecha_inicio"]').value);
    const fin    = new Date(document.querySelector('[name="fecha_fin"]').value);
    const tipo   = document.getElementById('tipo_vehiculo').value;

    const precios = { economico:30, compacto:40, sedan:50, suv:70, lujo:100, deportivo:120 };
    const nombres = { economico:'Económico', compacto:'Compacto', sedan:'Sedán', suv:'SUV', lujo:'Lujo', deportivo:'Deportivo' };

    if (inicio && fin && fin > inicio) {
        const dias = Math.ceil((fin - inicio) / (1000*60*60*24));
        const total = dias * precios[tipo];

        document.getElementById("precioTexto").textContent = `€${total}`;
        document.getElementById("detallePrecio").textContent = 
            `${dias} día${dias>1?'s':''} x €${precios[tipo]}/día (${nombres[tipo]})`;
    }
}
document.addEventListener('DOMContentLoaded', calcularPrecio);
</script>

</body>
</html>
