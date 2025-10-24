<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicio Técnico</title>
    <link rel="stylesheet" href="/style/style.css">
</head>
<body>
   
    
  <header>
    <h1>Servicio Técnico</h1>
    <button class="menu-toggle" onclick="toggleMenu()">☰ Menú</button>

    <?php if(isset($_SESSION['usuario_id'])): ?>
        <p class="welcome-msg">
            ¡Bienvenido <?= ucfirst($_SESSION['rol']) ?>, <?= htmlspecialchars($_SESSION['nombre']) ?> !
        </p>
    <?php endif; ?>

  
    <nav id="nav-menu">
        <a href="/index.php">Inicio</a>
        <a href="/view/crear_orden.php">Nueva Orden</a>

        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'superadmin'): ?>
           
            <div class="dropdown">
                <button class="dropbtn" onclick="if(window.innerWidth <= 768){this.parentElement.classList.toggle('open');}">Órdenes ▼</button>
                <div class="dropdown-content">
                    <a href="/view/ver_orden.php">Ver órdenes en curso</a>
                    <a href="/view/ordenes_reparadas.php">Órdenes Reparadas</a>
                    <a href="/view/ordenes_finalizadas.php">Órdenes Finalizadas</a>
                    <a href="/view/consultar_orden.php">Consultar estado de Orden</a>
                </div>
            </div>
             <a href="/view/crear_usuario.php">Crear Técnico</a>

            <a href="/view/cierre_diario.php">Cierre Diario</a>
        <?php endif; ?>
            <br>
        <a href="/view/logout.php" class="logout-button" style="color:red;">Cerrar sesión</a>
    </nav>
</header>



<script>
function toggleMenu() {
    const nav = document.getElementById('nav-menu');
    nav.classList.toggle('show');
}
</script>

</body>