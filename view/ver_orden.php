<?php
include 'partial/header.php';
require_once '../model/Orden.php';
require_once '../model/Cliente.php';

// Configurar PDO (igual que en tu controller)
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
    die("Error de conexión: " . $e->getMessage());
}

// Instanciar modelos
$ordenModel = new Orden($pdo);

// Obtener todas las órdenes
$ordenes = $ordenModel->listar();

// Filtrar: solo mostrar órdenes que NO estén entregadas
$ordenes = array_filter($ordenes, fn($o) => $o['estado'] !== 'Entregado');

?>

<h2>Órdenes Registradas</h2>

<table>
   <thead>
    <tr>
        <th>Cliente</th>
        <th>Creado Por</th>
        <th>Fecha de Ingreso</th>
        <th>Equipo</th>
        <th>Problema Reportado</th>
        <th>Estado</th>
        <th>Total</th>
        <th>Acciones</th>
    </tr>
</thead>
<tbody>
    <?php if (!empty($ordenes)): ?>
        <?php foreach ($ordenes as $orden): ?>
            <tr>
                <td><?= htmlspecialchars(($orden['cliente_nombre'] ?? '') . ' ' . ($orden['cliente_apellido'] ?? '')) ?></td>
               <td><?= htmlspecialchars($orden['creador_nombre'] ?? 'Desconocido') ?></td>

                <td><?= isset($orden['fecha_creacion']) ? date("d/m/Y H:i", strtotime($orden['fecha_creacion'])) : '' ?></td>
                <td><?= htmlspecialchars($orden['equipo'] ?? '') ?></td>
                <td><?= nl2br(htmlspecialchars($orden['problema_reportado'] ?? '')) ?></td>
                <td>
                    <span class="estado <?= strtolower(str_replace(' ', '_', $orden['estado'] ?? '')) ?>">
                        <?= htmlspecialchars($orden['estado'] ?? '') ?>
                    </span>
                </td>
                <td>$<?= number_format($orden['total'] ?? 0, 2) ?></td>
                <td class="acciones">
                    <a href="ver_detalle.php?id=<?= $orden['id'] ?>" class="btn ver">Ver</a>
                    <a href="editar_orden.php?id=<?= $orden['id'] ?>" class="btn editar">Editar</a>
                    <a href="borrar_orden.php?id=<?= $orden['id'] ?>" class="btn borrar" onclick="return confirm('¿Seguro que deseas borrar esta orden?')">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="8">No hay órdenes registradas</td>
        </tr>
    <?php endif; ?>
</tbody>