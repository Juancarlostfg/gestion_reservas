<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "gestion_reservas";



$conexion = new mysqli($host, $user, $password, $database);
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Forzar utf8mb4 en la conexión para que los datos se recuperen en UTF-8
$conexion->set_charset('utf8mb4');

?>