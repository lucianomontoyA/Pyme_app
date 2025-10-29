<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['pagina_actual'] = 'ver_orden';

include 'partial/header.php';
require_once '../model/Orden.php';
require_once '../model/Cliente.php';

// Configurar PDO (igual que en tu controller)
$host = 'localhost';
$db   = 'servicio_tecnico';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Instanciar modelos
$ordenModel = new Orden($pdo);

// Obtener todas las órdenes
$ordenes = $ordenModel->listar();

// Filtrar: solo mostrar órdenes que NO estén entregadas
$ordenes = array_filter($ordenes, function($o) {
    $estado = trim($o['estado']); // eliminar espacios al inicio y fin
    return in_array($estado, ['Ingresado', 'En revisión'], true);
});
?>

<!-- FILTRO INICIO -->

<!-- 🔎 Filtros -->
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

        const fecha = fila.cells[1].textContent.trim(); // columna fecha
        const partes = fecha.split(" ");
        const fechaOrden = partes[0].split("/").reverse().join("-"); // dd/mm/yyyy → yyyy-mm-dd

        let mostrar = true;

        // 🔍 Filtrar por texto (busca en todas las columnas)
        if (textoFiltro && !textoFila.includes(textoFiltro)) {
            mostrar = false;
        }

        // 📅 Filtrar por fechas
        if (fechaInicio && fechaOrden < fechaInicio) {
            mostrar = false;
        }
        if (fechaFin && fechaOrden > fechaFin) {
            mostrar = false;
        }

        fila.style.display = mostrar ? "" : "none";
    });
}
</script>





<!-- FILTRO FIN -->


<h2>Órdenes Registradas</h2>
<div class="table-container"> 
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Fecha de Ingreso</th>
            <th>Equipo</th>
            <th>Problema Reportado</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Código Público</th> <!-- NUEVA COLUMNA -->
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($ordenes)): ?>
            <?php foreach ($ordenes as $orden): ?>
                <tr>
                    <td><?= htmlspecialchars($orden['cliente_nombre'] . ' ' . $orden['cliente_apellido']) ?></td>

                    <td><?= date("d/m/Y H:i", strtotime($orden['fecha_creacion'])) ?></td>
                    <td><?= htmlspecialchars($orden['equipo']) ?></td>
                    <td><?= nl2br(htmlspecialchars($orden['problema_reportado'])) ?></td>
                    <td>
                        <span class="estado <?= strtolower(str_replace(' ', '_', $orden['estado'])) ?>">
                            <?= htmlspecialchars($orden['estado']) ?>
                        </span>
                    </td>
                    <td>$<?= number_format($orden['total'], 2) ?></td>
                    <td><?= htmlspecialchars($orden['codigo_publico']) ?></td> <!-- MOSTRAR CODIGO -->
                 <td class="acciones">
  <a href="ver_detalle.php?id=<?= $orden['id'] ?>" class="btn ver">Ver</a>
  <a href="editar_orden.php?id=<?= $orden['id'] ?>" class="btn editar">Editar</a>
  <a href="borrar_orden.php?id=<?= $orden['id'] ?>" class="btn borrar" onclick="return confirm('¿Seguro que deseas borrar esta orden?')">Borrar</a>
</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No hay órdenes registradas</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>