<?php
class CierreDiario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Generar cierre diario
    public function generar($fecha) {
        // Contar órdenes por estado del día
        $stmt = $this->pdo->prepare("
            SELECT estado, COUNT(*) as cantidad, SUM(total) as total
            FROM ordenes
            WHERE DATE(fecha_finalizacion) = ?
            GROUP BY estado
        ");
        $stmt->execute([$fecha]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Inicializar totales
        $totales = [
            'Ingresado' => 0,
            'En revisión' => 0,
            'Reparado' => 0,
            'Entregado' => 0,
            'total_ordenes' => 0,
            'total_recaudado' => 0
        ];

        foreach ($result as $row) {
            $estado = $row['estado'];
            $totales[$estado] = (int)$row['cantidad'];
            $totales['total_ordenes'] += (int)$row['cantidad'];
            $totales['total_recaudado'] += (float)$row['total'];
        }

        // Insertar o actualizar cierre diario
        $stmt2 = $this->pdo->prepare("
            INSERT INTO cierres_diarios
            (fecha, total_ordenes, total_recaudado, ordenes_ingresadas, ordenes_en_revision, ordenes_reparadas, ordenes_entregadas)
            VALUES (:fecha, :total_ordenes, :total_recaudado, :ingresadas, :revision, :reparadas, :entregadas)
            ON DUPLICATE KEY UPDATE
                total_ordenes = :total_ordenes_update,
                total_recaudado = :total_recaudado_update,
                ordenes_ingresadas = :ingresadas_update,
                ordenes_en_revision = :revision_update,
                ordenes_reparadas = :reparadas_update,
                ordenes_entregadas = :entregadas_update
        ");

        $stmt2->execute([
            ':fecha' => $fecha,
            ':total_ordenes' => $totales['total_ordenes'],
            ':total_recaudado' => $totales['total_recaudado'],
            ':ingresadas' => $totales['Ingresado'],
            ':revision' => $totales['En revisión'],
            ':reparadas' => $totales['Reparado'],
            ':entregadas' => $totales['Entregado'],

            // Para el UPDATE
            ':total_ordenes_update' => $totales['total_ordenes'],
            ':total_recaudado_update' => $totales['total_recaudado'],
            ':ingresadas_update' => $totales['Ingresado'],
            ':revision_update' => $totales['En revisión'],
            ':reparadas_update' => $totales['Reparado'],
            ':entregadas_update' => $totales['Entregado'],
        ]);

        return $totales;
    }

    // Listar historial de cierres
    public function listarHistorial() {
        $stmt = $this->pdo->query("SELECT * FROM cierres_diarios ORDER BY fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
