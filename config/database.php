<?php
// config/database.php

require_once __DIR__ . '/env.php';

// Configuración de la base de datos
if ($isProd) {
    // Datos de PRODUCCIÓN
    $host = 'localhost';
    $db   = 'u578954353_serv_tecnico';
    $user = 'u578954353_root';
    $pass = 'Ambeloquipi1!';
    $charset = 'utf8mb4';
} else {
    // Datos de DESARROLLO
    $host = 'localhost';
    $db   = 'servicio_tecnico';
    $user = 'root';
    $pass = 'root';
    $charset = 'utf8mb4';
}

// DSN para PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones recomendadas para PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Crear la conexión PDO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 🔥🔥🔥 CONFIGURAR TIMEZONE DE MYSQL A ARGENTINA 🔥🔥🔥
  $pdo->exec("SET time_zone = '-03:00'");


} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
