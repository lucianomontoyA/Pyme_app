<?php
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crear un usuario
     * @param string $nombre
     * @param string $username
     * @param string $password
     * @param string $rol ('superadmin', 'tecnico', 'cliente')
     * @param string|null $cliente_id (solo si rol='cliente')
     * @return string $id UUID del usuario creado
     */
    public function crear($nombre, $username, $password, $rol = 'cliente', $cliente_id = null, $email = null) {
    try {
        $this->pdo->beginTransaction();

        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, username, email, password, rol, cliente_id) 
                VALUES (:nombre, :username, :email, :password, :rol, :cliente_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':username' => $username,
            ':email' => $email,
            ':password' => $passHash,
            ':rol' => $rol,
            ':cliente_id' => $cliente_id
        ]);

        $sql2 = "SELECT id FROM usuarios WHERE username = :username ORDER BY fecha_creacion DESC LIMIT 1";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute([':username' => $username]);
        $usuario_id = $stmt2->fetchColumn();

        $this->pdo->commit();
        return $usuario_id;

    } catch (PDOException $e) {
        $this->pdo->rollBack();
        throw new Exception("Error al crear usuario: " . $e->getMessage());
    }
}


    /**
     * Obtener usuario por username
     * @param string $username
     * @return array|false
     */
    public function obtenerPorUsername($username) {
        $sql = "SELECT * FROM usuarios WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener usuario por ID
     * @param string $id
     * @return array|false
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar contraseña
     * @param string $passwordPlain
     * @param string $passwordHash
     * @return bool
     */
    public function verificarPassword($passwordPlain, $passwordHash) {
        return password_verify($passwordPlain, $passwordHash);
    }
}
?>
