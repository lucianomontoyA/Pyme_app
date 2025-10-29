<?php

require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']); // solo superadmin

require_once '../model/orden.php';
require_once '../model/cliente.php';
require_once '../config/database.php'; // $pdo

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);

// Traer todas las órdenes con estado "Entregado"
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

<!-- FILTRO INICIO -->
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
        
        <label for="fechaInicio">Desde:</label>
        <input type="date" id="fechaInicio" onchange="filtrarTabla()">

        <label for="fechaFin">Hasta:</label>
        <input type="date" id="fechaFin" onchange="filtrarTabla()">
    </form>
</div>

<script>
function filtrarTabla() {
    const textoFiltro = document.getElementById('filtroGeneral').value.toLowerCase();
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    const filas = document.querySelectorAll('table tbody tr');

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        let textoFila = '';

        // Concatenamos el texto de todas las celdas (excepto Acciones)
        for (let i = 0; i < celdas.length - 1; i++) {
            textoFila += celdas[i].textContent.toLowerCase() + ' ';
        }

        // Columna 15 (índice 14) = fecha_finalizacion
        const fecha = fila.cells[14].textContent.trim();
        const fechaOrden = fecha !== '–' ? fecha.split(" ")[0].split("/").reverse().join("-") : '';

        let mostrar = true;

        // 🔍 Filtrar por texto
        if (textoFiltro && !textoFila.includes(textoFiltro)) {
            mostrar = false;
        }

        // 📅 Filtrar por fechas
        if (fechaInicio && fechaOrden && fechaOrden < fechaInicio) {
            mostrar = false;
        }
        if (fechaFin && fechaOrden && fechaOrden > fechaFin) {
            mostrar = false;
        }

        fila.style.display = mostrar ? "" : "none";
    });
}
</script>
<!-- FILTRO FIN -->

<h2>Órdenes Finalizadas</h2>
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
            <th>Fecha de ingreso</th>
            <th>Fecha Revisión</th>
            <th>Fecha Reparación</th>
            <th>Fecha Finalización</th>
            <th>Acciones</th>
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
                <td><?= htmlspecialchars($orden['fecha_revision'] ?? '–') ?></td>
                <td><?= htmlspecialchars($orden['fecha_reparacion'] ?? '–') ?></td>
                <td><?= htmlspecialchars($orden['fecha_finalizacion'] ?? '–') ?></td>
                <td class="acciones">
                    <a href="ver_detalle.php?id=<?= $orden['id'] ?>" class="btn ver">Ver</a>
                    <a href="editar_orden.php?id=<?= $orden['id'] ?>" class="btn editar">Editar</a>
                    <a href="borrar_orden.php?id=<?= $orden['id'] ?>" class="btn borrar" onclick="return confirm('¿Seguro que deseas borrar esta orden?')">Borrar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="16">No hay órdenes finalizadas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    </table>
</div>
<?php include 'partial/footer.php'; ?>
