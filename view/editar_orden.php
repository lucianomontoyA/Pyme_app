<?php
session_start();
$_SESSION['pagina_actual'] = 'editar_orden';

require_once '../config/database.php';
include 'partial/header.php';
require_once '../model/cliente.php';
require_once '../model/orden.php';

// Instanciar modelos
$clienteModel = new Cliente($pdo);
$ordenModel   = new Orden($pdo);

// Obtener ID de la orden
$orden_id = $_GET['id'] ?? $_POST['orden_id'] ?? null;
if (!$orden_id) {
    die("ID de orden no especificado.");
}

$orden = $ordenModel->obtener($orden_id);
$cliente = $clienteModel->obtener($orden['cliente_id']);

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_orden'])) {
    $clienteModel->actualizar(
        $cliente['id'],
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['email'] ?? null,
        $_POST['telefono'] ?? null,
        $_POST['direccion'] ?? null,
        $_POST['cuit'] ?? null
    );

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

    $mensaje = "✅ La orden y su estado se actualizaron correctamente.";
    $orden = $ordenModel->obtener($orden_id);
    $cliente = $clienteModel->obtener($orden['cliente_id']);
}
?>

<h2>Editar Orden</h2>

<!-- ===== Talón de la Orden ===== -->
<style>
#talon {
    width: 320px;
    padding: 15px;
    border: 2px dashed #0072ff;
    border-radius: 8px;
    margin: 20px auto;
    text-align: center;
}

/* ===== Impresión ===== */
@media print {
    body * { visibility: hidden; } /* ocultar todo */
    #talon, #talon * { visibility: visible; }

    #talon {
        position: relative;
        left: 0;
        transform: none;
        margin: 0 auto 20mm auto;
        page-break-inside: avoid;
    }

    button { display: none; } /* ocultar botón al imprimir */
}

/* ===== Estilos del Formulario ===== */
.form-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.form-column {
    flex: 1;
    min-width: 300px;
}

.form-row-full {
    width: 100%;
}

form fieldset {
    border: 1px solid #ccc;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

form label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
}

form input[type="text"],
form input[type="email"],
form input[type="number"],
form textarea {
    width: 100%;
    padding: 7px;
    margin-top: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

form textarea {
    resize: vertical;
}

form button {
    background-color: #0072ff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

form button:hover {
    background-color: #005bb5;
}

@media (max-width: 768px) {
    .form-container {
        flex-direction: column;
    }
}
</style>

<div id="talon">
    <h3>Talón de Orden</h3>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></p>
    <p><strong>Código:</strong> <?= htmlspecialchars($orden['codigo_publico']) ?></p>
    <button type="button" onclick="imprimirTalones();" style="padding:5px 10px; margin-top:10px;">Imprimir Talones</button>
</div>

<?php if (isset($mensaje)): ?>
    <p style="color:green; font-weight:bold; text-align:center;"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<form action="" method="post">
    <input type="hidden" name="orden_id" value="<?= htmlspecialchars($orden_id) ?>">

    <div class="form-container">
        <!-- ===== Columna Cliente ===== -->
        <div class="form-column">
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
        </div>

        <!-- ===== Columna Orden ===== -->
        <div class="form-column">
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

                <label for="observaciones">Resolución / Observaciones:</label>
                <textarea name="observaciones" id="observaciones"><?= htmlspecialchars($orden['observaciones'] ?? '') ?></textarea>
            </fieldset>
        </div>
    </div>

    <!-- ===== Fila de Estado y Total ===== -->
    <div class="form-row-full">
        <fieldset>
            <legend>Estado y Total</legend>
            <p>Aca podrás controlar el flujo de las órdenes cambiándolas de estado:</p>
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
                <?php
                $estados = ['Ingresado','En revisión','Reparado','Entregado'];
                foreach ($estados as $estado) {
                    $checked = ($orden['estado'] === $estado) ? 'checked' : '';
                    echo "<label style='display:flex; align-items:center; gap:5px;'>
                            <input type='radio' name='estado' value='$estado' $checked>
                            $estado
                          </label>";
                }
                ?>
            </div>

            <label for="total">Total:</label>
            <input type="number" step="0.01" name="total" id="total" value="<?= htmlspecialchars($orden['total']) ?>">

            <button type="submit" name="guardar_orden" style="margin-top:10px;">Guardar Cambios</button>
        </fieldset>
    </div>
</form>

<script>
// Clonar talón y abrir ventana de impresión
function imprimirTalones() {
    const talon = document.getElementById('talon');
    const clon = talon.cloneNode(true); // clonar todo el contenido
    clon.removeChild(clon.querySelector('button')); // quitar botón del clon

    // Crear contenedor temporal solo para imprimir
    const contenedorImpresion = document.createElement('div');
    contenedorImpresion.appendChild(talon);
    contenedorImpresion.appendChild(clon);

    // Abrir ventana de impresión
    const ventana = window.open('', 'Imprimir', 'width=800,height=600');
    ventana.document.write('<html><head><title>Imprimir Talones</title>');
    ventana.document.write('<style>');
    ventana.document.write('body{ font-family: Arial; text-align:center; }');
    ventana.document.write('.talon{ width:320px; border:2px dashed #0072ff; border-radius:8px; padding:15px; margin:10px auto; }');
    ventana.document.write('</style></head><body>');
    ventana.document.write(contenedorImpresion.innerHTML);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    ventana.print();
    ventana.close();
}
</script>

<?php include 'partial/footer.php'; ?>
