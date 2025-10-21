<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']); // solo superadmin

require_once '../model/Orden.php';
require_once '../model/Cliente.php';
require_once '../config/database.php'; // ya tenemos $pdo

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);

// Fecha de hoy (YYYY-MM-DD)
$hoy = date('Y-m-d');

// Obtener órdenes entregadas hoy
$sql = "SELECT o.*, c.nombre, c.apellido 
        FROM ordenes o 
        JOIN clientes c ON o.cliente_id = c.id
        WHERE o.estado = 'Entregado' 
          AND DATE(o.fecha_creacion) = :hoy
        ORDER BY o.fecha_creacion ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':hoy' => $hoy]);
$ordenes_entregadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total cobrado
$total_cobrado = 0;
foreach ($ordenes_entregadas as $orden) {
    $total_cobrado += $orden['total'];
}
?>

<?php include 'partial/header.php'; ?>

<h2>Cierre Diario - <?= date('d/m/Y') ?></h2>

<div style="text-align:center; margin: 30px 0;">
    <h1 style="font-size: 48px; color: #007BFF;">$<?= number_format($total_cobrado, 2) ?></h1>
    <p>Total cobrado hoy</p>
</div>

<?php if (!empty($ordenes_entregadas)): ?>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Problema Reportado</th>
                <th>Observaciones</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ordenes_entregadas as $orden): ?>
                <tr>
                    <td><?= htmlspecialchars($orden['nombre'] . ' ' . $orden['apellido']) ?></td>
                    <td><?= htmlspecialchars($orden['equipo']) ?></td>
                    <td><?= nl2br(htmlspecialchars($orden['problema_reportado'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($orden['observaciones'])) ?></td>
                    <td>$<?= number_format($orden['total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No se entregaron órdenes hoy.</p>
<?php endif; ?>

<?php include 'partial/footer.php'; ?>
