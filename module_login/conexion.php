<?php
// Este archivo establece una conexión a una base de datos PostgreSQL utilizando PDO.

// Configuración de los parámetros de conexión
$host = "localhost"; // Dirección del servidor de base de datos
$port = "5432"; // Puerto de conexión
$dbname = "cobro_coactivo"; // Nombre de la base de datos
$username = "postgres"; // Nombre de usuario de la base de datos
$password = "1234"; // Contraseña del usuario de la base de datos

try {
    // Crear una nueva instancia de PDO para conectarse a la base de datos
    $connect = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    // Configurar el modo de error para que lance excepciones
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Manejo de errores: si ocurre un error, se detiene la ejecución y se muestra un mensaje
    die("Error de conexión: " . $e->getMessage());
}