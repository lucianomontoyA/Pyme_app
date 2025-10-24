<?php
session_start(); // 1️⃣ Iniciar sesión

require_once '../model/Cliente.php';
require_once '../model/Orden.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit;
}

// Configuración PDO
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

// Instanciamos modelos
$clienteModel = new Cliente($pdo);
$ordenModel   = new Orden($pdo);

// Procesar formulario POST para crear orden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_orden'])) {
    // Crear cliente
    $cliente_id = $clienteModel->crear(
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['email'] ,
        $_POST['telefono'] ,
        $_POST['direccion'],  // <-- agregamos
    $_POST['cuit']         // <-- agregamos
    );

    // Crear orden y asociar el usuario logueado
    $orden_id = $ordenModel->crear(
        $cliente_id,
        $_POST['equipo'],
        $_POST['marca'] ?? null,
        $_POST['modelo'] ?? null,
        $_POST['serie'] ?? null,
        $_POST['problema_reportado'] ?? null,
        $_POST['observaciones'] ?? null,
        $_POST['total'] ?? 0,
        $_SESSION['usuario_id'] // 2️⃣ Usuario logueado
    );

    header("Location: ../view/ver_orden.php?id=" . $orden_id);
    exit;
}


/*
================================================================================
FLUJO COMPLETO DEL SISTEMA DE CREACIÓN DE ORDENES
================================================================================

1. Inclusión de clases:
   - Se cargan 'Cliente.php' y 'Orden.php' con require_once para poder usar 
     sus métodos y manejar la información de clientes y órdenes.

2. Configuración y conexión a la base de datos:
   - Se definen host, db, user, pass y charset para PDO.
   - Se crea el DSN y las opciones de PDO.
   - Se instancia $pdo con try/catch:
       - Si la conexión falla, se detiene el script mostrando el error.

3. Instanciación de modelos:
   - $clienteModel = new Cliente($pdo) → permite crear y gestionar clientes.
   - $ordenModel = new Orden($pdo) → permite crear y gestionar órdenes.

4. Procesamiento del formulario POST:
   - Se verifica que la solicitud sea POST y que exista 'crear_orden'.
   - Esto garantiza que el código solo se ejecute al enviar el formulario.

5. Creación de cliente:
   - $cliente_id = $clienteModel->crear(...)  
       - Se pasan: nombre, apellido, email y teléfono (opcional).
       - Se inserta un nuevo registro en la tabla clientes.
       - Devuelve el ID del cliente recién creado.

6. Creación de orden:
   - $orden_id = $ordenModel->crear(...)  
       - Se pasa $cliente_id para asociar la orden al cliente.
       - Se pasan datos del equipo: tipo, marca, modelo, serie, problema.
       - Se inserta un nuevo registro en la tabla órdenes.
       - Devuelve el ID de la orden recién creada.

7. Redirección:
   - header("Location: ../view/ver_orden.php?id=" . $orden_id)
       - Redirige al detalle de la orden.
   - exit → termina la ejecución para evitar procesar código adicional.

================================================================================
NOTA:
- Los campos opcionales usan el operador ?? null para evitar errores si no se envían.
- Este bloque final sirve como referencia rápida del flujo completo, 
  permitiendo entender el código sin saturarlo de comentarios línea por línea.
================================================================================
*/
