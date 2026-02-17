<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['pagina_actual'] = 'ver_clientes';

include 'partial/header.php';
require_once '../model/cliente.php';
require_once '../config/database.php';

// Instanciar modelo Cliente
$clienteModel = new Cliente($pdo);
$ordenesCliente = [];
$clienteSeleccionado = null;

// Obtener todos los clientes
$clientes = $clienteModel->listar();



// ===============================
// 👉 SI SE SELECCIONÓ UN CLIENTE
// ===============================
if (isset($_GET['cliente_id']) && !empty($_GET['cliente_id'])) {

    $cliente_id = $_GET['cliente_id'];

    // 1️⃣ TRAER DATOS DEL CLIENTE
    $stmt = $pdo->prepare("SELECT nombre, apellido FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    $clienteSeleccionado = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2️⃣ TRAER ÓRDENES DEL CLIENTE
    $stmt = $pdo->prepare("
        SELECT id, equipo, problema_reportado, total, estado, fecha_creacion
        FROM ordenes
        WHERE cliente_id = ?
        ORDER BY fecha_creacion DESC
    ");
    $stmt->execute([$cliente_id]);
    $ordenesCliente = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!-- 🔎 FILTROS -->
<div>
    <h3 style="text-align: center; margin-top: 20px;">Filtros</h3>
    <form style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
        <input type="text" id="filtroGeneral" placeholder="Buscar por cualquier campo..." 
               onkeyup="filtrarTabla()" 
               style="padding: 6px 10px; flex: 1; min-width: 200px;">
    </form>
</div>







<h2>Clientes Registrados</h2>
<div class="table-container"> 
<div class="table-container-scroll">
<table>
<thead>
<tr>
    <th>Nombre</th>
    <th>Apellido</th>
    <th>Email</th>
    <th>Teléfono</th>
    <th>Dirección</th>
    <th>CUIT</th>
    <th>Fecha de Registro</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>
<?php if (!empty($clientes)): ?>
    <?php foreach ($clientes as $cliente): ?>
        <tr>
            <td><?= htmlspecialchars($cliente['nombre']) ?></td>
            <td><?= htmlspecialchars($cliente['apellido']) ?></td>
            <td><?= htmlspecialchars($cliente['email']) ?></td>
            <td><?= htmlspecialchars($cliente['telefono']) ?></td>
            <td><?= htmlspecialchars($cliente['direccion']) ?></td>
            <td><?= htmlspecialchars($cliente['cuit']) ?></td>
            <td><?= date("d/m/Y H:i", strtotime($cliente['fecha_creacion'])) ?></td>
            <td>
                <a href="?cliente_id=<?= $cliente['id'] ?>" class="btn-ver">
                    📋 Ver órdenes
                </a>
                <a href="editar_cliente.php?id=<?= $cliente['id'] ?>" class="btn-editar">
                    ✏️ Editar
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="8">No hay clientes registrados</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("filtroGeneral");
    const tablaClientes = document.querySelector(".table-container-scroll table tbody");
    const filas = tablaClientes.querySelectorAll("tr");

    // 👉 Mostrar solo los últimos 5 al cargar
    mostrarUltimos5();

    function mostrarUltimos5() {
        filas.forEach((fila, index) => {
            fila.style.display = index < 5 ? "" : "none";
        });
    }

    // 👉 BUSCADOR REAL
    window.filtrarTabla = function () {
        const textoFiltro = input.value.toLowerCase().trim();

        // Si no hay texto → volver a últimos 5
        if (textoFiltro === "") {
            mostrarUltimos5();
            return;
        }

        filas.forEach(fila => {
            let textoFila = fila.innerText.toLowerCase();
            fila.style.display = textoFila.includes(textoFiltro) ? "" : "none";
        });
    };

});
</script>



<?php if ($clienteSeleccionado): ?>
<hr style="margin:40px 0">

<h2>
📋 Órdenes de <?= htmlspecialchars($clienteSeleccionado['nombre'].' '.$clienteSeleccionado['apellido']) ?>
</h2>

<div class="table-container">
<table>
<thead>
<tr>
    <th>Cliente</th>
    <th>Equipo</th>
    <th>Falla</th>
    <th>Total</th>
    <th>Estado</th>
    <th>Fecha ingreso</th>
</tr>
</thead>

<tbody>
<?php if (!empty($ordenesCliente)): ?>
    <?php foreach ($ordenesCliente as $orden): ?>
        <tr>
            <td><?= htmlspecialchars($clienteSeleccionado['nombre'].' '.$clienteSeleccionado['apellido']) ?></td>
            <td><?= htmlspecialchars($orden['equipo']) ?></td>
            <td><?= htmlspecialchars($orden['problema_reportado']) ?></td>
            <td>$<?= number_format($orden['total'],2) ?></td>
            <td><?= htmlspecialchars($orden['estado']) ?></td>
            <td><?= date("d/m/Y", strtotime($orden['fecha_creacion'])) ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6">Este cliente aún no tiene órdenes</td></tr>
<?php endif; ?>
</tbody>

</table>
</div>
<?php endif; ?>
