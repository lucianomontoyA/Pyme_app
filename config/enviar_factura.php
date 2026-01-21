<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "🟢 Arranca enviar_factura.php<br>";

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
    die('❌ ID de orden no especificado');
}

$orden_id = $_GET['id'];
echo "🟢 ID recibido: $orden_id<br>";

// =======================
// MODELOS
// =======================
$ordenModel   = new Orden($pdo);
$clienteModel = new Cliente($pdo);

$orden = $ordenModel->obtener($orden_id);
if (!$orden) {
    die('❌ Orden no encontrada');
}
echo "🟢 Orden OK<br>";

$cliente = $clienteModel->obtener($orden['cliente_id']);
if (!$cliente) {
    die('❌ Cliente no encontrado');
}
echo "🟢 Cliente OK<br>";

// =======================
// EMISOR
// =======================
$emisor = [
    'nombre'        => 'Alejandro Castellini',
    'cuit'          => '20-12345678-9',
    'direccion'     => 'Av. Colón 1234, Mar del Plata, Buenos Aires',
    'condicion_iva' => 'Responsable Inscripto',
    'telefono'      => '+54 9 2235247644',
    'email'         => 'lucianomontoya@moyan.dev'
];

// =======================
// GENERAR HTML
// =======================
echo "🟢 Generando HTML del PDF...<br>";

ob_start();
include __DIR__ . '/../view/ver_factura.php';
$html = ob_get_clean();

if (empty($html)) {
    die('❌ HTML vacío');
}

echo "🟢 HTML generado (" . strlen($html) . " bytes)<br>";

// =======================
// PDF
// =======================
echo "🟢 Generando PDF...<br>";

$dompdf = new Dompdf(['isRemoteEnabled' => true]);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dir = __DIR__ . '/../storage/facturas';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$pdfPath = $dir . '/factura_' . $orden['codigo_publico'] . '.pdf';
file_put_contents($pdfPath, $dompdf->output());

if (!file_exists($pdfPath)) {
    die('❌ PDF no se guardó');
}

echo "🟢 PDF guardado correctamente<br>";
echo "✅ FIN OK";
