<?php

echo "entro<br>";

echo "DIR vale: " .realpath( __DIR__ ). "<br>"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $destino = trim($_POST['destino']);

    $origen = "lucianomontoya@moyan.dev";
    $asunto = "Mensaje automático";
    $mensaje = "Hola.";

    // === ARCHIVO ADJUNTO FIJO ===
    $archivo_ruta = __DIR__ ."/tito.jpg";  // tu archivo
    $archivo_nombre = basename($archivo_ruta);

    // Leer y codificar archivo
    $contenido_archivo = chunk_split(base64_encode(file_get_contents($archivo_ruta)));

    // Boundary único
    $boundary = md5(uniqid(time()));

    // Headers
    $headers  = "From: $origen\r\n";
    $headers .= "Reply-To: $origen\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // Cuerpo del mail
    $cuerpo  = "--$boundary\r\n";
    $cuerpo .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $cuerpo .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $cuerpo .= "$mensaje\r\n\r\n";

    // Parte del archivo adjunto
    $cuerpo .= "--$boundary\r\n";
    $cuerpo .= "Content-Type: image/jpeg; name=\"$archivo_nombre\"\r\n";
    $cuerpo .= "Content-Transfer-Encoding: base64\r\n";
    $cuerpo .= "Content-Disposition: attachment; filename=\"$archivo_nombre\"\r\n\r\n";
    $cuerpo .= "$contenido_archivo\r\n";
    $cuerpo .= "--$boundary--";

    // Enviar
    if (mail($destino, $asunto, $cuerpo, $headers)) {
        echo "Mail enviado correctamente.";
    } else {
        echo "Error al enviar el mail.";
    }

    exit;
}
