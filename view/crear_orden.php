<?php 
session_start();
$_SESSION['pagina_actual'] = 'crear_orden';

require_once '../config/database.php';
include 'partial/header.php';
require_once '../model/cliente.php';

// ----------------------
// Instanciar Cliente
// ----------------------
$cliente = new Cliente($pdo);
$clientes = $cliente->listar();

$usuario_id = $_SESSION['usuario_id'] ?? null;
?>

<h2 style="text-align:center; margin-bottom:20px;">Crear Nueva Orden</h2>

<form action="/controller/orden_controller.php" method="post" class="form-grid">
    <!-- ============================= -->
    <!-- SECCIÓN CLIENTE -->
    <!-- ============================= -->
    <fieldset>
        <legend>Cliente</legend>

        <div class="form-row">
            <label for="tipo_cliente">Tipo de cliente:</label>
            <select id="tipo_cliente" name="tipo_cliente" onchange="toggleCliente()">
                <option value="nuevo">Nuevo cliente</option>
                <option value="existente">Cliente existente</option>
            </select>
        </div>

        <!-- CLIENTE EXISTENTE -->
        <div id="cliente_existente" style="display:none; margin-top:10px;">
            <div class="form-row">
                <label for="cliente_buscar">Buscar cliente:</label>
                <input type="text" id="cliente_buscar" placeholder="Escribí nombre, apellido o email..." autocomplete="off">
            </div>
            <div class="form-row">
                <label for="cliente_id">Seleccionar cliente:</label>
                <select name="cliente_id" id="cliente_id">
                    <option value="">-- Seleccionar cliente --</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- CLIENTE NUEVO -->
        <div id="cliente_nuevo">
            <div class="form-row">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>

            <div class="form-row">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" required>
            </div>

            <div class="form-row">
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" required>
            </div>

            <div class="form-row">
                <label for="cuit">CUIT / NIF:</label>
                <input type="text" name="cuit" id="cuit">
            </div>

            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email">
            </div>

            <div class="form-row">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono">
            </div>
        </div>
    </fieldset>

    <!-- ============================= -->
    <!-- SECCIÓN EQUIPO -->
    <!-- ============================= -->
    <fieldset>
        <legend>Datos del Equipo</legend>

        <div class="form-row">
            <label for="equipo">Equipo:</label>
            <input type="text" name="equipo" id="equipo" required>
        </div>

        <div class="form-row">
            <label for="marca">Marca:</label>
            <input type="text" name="marca" id="marca">
        </div>

        <div class="form-row">
            <label for="modelo">Modelo:</label>
            <input type="text" name="modelo" id="modelo">
        </div>

        <div class="form-row">
            <label for="serie">Serie:</label>
            <input type="text" name="serie" id="serie">
        </div>

        <div class="form-row full">
            <label for="problema_reportado">Problema Reportado:</label>
            <textarea name="problema_reportado" id="problema_reportado" rows="3"></textarea>
        </div>
    </fieldset>

    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">

    <div class="form-actions">
        <button type="submit" name="crear_orden">Crear Orden</button>
    </div>

</form>



<script>
// ----------------------
// Toggle Cliente Nuevo/Existente
// ----------------------
function toggleCliente() {
    const tipo = document.getElementById('tipo_cliente').value;
    const nuevo = document.getElementById('cliente_nuevo');
    const existente = document.getElementById('cliente_existente');

    if (tipo === 'existente') {
        existente.style.display = 'block';
        nuevo.style.display = 'none';
        nuevo.querySelectorAll('input').forEach(i => i.required = false);
    } else {
        existente.style.display = 'none';
        nuevo.style.display = 'block';
        nuevo.querySelectorAll('input').forEach(i => i.required = true);
    }
}

// ----------------------
// Autocompletado Cliente Existente
// ----------------------
const inputBuscar = document.getElementById('cliente_buscar');
const selectCliente = document.getElementById('cliente_id');

let timeout = null;

inputBuscar.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value.trim();

    if (query.length < 2) {
        selectCliente.innerHTML = '<option value="">-- Seleccionar cliente --</option>';
        return;
    }

    timeout = setTimeout(() => {
        fetch(`/controller/buscar_clientes.php?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                selectCliente.innerHTML = '<option value="">-- Seleccionar cliente --</option>';
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.nombre} ${c.apellido} (${c.email || 'sin email'})`;
                    selectCliente.appendChild(opt);
                });

                if (data.length === 1) {
                    selectCliente.selectedIndex = 1;
                }
            })
            .catch(err => console.error(err));
    }, 300);
});

selectCliente.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected && selected.value) {
        inputBuscar.value = selected.textContent;
    }
});
</script>

<?php include 'partial/footer.php'; ?>