<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']); // solo superadmin

require_once '../model/orden.php';
require_once '../model/cliente.php';
require_once '../model/cierre_diario.php';
require_once '../config/database.php'; // ya tenemos $pdo

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);
$cierreDiario = new CierreDiario($pdo);

// =========================
// Procesar cierre diario si se envía el formulario
// =========================
$totales_diario = null;
$fecha = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha'])) {
    $fecha = $_POST['fecha'];
    $totales_diario = $cierreDiario->generar($fecha);
}

// =========================
// Mostrar órdenes entregadas hoy
// =========================
$hoy = date('Y-m-d');

$sql = "SELECT o.*, c.nombre, c.apellido 
        FROM ordenes o 
        JOIN clientes c ON o.cliente_id = c.id
        WHERE o.estado = 'Entregado' 
          AND DATE(o.fecha_finalizacion) = :hoy
        ORDER BY o.fecha_finalizacion ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':hoy' => $hoy]);
$ordenes_entregadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total cobrado hoy
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

<!-- ==================== CIERRE DIARIO ==================== -->
<section class="cierre-diario">
    <h3>📅 Cierre Diario</h3>
    
    <form method="post" style="margin-bottom:20px;">
        <p>Aca vas a poder insertar, en tu base de datos, el cierre de caja de cualquier día.</p>
        <label for="fecha">Seleccionar fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
        <button type="submit">Generar Cierre Diario</button>
    </form>

    <?php if ($totales_diario): ?>
    <div class="resultado-cierre">
        <h4>Resultados del <?= htmlspecialchars($fecha) ?></h4>
        <ul style="list-style:none; padding:0;">
            <li>Ingresadas: <?= $totales_diario['Ingresado'] ?></li>
            <li>En revisión: <?= $totales_diario['En revisión'] ?></li>
            <li>Reparadas: <?= $totales_diario['Reparado'] ?></li>
            <li>Entregadas: <?= $totales_diario['Entregado'] ?></li>
            <li>Total órdenes: <?= $totales_diario['total_ordenes'] ?></li>
            <li>Total recaudado: $<?= number_format($totales_diario['total_recaudado'], 2) ?></li>
        </ul>
    </div>
    <?php endif; ?>
</section>

<hr style="margin:30px 0;">

<!-- ==================== TABLA ÓRDENES ENTREGADAS HOY ==================== -->
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
