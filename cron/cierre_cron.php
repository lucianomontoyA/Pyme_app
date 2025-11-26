<?php
// config/database.php

// Incluir la variable de entorno



// Configuración de la base de datos

    // Datos de PRODUCCIÓN
   // $host = 'localhost';
    //$db   = 'u578954353_serv_tecnico';
   // $user = 'u578954353_root';
   // $pass = 'Ambeloquipi1!';
   // $charset = 'utf8mb4';
    
    $host = 'localhost';
    $db   = 'servicio_tecnico';
    $user = 'root';
    $pass = 'root';
    $charset = 'utf8mb4';


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
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

 $stmt = $pdo->prepare("
            SELECT estado, COUNT(*) as cantidad, SUM(total) as total
            FROM ordenes
            WHERE DATE(fecha_finalizacion) BETWEEN ? AND ?
            GROUP BY estado
        ");

// Uso la fecha del dia ya que es un cierre diario
$fecha_inicio = date('Y-m-d 00:00:00');
$fecha_fin = date('Y-m-d 23:59:59');

$stmt->execute([$fecha_inicio, $fecha_fin]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

var_dump($resultados);

$totales = [
    'Ingresado' => 0,
    'En revisión' => 0,
    'Reparado' => 0,
    'Entregado' => 0,
    'total_ordenes' => 0,
    'total_recaudado' => 0
];

foreach ($resultados as $row) {
    $estado = $row['estado'];
    $cantidad = (int)$row['cantidad'];
    $total = (float)$row['total'];

    if (isset($totales[$estado])) {
        $totales[$estado] += $cantidad;
    }

    $totales['total_ordenes'] += $cantidad;
    $totales['total_recaudado'] += $total;
}


  $stmt = $pdo->prepare("
            INSERT INTO cierres_diarios
            (fecha, ordenes_ingresadas, ordenes_en_revision, ordenes_reparadas, ordenes_entregadas, total_ordenes, total_recaudado)
            VALUES (now()
            , :ingresadas, :revision, :reparadas, :entregadas, :total_ordenes, :total_recaudado)
            ON DUPLICATE KEY UPDATE
                ordenes_ingresadas = :ingresadas_update,
                ordenes_en_revision = :revision_update,
                ordenes_reparadas = :reparadas_update,
                ordenes_entregadas = :entregadas_update,
                total_ordenes = :total_ordenes_update,
                total_recaudado = :total_recaudado_update
        ");

        $stmt->execute([
          //  ':fecha' => $fecha,
            ':ingresadas' => $totales['Ingresado'],
            ':revision' => $totales['En revisión'],
            ':reparadas' => $totales['Reparado'],
            ':entregadas' => $totales['Entregado'],
            ':total_ordenes' => $totales['total_ordenes'],
            ':total_recaudado' => $totales['total_recaudado'],

            // Para UPDATE
            ':ingresadas_update' => $totales['Ingresado'],
            ':revision_update' => $totales['En revisión'],
            ':reparadas_update' => $totales['Reparado'],
            ':entregadas_update' => $totales['Entregado'],
            ':total_ordenes_update' => $totales['total_ordenes'],
            ':total_recaudado_update' => $totales['total_recaudado'],
        ]);