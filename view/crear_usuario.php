<?php



session_start();
require_once '../config/database.php';
require_once '../model/Usuario.php';

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
        $success = "Usuario técnico creado correctamente.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php include 'partial/header.php'; ?>

<h2>Crear Usuario Técnico</h2>

<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    <label>Nombre</label>
    <input type="text" name="nombre" required>
    
    <label>Username</label>
    <input type="text" name="username" required>
    
    <label>Email</label>
    <input type="email" name="email">
    
    <label>Contraseña</label>
    <input type="password" name="password" required>
    
    <button type="submit">Crear Usuario</button>
</form>

<?php include 'partial/footer.php'; ?>
