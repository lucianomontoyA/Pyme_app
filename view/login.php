<?php
// Iniciar sesión solo si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';
require_once '../model/usuario.php';

$usuarioModel = new Usuario($pdo);
$error = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $usuario = $usuarioModel->obtenerPorUsername($username);

    if ($usuario && $usuarioModel->verificarPassword($password, $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['nombre'] = $usuario['nombre'];

        header("Location: /index.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

// Indicar que estamos en login para ocultar nav
$_SESSION['pagina_actual'] = 'login';

// Solo incluir header una vez
include 'partial/header.php';
?>

<h2>Login</h2>

<?php if($error): ?>
    <p style="color: #ff4d4d; font-weight: bold; text-align:center; margin-bottom:15px;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="post" autocomplete="off" autocomplete="new-password" style="max-width:400px; margin: 0 auto;">
    <label for="username">Usuario</label>
    <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required >

    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>

    <button type="submit">Ingresar</button>
</form>

<?php include 'partial/footer.php'; ?>
