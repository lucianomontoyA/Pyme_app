<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /view/login.php");
        exit;
    }
}

function checkRole(array $rolesPermitidos) {
    checkLogin(); // Asegurarnos de que esté logueado
    if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
        // Opcional: mostrar mensaje de error o redirigir
        echo "<p style='color:red; text-align:center;'>No tienes permisos para acceder a esta página.</p>";
        exit;
    }
}
?>
