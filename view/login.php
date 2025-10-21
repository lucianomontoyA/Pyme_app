<?php
session_start();
require_once '../config/database.php';
require_once '../model/usuario.php';

$usuarioModel = new Usuario($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $usuario = $usuarioModel->obtenerPorUsername($username);

    if ($usuario && $usuarioModel->verificarPassword($password, $usuario['password'])) {
        // Guardar datos de sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['nombre'] = $usuario['nombre'];

        // Redirigir según rol (ejemplo)
        header("Location: /index.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<?php include 'partial/header.php'; ?>

<h2>Login</h2>
  <a href="view/crear_usuario.php" class="btn-cierre">Crear Tecnico</a>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
    <label>Usuario</label>
    <input type="text" name="username" required>
    <label>Contraseña</label>
    <input type="password" name="password" required>
    <button type="submit">Ingresar</button>
</form>

<?php include 'partial/footer.php'; ?>
