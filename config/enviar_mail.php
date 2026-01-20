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
if (!$cliente || empty($cliente['email'])) {
    die('Cliente sin email');
}

// =======================
// EMISOR
// =======================
$origen = "lucianomontoya@moyan.dev";

// =======================
// GENERAR PDF
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

// PDF en memoria
$pdfContenido = chunk_split(base64_encode($dompdf->output()));
$nombrePdf = 'factura_' . $orden['codigo_publico'] . '.pdf';

// =======================
// MAIL
// =======================
$destino = $cliente['email'];
$asunto  = 'Factura / Remito - Orden ' . $orden['codigo_publico'];

$mensaje = "Hola {$cliente['nombre']},\n\n".
           "Adjuntamos la factura correspondiente a su orden.\n\n".
           "Gracias.";

// Boundary
$boundary = md5(uniqid(time()));

// Headers
$headers  = "From: $origen\r\n";
$headers .= "Reply-To: $origen\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

// Cuerpo
$cuerpo  = "--$boundary\r\n";
$cuerpo .= "Content-Type: text/plain; charset=UTF-8\r\n";
$cuerpo .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$cuerpo .= "$mensaje\r\n\r\n";

// Adjunto PDF
$cuerpo .= "--$boundary\r\n";
$cuerpo .= "Content-Type: application/pdf; name=\"$nombrePdf\"\r\n";
$cuerpo .= "Content-Transfer-Encoding: base64\r\n";
$cuerpo .= "Content-Disposition: attachment; filename=\"$nombrePdf\"\r\n\r\n";
$cuerpo .= "$pdfContenido\r\n";
$cuerpo .= "--$boundary--";

// =======================
// ENVIAR
// =======================
if (mail($destino, $asunto, $cuerpo, $headers)) {
    echo "✅ Factura enviada correctamente a $destino";
} else {
    echo "❌ Error al enviar la factura";
}
