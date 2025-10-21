<?php 
include 'partial/header.php'; 

require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin','tecnico']); //  

// Obtenemos el ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'] ?? null;
?>

<h2>Crear Nueva Orden</h2>

<form action="/controller/orden_controller.php" method="post">
    <fieldset>
        <legend>Datos del Cliente</legend>
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" id="apellido" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono">
    </fieldset>

    <fieldset>
        <legend>Datos del Equipo</legend>
        <label for="equipo">Equipo:</label>
        <input type="text" name="equipo" id="equipo" required>

        <label for="marca">Marca:</label>
        <input type="text" name="marca" id="marca">

        <label for="modelo">Modelo:</label>
        <input type="text" name="modelo" id="modelo">

        <label for="serie">Serie:</label>
        <input type="text" name="serie" id="serie">

        <label for="problema_reportado">Problema Reportado:</label>
        <textarea name="problema_reportado" id="problema_reportado"></textarea>
    </fieldset>

    <!-- Campo oculto para enviar el ID del usuario -->
    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">

    <button type="submit" name="crear_orden">Crear Orden</button>
</form>

<?php include 'partial/footer.php'; ?>
