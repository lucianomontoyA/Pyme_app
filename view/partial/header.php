<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- ✅ Necesario para que el responsive funcione en celulares -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Servicio Técnico</title>
    <!-- ✅ Forzamos actualización del CSS en caso de caché -->
    <link rel="stylesheet" href="/style/style.css?v=<?= time() ?>">
</head>
<body>

<header>
     <div class="header-top">
        <img src="/img/logo.png" alt="Logo" class="logo">
        <h1>Servicio Técnico</h1>
    </div>

    <?php if(isset($_SESSION['usuario_id'])): ?>
        <button class="menu-toggle" onclick="toggleMenu()">≡ Menú</button>

        <p class="welcome-msg">
            ¡Bienvenido <?= ucfirst($_SESSION['rol']) ?>, <?= htmlspecialchars($_SESSION['nombre']) ?>!
        </p>

        <nav id="nav-menu">
            <a href="/index.php">Inicio</a>
            <a href="/view/crear_orden.php">Nueva Orden</a>

            <?php if($_SESSION['rol'] === 'superadmin'): ?>
                <div class="dropdown">
                    <button class="dropbtn">Clientes ▼</button>
                    <div class="dropdown-content">
                        <a href="/view/ver_cliente.php">Ver Clientes</a>
                        <a href="/view/crear_cliente.php">Nuevo Cliente</a>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="dropbtn">Órdenes ▼</button>
                    <div class="dropdown-content">
                        <a href="/view/ver_orden.php">Ver órdenes en curso</a>
                        <a href="/view/ordenes_reparadas.php">Órdenes Reparadas</a>
                        <a href="/view/ordenes_finalizadas.php">Órdenes Finalizadas</a>
                        <a href="/view/consultar_orden.php">Consultar estado de Orden</a>
                        <a href="/view/crear_usuario.php">Crear Técnico</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] !== 'tecnico'): ?>
                <div class="dropdown cierres">
                    <button class="dropbtn">Cierres ▼</button>
                    <div class="dropdown-content">
                        <a href="/view/cierre_diario.php">Cierre Diario</a>
                        <a href="/view/cierres_diarios.php">Cierres Históricos</a>
                    </div>
                </div>
            <?php endif; ?>

            <a href="/view/logout.php" class="logout-button" style="color:red;">Cerrar sesión</a>
        </nav>
    <?php endif; ?>
</header>


<script>
function toggleMenu() {
    const nav = document.getElementById('nav-menu');
    nav.classList.toggle('show');
}
</script>

</body>
</html>
