<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']);

require_once '../model/orden.php';
require_once '../model/cliente.php';
require_once '../config/database.php';

$ordenModel   = new Orden($pdo);
$clienteModel = new Cliente($pdo);

if (!isset($_GET['id'])) {
    die("ID de orden no especificado.");
}

$orden_id = $_GET['id'];

$orden    = $ordenModel->obtener($orden_id);

if (!$orden) {
    die("Orden no encontrada.");
}

$cliente = $clienteModel->obtener($orden['cliente_id']);

/* ==========================================
   Datos del emisor (luego pueden ir a la BD)
========================================== */
$emisor = [
    'nombre'        => 'Alejandro Castellini',
    'cuit'          => '20-12345678-9',
    'direccion'     => 'Av. Colón 1234, Mar del Plata, Buenos Aires',
    'condicion_iva' => 'Responsable Inscripto',
    'telefono'      => '+54 9 2235247644',
    'email'         => 'javierp89@outlook.es'
];
?>

<?php include 'partial/header.php'; ?>

<style>
/* Solo visible al imprimir */
.print-only {
    display: none;
}

@media print {
    .print-only {
        display: block;
    }
}





/* =========================
   BOTONES
========================= */
.print-button {
    margin-bottom: 20px;
    padding: 10px 15px;
    background-color: #007BFF;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}

.print-button:hover {
    background-color: #0056b3;
    color: #fff;
}

.print-button.success {
    background-color: #28a745;
}

.print-button.success:hover {
    background-color: #1e7e34;
}

/* =========================
   CONTENEDOR
========================= */
.orden-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

/* =========================
   REMITO
========================= */
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
    color: #e7e7e7;
    font-size: 22px;
    text-transform: uppercase;
}

.section {
    margin-bottom: 15px;
}

.section p {
    margin: 5px 0;
    font-size: 14px;
}

hr {
    border: 1px dashed #aaa;
    margin: 15px 0;
}

.firma {
    margin-top: 30px;
    text-align: center;
}

.firma p {
    margin: 0;
    font-weight: bold;
}

/* =========================
   LAYOUT
========================= */
.flex-row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.flex-col {
    width: 48%;
}

/* =========================
   ENCABEZADO
========================= */
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

/* LOGO */
.remito-logo {
    width: 120px;
    display: block;
    margin: 0 auto 10px auto;
    border-radius: 8px;
}

/* =========================
   IMPRESIÓN
========================= */
@media print {
    .print-button,
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

    body {
        margin: 0 !important;
        background: #fff !important;
    }

    .remito-copy {
        page-break-inside: avoid;
    }
}
</style>

<div class="orden-container">

    <button onclick="window.print()" class="print-button">
        Imprimir Remito
    </button>
    <button class="print-button success"
        onclick="window.location.href='/config/enviar_mail.php?id=<?= urlencode($orden['id']) ?>'">
         Enviar factura por mail
    </button>
    <?php for ($i = 0; $i < 2; $i++): ?>
    <div class="remito-copy <?= $i === 1 ? 'print-only' : '' ?>">

        <div class="remito-copy">

            <h2>Remito  de Servicio</h2>

            <div class="remito-header">
                <img src="/img/logo.png" alt="Logo" class="remito-logo">
                <h1><?= htmlspecialchars($emisor['nombre']) ?></h1>
                <p><strong>CUIT:</strong> <?= htmlspecialchars($emisor['cuit']) ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($emisor['direccion']) ?></p>
                <p><strong>Condición IVA:</strong> <?= htmlspecialchars($emisor['condicion_iva']) ?></p>
                <p>
                    <strong>Tel:</strong> <?= htmlspecialchars($emisor['telefono']) ?> |
                    <strong>Email:</strong> <?= htmlspecialchars($emisor['email']) ?>
                </p>
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
                        <p><strong>Total:</strong> $<?= number_format($orden['total'], 2) ?></p>
                        <p><strong>Fecha creación:</strong> <?= htmlspecialchars($orden['fecha_creacion']) ?></p>
                        <p><strong>Fecha revisión:</strong> <?= htmlspecialchars($orden['fecha_revision'] ?? '-') ?></p>
                        <p><strong>Fecha reparación:</strong> <?= htmlspecialchars($orden['fecha_reparacion'] ?? '-') ?></p>
                        <p><strong>Fecha finalización:</strong> <?= htmlspecialchars($orden['fecha_finalizacion'] ?? '-') ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>Equipo</h3>
                <p><strong>Equipo:</strong> <?= htmlspecialchars($orden['equipo']) ?></p>
                <p>
                    <strong>Marca / Modelo / Serie:</strong>
                    <?= htmlspecialchars($orden['marca'] ?? '-') ?> /
                    <?= htmlspecialchars($orden['modelo'] ?? '-') ?> /
                    <?= htmlspecialchars($orden['serie'] ?? '-') ?>
                </p>
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

<?php include 'partial/footer.php'; ?>
