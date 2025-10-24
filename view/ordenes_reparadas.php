<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']); // superadmin y técnico pueden ver

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['pagina_actual'] = 'ordenes_reparadas.php';



require_once '../model/Orden.php';
require_once '../model/Cliente.php';
require_once '../config/database.php'; // $pdo

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);

// Obtener todas las órdenes con estado "Reparado"
$ordenes = $ordenModel->listar(); // Listar todas
$ordenes_reparadas = array_filter($ordenes, fn($o) => $o['estado'] === 'Reparado');
?>

<?php include 'partial/header.php'; ?>

<h2>Órdenes Reparadas</h2>
<div class="table-container"> 
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
            <th>Acciones</th>
            
            
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($ordenes_reparadas)) : ?>
            <?php foreach ($ordenes_reparadas as $orden) : 
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
                <td>$<?= number_format($orden['total'], 2) ?></td>
                <td><?= htmlspecialchars($orden['codigo_publico']) ?></td>
                <td><?= date("d/m/Y H:i", strtotime($orden['fecha_creacion'])) ?></td>
                <td class="acciones">
                        <a href="ver_detalle.php?id=<?= $orden['id'] ?>" class="btn ver">Ver</a>
                        <a href="editar_orden.php?id=<?= $orden['id'] ?>" class="btn editar">Editar</a>
                        <a href="borrar_orden.php?id=<?= $orden['id'] ?>" class="btn borrar" onclick="return confirm('¿Seguro que deseas borrar esta orden?')">Borrar</a>
                    </td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="12">No hay órdenes reparadas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php include 'partial/footer.php'; ?>
