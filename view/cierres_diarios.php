<?php
require_once __DIR__ . '/../config/auth.php';
checkLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/cierre_diario.php';

$cierreDiario = new CierreDiario($pdo);

$mensaje = null;
$totales_diario = null;
$totales_historico = null;
$fecha = null;
$fecha_desde = null;
$fecha_hasta = null;

// Cierre diario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha'])) {
    $fecha = $_POST['fecha'];
    $totales_diario = $cierreDiario->generar($fecha);
    $mensaje = "Cierre diario generado para $fecha.";
}

// Cierre histórico (rango)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_desde'], $_POST['fecha_hasta'])) {
    $fecha_desde = $_POST['fecha_desde'];
    $fecha_hasta = $_POST['fecha_hasta'];
    $totales_historico = $cierreDiario->generarRango($fecha_desde, $fecha_hasta);
    $mensaje = "Cierre histórico generado del $fecha_desde al $fecha_hasta.";
}

// Historial de cierres guardados
$historial = $cierreDiario->listarHistorial();

include __DIR__ . '/partial/header.php';
?>

<main class="container cierres">
    <h2>📊 Gestión de Cierres</h2>

    <!-- ==================== CIERRE HISTÓRICO ==================== -->
    <section class="cierre-historico">
        <h3>📆 Cierre Histórico (por rango)</h3>
        
        <form method="post" class="filtros-fechas">
             <p>Aca vas a poder ver Tus cierres diarios Agrupados por fechas (si no los insertaste no los veras). </p>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <div>
                    <label for="fecha_desde">Desde:</label>
                    <input type="date" id="fecha_desde" name="fecha_desde" required>
                </div>
                <div>
                    <label for="fecha_hasta">Hasta:</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta" required>
                </div>
                <button type="submit" class="btn-generar">Generar Cierre Histórico</button>
            </div>
        </form>

        <?php if ($totales_historico): ?>
        <div class="resultado-cierre">
            <h4>Resumen del <?= htmlspecialchars($fecha_desde) ?> al <?= htmlspecialchars($fecha_hasta) ?></h4>
            <ul style="list-style:none; padding:0;">
                <li>Ingresadas: <?= $totales_historico['Ingresado'] ?></li>
                <li>En revisión: <?= $totales_historico['En revisión'] ?></li>
                <li>Reparadas: <?= $totales_historico['Reparado'] ?></li>
                <li>Entregadas: <?= $totales_historico['Entregado'] ?></li>
                <li>Total órdenes: <?= $totales_historico['total_ordenes'] ?></li>
                <li>Total recaudado: $<?= number_format($totales_historico['total_recaudado'], 2) ?></li>
            </ul>
        </div>
        <?php endif; ?>
    </section>

    <hr style="margin:30px 0;">

    
</main>
    <!-- ==================== HISTORIAL ==================== -->
    <section class="historial-cierres">
        <h3>🧾 Historial de Cierres Guardados</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Ingresadas</th>
                        <th>En revisión</th>
                        <th>Reparadas</th>
                        <th>Entregadas</th>
                        <th>Total Órdenes</th>
                        <th>Total Recaudado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($historial)): ?>
                        <?php foreach($historial as $cierre): ?>
                            <tr>
                                <td><?= htmlspecialchars($cierre['fecha']) ?></td>
                                <td><?= $cierre['ordenes_ingresadas'] ?></td>
                                <td><?= $cierre['ordenes_en_revision'] ?></td>
                                <td><?= $cierre['ordenes_reparadas'] ?></td>
                                <td><?= $cierre['ordenes_entregadas'] ?></td>
                                <td><?= $cierre['total_ordenes'] ?></td>
                                <td>$<?= number_format($cierre['total_recaudado'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No hay cierres registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

   

<?php include __DIR__ . '/partial/footer.php'; ?>
