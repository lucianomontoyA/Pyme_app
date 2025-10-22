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
        <nav>
             <a href="/index.php">inicio</a>
            <a href="/view/crear_orden.php">Nueva Orden</a>
            <a href="/view/crear_usuario.php" class="btn-cierre">Crear Tecnico</a>
            <a href="/view/ver_orden.php">Ver Órdenes</a>
            <a href="/view/ordenes_finalizadas.php" class="btn-finalizadas">Órdenes Finalizadas</a>
            <a href="/view/cierre_diario.php" class="btn-cierre">Cierre Diario</a>
             <a href="/view/consultar_orden.php">Consultar Orden</a>
            
            
            <?php if(isset($_SESSION['usuario_id'])): ?>
            <a href="/view/logout.php" class="logout-button" style="color: red;">Cerrar sesión</a>
            <?php endif; ?>
            
        </nav>
    </header>
    <main>
