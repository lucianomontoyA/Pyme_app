<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']); // solo superadmin


require_once '../model/Orden.php';
require_once '../model/Cliente.php';
require_once '../config/database.php'; // $pdo

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);

// Obtener todas las órdenes con estado "Entregado"
$ordenes = $ordenModel->listar(); // Listar todas
$ordenes_entregadas = array_filter($ordenes, fn($o) => $o['estado'] === 'Entregado');
?>

<?php include 'partial/header.php'; ?>

<h2>Órdenes Finalizadas</h2>

<table>
    <thead>
        <tr>
            <th>Nombre Cliente</th>
            <th>Apellido Cliente</th>
            <th>Equipo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Serie</th>
            <th>Problema Reportado</th>
            <th>Observaciones</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Código Público</th>
            <th>Fecha Creación</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($ordenes_entregadas)) : ?>
            <?php foreach ($ordenes_entregadas as $orden) : 
                $cliente = $clienteModel->obtener($orden['cliente_id']);
            ?>
            <tr>
                <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                <td><?= htmlspecialchars($cliente['apellido']) ?></td>
                <td><?= htmlspecialchars($orden['equipo']) ?></td>
                <td><?= htmlspecialchars($orden['marca']) ?></td>
                <td><?= htmlspecialchars($orden['modelo']) ?></td>
                <td><?= htmlspecialchars($orden['serie']) ?></td>
                <td><?= htmlspecialchars($orden['problema_reportado']) ?></td>
                <td><?= htmlspecialchars($orden['observaciones']) ?></td>
                <td><?= htmlspecialchars($orden['estado']) ?></td>
                <td><?= htmlspecialchars($orden['total']) ?></td>
                <td><?= htmlspecialchars($orden['codigo_publico']) ?></td>
                <td><?= htmlspecialchars($orden['fecha_creacion']) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="12">No hay órdenes finalizadas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'partial/footer.php'; ?>
