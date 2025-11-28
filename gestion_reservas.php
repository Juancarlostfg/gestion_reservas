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

// No lanzar excepciones automáticas de mysqli (así usamos nuestros mensajes)
mysqli_report(MYSQLI_REPORT_OFF);

$notice = '';
$error  = '';

// PROCESAR ACCIONES (confirmar / cancelar / borrar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['reserva_id'])) {
    $action     = $_POST['action'];
    $reserva_id = (int) $_POST['reserva_id'];

    // CONFIRMAR
    if ($action === 'confirmar') {
        $conexion->begin_transaction();
        try {
            // OJO: aquí usamos estado_reserva (nombre real de la columna)
            $stmt = $conexion->prepare(
                "SELECT estado_reserva 
                 FROM reservas 
                 WHERE id = ? 
                 FOR UPDATE"
            );
            $stmt->bind_param('i', $reserva_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$row) {
                throw new Exception("Reserva no encontrada.");
            }

            if ($row['estado_reserva'] === 'confirmada') {
                throw new Exception("La reserva ya está confirmada.");
            }

            $stmt = $conexion->prepare(
                "UPDATE reservas 
                 SET estado_reserva = 'confirmada' 
                 WHERE id = ?"
            );
            $stmt->bind_param('i', $reserva_id);
            $stmt->execute();
            $stmt->close();

            $conexion->commit();
            $notice = "Reserva #{$reserva_id} confirmada correctamente.";
        } catch (Exception $e) {
            $conexion->rollback();
            $error = "No se pudo confirmar: " . $e->getMessage();
        }
    }

    // CANCELAR
    if ($action === 'cancelar') {
        $stmt = $conexion->prepare(
            "UPDATE reservas 
             SET estado_reserva = 'cancelada' 
             WHERE id = ?"
        );
        $stmt->bind_param('i', $reserva_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $notice = "Reserva #{$reserva_id} cancelada.";
        } else {
            $error = "No se pudo cancelar (quizá ya estaba cancelada o no existe).";
        }

        $stmt->close();
    }

    // BORRAR
    if ($action === 'borrar') {
        $stmt = $conexion->prepare(
            "DELETE FROM reservas 
             WHERE id = ?"
        );
        $stmt->bind_param('i', $reserva_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $notice = "Reserva #{$reserva_id} eliminada.";
        } else {
            $error = "No se pudo eliminar (no encontrada).";
        }

        $stmt->close();
    }
}

// CARGAR LISTA DE RESERVAS
// OJO: nombres de columnas según tu tabla: 
// id, modelo_vehiculo, detalles_reserva, usuario_id, fecha_inicio, fecha_fin, ubicacion, tipo_vehiculo, estado_reserva, fecha_creacion
$sql = "SELECT r.id,
               r.modelo_vehiculo,
               r.detalles_reserva,
               r.usuario_id,
               r.fecha_inicio,
               r.fecha_fin,
               r.ubicacion,
               r.tipo_vehiculo,
               r.estado_reserva,
               r.fecha_creacion,
               u.nombre AS cliente
        FROM reservas r
        LEFT JOIN usuarios u ON r.usuario_id = u.id
        ORDER BY r.fecha_creacion DESC";

$result   = $conexion->query($sql);
$reservas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

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

        <!-- HERO -->
       <!-- HERO (sustituye el bloque hero anterior) -->
<section class="hero-gestion" role="img" aria-label="Fondo flota">
  <div class="hero-content">
    <h1>⚙️ Gestión de Reservas</h1>
    <p>Administra y gestiona todas las reservas del sistema</p>
  </div>
</section>


        <!-- MENSAJES -->
        <?php if ($notice): ?>
            <div style="background:#d1fae5;padding:12px;border-radius:8px;margin-bottom:12px;color:#065f46;">
                ✅ <?php echo htmlspecialchars($notice); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background:#fee2e2;padding:12px;border-radius:8px;margin-bottom:12px;color:#991b1b;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- TABLA DE RESERVAS -->
        <h2>Listado de Reservas</h2>

        <table style="width:100%; border-collapse:collapse; margin-top:10px;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:8px;border:1px solid #e5e7eb;">ID</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Cliente</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Modelo</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Fecha Inicio</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Fecha Fin</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Ubicación</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Estado</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($reservas) === 0): ?>
                    <tr>
                        <td colspan="8" style="padding:12px;text-align:center;">No hay reservas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td style="padding:8px;border:1px solid #f3f4f6;"><?php echo (int)$r['id']; ?></td>
                            <td style="padding:8px;border:1px solid #f3f4f6;"><?php echo htmlspecialchars($r['cliente'] ?? '—'); ?></td>
                            <td style="padding:8px;border:1px solid #f3f4f6;"><?php echo htmlspecialchars($r['modelo_vehiculo']); ?></td>
                            <td style="padding:8px;border:1px solid #f3f4f6;"><?php echo htmlspecialchars($r['fecha_inicio']); ?></td>
                            <td style="padding:8px;border:1px solid #f3f4f6;"><?php echo htmlspecialchars($r['fecha_fin']); ?></td>
                            <td style="padding:8px;border:1px solid #f3f4f6;"><?php echo htmlspecialchars($r['ubicacion']); ?></td>
                            <td style="padding:8px;border:1px solid #f3f4f6;"><?php echo htmlspecialchars($r['estado_reserva']); ?></td>
                            <td style="padding:8px;border:1px solid #f3f4f6; white-space:nowrap;">
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="reserva_id" value="<?php echo (int)$r['id']; ?>">

                                    <?php if ($r['estado_reserva'] !== 'confirmada'): ?>
                                        <button name="action" value="confirmar"
                                                style="background:#16a34a;color:white;border:none;padding:5px 8px;border-radius:6px;cursor:pointer;margin-right:4px;">
                                            Confirmar
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($r['estado_reserva'] !== 'cancelada'): ?>
                                        <button name="action" value="cancelar"
                                                style="background:#f59e0b;color:white;border:none;padding:5px 8px;border-radius:6px;cursor:pointer;margin-right:4px;">
                                            Cancelar
                                        </button>
                                    <?php endif; ?>

                                    <button name="action" value="borrar"
                                            onclick="return confirm('¿Eliminar la reserva #<?php echo (int)$r['id']; ?>?');"
                                            style="background:#dc2626;color:white;border:none;padding:5px 8px;border-radius:6px;cursor:pointer;">
                                        Borrar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2025 Autos Costa Sol - Panel de Gestión</p>
        </footer>
    </div>
</body>
</html>
