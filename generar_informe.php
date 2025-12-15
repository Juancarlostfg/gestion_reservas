<?php
session_start();

// empleados solamente
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

$basePath = __DIR__;

// Comando Python (ruta absoluta)
$comando = 'python "' . $basePath . '\\informe_reservas.py"';

// Ejecutar y capturar salida
$salida = shell_exec($comando . ' 2>&1');

// Ruta del PDF
$pdfPath = $basePath . DIRECTORY_SEPARATOR . 'informe_reservas.pdf';

// Si no existe el PDF → mostrar error real
if (!file_exists($pdfPath)) {
    echo "<h2>❌ Error al generar el informe</h2>";
    echo "<pre>" . htmlspecialchars($salida) . "</pre>";
    echo '<p><a href="dashboard.php">← Volver al dashboard</a></p>';
    exit();
}

// Descargar PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="informe_reservas.pdf"');
header('Content-Length: ' . filesize($pdfPath));
readfile($pdfPath);
exit();
