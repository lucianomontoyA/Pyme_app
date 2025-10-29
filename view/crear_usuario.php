<?php



session_start();
require_once '../config/database.php';
require_once '../model/usuario.php';

// Solo superadmin puede acceder
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'superadmin') {
    header("Location: /index.php");
    exit;
}

$usuarioModel = new Usuario($pdo);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $usuarioModel->crear($nombre, $username, $password, 'tecnico');
        $success = "Técnico creado correctamente.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php include 'partial/header.php'; ?>



<h2>Crear Técnico</h2>

<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post" autocomplete="off">
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" value="<?= $_POST['nombre'] ?? '' ?>" required>

    <label for="username">Usuario</label>
    <input type="text" id="username" name="username" value="<?= $_POST['username'] ?? '' ?>" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= $_POST['email'] ?? '' ?>">

    <label for="password">Contraseña</label>
    <div style="position: relative;">
        <input type="password" id="password" name="password" autocomplete="new-password" required>
        <button type="button" class="btn-ver-pass" onclick="togglePassword()">👁️</button>
    </div>

    <button type="submit">Crear Técnico</button>
</form>

<script>
function togglePassword() {
    const passInput = document.getElementById('password');
    if (passInput.type === 'password') {
        passInput.type = 'text';
    } else {
        passInput.type = 'password';
    }
}
</script>

<?php include 'partial/footer.php'; ?>
