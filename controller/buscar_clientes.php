<?php
require_once '../config/database.php';

$q = $_GET['q'] ?? '';

if ($q) {
    $sql = "SELECT id, nombre, apellido, email, direccion, telefono, cuit
            FROM clientes 
            WHERE nombre LIKE :q1 
               OR apellido LIKE :q2 
               OR email LIKE :q3
            ORDER BY nombre ASC
            LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':q1' => "%$q%",
        ':q2' => "%$q%",
        ':q3' => "%$q%"
    ]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clientes);
} else {
    echo json_encode([]);
}
