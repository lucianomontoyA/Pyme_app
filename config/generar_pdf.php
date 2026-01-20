<?php
require_once __DIR__ . '/../config/auth.php';
checkRole(['superadmin']);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/orden.php';
require_once __DIR__ . '/../model/cliente.php';

use Dompdf\Dompdf;

// =======================
// VALIDACIÓN
// =======================
if (!isset($_GET['id'])) {
    die('ID de orden no especificado');
}

$orden_id = $_GET['id'];


// =======================
// MODELOS
// =======================
$ordenModel   = new Orden($pdo);
$clienteModel = new Cliente($pdo);

$orden = $ordenModel->obtener($orden_id);
if (!$orden) {
    die('Orden no encontrada');
}

$cliente = $clienteModel->obtener($orden['cliente_id']);

// =======================
// EMISOR
// =======================
$emisor = [
    'nombre'        => 'Alejandro Castellini',
    'cuit'          => '20-12345678-9',
    'direccion'     => 'Av. Colón 1234, Mar del Plata, Buenos Aires',
    'condicion_iva' => 'Responsable Inscripto',
    'telefono'      => '+54 9 2235247644',
    'email'         => 'javierp89@outlook.es'
];

// =======================
// HTML → PDF
// =======================
ob_start();
include __DIR__ . '/../view/ver_factura.php';
$html = ob_get_clean();

$dompdf = new Dompdf([
    'isRemoteEnabled' => true
]);

$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream(
    'factura_' . $orden['codigo_publico'] . '.pdf',
    ['Attachment' => false]
);
