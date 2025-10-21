<?php
class Cliente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Crear un cliente y devolver su UUID generado por MySQL
    public function crear($nombre, $apellido, $email = null, $telefono = null) {
        try {
            // Iniciar transacción
            $this->pdo->beginTransaction();

            // Insertar cliente sin especificar ID (MySQL generará UUID automáticamente)
            $sql = "INSERT INTO clientes (nombre, apellido, email, telefono) 
                    VALUES (:nombre, :apellido, :email, :telefono)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':email' => $email,
                ':telefono' => $telefono
            ]);

            // Obtener el UUID generado de manera segura usando la misma transacción
            $sql2 = "SELECT id FROM clientes 
                     WHERE nombre = :nombre AND apellido = :apellido 
                     ORDER BY fecha_creacion DESC LIMIT 1";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido
            ]);
            $cliente_id = $stmt2->fetchColumn();

            // Confirmar transacción
            $this->pdo->commit();

            return $cliente_id;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al crear cliente: " . $e->getMessage());
        }
    }


    public function actualizar($id, $nombre, $apellido, $email = null, $telefono = null) {
    $sql = "UPDATE clientes SET nombre = :nombre, apellido = :apellido, email = :email, telefono = :telefono WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':email' => $email,
        ':telefono' => $telefono,
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
