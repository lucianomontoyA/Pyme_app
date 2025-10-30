<?php
require_once __DIR__ . '/config/auth.php';
checkLogin();

// ✅ conexión a la base centralizada
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/model/Orden.php';

// ✅ Instancia del modelo
$ordenModel = new Orden($pdo);
$ordenes = $ordenModel->listar();

// ✅ Contamos por estado
$estadisticas = [
    'Ingresado'   => 0,
    'En revisión' => 0,
    'Reparado'    => 0,
    'Entregado'   => 0
];

foreach ($ordenes as $orden) {
    $estado = $orden['estado'] ?? 'Ingresado';
    if (isset($estadisticas[$estado])) {
        $estadisticas[$estado]++;
    }
}

include __DIR__ . '/view/partial/header.php';
?>

<main class="home-container">
    <h2>Bienvenido a Servicio Técnico</h2>
    <p>Usá el menú para crear o ver órdenes.</p>

<!-- =========================
     COTIZACIONES + ÓRDENES
========================= -->
<div class="cotizacion-container">
    <!-- Dólar Blue -->
    <div id="dolarBlue" class="cotizacion-card">
        <h3>💵 Dólar Blue</h3>
        <div class="cotizacion-precios">
            <div class="precio-item">
                <span class="label">Compra</span>
                <span id="compra" class="valor">--</span>
            </div>
            <div class="precio-item">
                <span class="label">Venta</span>
                <span id="venta" class="valor">--</span>
            </div>
            <div class="precio-item promedio">
                <span class="label">Promedio</span>
                <span id="valor" class="valor">--</span>
            </div>
        </div>
        <p class="update-time" id="actualizado">Cargando...</p>
    </div>

    <!-- Bitcoin -->
    <div id="cryptoCard" class="cotizacion-card">
        <h3>₿ Bitcoin (BTC)</h3>
        <canvas id="btcChart"></canvas>
        <p class="update-time" id="updateCrypto">Cargando...</p>
    </div>

    <!-- Órdenes -->
    <div class="cotizacion-card">
        <h3>🛠 Órdenes</h3>
        <canvas id="ordenesChart"></canvas>
        <p class="update-time">Actualizado recientemente</p>
    </div>
</div>

<!-- =========================
     SCRIPT PARA TODOS LOS GRÁFICOS
========================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // ==== Dólar Blue ====
  fetch('https://api.bluelytics.com.ar/v2/latest')
    .then(r => r.json())
    .then(data => {
      document.getElementById('compra').textContent = `$${data.blue.value_buy}`;
      document.getElementById('venta').textContent  = `$${data.blue.value_sell}`;
      document.getElementById('valor').textContent  = `$${data.blue.value_avg}`;
      const ahora = new Date();
      document.getElementById('actualizado').textContent =
        `Actualizado a las ${ahora.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' })}`;
    })
    .catch(() => {
      document.getElementById('dolarBlue').innerHTML = '<p>No se pudo cargar la cotización 😢</p>';
    });

  // ==== Bitcoin ====
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
            label: 'Precio USD',
            data: precios,
            borderColor: '#00eaff',
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 0,
            fill: true,
            backgroundColor: 'rgba(0,234,255,0.1)'
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            x: { ticks: { color: '#ccc' }, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { ticks: { color: '#ccc' }, grid: { color: 'rgba(255,255,255,0.05)' } }
          }
        }
      });
      const ahora = new Date();
      document.getElementById('updateCrypto').textContent =
        `Actualizado a las ${ahora.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' })}`;
    } catch {
      document.getElementById('cryptoCard').innerHTML = '<p>No se pudo cargar el gráfico 😢</p>';
    }
  }
  cargarBTC();

  // ==== Órdenes (con datos reales) ====
  const ctxOrdenes = document.getElementById('ordenesChart').getContext('2d');
  new Chart(ctxOrdenes, {
    type: 'bar',
    data: {
      labels: ['Ingresado','En revisión','Reparado','Entregado'],
      datasets: [{
        label: 'Órdenes',
        data: [
          <?= $estadisticas['Ingresado'] ?>,
          <?= $estadisticas['En revisión'] ?>,
          <?= $estadisticas['Reparado'] ?>,
          <?= $estadisticas['Entregado'] ?>
        ],
        backgroundColor: ['#ffbb33', '#33b5e5', '#00C851', '#ff4444'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });
</script>

<div class="menu-cards">
    <a href="view/crear_orden.php" class="card">
        <h3>Nueva Orden</h3>
        <p>Registrar un nuevo ingreso de equipo.</p>
    </a>

    <a href="view/ver_orden.php" class="card">
        <h3>Ver Órdenes</h3>
        <p>Consultar todas las órdenes activas.</p>
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
