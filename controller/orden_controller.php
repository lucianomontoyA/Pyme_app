<?php
session_start(); // 1️⃣ Iniciar sesión

require_once '../model/cliente.php';
require_once '../model/orden.php';

// 2️⃣ Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit;
}

// 3️⃣ Configuración de conexión PDO
$host = 'localhost';
$db   = 'servicio_tecnico';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 4️⃣ Instanciamos los modelos
$clienteModel = new Cliente($pdo);
$ordenModel   = new Orden($pdo);

// 5️⃣ Procesar formulario para crear orden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_orden'])) {

    $tipo_cliente = $_POST['tipo_cliente'] ?? 'nuevo';
    $cliente_id = null;

    // Si el cliente es existente, usamos su ID directamente
    if ($tipo_cliente === 'existente' && !empty($_POST['cliente_id'])) {
        $cliente_id = $_POST['cliente_id'];

    // Si el cliente es nuevo, lo creamos
    } else {
        $cliente_id = $clienteModel->crear(
            $_POST['nombre'] ?? '',
            $_POST['apellido'] ?? '',
            $_POST['email'] ?? '',
            $_POST['telefono'] ?? '',
            $_POST['direccion'] ?? '',
            $_POST['cuit'] ?? ''
        );
    }

    // 6️⃣ Crear orden y asociarla al usuario logueado
    $orden_id = $ordenModel->crear(
        $cliente_id,
        $_POST['equipo'] ?? '',
        $_POST['marca'] ?? '',
        $_POST['modelo'] ?? '',
        $_POST['serie'] ?? '',
        $_POST['problema_reportado'] ?? '',
        $_POST['observaciones'] ?? '',
        $_POST['total'] ?? 0,
        $_SESSION['usuario_id']
    );

    // 7️⃣ Redirigir al detalle de la orden creada
    header("Location: ../view/ver_orden.php?id=" . $orden_id);
    exit;
}

/*
================================================================================
FLUJO COMPLETO DEL CONTROLADOR DE ÓRDENES
================================================================================
1️⃣ Se inicia la sesión para acceder a los datos del usuario logueado.
2️⃣ Se verifica que el usuario esté autenticado; si no, se lo redirige al login.
3️⃣ Se configura y establece la conexión PDO con la base de datos.
4️⃣ Se instancian los modelos Cliente y Orden.
5️⃣ Al recibir un formulario POST:
    - Se verifica si el cliente es nuevo o existente.
    - Si es nuevo, se crea en la tabla "clientes" y se obtiene su ID.
    - Si es existente, se usa el ID que viene del formulario.
6️⃣ Se crea la nueva orden asociada al cliente y al usuario logueado.
7️⃣ Se redirige al detalle de la orden recién creada.
================================================================================
NOTA:
- Todos los valores opcionales usan ?? '' o ?? null para evitar errores.
- Esto garantiza un flujo limpio y seguro al crear órdenes desde el formulario.
================================================================================
*/