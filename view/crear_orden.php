<?php 
session_start();
$_SESSION['pagina_actual'] = 'crear_orden';

require_once '../config/database.php';
include 'partial/header.php';
require_once '../model/Cliente.php';


// ----------------------
// Instanciar Cliente
// ----------------------
$cliente = new Cliente($pdo);
$clientes = $cliente->listar(); // Últimos 10 clientes para select inicial

// ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'] ?? null;
?>

<h2>Crear Nueva Orden</h2>

<form action="/controller/orden_controller.php" method="post">
    <fieldset>
        <legend>Cliente</legend>

        <label for="tipo_cliente">Tipo de cliente:</label>
        <select id="tipo_cliente" name="tipo_cliente" onchange="toggleCliente()">
            <option value="nuevo">Nuevo cliente</option>
            <option value="existente">Cliente existente</option>
        </select>

        <!-- CLIENTE EXISTENTE -->
        <div id="cliente_existente" style="display:none; margin-top:10px;">
            <label for="cliente_buscar">Buscar cliente:</label>
            <input type="text" id="cliente_buscar" placeholder="Escribí nombre, apellido o email..." autocomplete="off">

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

        <!-- CLIENTE NUEVO -->
        <div id="cliente_nuevo">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required>

            <label for="direccion">Dirección:</label>
            <input type="text" name="direccion" id="direccion" required>

            <label for="cuit">CUIT / NIF:</label>
            <input type="text" name="cuit" id="cuit">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email">

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono">
        </div>
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

    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">

    <button type="submit" name="crear_orden">Crear Orden</button>
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

    // Esperar 300ms para no hacer demasiadas peticiones
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

                // Si solo hay un resultado, se selecciona automáticamente
                if (data.length === 1) {
                    selectCliente.selectedIndex = 1;
                }
            })
            .catch(err => console.error(err));
    }, 300);
});

// Autocompletar el input al cambiar select
selectCliente.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected && selected.value) {
        inputBuscar.value = selected.textContent;
    }
});

</script>

<?php include 'partial/footer.php'; ?>
