<?php
session_start();
$_SESSION['pagina_actual'] = 'editar_cliente';

require_once '../config/database.php';
require_once '../model/cliente.php';
include 'partial/header.php';

// Instanciar modelo
$clienteModel = new Cliente($pdo);

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de cliente no válido.");
}

$id = $_GET['id'];

// Obtener cliente existente
$cliente = $clienteModel->obtener($id);

if (!$cliente) {
    die("Cliente no encontrado.");
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_cliente'])) {
    try {
        $clienteModel->actualizar(
            $id,
            $_POST['nombre'] ?? '',
            $_POST['apellido'] ?? '',
            $_POST['email'] ?? null,
            $_POST['telefono'] ?? null,
            $_POST['direccion'] ?? null,
            $_POST['cuit'] ?? null
        );

        $_SESSION['mensaje'] = "Cliente actualizado correctamente.";
        header("Location: ver_cliente.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<h2>Editar Cliente</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form action="" method="post">
    <fieldset>
        <legend>Datos del Cliente</legend>

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required
               value="<?= htmlspecialchars($cliente['nombre']) ?>">

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" id="apellido" required
               value="<?= htmlspecialchars($cliente['apellido']) ?>">

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion"
               value="<?= htmlspecialchars($cliente['direccion']) ?>">

        <label for="cuit">CUIT / NIF:</label>
        <input type="text" name="cuit" id="cuit"
               value="<?= htmlspecialchars($cliente['cuit']) ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email"
               value="<?= htmlspecialchars($cliente['email']) ?>">

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono"
               value="<?= htmlspecialchars($cliente['telefono']) ?>">
    </fieldset>

    <button type="submit" name="editar_cliente">Guardar Cambios</button>
</form>

<?php include 'partial/footer.php'; ?>
