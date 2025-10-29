<?php
session_start();
$_SESSION['pagina_actual'] = 'editar_orden';

include 'partial/header.php';
require_once '../model/Cliente.php';
require_once '../model/Orden.php';

// Configuración PDO
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
$clienteModel = new Cliente($pdo);
$ordenModel   = new Orden($pdo);

// Obtener ID de la orden (por GET o POST)
$orden_id = $_GET['id'] ?? $_POST['orden_id'] ?? null;
if (!$orden_id) {
    die("ID de orden no especificado.");
}

$orden = $ordenModel->obtener($orden_id);
$cliente = $clienteModel->obtener($orden['cliente_id']);

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_orden'])) {
    // Actualizar datos del cliente
    $clienteModel->actualizar(
        $cliente['id'],
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['email'] ?? null,
        $_POST['telefono'] ?? null,
        $_POST['direccion'] ?? null,
        $_POST['cuit'] ?? null
    );

    // Actualizar datos de la orden
    $ordenModel->actualizar(
        $orden_id,
        $_POST['equipo'],
        $_POST['marca'] ?? null,
        $_POST['modelo'] ?? null,
        $_POST['serie'] ?? null,
        $_POST['problema_reportado'] ?? null,
        $_POST['observaciones'] ?? null, 
        $_POST['estado'] ?? 'Ingresado',
        $_POST['total'] ?? 0.00
    );

    header("Location: ver_orden.php?id=" . $orden_id);
    exit;
}
?>

<h2>Editar Orden</h2>

<form action="" method="post">
    <input type="hidden" name="orden_id" value="<?= htmlspecialchars($orden_id) ?>">

    <fieldset>
        <legend>Datos del Cliente</legend>
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" required>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" id="apellido" value="<?= htmlspecialchars($cliente['apellido']) ?>" required>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">

        <label for="cuit">CUIT / NIF:</label>
        <input type="text" name="cuit" id="cuit" value="<?= htmlspecialchars($cliente['cuit'] ?? '') ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($cliente['email']) ?>">

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($cliente['telefono']) ?>">
    </fieldset>

    <fieldset>
        <legend>Datos del Equipo</legend>
        <label for="equipo">Equipo:</label>
        <input type="text" name="equipo" id="equipo" value="<?= htmlspecialchars($orden['equipo']) ?>" required>

        <label for="marca">Marca:</label>
        <input type="text" name="marca" id="marca" value="<?= htmlspecialchars($orden['marca']) ?>">

        <label for="modelo">Modelo:</label>
        <input type="text" name="modelo" id="modelo" value="<?= htmlspecialchars($orden['modelo']) ?>">

        <label for="serie">Serie:</label>
        <input type="text" name="serie" id="serie" value="<?= htmlspecialchars($orden['serie']) ?>">

        <label for="problema_reportado">Problema Reportado:</label>
        <textarea name="problema_reportado" id="problema_reportado"><?= htmlspecialchars($orden['problema_reportado']) ?></textarea>

        <label for="observaciones">Resolución / Observaciones :</label>
        <textarea name="observaciones" id="observaciones"><?= htmlspecialchars($orden['observaciones'] ?? '') ?></textarea>

        <label for="estado">Estado:</label>
        <select name="estado" id="estado">
            <?php
            $estados = ['Ingresado','En revisión','Reparado','Entregado'];
            foreach ($estados as $estado) {
                $selected = ($orden['estado'] === $estado) ? 'selected' : '';
                echo "<option value=\"$estado\" $selected>$estado</option>";
            }
            ?>
        </select>

        <label for="total">Total:</label>
        <input type="number" step="0.01" name="total" id="total" value="<?= htmlspecialchars($orden['total']) ?>">
    </fieldset>

    <button type="submit" name="guardar_orden">Guardar Cambios</button>
</form>

<?php include 'partial/footer.php'; ?>
