<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']);

require_once '../model/Orden.php';
require_once '../model/Cliente.php';
require_once '../config/database.php';

$ordenModel = new Orden($pdo);
$clienteModel = new Cliente($pdo);

if (!isset($_GET['id'])) die("ID de orden no especificado.");

$orden_id = $_GET['id'];
$orden = $ordenModel->obtener($orden_id);
if (!$orden) die("Orden no encontrada.");

$cliente = $clienteModel->obtener($orden['cliente_id']);

/* ==========================================
   Datos del emisor (pueden venir de BD luego)
========================================== */
$emisor = [
    'nombre' => 'Alejandro Castellini',
    'cuit' => '20-12345678-9',
    'direccion' => 'Av. Colón 1234, Mar del Plata, Buenos Aires',
    'condicion_iva' => 'Responsable Inscripto',
    'telefono' => '+54 9 2235247644',
    'email' => 'javierp89@outlook.es'
];
?>

<?php include 'partial/header.php'; ?>

<style>
/* =========================
   Estilos Remito / PDF
========================= */
.orden-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.print-button {
    margin-bottom: 20px;
    padding: 10px 15px;
    background-color: #007BFF;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.print-button:hover {
    background-color: #0056b3;
}

/* Cada copia del remito */
.remito-copy {
    border: 2px solid #333;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 10px;
    page-break-inside: avoid;
}

.remito-copy h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #1e1e2f;
    font-size: 22px;
    text-transform: uppercase;
}

.remito-copy .section {
    margin-bottom: 15px;
}

.remito-copy .section p {
    margin: 5px 0;
    font-size: 14px;
}

.remito-copy hr {
    border: 1px dashed #aaa;
    margin: 15px 0;
}

.remito-copy .firma {
    margin-top: 30px;
    text-align: center;
}

.remito-copy .firma p {
    margin: 0;
    font-weight: bold;
}

/* Layout columnas de cliente / equipo */
.remito-copy .flex-row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.remito-copy .flex-row .flex-col {
    width: 48%;
}

/* Encabezado emisor */
.remito-header {
    text-align: center;
    margin-bottom: 25px;
}

.remito-header h1 {
    font-size: 20px;
    margin: 0;
    text-transform: uppercase;
}

.remito-header p {
    margin: 3px 0;
    font-size: 13px;
}

/* === LOGO EN FACTURA / REMITO === */
.remito-logo {
    width: 120px;
    height: auto;
    display: block;
    margin: 0 auto 10px auto;
    border-radius: 8px;
    filter: drop-shadow(0 0 6px rgba(0, 0, 0, 0.3));
}

/* Para impresión también se ve nítido */
@media print {
    .remito-logo {
        max-width: 120px;
        filter: none;
    }
}



/* PDF / impresión */
@media print {
    .print-button { display: none; }
    body { margin: 0; }
     header,
    nav,
    .menu,
    .navbar,
    .user-info,
    .sidebar,
    .footer,
    .topbar {
        display: none !important;
    }

    /* Ajustes del body para impresión limpia */
    body {
        margin: 0 !important;
        background: #fff !important;
    }

    /* Evita que se corte el remito entre páginas */
    .remito-copy {
        page-break-inside: avoid;
    }
}

</style>

<div class="orden-container">
    <button onclick="window.print()" class="print-button">Imprimir Remito</button>
   

    <div class="remito">
        <?php for ($i = 0; $i < 2; $i++): ?>
             <h2>Remito / Factura de Servicio</h2>
            <div class="remito-copy">
            
            <div class="remito-header">
                 <img src="/img/logo.png" alt="Logo" class="remito-logo">
                <h1><?= htmlspecialchars($emisor['nombre']) ?></h1>
                <p><strong>CUIT:</strong> <?= htmlspecialchars($emisor['cuit']) ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($emisor['direccion']) ?></p>
                <p><strong>Condición IVA:</strong> <?= htmlspecialchars($emisor['condicion_iva']) ?></p>
                <p><strong>Tel:</strong> <?= htmlspecialchars($emisor['telefono']) ?> | <strong>Email:</strong> <?= htmlspecialchars($emisor['email']) ?></p>
            </div>

           

            <div class="flex-row">
                <div class="flex-col">
                    <div class="section">
                        <h3>Cliente</h3>
                        <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></p>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($cliente['direccion'] ?? '-') ?></p>
                        <p><strong>CUIT / CUIL:</strong> <?= htmlspecialchars($cliente['cuit'] ?? '-') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($cliente['email'] ?? '-') ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex-col">
                    <div class="section">
                        <h3>Orden</h3>
                        <p><strong>Código:</strong> <?= htmlspecialchars($orden['codigo_publico']) ?></p>
                        <p><strong>Estado:</strong> <?= htmlspecialchars($orden['estado']) ?></p>
                        <p><strong>Total:</strong> $<?= htmlspecialchars(number_format($orden['total'], 2)) ?></p>
                        <p><strong>Fecha Recogida:</strong> <?= htmlspecialchars($orden['fecha_creacion']) ?></p>
                        <p><strong>Fecha revisión:</strong> <?= htmlspecialchars($orden['fecha_revision'] ?? '-') ?></p>
                        <p><strong>Fecha reparación:</strong> <?= htmlspecialchars($orden['fecha_reparacion'] ?? '-') ?></p>
                        <p><strong>Fecha finalización:</strong> <?= htmlspecialchars($orden['fecha_finalizacion'] ?? '-') ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>Equipo</h3>
                <p><strong>Equipo:</strong> <?= htmlspecialchars($orden['equipo']) ?></p>
                <p><strong>Marca / Modelo / Serie:</strong> <?= htmlspecialchars($orden['marca'] ?? '-') ?> / <?= htmlspecialchars($orden['modelo'] ?? '-') ?> / <?= htmlspecialchars($orden['serie'] ?? '-') ?></p>
                <p><strong>Problema reportado:</strong> <?= htmlspecialchars($orden['problema_reportado'] ?? '-') ?></p>
                <p><strong>Observaciones / Resolución:</strong> <?= htmlspecialchars($orden['observaciones'] ?? '-') ?></p>
            </div>

            <hr>
            <div class="firma">
                <p>__________________________</p>
                <p>Firma Cliente / Técnico</p>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>


<script>
// Espera a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btnPrint');
    const remito = document.getElementById('remito');

    if (btn && remito) {
        btn.addEventListener('click', function() {
            // Guarda el contenido original
            const originalContent = document.body.innerHTML;

            // Reemplaza el body solo con el div del remito
            const printContent = remito.outerHTML;
            document.body.innerHTML = printContent;

            // Lanza la impresión
            window.print();

            // Restaura el contenido original
            document.body.innerHTML = originalContent;

            // Recarga para restaurar scripts y estilos
            location.reload();
        });
    } else {
        console.error("No se encontró el botón o el div del remito.");
    }
});
</script>
<?php include 'partial/footer.php'; ?>
