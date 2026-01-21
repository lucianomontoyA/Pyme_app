<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura / Remito</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        p {
            margin: 3px 0;
        }

        .orden-container {
            width: 100%;
            padding: 20px;
        }

        .titulo {
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .remito-copy {
            border: 2px solid #000;
            padding: 20px;
            border-radius: 8px;
        }

        /* ===== HEADER ===== */
        .remito-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .remito-header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
        }

        .remito-header p {
            font-size: 12px;
        }

        /* ===== SECTIONS ===== */
        .section {
            margin-bottom: 15px;
        }

        .section h3 {
            font-size: 14px;
            margin-bottom: 6px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        /* ===== COLUMNS ===== */
        .flex-col {
            width: 48%;
            float: left;
        }

        .flex-col.right {
            float: right;
        }

        .clear {
            clear: both;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 15px 0;
        }

        .firma {
            margin-top: 30px;
            text-align: center;
        }

        .firma p {
            font-weight: bold;
            margin: 0;
        }
    </style>
</head>

<body>

<div class="orden-container">

<?php for ($i = 0; $i < 2; $i++): ?>

    <div class="titulo">Remito  de Servicio</div>

    <div class="remito-copy">

        <!-- EMISOR -->
        <div class="remito-header">
            <h1><?= htmlspecialchars($emisor['nombre']) ?></h1>
            
            <p><?= htmlspecialchars($emisor['direccion']) ?></p>
            <p><?= htmlspecialchars($emisor['condicion_iva']) ?></p>
            
        </div>

        <!-- CLIENTE -->
        <div class="flex-col">
            <div class="section">
                <h3>Cliente</h3>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($cliente['direccion'] ?? '-') ?></p>
                <p><strong>CUIT / CUIL:</strong> <?= htmlspecialchars($cliente['cuit'] ?? '-') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($cliente['email'] ?? '-') ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono'] ?? '-') ?></p>
            </div>
        </div>

        <!-- ORDEN -->
        <div class="flex-col right">
            <div class="section">
                <h3>Orden</h3>
                <p><strong>Código:</strong> <?= htmlspecialchars($orden['codigo_publico']) ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($orden['estado']) ?></p>
                <p><strong>Total:</strong> $<?= number_format($orden['total'], 2) ?></p>
                <p><strong>Fecha creación:</strong> <?= htmlspecialchars($orden['fecha_creacion']) ?></p>
                <p><strong>Fecha revisión:</strong> <?= htmlspecialchars($orden['fecha_revision'] ?? '-') ?></p>
                <p><strong>Fecha reparación:</strong> <?= htmlspecialchars($orden['fecha_reparacion'] ?? '-') ?></p>
                <p><strong>Fecha finalización:</strong> <?= htmlspecialchars($orden['fecha_finalizacion'] ?? '-') ?></p>
            </div>
        </div>

        <div class="clear"></div>

        <!-- EQUIPO -->
        <div class="section">
            <h3>Equipo</h3>
            <p><strong>Equipo:</strong> <?= htmlspecialchars($orden['equipo']) ?></p>
            <p>
                <strong>Marca / Modelo / Serie:</strong>
                <?= htmlspecialchars($orden['marca'] ?? '-') ?> /
                <?= htmlspecialchars($orden['modelo'] ?? '-') ?> /
                <?= htmlspecialchars($orden['serie'] ?? '-') ?>
            </p>
            <p><strong>Problema reportado:</strong> <?= htmlspecialchars($orden['problema_reportado'] ?? '-') ?></p>
            <p><strong>Observaciones:</strong> <?= htmlspecialchars($orden['observaciones'] ?? '-') ?></p>
        </div>

        <hr>

        <div class="firma">
            <p>______________________________</p>
            <p>Firma Cliente / Técnico</p>
        </div>

    </div>

    <?php if ($i === 0): ?>
        <div style="page-break-after: always;"></div>
    <?php endif; ?>

<?php endfor; ?>

</div>

</body>
</html>
