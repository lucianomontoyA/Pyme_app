<?php
session_start();
$_SESSION['pagina_actual'] = 'crear_cliente';

require_once '../config/database.php';
require_once '../model/usuario.php';
include 'partial/header.php';
require_once '../model/cliente.php';

// Instanciar Cliente
$clienteModel = new Cliente($pdo);

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_cliente'])) {
    try {
        $cliente_id = $clienteModel->crear(
            $_POST['nombre'] ?? '',
            $_POST['apellido'] ?? '',
            $_POST['email'] ?? null,
            $_POST['telefono'] ?? null,
            $_POST['direccion'] ?? null,
            $_POST['cuit'] ?? null
        );

        $_SESSION['mensaje'] = "Cliente creado correctamente.";
        header("Location: ver_cliente.php"); // Cambiá esto si querés otra página
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<h2>Crear Nuevo Cliente</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form action="" method="post">
    <fieldset>
        <legend>Datos del Cliente</legend>

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" id="apellido" required>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion">

        <label for="cuit">CUIT / NIF:</label>
        <input type="text" name="cuit" id="cuit">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono">
    </fieldset>

    <button type="submit" name="crear_cliente">Crear Cliente</button>
</form>

<?php include 'partial/footer.php'; ?>
