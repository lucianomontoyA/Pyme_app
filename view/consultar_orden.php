<?php
require_once '../config/database.php';
require_once '../model/orden.php';
require_once '../model/cliente.php';

// 🔒 Si querés que esté abierto al público, comenta esta línea
// require_once __DIR__ . '/../config/auth.php';
// checkRole(['superadmin']);

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);

$orden = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo_publico'] ?? '');

    if (!empty($codigo)) {
        // Buscar la orden por código público (sin filtrar por estado)
        $sql = "SELECT o.*, c.nombre, c.apellido
                FROM ordenes o
                JOIN clientes c ON o.cliente_id = c.id
                WHERE o.codigo_publico = :codigo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        $orden = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$orden) {
            $error = "No se encontró ninguna orden con ese código.";
        }
    } else {
        $error = "Por favor ingresa un código público.";
    }
}
?>

<?php include 'partial/header.php'; ?>

<h2>Consultar Estado de Orden</h2>

<form method="post" autocomplete="off">
    <label for="codigo_publico">Ingrese su Código Público</label>
    <input type="text" id="codigo_publico" name="codigo_publico" required>
    <button type="submit">Consultar</button>
</form>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($orden): ?>
    <h3>Detalles de la Orden</h3>
    <div class="table-container"> 
    <table>
        <tr><th>Cliente</th><td><?= htmlspecialchars($orden['nombre'] . ' ' . $orden['apellido']) ?></td></tr>
        <tr><th>Código Público</th><td><strong><?= htmlspecialchars($orden['codigo_publico']) ?></strong></td></tr>
        <tr><th>Equipo</th><td><?= htmlspecialchars($orden['equipo']) ?></td></tr>
        <tr><th>Marca</th><td><?= htmlspecialchars($orden['marca']) ?></td></tr>
        <tr><th>Modelo</th><td><?= htmlspecialchars($orden['modelo']) ?></td></tr>
        <tr><th>Serie</th><td><?= htmlspecialchars($orden['serie']) ?></td></tr>
        <tr><th>Problema Reportado</th><td><?= nl2br(htmlspecialchars($orden['problema_reportado'])) ?></td></tr>
        <tr><th>Observaciones</th><td><?= nl2br(htmlspecialchars($orden['observaciones'])) ?></td></tr>
        <tr><th>Estado</th><td><?= htmlspecialchars($orden['estado']) ?></td></tr>
        <tr><th>Total</th><td>$<?= number_format($orden['total'], 2) ?></td></tr>
        <tr><th>Fecha de Creación</th><td><?= date("d/m/Y H:i", strtotime($orden['fecha_creacion'])) ?></td></tr>
    </table>
    </div>
<?php endif; ?>

<?php include 'partial/footer.php'; ?>
