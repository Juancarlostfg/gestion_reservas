<?php
session_start();

// Solo empleados pueden generar informes
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

// Ruta base del proyecto
$basePath = __DIR__;

// Comando para ejecutar Python

$comando = 'python "' . $basePath . '\\informe_reservas.py"';

// Ejecutar el script de Python y capturar la salida (por si hay errores)
$salida = shell_exec($comando . ' 2>&1');

// Ruta del PDF generado
$pdfPath = $basePath . DIRECTORY_SEPARATOR . 'informe_reservas.pdf';

if (!file_exists($pdfPath)) {
    // Si algo falla, mostramos la salida para depuración
    echo "<h2>Error al generar el informe</h2>";
    echo "<pre>" . htmlspecialchars($salida) . "</pre>";
    echo '<p><a href="dashboard.php">← Volver al dashboard</a></p>';
    exit();
}

// Enviar el PDF al navegador para descarga
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="informe_reservas.pdf"');
header('Content-Length: ' . filesize($pdfPath));
readfile($pdfPath);
exit();
