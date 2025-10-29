<?php
session_start();
require_once '../model/Cliente.php';

$host = 'localhost';
$db   = 'servicio_tecnico';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die(json_encode([]));
}

$q = $_GET['q'] ?? '';
if ($q) {
    $sql = "SELECT id, nombre, apellido, email 
            FROM clientes 
            WHERE nombre LIKE :q 
               OR apellido LIKE :q 
               OR email LIKE :q
            ORDER BY nombre ASC
            LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => "%$q%"]);
    $clientes = $stmt->fetchAll();
    echo json_encode($clientes);
} else {
    echo json_encode([]);
}
