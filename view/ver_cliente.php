<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['pagina_actual'] = 'ver_clientes';

include 'partial/header.php';
require_once '../model/Cliente.php';

// Configurar PDO (igual que en tu controller)
$host = 'localhost';
$db = 'servicio_tecnico';
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

// Instanciar modelo Cliente
$clienteModel = new Cliente($pdo);

// Obtener todos los clientes
$clientes = $clienteModel->listar();
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

<script>
function filtrarTabla() {
    const textoFiltro = document.getElementById('filtroGeneral').value.toLowerCase();
    const filas = document.querySelectorAll('table tbody tr');

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        let textoFila = '';

        // Concatenamos todas las celdas
        celdas.forEach(td => {
            textoFila += td.textContent.toLowerCase() + ' ';
        });

        fila.style.display = textoFila.includes(textoFiltro) ? '' : 'none';
    });
}
</script>


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
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clientes)): ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr data-fecha="<?= htmlspecialchars(date("Y-m-d", strtotime($cliente['fecha_creacion']))) ?>">
                        <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                        <td><?= htmlspecialchars($cliente['apellido']) ?></td>
                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                        <td><?= htmlspecialchars($cliente['direccion']) ?></td>
                        <td><?= htmlspecialchars($cliente['cuit']) ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($cliente['fecha_creacion'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No hay clientes registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>