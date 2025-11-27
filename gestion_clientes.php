<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/conexion.php';

// ✅ Solo empleados pueden entrar aquí
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'empleado') {
    // O lo mandas al dashboard, o a una página de error
    header('Location: dashboard.php');
    exit();
}

require_once __DIR__ . '/includes/conexion.php'; // Debe exponer $conexion como mysqli
mysqli_report(MYSQLI_REPORT_OFF); 

// Mensajes para la UI
$mensaje = '';
$error = '';

// Procesar acciones: add, edit, delete
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardar nuevo o editar existente
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $email === '') {
        $error = 'Nombre y correo son obligatorios.';
    } else {
        if (isset($_POST['id']) && (int)$_POST['id'] > 0) {
            // Edit
            $id_edit = (int)$_POST['id'];
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sssi', $nombre, $email, $telefono, $id_edit);
            if ($stmt->execute()) {
                $mensaje = 'Cliente actualizado correctamente.';
            } else {
                // Posible duplicado de email
                if ($conexion->errno === 1062) {
                    $error = 'Ya existe un usuario con ese email.';
                } else {
                    $error = 'Error al actualizar: ' . $conexion->error;
                }
            }
            $stmt->close();
        } else {
            // Insert nuevo. Generamos contraseña temporal (aleatoria) y hash
            try {
                $password_plain = bin2hex(random_bytes(4)); // 8 chars hex
            } catch (Exception $e) {
                $password_plain = substr(md5(uniqid('', true)), 0, 8);
            }
            $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);
            $rol = 'cliente';
            $sql = "INSERT INTO usuarios (email, password, nombre, rol, fecha_creacion) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('ssss', $email, $password_hash, $nombre, $rol);
            if ($stmt->execute()) {
                $mensaje = 'Cliente creado. Contraseña temporal: ' . $password_plain;
            } else {
                if ($conexion->errno === 1062) {
                    $error = 'Ya existe un usuario con ese email.';
                } else {
                    $error = 'Error al crear: ' . $conexion->error;
                }
            }
            $stmt->close();
        }
    }
}

// Borrar cliente
if ($action === 'delete' && $id > 0) {
    // Seguridad: no permitir borrar tu propio usuario (opcional)
    $current = $_SESSION['usuario_id'] ?? 0;
    if ($id == $current) {
        $error = 'No puedes borrarte a ti mismo.';
    } else {
        $stmt = $conexion->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensaje = 'Cliente eliminado correctamente.';
        } else {
            $error = 'Error al eliminar: ' . $conexion->error;
        }
        $stmt->close();
    }
}

// Datos para editar (si action=edit)
$cliente_edit = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $conexion->prepare('SELECT id, email, nombre, telefono, rol FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $cliente_edit = $res->fetch_assoc();
    $stmt->close();
}

// Listado simple de clientes
$clientes = [];
$sql_list = 'SELECT id, email, nombre, telefono, rol, fecha_creacion FROM usuarios ORDER BY fecha_creacion DESC';
$result = $conexion->query($sql_list);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    $result->free();
}

