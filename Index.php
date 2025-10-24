<?php
require_once __DIR__ . '/config/auth.php';
checkLogin();


// index.php en la raíz
include __DIR__ . '/view/partial/header.php';
?>

<main class="home-container">
    <h2>Bienvenido a Servicio Técnico</h2>
    <p>Usá el menú para crear o ver órdenes.</p>

    <div class="menu-cards">
        <a href="view/crear_orden.php" class="card">
            <h3>Nueva Orden</h3>
            <p>Registrar un nuevo ingreso de equipo.</p>
        </a>

        <a href="view/ver_orden.php" class="card">
            <h3>Ver Órdenes</h3>
            <p>Consultar todas las órdenes activas.</p>
        </a>
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'tecnico'): ?>
        <a href="view/ordenes_finalizadas.php" class="card card-finalizadas">
            <h3>Órdenes Finalizadas</h3>
            <p>Revisar órdenes entregadas.</p>
        </a>
             <?php endif; ?>
    </div>




</main>

<?php
include __DIR__ . '/view/partial/footer.php';
?>
