<?php
session_start();
include 'includes/conexion.php';

// Solo empleados
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

$notice = '';
$error  = '';

$editando = false;
$vehiculo_editar = [
    'id'          => '',
    'marca'       => '',
    'modelo'      => '',
    'matricula'   => '',
    'tipo_vehiculo' => 'sedan',
    'combustible' => 'gasolina',
    'transmision' => 'manual',
    'plazas'      => 5,
    'puertas'     => 5,
    'precio_dia'  => 50,
    'imagen'      => '',
    'descripcion' => ''
];

// Si venimos con ?editar=ID → cargar datos del vehículo
if (isset($_GET['editar'])) {
    $edit_id = (int)$_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM vehiculos WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $vehiculo_editar = $res->fetch_assoc();
        $editando = true;
    }
    $stmt->close();
}

// CREAR / ACTUALIZAR / ELIMINAR / CAMBIAR DISPONIBILIDAD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // CREAR VEHÍCULO
    if ($action === 'crear') {
        $marca       = trim($_POST['marca'] ?? '');
        $modelo      = trim($_POST['modelo'] ?? '');
        $matricula   = trim($_POST['matricula'] ?? '');
        $tipo        = $_POST['tipo_vehiculo'] ?? 'sedan';
        $combustible = $_POST['combustible'] ?? 'gasolina';
        $transmision = $_POST['transmision'] ?? 'manual';
        $plazas      = (int)($_POST['plazas'] ?? 5);
        $puertas     = (int)($_POST['puertas'] ?? 5);
        $precio_dia  = (float)($_POST['precio_dia'] ?? 50);
        $imagen      = trim($_POST['imagen'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($marca === '' || $modelo === '' || $matricula === '') {
            $error = "Marca, modelo y matrícula son obligatorios.";
        } else {
            $stmt = $conexion->prepare(
                "INSERT INTO vehiculos 
                 (marca, modelo, matricula, tipo_vehiculo, combustible, transmision, plazas, puertas, precio_dia, imagen, descripcion) 
                 VALUES (?,?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->bind_param(
                "ssssssiiiss",
                $marca, $modelo, $matricula, $tipo, $combustible, $transmision,
                $plazas, $puertas, $precio_dia, $imagen, $descripcion
            );

            if ($stmt->execute()) {
                $notice = "Vehículo añadido correctamente.";
            } else {
                if ($stmt->errno === 1062) {
                    $error = "Ya existe un vehículo con esa matrícula.";
                } else {
                    $error = "Error al guardar el vehículo: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }

    // ACTUALIZAR VEHÍCULO
    if ($action === 'actualizar' && isset($_POST['vehiculo_id'])) {
        $vehiculo_id = (int)$_POST['vehiculo_id'];

        $marca       = trim($_POST['marca'] ?? '');
        $modelo      = trim($_POST['modelo'] ?? '');
        $matricula   = trim($_POST['matricula'] ?? '');
        $tipo        = $_POST['tipo_vehiculo'] ?? 'sedan';
        $combustible = $_POST['combustible'] ?? 'gasolina';
        $transmision = $_POST['transmision'] ?? 'manual';
        $plazas      = (int)($_POST['plazas'] ?? 5);
        $puertas     = (int)($_POST['puertas'] ?? 5);
        $precio_dia  = (float)($_POST['precio_dia'] ?? 50);
        $imagen      = trim($_POST['imagen'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($marca === '' || $modelo === '' || $matricula === '') {
            $error = "Marca, modelo y matrícula son obligatorios.";
        } else {
            $stmt = $conexion->prepare(
                "UPDATE vehiculos SET
                    marca = ?, modelo = ?, matricula = ?, tipo_vehiculo = ?, combustible = ?, 
                    transmision = ?, plazas = ?, puertas = ?, precio_dia = ?, imagen = ?, descripcion = ?
                 WHERE id = ?"
            );
            $stmt->bind_param(
                "ssssssiiissi",
                $marca, $modelo, $matricula, $tipo, $combustible, $transmision,
                $plazas, $puertas, $precio_dia, $imagen, $descripcion, $vehiculo_id
            );

            if ($stmt->execute()) {
                $notice = "Vehículo actualizado correctamente.";
            } else {
                if ($stmt->errno === 1062) {
                    $error = "Ya existe un vehículo con esa matrícula.";
                } else {
                    $error = "Error al actualizar el vehículo: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }

    // ELIMINAR VEHÍCULO
    if ($action === 'eliminar' && isset($_POST['vehiculo_id'])) {
        $vehiculo_id = (int)$_POST['vehiculo_id'];
        $stmt = $conexion->prepare("DELETE FROM vehiculos WHERE id = ?");
        $stmt->bind_param("i", $vehiculo_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $notice = "Vehículo eliminado.";
        } else {
            $error = "No se pudo eliminar el vehículo.";
        }
        $stmt->close();
    }

    // CAMBIAR DISPONIBILIDAD
    if ($action === 'toggle' && isset($_POST['vehiculo_id'])) {
        $vehiculo_id = (int)$_POST['vehiculo_id'];
        $disponible  = (int)($_POST['disponible'] ?? 1);
        $nuevo = $disponible ? 0 : 1;

        $stmt = $conexion->prepare("UPDATE vehiculos SET disponible = ? WHERE id = ?");
        $stmt->bind_param("ii", $nuevo, $vehiculo_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $notice = "Disponibilidad actualizada.";
        } else {
            $error = "No se pudo actualizar la disponibilidad.";
        }
        $stmt->close();
    }

    // Evitar reenvío de formulario al refrescar
    header("Location: gestion_vehiculos.php");
    exit();
}

// LISTADO DE VEHÍCULOS
$sql = "SELECT * FROM vehiculos ORDER BY marca, modelo";
$result = $conexion->query($sql);
$vehiculos = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Vehículos - Autos Costa Sol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <span class="logo-icon">🚗</span>
                <span>Autos Costa Sol</span>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
                <a href="vehiculos.php" class="nav-link">🚘 Vista Vehículos</a>
                <a href="logout.php" class="btn-logout">🚪 Salir</a>
            </div>
        </nav>
    </header>

    <!-- HERO -->
    <section class="hero" 
        style="
            background-image: url('img/manos.jpg');
            background-size: cover;
            background-position: center;
            border-radius: 14px;
            height: 220px;
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
                <h1 style="margin:0; font-size:26px; color:#fff;">
                    Gestión de Vehículos 🚘
                </h1>
                <p style="margin:6px 0 0; color:#f9fafb;">
                    Alta, baja, edición y disponibilidad de la flota
                </p>
            </div>
        </div>
    </section>

    <!-- MENSAJES -->
    <?php if ($notice): ?>
        <div style="background:#d1fae5;color:#065f46;padding:10px 14px;border-radius:8px;margin-top:12px;">
            ✅ <?php echo htmlspecialchars($notice); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;margin-top:12px;">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO ALTA / EDICIÓN -->
    <div class="form-container" style="margin-top:18px;">
        <h2 style="margin-top:0;">
            <?php echo $editando ? '✏️ Editar vehículo' : '➕ Añadir vehículo a la flota'; ?>
        </h2>

        <form method="post">
            <input type="hidden" name="action" value="<?php echo $editando ? 'actualizar' : 'crear'; ?>">
            <?php if ($editando): ?>
                <input type="hidden" name="vehiculo_id" value="<?php echo (int)$vehiculo_editar['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" class="form-control" required
                       value="<?php echo htmlspecialchars($vehiculo_editar['marca']); ?>">
            </div>

            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo" class="form-control" required
                       value="<?php echo htmlspecialchars($vehiculo_editar['modelo']); ?>">
            </div>

            <div class="form-group">
                <label>Matrícula</label>
                <input type="text" name="matricula" class="form-control" required
                       value="<?php echo htmlspecialchars($vehiculo_editar['matricula']); ?>"
                       placeholder="1234-ABC">
            </div>

            <div class="form-group">
                <label>Tipo de vehículo</label>
                <select name="tipo_vehiculo" class="form-control">
                    <?php
                    $tipos = ['economico','compacto','sedan','suv','lujo','deportivo'];
                    foreach ($tipos as $t):
                    ?>
                        <option value="<?php echo $t; ?>"
                            <?php echo ($vehiculo_editar['tipo_vehiculo'] === $t) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($t); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Combustible</label>
                <select name="combustible" class="form-control">
                    <?php
                    $combustibles = ['gasolina','diesel','hibrido','electrico'];
                    foreach ($combustibles as $c):
                    ?>
                        <option value="<?php echo $c; ?>"
                            <?php echo ($vehiculo_editar['combustible'] === $c) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($c); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Transmisión</label>
                <select name="transmision" class="form-control">
                    <?php
                    $trans = ['manual','automatica'];
                    foreach ($trans as $t):
                    ?>
                        <option value="<?php echo $t; ?>"
                            <?php echo ($vehiculo_editar['transmision'] === $t) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($t); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Plazas</label>
                <input type="number" name="plazas" class="form-control" min="2" max="9"
                       value="<?php echo (int)$vehiculo_editar['plazas']; ?>">
            </div>

            <div class="form-group">
                <label>Puertas</label>
                <input type="number" name="puertas" class="form-control" min="2" max="5"
                       value="<?php echo (int)$vehiculo_editar['puertas']; ?>">
            </div>

            <div class="form-group">
                <label>Precio por día (€)</label>
                <input type="number" step="0.01" name="precio_dia" class="form-control" required
                       value="<?php echo number_format((float)$vehiculo_editar['precio_dia'], 2, '.', ''); ?>">
            </div>

            <div class="form-group">
                <label>Nombre de imagen (opcional)</label>
                <input type="text" name="imagen" class="form-control"
                       value="<?php echo htmlspecialchars($vehiculo_editar['imagen']); ?>"
                       placeholder="ej: audi-tt.jpg">
                <small style="color:#6b7280;">La imagen debe estar en la carpeta <code>img/</code> del proyecto.</small>
            </div>

            <div class="form-group">
                <label>Descripción (opcional)</label>
                <textarea name="descripcion" rows="3" class="form-control"><?php
                    echo htmlspecialchars($vehiculo_editar['descripcion']);
                ?></textarea>
            </div>

            <button type="submit" class="btn-primary" style="margin-top:8px;">
                <?php echo $editando ? '💾 Guardar cambios' : 'Guardar vehículo'; ?>
            </button>

            <?php if ($editando): ?>
                <a href="gestion_vehiculos.php" class="nav-link" style="margin-left:10px;">
                    Cancelar edición
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- LISTADO DE VEHÍCULOS -->
    <h2>Flota registrada</h2>

    <?php if (count($vehiculos) === 0): ?>
        <p style="background:#f9fafb;padding:16px;border-radius:10px;border:1px solid #e5e7eb;">
            No hay vehículos registrados todavía.
        </p>
    <?php else: ?>
        <table style="margin-top:10px;">
            <thead>
            <tr>
                <th>ID</th>
                <th>Marca / Modelo</th>
                <th>Matrícula</th>
                <th>Tipo</th>
                <th>Comb.</th>
                <th>Trans.</th>
                <th>Plazas</th>
                <th>Precio/día</th>
                <th>Disp.</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($vehiculos as $v): ?>
                <tr>
                    <td><?php echo (int)$v['id']; ?></td>
                    <td><?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($v['matricula']); ?></td>
                    <td><?php echo ucfirst($v['tipo_vehiculo']); ?></td>
                    <td><?php echo ucfirst($v['combustible']); ?></td>
                    <td><?php echo ucfirst($v['transmision']); ?></td>
                    <td><?php echo (int)$v['plazas']; ?></td>
                    <td>€<?php echo number_format($v['precio_dia'], 2, ',', '.'); ?></td>
                    <td>
                        <?php if ($v['disponible']): ?>
                            <span style="color:#16a34a;font-weight:600;">Disponible</span>
                        <?php else: ?>
                            <span style="color:#dc2626;font-weight:600;">No disp.</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <!-- Editar -->
                        <a href="gestion_vehiculos.php?editar=<?php echo (int)$v['id']; ?>" 
                           class="btn"
                           style="background:#e0f2fe;color:#0369a1;text-decoration:none;">
                            Editar
                        </a>

                        <!-- Cambiar disponibilidad -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="vehiculo_id" value="<?php echo (int)$v['id']; ?>">
                            <input type="hidden" name="disponible" value="<?php echo (int)$v['disponible']; ?>">
                            <button type="submit" class="btn"
                                    style="background:#e5e7eb;color:#111827;border:none;">
                                Cambiar
                            </button>
                        </form>

                        <!-- Eliminar -->
                        <form method="post" style="display:inline;"
                              onsubmit="return confirm('¿Eliminar el vehículo <?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?>?');">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="vehiculo_id" value="<?php echo (int)$v['id']; ?>">
                            <button type="submit" class="btn-delete">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <footer class="footer">
        <p>© 2024 Autos Costa Sol - Gestión de Vehículos</p>
    </footer>
</div>
</body>
</html>
