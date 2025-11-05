<?php
session_start();
require_once '../model/Cliente.php';

require_once '../config/database.php';

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
