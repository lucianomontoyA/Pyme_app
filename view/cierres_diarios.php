<?php
// Iniciar sesión y verificar login
require_once __DIR__ . '/../config/auth.php';
checkLogin();

// Conexión a la base de datos
require_once __DIR__ . '/../config/database.php';

// Modelo de CierreDiario
require_once __DIR__ . '/../model/cierre_diario.php';

$cierreDiario = new CierreDiario($pdo);

// Si se envía formulario para generar cierre diario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha'])) {
    $fecha = $_POST['fecha'];
    $totales = $cierreDiario->generar($fecha);
    $mensaje = "Cierre diario generado para $fecha.";
}

// Listar historial de cierres
$historial = $cierreDiario->listarHistorial();

// Incluir header
include __DIR__ . '/partial/header.php';
?>

<main class="container">
    
    <!-- Formulario para generar cierre -->
    <form method="post" style="margin-bottom:20px;">
        <h2 >Cierres Diarios</h2>

        <label for="fecha">Seleccionar fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
        <button type="submit">Generar Cierre</button>
    </form>

 <?php if(isset($mensaje)): ?>
    <div style="text-align: center; color: green; font-weight: bold;">
        <p><?= htmlspecialchars($mensaje) ?></p>
        <ul style="list-style: none; padding: 0;">
            <li>Ingresadas: <?= $totales['Ingresado'] ?></li>
            <li>En revisión: <?= $totales['En revisión'] ?></li>
            <li>Reparadas: <?= $totales['Reparado'] ?></li>
            <li>Entregadas: <?= $totales['Entregado'] ?></li>
            <li>Total órdenes: <?= $totales['total_ordenes'] ?></li>
            <li>Total recaudado: $<?= number_format($totales['total_recaudado'], 2) ?></li>
        </ul>
    </div>
<?php endif; ?>
    <!-- Historial de cierres -->
    
   <div class="table-container">
    <h3>Historial de Cierres</h3>
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
                <tr>
                    <td colspan="7">No hay cierres registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
   </div>
</main>

<?php include __DIR__ . '/partial/footer.php'; ?>
