<?php
$host = "localhost";
$port = "5432";
$dbname = "cobro_coactivo";
$username = "postgres";
$password = "1234";

try {
    $connect = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}