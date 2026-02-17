<?php

require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']);

require_once '../model/orden.php';
require_once '../model/cliente.php';
require_once '../config/database.php';

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);

$sql = "SELECT o.*, c.nombre, c.apellido
        FROM ordenes o
        JOIN clientes c ON o.cliente_id = c.id
        WHERE o.estado = 'Entregado'
        ORDER BY o.fecha_finalizacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$ordenes_entregadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'partial/header.php'; ?>

<!-- FILTRO -->
<div>
    <h3 style="text-align: center; margin-top: 20px;">Filtros</h3>
    <form style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
        <input 
            type="text" 
            id="filtroGeneral" 
            placeholder="Buscar por cualquier campo..." 
            onkeyup="filtrarTabla()" 
            style="padding: 6px 10px; flex: 1; min-width: 200px;"
        >
        
        <label>Desde:</label>
        <input type="date" id="fechaInicio" onchange="filtrarTabla()">

        <label>Hasta:</label>
        <input type="date" id="fechaFin" onchange="filtrarTabla()">
    </form>
</div>

<script>
function filtrarTabla() {
    const textoFiltro = filtroGeneral.value.toLowerCase();
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    document.querySelectorAll('table tbody tr').forEach(fila => {
        let textoFila = fila.innerText.toLowerCase();

        // Fecha Finalización → columna índice 6
        const fechaTexto = fila.cells[6].textContent.trim();
        const fechaOrden = fechaTexto !== '–'
            ? fechaTexto.split(" ")[0].split("/").reverse().join("-")
            : '';

        let mostrar = true;

        if (textoFiltro && !textoFila.includes(textoFiltro)) {
            mostrar = false;
        }

        if (fechaInicio && fechaOrden && fechaOrden < fechaInicio) {
            mostrar = false;
        }

        if (fechaFin && fechaOrden && fechaOrden > fechaFin) {
            mostrar = false;
        }

        fila.style.display = mostrar ? '' : 'none';
    });
}
</script>

<h2>Órdenes Finalizadas</h2>

<div class="table-container">
<table>
<thead>
<tr>
    <th>Cliente</th>
    <th>Equipo</th>
    <th>Estado</th>
    <th>Total</th>
    <th>Código</th>
    <th>Ingreso</th>
    <th>Finalización</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>
<?php if (!empty($ordenes_entregadas)) : ?>
<?php foreach ($ordenes_entregadas as $orden) : 
    $cliente = $clienteModel->obtener($orden['cliente_id']);
?>
<tr>
    <!-- Cliente -->
    <td>
        <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
    </td>

    <!-- Equipo -->
    <td>
        <?= htmlspecialchars($orden['equipo']) ?><br>
        <small>
            <?= htmlspecialchars($orden['marca'] . ' ' . $orden['modelo']) ?>
        </small>
    </td>

    <td><?= htmlspecialchars($orden['estado']) ?></td>
    <td><?= htmlspecialchars($orden['total']) ?></td>
    <td><?= htmlspecialchars($orden['codigo_publico']) ?></td>
    <td><?= htmlspecialchars($orden['fecha_creacion']) ?></td>
    <td><?= htmlspecialchars($orden['fecha_finalizacion'] ?? '–') ?></td>

    <td class="acciones">
        <a href="ver_detalle.php?id=<?= $orden['id'] ?>" class="btn ver">Remito</a>
        <a href="editar_orden.php?id=<?= $orden['id'] ?>" class="btn editar">Editar</a>
            </td>
</tr>
<?php endforeach; ?>
<?php else : ?>
<tr>
    <td colspan="8">No hay órdenes finalizadas.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

<?php include 'partial/footer.php'; ?>
