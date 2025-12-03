<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $destino = trim($_POST['destino']);

    $origen = "lucianomontoya@moyan.dev";
    $asunto = "Mensaje automático";
    $mensaje = "Hola.";

    $headers = "From: $origen\r\n";
    $headers .= "Reply-To: $origen\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($destino, $asunto, $mensaje, $headers)) {
        echo "Mail enviado correctamente.";
    } else {
        echo "Error al enviar el mail.";
    }
}
