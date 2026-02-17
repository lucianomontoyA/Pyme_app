<?php
require_once __DIR__ . '/config/auth.php';
checkLogin();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/model/orden.php';

$ordenModel = new Orden($pdo);
$ordenes = $ordenModel->listar();


// ===============================
// ESTADÍSTICAS DE ÓRDENES ACTIVAS (SIN ENTREGADAS)
// ===============================
$estadisticas = [
    'Ingresado'   => 0,
    'En revisión' => 0,
    'Reparado'    => 0
];

foreach ($ordenes as $orden) {
    $estado = $orden['estado'] ?? 'Ingresado';
    if (isset($estadisticas[$estado])) {
        $estadisticas[$estado]++;
    }
}


// ===============================
// FECHA ARGENTINA
// ===============================
date_default_timezone_set('America/Argentina/Buenos_Aires');
$hoy = date('Y-m-d');

$cobrosHoy = 0.0;
$gastosHoy = 0.0;
$balanceHoy = 0.0;
$entregadosMes = 0;

try {

    // COBROS DEL DÍA
    $stmt = $pdo->prepare("
        SELECT SUM(total) 
        FROM ordenes 
        WHERE DATE(fecha_finalizacion) = ?
    ");
    $stmt->execute([$hoy]);
    $cobrosHoy = (float)($stmt->fetchColumn() ?? 0);

    // GASTOS DEL DÍA
    $stmt = $pdo->prepare("
        SELECT SUM(monto) 
        FROM gastos 
        WHERE fecha = ?
    ");
    $stmt->execute([$hoy]);
    $gastosHoy = (float)($stmt->fetchColumn() ?? 0);

    $balanceHoy = $cobrosHoy - $gastosHoy;

    // ===============================
    // ENTREGADOS DEL MES ACTUAL
    // ===============================
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM ordenes 
        WHERE estado = 'Entregado'
        AND MONTH(fecha_finalizacion) = MONTH(CURDATE())
        AND YEAR(fecha_finalizacion) = YEAR(CURDATE())
    ");
    $stmt->execute();
    $entregadosMes = (int)($stmt->fetchColumn() ?? 0);

} catch (Exception $e) {}

include __DIR__ . '/view/partial/header.php';
?>

<main class="home-container">
<h2>Bienvenido a Servicio Técnico</h2>
<p>Usá el menú para crear o ver órdenes.</p>

<!-- MOVIMIENTOS -->
<div class="movimientos-box">
    <h3>📊 Movimientos de hoy (<?= $hoy ?>)</h3>

    <div class="mov-item ingreso">
        <span>Cobros:</span>
        <strong>$<?= number_format($cobrosHoy, 2) ?></strong>
    </div>

    <div class="mov-item gasto">
        <span>Gastos:</span>
        <strong>$<?= number_format($gastosHoy, 2) ?></strong>
    </div>

    <div class="mov-item balance">
        <span>Balance:</span>
        <strong>$<?= number_format($balanceHoy, 2) ?></strong>
    </div>
</div>


<!-- COTIZACIONES + ORDENES -->
<div class="cotizacion-container">

    <!-- DOLAR -->
    <div id="dolarBlue" class="cotizacion-card">
        <h3>💵 Dólar Oficial</h3>
        <div class="cotizacion-precios">
            <div class="precio-item"><span>Compra</span><span id="compra">--</span></div>
            <div class="precio-item"><span>Venta</span><span id="venta">--</span></div>
            <div class="precio-item promedio"><span>Promedio</span><span id="valor">--</span></div>
        </div>
        <p id="actualizado">Cargando...</p>
    </div>

    <!-- BITCOIN -->
    <div id="cryptoCard" class="cotizacion-card">
        <h3>₿ Bitcoin</h3>
        <canvas id="btcChart"></canvas>
        <p id="updateCrypto">Cargando...</p>
    </div>

    <!-- ORDENES -->
    <div class="cotizacion-card">
        <h3>🛠 Órdenes activas</h3>
        <canvas id="ordenesChart"></canvas>

        <div class="ordenes-legend">
            <span><i class="dot amarillo"></i> Ingresado</span>
            <span><i class="dot celeste"></i> En revisión</span>
            <span><i class="dot verde"></i> Reparado</span>
        </div>

        <!-- 👇 NUEVO TEXTO DE ENTREGADOS -->
        <p style="margin-top:15px;font-weight:bold">
            📦 Entregados este mes: <?= $entregadosMes ?>
        </p>

        <p class="update-time">Actualizado recientemente</p>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// DOLAR
fetch('https://dolarapi.com/v1/dolares/oficial')
.then(r=>r.json()).then(data=>{
document.getElementById('compra').textContent='$'+data.compra;
document.getElementById('venta').textContent='$'+data.venta;
document.getElementById('valor').textContent='$'+((data.compra+data.venta)/2).toFixed(2);
document.getElementById('actualizado').textContent='Actualizado recientemente';
}).catch(()=>{document.getElementById('dolarBlue').innerHTML='No se pudo cargar';});

async function cargarBTC() {
  try {
    const res = await fetch('https://api.coingecko.com/api/v3/coins/bitcoin/market_chart?vs_currency=usd&days=7');
    const data = await res.json();

    const precios = data.prices.map(p => p[1]);
    const fechas = data.prices.map(p => {
      const d = new Date(p[0]);
      return d.toLocaleDateString('es-AR', { day: '2-digit', month: 'short' });
    });

    const ctx = document.getElementById('btcChart').getContext('2d');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: fechas,
        datasets: [{
          label: 'Precio BTC USD',
          data: precios,
          borderColor: '#00eaff',
          borderWidth: 2,
          tension: 0.35,
          pointRadius: 0,
          fill: true,
          backgroundColor: 'rgba(0,234,255,0.12)'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: {
            ticks: { color: '#ccc' },
            grid: { color: 'rgba(255,255,255,0.05)' }
          },
          y: {
            ticks: { 
              color: '#ccc',
              callback: value => '$' + value.toLocaleString()
            },
            grid: { color: 'rgba(255,255,255,0.05)' }
          }
        }
      }
    });

    const ahora = new Date();
    document.getElementById('updateCrypto').textContent =
      `Actualizado ${ahora.toLocaleTimeString('es-AR',{hour:'2-digit',minute:'2-digit'})}`;

  } catch {
    document.getElementById('cryptoCard').innerHTML = '<p>No se pudo cargar el gráfico 😢</p>';
  }
}cargarBTC();




