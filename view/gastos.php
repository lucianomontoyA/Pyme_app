<?php
session_start();
require_once '../config/database.php';

// Solo superadmin puede acceder
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'superadmin') {
    header("Location: /index.php");
    exit;
}

$error = '';
$success = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo = trim($_POST['tipo']);
    $descripcion = trim($_POST['descripcion']);
    $monto = trim($_POST['monto']);
    $fecha = trim($_POST['fecha']) ?: date("Y-m-d"); // 👉 Si no viene fecha, usar hoy

    if ($tipo === '' || $monto === '') {
        $error = "El tipo de gasto y el monto son obligatorios.";
    } elseif (!is_numeric($monto)) {
        $error = "El monto debe ser un número válido.";
    } else {
        try {
            // ⚠️ Cuando crees la tabla, reemplazá por tu INSERT real
            
            $stmt = $pdo->prepare("INSERT INTO gastos (tipo, descripcion, monto, fecha) VALUES (?, ?, ?, ?)");
            $stmt->execute([$tipo, $descripcion, $monto, $fecha]);
            
            $success = "Gasto registrado correctamente.";

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<?php include 'partial/header.php'; ?>

<h2>Cargar Gastos Diarios</h2>

<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post" autocomplete="off">

    <label for="tipo">Tipo de Gasto</label>
    <select id="tipo" name="tipo" required>
        <option value="">Seleccionar</option>
        <option value="insumos" <?= (($_POST['tipo'] ?? '') == 'insumos') ? 'selected' : '' ?>>Insumos</option>
        <option value="transporte" <?= (($_POST['tipo'] ?? '') == 'transporte') ? 'selected' : '' ?>>Gasto de transporte</option>
        <option value="viaticos" <?= (($_POST['tipo'] ?? '') == 'viaticos') ? 'selected' : '' ?>>Viáticos</option>
        <option value="otros" <?= (($_POST['tipo'] ?? '') == 'otros') ? 'selected' : '' ?>>Otros</option>
    </select>

    <label for="fecha">Fecha del gasto (opcional)</label>
    <input type="date" id="fecha" name="fecha" value="<?= $_POST['fecha'] ?? '' ?>">


    <label for="descripcion">Descripción (opcional)</label>
    <textarea id="descripcion" name="descripcion" rows="3"><?= $_POST['descripcion'] ?? '' ?></textarea>

    <label for="monto">Monto</label>
    <input type="text" id="monto" name="monto" value="<?= $_POST['monto'] ?? '' ?>" required>


    <button type="submit">Cargar Gasto</button>
</form>

<?php include 'partial/footer.php'; ?>
