<?php
session_start();
include 'includes/conexion.php';

// ⚠️ CONFIG CORREO
include 'includes/mail_config.php'; // aquí tienes SMTP_HOST, SMTP_USER, etc.

require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Función para enviar correo de confirmación
function enviarCorreoConfirmacion($emailDestino, $nombreDestino, $datosReserva) {
    $mail = new PHPMailer(true);

    try {
        // Config SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;

        // Remitente y destinatario
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($emailDestino, $nombreDestino);

        // Contenido del mensaje
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de reserva - Autos Costa Sol';

        $body  = "<h2>Hola {$nombreDestino},</h2>";
        $body .= "<p>Tu reserva se ha registrado correctamente con los siguientes datos:</p>";
        $body .= "<ul>";
        $body .= "<li><strong>Vehículo:</strong> {$datosReserva['modelo']}</li>";
        $body .= "<li><strong>Tipo:</strong> {$datosReserva['tipo']}</li>";
        $body .= "<li><strong>Fechas:</strong> {$datosReserva['inicio']} al {$datosReserva['fin']}</li>";
        $body .= "<li><strong>Ubicación:</strong> {$datosReserva['ubicacion']}</li>";
        $body .= "<li><strong>Precio total:</strong> {$datosReserva['precio']} €</li>";
        $body .= "</ul>";
        $body .= "<p>Gracias por confiar en Autos Costa Sol.</p>";

        $mail->Body    = $body;
        $mail->AltBody = "Reserva confirmada: {$datosReserva['modelo']} del {$datosReserva['inicio']} al {$datosReserva['fin']}. Precio total: {$datosReserva['precio']} €.";

        $mail->send();
    } catch (Exception $e) {
        // Si falla el correo, no rompemos la web
        // error_log('Error al enviar email: ' . $mail->ErrorInfo);
    }
}

// Solo clientes
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

// Preseleccionar tipo de vehículo si viene de la página de vehículos
$tipo_preseleccionado = '';
$modelo_preseleccionado = '';
if (isset($_GET['tipo'])) {
    $tipo_preseleccionado = $_GET['tipo'];
}
if (isset($_GET['modelo'])) {
    $modelo_preseleccionado = $_GET['modelo'];
}

$mensaje = "";

if ($_POST) {
    $modelo_vehiculo = $_POST['modelo_vehiculo'];
    $tipo_vehiculo   = $_POST['tipo_vehiculo'];
    $fecha_inicio    = $_POST['fecha_inicio'];
    $fecha_fin       = $_POST['fecha_fin'];
    $ubicacion       = $_POST['ubicacion'];
    $observaciones   = $_POST['observaciones'];
    $usuario_id      = $_SESSION['usuario_id'];
    
    // Calcular días
    $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
    if ($dias < 1) {
        $dias = 1;
    }

    // Tarifas por tipo
    $precios = [
        'economico' => 30, 
        'compacto'  => 40, 
        'sedan'     => 50, 
        'suv'       => 70, 
        'lujo'      => 100,
        'deportivo' => 120
    ];
    $precio_dia   = isset($precios[$tipo_vehiculo]) ? $precios[$tipo_vehiculo] : 50;
    $precio_total = $dias * $precio_dia;
    
    // Insert en la base de datos
    $modelo_vehiculo = $conexion->real_escape_string($modelo_vehiculo);
    $tipo_vehiculo   = $conexion->real_escape_string($tipo_vehiculo);
    $ubicacion       = $conexion->real_escape_string($ubicacion);
    $observaciones   = $conexion->real_escape_string($observaciones);

    $sql = "INSERT INTO reservas 
                (modelo_vehiculo, tipo_vehiculo, fecha_inicio, fecha_fin, ubicacion, observaciones, usuario_id, precio_total) 
            VALUES 
                ('$modelo_vehiculo', '$tipo_vehiculo', '$fecha_inicio', '$fecha_fin', '$ubicacion', '$observaciones', $usuario_id, $precio_total)";
    
    if ($conexion->query($sql) === TRUE) {

        // Obtener email y nombre del usuario que ha hecho la reserva
        $resUsuario = $conexion->query("SELECT email, nombre FROM usuarios WHERE id = $usuario_id");
        $rowUsuario = $resUsuario ? $resUsuario->fetch_assoc() : null;

        if ($rowUsuario) {
            $emailDestino  = $rowUsuario['email'];
            $nombreDestino = $rowUsuario['nombre'];

            // Datos para el correo
            $datosReserva = [
                'modelo'    => $modelo_vehiculo,
                'tipo'      => $tipo_vehiculo,
                'inicio'    => $fecha_inicio,
                'fin'       => $fecha_fin,
                'ubicacion' => $ubicacion,
                'precio'    => $precio_total
            ];

            // Enviar correo (si falla, la reserva sigue creada)
            enviarCorreoConfirmacion($emailDestino, $nombreDestino, $datosReserva);
        }

        $mensaje = "<div style='color: green; padding: 15px; background: #d4edda; border-radius: 5px; border: 1px solid #c3e6cb; margin-bottom: 20px;'>
                        ✅ <strong>¡Reserva creada exitosamente!</strong><br>
                        Precio total: <strong>€$precio_total</strong> para $dias días.
                    </div>";
    } else {
        $mensaje = "<div style='color: #721c24; padding: 15px; background: #f8d7da; border-radius: 5px; border: 1px solid #f5c6cb; margin-bottom: 20px;'>
                        ❌ <strong>Error:</strong> " . $conexion->error . "
                    </div>";
    }
}

