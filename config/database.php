<?php
// Configuración de la base de datos
$host = 'localhost';
$db   = 'servicio_tecnico'; // nombre de tu base
$user = 'root';             // tu usuario
$pass = 'root';             // tu contraseña
$charset = 'utf8mb4';

// DSN para PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones recomendadas para PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // lanzar excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // resultados como array asociativo
    PDO::ATTR_EMULATE_PREPARES => false,             // usar prepared statements reales
];

// Crear la conexión PDO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
