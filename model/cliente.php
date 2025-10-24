<?php
class Cliente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Crear un cliente y devolver su UUID generado por MySQL
  public function crear($nombre, $apellido, $email = null, $telefono = null, $direccion = null, $cuit = null) {
    try {
        // Generamos el UUID en PHP (mejor práctica que depender del DEFAULT de MySQL)
        $uuid = $this->pdo->query("SELECT UUID()")->fetchColumn();

        // Insertar cliente con UUID manual
        $sql = "INSERT INTO clientes (id, nombre, apellido, email, telefono, direccion, cuit) 
                VALUES (:id, :nombre, :apellido, :email, :telefono, :direccion, :cuit)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $uuid,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':email' => $email,
            ':telefono' => $telefono,
            ':direccion' => $direccion,
            ':cuit' => $cuit
        ]);

        // Devolvemos el UUID recién creado
        return $uuid;
    } catch (PDOException $e) {
        throw new Exception("Error al crear cliente: " . $e->getMessage());
    }
}


    public function actualizar($id, $nombre, $apellido, $email = null, $telefono = null, $direccion = null, $cuit = null) {
        $sql = "UPDATE clientes 
                SET nombre = :nombre, apellido = :apellido, email = :email, telefono = :telefono, direccion = :direccion, cuit = :cuit
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':email' => $email,
            ':telefono' => $telefono,
            ':direccion' => $direccion,
            ':cuit' => $cuit,
            ':id' => $id
        ]);
    }

    // Obtener cliente por ID
    public function obtener($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