$fecha_hoy    = date('Y-m-d');
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
        <!-- Header -->
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

        <!-- Hero -->
        <section class="hero" 
            style="
                background-image: url('img/flota.jpg');
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
                    background: rgba(0,0,0,0.35);
                    backdrop-filter: blur(4px);
                ">
                    <h1 style="margin:0; font-size:28px; color:#ffffff; text-shadow:0 6px 18px rgba(0,0,0,0.9);">
                        Nueva Reserva 🚗
                    </h1>
                    <p style="margin:6px 0 0; color:#f9fafb; text-shadow:0 4px 10px rgba(0,0,0,0.8);">
                        Completa los datos para reservar tu vehículo ideal
                    </p>
                </div>
            </div>
        </section>

        <?php echo $mensaje; ?>

        <div class="form-container">
            <form method="post" id="formReserva">
                <div class="form-group">
                    <label for="modelo_vehiculo">🚗 Modelo del Vehículo:</label>
                    <input type="text" id="modelo_vehiculo" name="modelo_vehiculo" 
                           placeholder="Ej: Toyota Corolla, BMW X3, Audi TT..." 
                           required
                           value="<?php echo isset($_POST['modelo_vehiculo']) ? htmlspecialchars($_POST['modelo_vehiculo']) : htmlspecialchars($modelo_preseleccionado); ?>"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label for="tipo_vehiculo">🏷️ Tipo de Vehículo:</label>
                    <select id="tipo_vehiculo" name="tipo_vehiculo" required onchange="calcularPrecio()" class="form-control">
                        <option value="economico" <?php echo $tipo_preseleccionado == 'economico' ? 'selected' : ''; ?>>Económico</option>
                        <option value="compacto" <?php echo $tipo_preseleccionado == 'compacto' ? 'selected' : ''; ?>>Compacto</option>
                        <option value="sedan" <?php echo ($tipo_preseleccionado == 'sedan' || empty($tipo_preseleccionado)) ? 'selected' : ''; ?>>Sedán</option>
                        <option value="suv" <?php echo $tipo_preseleccionado == 'suv' ? 'selected' : ''; ?>>SUV</option>
                        <option value="lujo" <?php echo $tipo_preseleccionado == 'lujo' ? 'selected' : ''; ?>>Lujo</option>
                        <option value="deportivo" <?php echo $tipo_preseleccionado == 'deportivo' ? 'selected' : ''; ?>>Deportivo</option>
                    </select>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px; text-align: center;">
                        <small style="color: #666;">💳 €30/día</small>
                        <small style="color: #666;">💳 €40/día</small>
                        <small style="color: #666;">💳 €50/día</small>
                        <small style="color: #666;">💳 €70/día</small>
                        <small style="color: #666;">💳 €100/día</small>
                        <small style="color: #666;">💳 €120/día</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div class="form-group">
                        <label for="fecha_inicio">📅 Fecha de Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" 
                               required 
                               min="<?php echo $fecha_hoy; ?>"
                               value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : $fecha_hoy; ?>"
                               onchange="calcularPrecio()"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">📅 Fecha de Fin:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" 
                               required
                               min="<?php echo $fecha_manana; ?>"
                               value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : $fecha_manana; ?>"
                               onchange="calcularPrecio()"
                               class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ubicacion">📍 Ubicación de Recogida:</label>
                    <select id="ubicacion" name="ubicacion" required class="form-control">
                        <option value="malaga">Málaga Aeropuerto</option>
                        <option value="madrid">Madrid Centro</option>
                        <option value="barcelona">Barcelona Sants</option>
                        <option value="valencia">Valencia Aeropuerto</option>
                        <option value="sevilla">Sevilla Centro</option>
                        <option value="bilbao">Bilbao Centro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="observaciones">📝 Observaciones (opcional):</label>
                    <textarea id="observaciones" name="observaciones" 
                              placeholder="Necesito silla infantil, GPS, conductor adicional, etc..."
                              rows="4" class="form-control"><?php echo isset($_POST['observaciones']) ? htmlspecialchars($_POST['observaciones']) : ''; ?></textarea>
                </div>

                <div style="background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 25px 0; text-align: center;">
                    <h3 style="color: #3498db; margin-bottom: 10px;">💰 Precio Estimado</h3>
                    <div style="font-size: 2em; font-weight: bold; color: #27ae60;" id="precioTexto">€50</div>
                    <div style="color: #7f8c8d; font-size: 0.9em;" id="detallePrecio">1 día x €50/día (Sedán)</div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1em;">
                    🚗 Confirmar Reserva
                </button>
            </form>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2024 Autos Costa Sol - Tu viaje perfecto comienza aquí</p>
        </footer>
    </div>

    <script>
        function calcularPrecio() {
            const fechaInicio = new Date(document.getElementById('fecha_inicio').value);
            const fechaFin    = new Date(document.getElementById('fecha_fin').value);
            const tipoVehiculo = document.getElementById('tipo_vehiculo').value;
            
            const precios = {
                'economico': 30,
                'compacto': 40,
                'sedan': 50,
                'suv': 70,
                'lujo': 100,
                'deportivo': 120
            };
            
            const nombres = {
                'economico': 'Económico',
                'compacto': 'Compacto',
                'sedan': 'Sedán',
                'suv': 'SUV',
                'lujo': 'Lujo',
                'deportivo': 'Deportivo'
            };
            
            if (fechaInicio && fechaFin && fechaFin > fechaInicio) {
                const dias = Math.ceil((fechaFin - fechaInicio) / (1000 * 60 * 60 * 24));
                const precioTotal = dias * precios[tipoVehiculo];
                
                document.getElementById('precioTexto').textContent = `€${precioTotal}`;
                document.getElementById('detallePrecio').textContent = 
                    `${dias} día${dias > 1 ? 's' : ''} x €${precios[tipoVehiculo]}/día (${nombres[tipoVehiculo]})`;
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            calcularPrecio();
        });
    </script>
</body>
</html>
