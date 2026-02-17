<?php
require_once __DIR__ . '/../config/auth.php';
checkLogin();
require_once '../config/database.php';


date_default_timezone_set('America/Argentina/Buenos_Aires');

$hoy = date('Y-m-d');
$mesActual = date('Y-m');

// ===============================
// VARIABLES
// ===============================
$cobrosHoy = 0.0;
$gastosHoy = 0.0;
$balanceHoy = 0.0;

$cantidadOrdenesHoy = 0;
$cantidadGastosHoy = 0;
$promedioPorOrdenHoy = 0.0;

$ordenesPendientes = 0;
$ordenesEnRevision = 0;

$balanceMes = 0.0;
$ultimoMovimiento = null;
$estadoDia = '🟡 Día ajustado';

try {

    // ===============================
    // COBROS DEL DÍA (ORDENES ENTREGADAS)
    // ===============================
    $stmt = $pdo->prepare("
        SELECT IFNULL(SUM(total),0)
        FROM ordenes
        WHERE estado = 'Entregado'
        AND DATE(fecha_finalizacion) = ?
    ");
    $stmt->execute([$hoy]);
    $cobrosHoy = (float)$stmt->fetchColumn();

    // ===============================
    // GASTOS DEL DÍA
    // ===============================
    $stmt = $pdo->prepare("
        SELECT IFNULL(SUM(monto),0)
        FROM gastos
        WHERE fecha = ?
    ");
    $stmt->execute([$hoy]);
    $gastosHoy = (float)$stmt->fetchColumn();

    // ===============================
    // BALANCE DEL DÍA
    // ===============================
    $balanceHoy = $cobrosHoy - $gastosHoy;

    // ===============================
    // CANTIDAD DE ÓRDENES ENTREGADAS HOY
    // ===============================
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM ordenes
        WHERE estado = 'Entregado'
        AND DATE(fecha_finalizacion) = ?
    ");
    $stmt->execute([$hoy]);
    $cantidadOrdenesHoy = (int)$stmt->fetchColumn();

    // ===============================
    // CANTIDAD DE GASTOS HOY
    // ===============================
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM gastos
        WHERE fecha = ?
    ");
    $stmt->execute([$hoy]);
    $cantidadGastosHoy = (int)$stmt->fetchColumn();

    // ===============================
    // PROMEDIO POR ORDEN
    // ===============================
    if ($cantidadOrdenesHoy > 0) {
        $promedioPorOrdenHoy = $cobrosHoy / $cantidadOrdenesHoy;
    }

    // ===============================
    // ÓRDENES PENDIENTES (INGRESADO)
    // ===============================
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM ordenes
        WHERE estado = 'Ingresado'
    ");
    $ordenesPendientes = (int)$stmt->fetchColumn();

    // ===============================
    // ÓRDENES EN REVISIÓN
    // ===============================
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM ordenes
        WHERE estado = 'En revisión'
    ");
    $ordenesEnRevision = (int)$stmt->fetchColumn();

    // ===============================
    // BALANCE DEL MES
    // ===============================
    $stmt = $pdo->prepare("
        SELECT
            (SELECT IFNULL(SUM(total),0)
             FROM ordenes
             WHERE estado = 'Entregado'
             AND DATE_FORMAT(fecha_finalizacion,'%Y-%m') = ?)
            -
            (SELECT IFNULL(SUM(monto),0)
             FROM gastos
             WHERE DATE_FORMAT(fecha,'%Y-%m') = ?)
    ");
    $stmt->execute([$mesActual, $mesActual]);
    $balanceMes = (float)$stmt->fetchColumn();

    // ===============================
    // ÚLTIMO MOVIMIENTO (ORDEN O GASTO)
    // ===============================
    $stmt = $pdo->query("
        SELECT MAX(fecha) FROM (
            SELECT fecha_finalizacion AS fecha FROM ordenes
            WHERE estado = 'Entregado'
            UNION
            SELECT fecha FROM gastos
        ) AS movimientos
    ");
    $ultimoMovimiento = $stmt->fetchColumn();

    // ===============================
    // ESTADO DEL DÍA
    // ===============================
    if ($balanceHoy > 0) {
        $estadoDia = '🟢 Día rentable';
    } elseif ($balanceHoy < 0) {
        $estadoDia = '🔴 Día negativo';
    }

} catch (Exception $e) {
    // No romper la pantalla en producción
}

include 'partial/header.php';

?>

<!-- ===============================
     CIERRE PARCIAL DEL DÍA
=============================== -->
<div class="movimientos-box">

    <h3>📊 Cierre parcial del día (<?= $hoy ?>)</h3>

    <div class="mov-grid">

        <div class="mov-item ingreso">
            <span>Cobros</span>
            <strong>$<?= number_format($cobrosHoy, 2) ?></strong>
        </div>

        <div class="mov-item gasto">
            <span>Gastos</span>
            <strong>$<?= number_format($gastosHoy, 2) ?></strong>
        </div>

        <div class="mov-item balance">
            <span>Balance</span>
            <strong>$<?= number_format($balanceHoy, 2) ?></strong>
        </div>

        <div class="mov-item">
            <span>Órdenes entregadas</span>
            <strong><?= $cantidadOrdenesHoy ?></strong>
        </div>

       

        <div class="mov-item">
            <span>Pendientes</span>
            <strong><?= $ordenesPendientes ?></strong>
        </div>

        <div class="mov-item">
            <span>En revisión</span>
            <strong><?= $ordenesEnRevision ?></strong>
        </div>

       
    </div>

    <div class="estado-dia">
        <?= $estadoDia ?>
    </div>

    <small>
        Último movimiento:
        <?= $ultimoMovimiento ? date('d/m/Y H:i', strtotime($ultimoMovimiento)) : '—' ?>
    </small>

</div>