// HTML de la página con estilo mejorado
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        :root{
            --bg:#f7fafc;
            --card:#ffffff;
            --muted:#6b7280;
            --accent:#2563eb;
            --success:#16a34a;
            --danger:#dc2626;
            --border:#e5e7eb;
        }
        body{font-family:Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:var(--bg); color:#111827; margin:0; padding:24px;}
        .container{max-width:1100px; margin:0 auto;}
        header{display:flex; align-items:center; justify-content:space-between; margin-bottom:18px}
        h1{font-size:22px; margin:0}
        .card{background:var(--card); border:1px solid var(--border); border-radius:12px; padding:18px; box-shadow:0 6px 18px rgba(15,23,42,0.04);}
        .grid{display:grid; grid-template-columns: 1fr 360px; gap:18px}
        form input, form select, form textarea{width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px;}
        form label{font-size:13px; color:var(--muted); display:block; margin-bottom:6px}
        .btn{display:inline-block; padding:8px 12px; border-radius:8px; background:var(--accent); color:#fff; text-decoration:none; border:none; cursor:pointer}
        .btn.ghost{background:transparent; color:var(--accent); border:1px solid var(--accent)}
        .msg{padding:12px; border-radius:10px; margin-bottom:12px}
        .ok{background:rgba(16,185,129,0.12); color:var(--success); border:1px solid rgba(16,185,129,0.18)}
        .err{background:rgba(239,68,68,0.08); color:var(--danger); border:1px solid rgba(239,68,68,0.12)}
        table{width:100%; border-collapse:collapse; font-size:14px}
        thead th{text-align:left; padding:12px; border-bottom:1px solid var(--border); color:var(--muted); font-weight:600}
        tbody td{padding:12px; border-bottom:1px solid #f3f4f6}
        .actions a{margin-right:8px; color:var(--accent); text-decoration:none}
        .small{font-size:13px; color:var(--muted)}
        @media (max-width:900px){ .grid{grid-template-columns:1fr} }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex; gap:8px; margin-right:12px;">
    <a href="dashboard.php" class="btn ghost">⟵ Volver al dashboard</a>
    <a href="logout.php" class="btn" style="background:#dc2626;">Salir</a>
</div>
<h1>Gestión de clientes</h1>
            <div class="small">Usuario: <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? ''); ?> — Rol: <?php echo htmlspecialchars($_SESSION['usuario_rol'] ?? ''); ?></div>
        </header>

        <?php if ($mensaje): ?>
            <div class="msg ok card"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg err card"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <h2 style="margin-top:0"><?php echo $cliente_edit ? 'Editar cliente' : 'Nuevo cliente'; ?></h2>
                <form method="post" action="gestion_clientes.php" >
                    <input type="hidden" name="id" value="<?php echo $cliente_edit ? (int)$cliente_edit['id'] : ''; ?>">

                    <div style="margin-bottom:10px">
                        <label>Nombre</label>
                        <input type="text" name="nombre" required value="<?php echo $cliente_edit ? htmlspecialchars($cliente_edit['nombre']) : ''; ?>">
                    </div>

                    <div style="margin-bottom:10px">
                        <label>Email</label>
                        <input type="email" name="email" required value="<?php echo $cliente_edit ? htmlspecialchars($cliente_edit['email']) : ''; ?>">
                    </div>

                    <div style="margin-bottom:10px">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?php echo $cliente_edit ? htmlspecialchars($cliente_edit['telefono']) : ''; ?>">
                    </div>

                    <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px">
                        <button class="btn" type="submit"><?php echo $cliente_edit ? 'Actualizar' : 'Crear cliente'; ?></button>
                        <?php if ($cliente_edit): ?>
                            <a class="btn ghost" href="gestion_clientes.php">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>

                <p class="small" style="margin-top:12px">Al crear un cliente se genera una contraseña temporal que se muestra en pantalla. El usuario deberá cambiarla en su perfil.</p>
            </div>

            <div>
                <div class="card" style="margin-bottom:12px">
                    <h3 style="margin:0 0 8px 0">Buscar usuarios</h3>
                    <input id="q" placeholder="Buscar por email o nombre" oninput="filterTable()">
                </div>

                <div class="card">
                    <h3 style="margin-top:0">Listado de usuarios</h3>
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clientes) === 0): ?>
                                <tr><td colspan="7">No hay usuarios.</td></tr>
                            <?php else: ?>
                                <?php foreach ($clientes as $c): ?>
                                    <tr>
                                        <td><?php echo (int)$c['id']; ?></td>
                                        <td><?php echo htmlspecialchars($c['email']); ?></td>
                                        <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($c['rol']); ?></td>
                                        <td><?php echo htmlspecialchars($c['fecha_creacion']); ?></td>
                                        <td class="actions">
                                            <a href="gestion_clientes.php?action=edit&id=<?php echo (int)$c['id']; ?>">Editar</a>
                                            <?php if ((int)$c['id'] !== (int)($_SESSION['usuario_id'] ?? 0)): ?>
                                                <a href="gestion_clientes.php?action=delete&id=<?php echo (int)$c['id']; ?>" onclick="return confirm('¿Borrar este usuario?');">Borrar</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        function filterTable(){
            const q = document.getElementById('q').value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.indexOf(q) === -1 ? 'none' : '';
            });
        }
    </script>
</body>
</html>
