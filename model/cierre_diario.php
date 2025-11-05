<?php
class CierreDiario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ===============================
    // Generar Cierre Diario (por fecha)
    // ===============================
    public function generar($fecha) {
        $totales = $this->calcularTotales($fecha, $fecha);

        // Guardar en DB
        $this->guardarCierre($fecha, $totales);

        return $totales;
    }

    // ===============================
    // Generar Cierre Histórico (rango de fechas)
    // ===============================
    public function generarRango($desde, $hasta) {
        return $this->calcularTotales($desde, $hasta);
    }

    // ===============================
    // Listar Historial de Cierres
    // ===============================
    public function listarHistorial() {
        $stmt = $this->pdo->query("SELECT * FROM cierres_diarios ORDER BY fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // Función interna: calcular totales
    // ===============================
    private function calcularTotales($fecha_inicio, $fecha_fin) {
        $stmt = $this->pdo->prepare("
            SELECT estado, COUNT(*) as cantidad, SUM(total) as total
            FROM ordenes
            WHERE DATE(fecha_finalizacion) BETWEEN ? AND ?
            GROUP BY estado
        ");
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totales = [
            'Ingresado' => 0,
            'En revisión' => 0,
            'Reparado' => 0,
            'Entregado' => 0,
            'total_ordenes' => 0,
            'total_recaudado' => 0
        ];

        foreach ($resultados as $row) {
            $estado = $row['estado'];
            $cantidad = (int)$row['cantidad'];
            $total = (float)$row['total'];

            if (isset($totales[$estado])) {
                $totales[$estado] += $cantidad;
            }

            $totales['total_ordenes'] += $cantidad;
            $totales['total_recaudado'] += $total;
        }

        return $totales;
    }

    // ===============================
    // Función interna: guardar cierre diario en DB
    // ===============================
    private function guardarCierre($fecha, $totales) {
        $stmt = $this->pdo->prepare("
            INSERT INTO cierres_diarios
            (fecha, ordenes_ingresadas, ordenes_en_revision, ordenes_reparadas, ordenes_entregadas, total_ordenes, total_recaudado)
            VALUES (:fecha, :ingresadas, :revision, :reparadas, :entregadas, :total_ordenes, :total_recaudado)
            ON DUPLICATE KEY UPDATE
                ordenes_ingresadas = :ingresadas_update,
                ordenes_en_revision = :revision_update,
                ordenes_reparadas = :reparadas_update,
                ordenes_entregadas = :entregadas_update,
                total_ordenes = :total_ordenes_update,
                total_recaudado = :total_recaudado_update
        ");

        $stmt->execute([
            ':fecha' => $fecha,
            ':ingresadas' => $totales['Ingresado'],
            ':revision' => $totales['En revisión'],
            ':reparadas' => $totales['Reparado'],
            ':entregadas' => $totales['Entregado'],
            ':total_ordenes' => $totales['total_ordenes'],
            ':total_recaudado' => $totales['total_recaudado'],

            // Para UPDATE
            ':ingresadas_update' => $totales['Ingresado'],
            ':revision_update' => $totales['En revisión'],
            ':reparadas_update' => $totales['Reparado'],
            ':entregadas_update' => $totales['Entregado'],
            ':total_ordenes_update' => $totales['total_ordenes'],
            ':total_recaudado_update' => $totales['total_recaudado'],
        ]);
    }
}
