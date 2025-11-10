<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']); // solo superadmin

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['pagina_actual'] = 'cambiar_estado';

require_once '../config/database.php';
include 'partial/header.php';
require_once '../model/orden.php';

$ordenModel = new Orden($pdo);

// Obtener ID de la orden
$orden_id = $_GET['id'] ?? $_POST['orden_id'] ?? null;
if (!$orden_id) {
    die("ID de orden no especificado.");
}

$orden = $ordenModel->obtener($orden_id);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_estado'])) {
    $nuevo_estado = $_POST['estado'] ?? $orden['estado'];
    $nuevo_total = $_POST['total'] ?? $orden['total'];
    $observaciones = $_POST['observaciones'] ?? $orden['observaciones'];

    // ✅ actualizamos estado, total y observaciones
    $stmt = $pdo->prepare("UPDATE ordenes 
                           SET estado = :estado, total = :total, observaciones = :observaciones 
                           WHERE id = :id");
    $stmt->execute([
        ':estado' => $nuevo_estado,
        ':total' => $nuevo_total,
        ':observaciones' => $observaciones,
        ':id' => $orden_id
    ]);

    // Actualizar fechas automáticas (igual que en el modelo)
  if ($nuevo_estado === 'En revisión') {
    $stmt = $pdo->prepare("UPDATE ordenes 
                           SET fecha_revision = IF(fecha_revision IS NULL, NOW(), fecha_revision)
                           WHERE id = :id");
    $stmt->execute([':id' => $orden_id]);
} elseif ($nuevo_estado === 'Reparado') {
    $stmt = $pdo->prepare("UPDATE ordenes 
                           SET fecha_reparacion = IF(fecha_reparacion IS NULL, NOW(), fecha_reparacion)
                           WHERE id = :id");
    $stmt->execute([':id' => $orden_id]);
} elseif ($nuevo_estado === 'Entregado') {
    $stmt = $pdo->prepare("UPDATE ordenes 
                           SET fecha_finalizacion = IF(fecha_finalizacion IS NULL, NOW(), fecha_finalizacion)
                           WHERE id = :id");
    $stmt->execute([':id' => $orden_id]);
}


    $mensaje = "✅ Estado, total y observaciones actualizados correctamente.";
    $orden = $ordenModel->obtener($orden_id); // refrescar datos
}
?>

<h2 style="text-align:center;">Cambiar Estado de la Orden</h2>

<?php if (isset($mensaje)): ?>
    <p style="color:green; font-weight:bold; text-align:center;"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<form method="post" style="max-width:600px;margin:20px auto;">
    <input type="hidden" name="orden_id" value="<?= htmlspecialchars($orden_id) ?>">

    <fieldset style="border:1px solid #ccc;padding:15px;border-radius:8px;">
        <legend>Estado y Total</legend>
        <p>Aquí podés actualizar el estado, total y observaciones de la orden:</p>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <?php
            $estados = ['Ingresado','En revisión','Reparado','Entregado'];
            foreach ($estados as $estado) {
                $checked = ($orden['estado'] === $estado) ? 'checked' : '';
                echo "<label style='display:flex;align-items:center;gap:5px;'>
                        <input type='radio' name='estado' value='$estado' $checked> $estado
                      </label>";
            }
            ?>
        </div>

        <label for="total">Total:</label>
        <input type="number" step="0.01" name="total" id="total"
               value="<?= htmlspecialchars($orden['total']) ?>"
               style="width:100%;padding:7px;margin-top:5px;margin-bottom:15px;">

        <label for="observaciones">Resolución / Observaciones:</label>
        <textarea name="observaciones" id="observaciones" rows="5"
                  style="width:100%;padding:7px;margin-top:5px;"><?= htmlspecialchars($orden['observaciones'] ?? '') ?></textarea>

        <button type="submit" name="guardar_estado"
                style="margin-top:15px;background:#0072ff;color:#fff;padding:10px 15px;border:none;border-radius:4px;cursor:pointer;">
            Guardar Cambios
        </button>
        <a href="ver_orden.php" style="margin-left:10px;color:#0072ff;">← Volver</a>
    </fieldset>
</form>

<?php include 'partial/footer.php'; ?>
