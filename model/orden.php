<?php
class Orden {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Crear una orden
    public function crear(
        $cliente_id, $equipo, $marca = null, $modelo = null, $serie = null, 
        $problema_reportado = null, $observaciones = null, $total = 0.00, $usuario_id = null
    ) {
        $codigo_publico = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

        $sql = "INSERT INTO ordenes 
                (cliente_id, equipo, marca, modelo, serie, problema_reportado, observaciones, total, codigo_publico, usuario_id)
                VALUES 
                (:cliente_id, :equipo, :marca, :modelo, :serie, :problema_reportado, :observaciones, :total, :codigo_publico, :usuario_id)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':cliente_id' => $cliente_id,
            ':equipo' => $equipo,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':serie' => $serie,
            ':problema_reportado' => $problema_reportado,
            ':observaciones' => $observaciones,
            ':total' => $total,
            ':codigo_publico' => $codigo_publico,
            ':usuario_id' => $usuario_id
        ]);

        return $this->pdo->lastInsertId();
    }

    public function actualizar(
        $id,
        $equipo,
        $marca = null,
        $modelo = null,
        $serie = null,
        $problema_reportado = null,
        $observaciones = null,
        $estado = 'Ingresado',
        $total = 0.00
    ) {
        // Validar estado
        $estados_validos = ['Ingresado','En revisión','Reparado','Entregado'];
        if (!in_array($estado, $estados_validos)) {
            $estado = 'Ingresado';
        }

        // Asegurarse que total sea decimal
        $total = floatval($total);

        $sql = "UPDATE ordenes SET 
                    equipo = :equipo,
                    marca = :marca,
                    modelo = :modelo,
                    serie = :serie,
                    problema_reportado = :problema_reportado,
                    observaciones = :observaciones,
                    estado = :estado,
                    total = :total";

        if ($estado === 'En revisión') {
            $sql .= ", fecha_revision = IF(fecha_revision IS NULL, NOW(), fecha_revision)";
        } elseif ($estado === 'Reparado') {
            $sql .= ", fecha_reparacion = IF(fecha_reparacion IS NULL, NOW(), fecha_reparacion)";
        } elseif ($estado === 'Entregado') {
            $sql .= ", fecha_finalizacion = IF(fecha_finalizacion IS NULL, NOW(), fecha_finalizacion)";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':equipo' => $equipo,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':serie' => $serie,
            ':problema_reportado' => $problema_reportado,
            ':observaciones' => $observaciones,
            ':estado' => $estado,
            ':total' => $total,
            ':id' => $id
        ]);
    }

    // Obtener orden por ID
    public function obtener($id) {
        $sql = "SELECT * FROM ordenes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar todas las órdenes
    public function listar() {
        $sql = "SELECT o.*, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, 
                       u.nombre AS creador_nombre
                FROM ordenes o
                LEFT JOIN clientes c ON o.cliente_id = c.id
                LEFT JOIN usuarios u ON o.usuario_id = u.id
                ORDER BY o.fecha_creacion DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCodigoPublico($codigo) {
        $stmt = $this->pdo->prepare("SELECT * FROM ordenes WHERE codigo_publico = :codigo LIMIT 1");
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // === NUEVO MÉTODO PARA EL GRÁFICO DE ÓRDENES ===
    public function obtenerResumenPorEstado() {
        $sql = "SELECT estado, COUNT(*) as cantidad FROM ordenes GROUP BY estado";
        $stmt = $this->pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Aseguramos que los estados estén siempre en el mismo orden
        $estados = ['Ingresado', 'En revisión', 'Reparado', 'Entregado'];
        $conteos = [];

        foreach ($estados as $estado) {
            $encontrado = array_filter($resultados, fn($r) => $r['estado'] === $estado);
            $conteos[] = $encontrado ? array_values($encontrado)[0]['cantidad'] : 0;
        }

        return $conteos;
    }
}
