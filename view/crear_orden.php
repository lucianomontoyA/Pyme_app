<?php 
session_start();
$_SESSION['pagina_actual'] = 'crear_orden';

require_once '../config/database.php';
include 'partial/header.php';
require_once '../model/cliente.php';

// Instanciar Cliente
$clienteModel = new Cliente($pdo);
$clientes = $clienteModel->listar();

$usuario_id = $_SESSION['usuario_id'] ?? null;
?>

<h2>Crear Nueva Orden</h2>

<form action="/controller/orden_controller.php" method="post">
    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">

    <div class="form-container">
        <!-- Columna Cliente -->
        <div class="form-column">
            <fieldset>
                <legend>Cliente</legend>

                <div class="form-row">
                    <label for="tipo_cliente">Tipo de cliente:</label>
                    <select id="tipo_cliente" name="tipo_cliente" onchange="toggleCliente()">
                        <option value="nuevo">Nuevo cliente</option>
                        <option value="existente">Cliente existente</option>
                    </select>
                </div>

                <!-- Cliente existente -->
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

                <!-- Cliente nuevo -->
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
                        <input type="text" name="direccion" id="direccion">
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
        </div>

        <!-- Columna Equipo -->
        <div class="form-column">
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
                <div class="form-row form-row-full">
                    <label for="problema_reportado">Problema Reportado:</label>
                    <textarea name="problema_reportado" id="problema_reportado" rows="3"></textarea>
                </div>
            </fieldset>
        </div>
    </div>

    <!-- Botón Crear Orden -->
    <div style="text-align:center;">
        <button type="submit" name="crear_orden">Crear Orden</button>
    </div>
</form>

<style>
/* ===== Formulario ===== */
.form-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.form-column {
    flex: 1;
    min-width: 300px;
}

.form-row-full { width: 100%; }

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
form textarea,
form select {
    width: 100%;
    padding: 7px;
    margin-top: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

form textarea { resize: vertical; }

form button {
    padding: 8px 15px;
    background-color: #0072ff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

form button:hover { background-color: #005bb5; }

@media (max-width: 768px) {
    .form-container { flex-direction: column; }
}
</style>

<script>
// Toggle cliente nuevo/existente
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

// Autocompletado cliente existente
const inputBuscar = document.getElementById('cliente_buscar');
const selectCliente = document.getElementById('cliente_id');
let clientesData = [];

inputBuscar.addEventListener('input', function() {
    const query = this.value.trim();
    if (query.length < 2) {
        selectCliente.innerHTML = '<option value="">-- Seleccionar cliente --</option>';
        clientesData = [];
        return;
    }

    fetch(`/controller/buscar_clientes.php?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            clientesData = data;
            selectCliente.innerHTML = '<option value="">-- Seleccionar cliente --</option>';
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${c.nombre} ${c.apellido} (${c.email || 'sin email'})`;
                selectCliente.appendChild(opt);
            });
        })
        .catch(err => console.error(err));
});

selectCliente.addEventListener('change', function() {
    const selected = clientesData.find(c => c.id == this.value);
    if (selected) {
        document.getElementById('nombre').value = selected.nombre;
        document.getElementById('apellido').value = selected.apellido;
        document.getElementById('email').value = selected.email || '';
        document.getElementById('direccion').value = selected.direccion || '';
        document.getElementById('telefono').value = selected.telefono || '';
        document.getElementById('cuit').value = selected.cuit || '';
    }
});
</script>

<?php include 'partial/footer.php'; ?>