// GRAFICO ORDENES (SIN ENTREGADO)
new Chart(document.getElementById('ordenesChart'),{
type:'pie',
data:{
labels:['Ingresado','En revisión','Reparado'],
datasets:[{
data:[
<?= $estadisticas['Ingresado'] ?>,
<?= $estadisticas['En revisión'] ?>,
<?= $estadisticas['Reparado'] ?>
],
backgroundColor:['#ffbb33','#33b5e5','#00C851']
}]
},
options:{plugins:{legend:{display:false}}}
});

document.getElementById('ordenesChart').onclick=()=>window.location.href='/view/ver_orden.php';
</script>


<div class="menu-cards">
    <a href="view/crear_orden.php" class="card">
        <h3>Nueva Orden</h3>
        <p>Registrar ingreso de equipo.</p>
    </a>

    <a href="view/ver_orden.php" class="card">
        <h3>Ver Órdenes</h3>
        <p>Consultar órdenes activas.</p>
    </a>

    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'tecnico'): ?>
    <a href="view/ordenes_finalizadas.php" class="card card-finalizadas">
        <h3>Órdenes Finalizadas</h3>
        <p>Revisar órdenes entregadas.</p>
    </a>
    <?php endif; ?>
</div>

</main>

<?php include __DIR__ . '/view/partial/footer.php'; ?>
