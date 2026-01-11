<?php require APPROOT . '/Views/layout/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid py-4">
    <div class="row">
        <?php require APPROOT . '/Views/layout/sidebar.php'; ?>

        <main class="col-md-9 px-md-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h2 class="fw-bold mb-0" style="color: var(--escom-blue);">Analítica de Casilleros</h2>
                <button class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Imprimir Reporte
                </button>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-muted mb-4 small text-uppercase">Estado del Inventario</h6>
                            <div style="height: 220px;">
                                <canvas id="chartStatus"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-muted mb-4 small text-uppercase">Densidad de Ocupación por Edificio (%)</h6>
                            <div style="height: 220px;">
                                <canvas id="chartBuildings"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-muted mb-4 small text-uppercase">Uso por Carrera (ISC, LCD, LIA)</h6>
                            <div style="height: 200px;">
                                <canvas id="chartCareers"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-uppercase" style="font-size: 0.7rem;">
                            <tr>
                                <th class="ps-4">Locker</th>
                                <th>Edificio</th>
                                <th>Estado</th>
                                <th>Ocupante</th>
                                <th>Carrera</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.85rem;">
                            <?php foreach($data['reporte'] as $row): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $row->numero_locker; ?></td>
                                <td>Edificio <?= $row->edificio; ?></td>
                                <td>
                                    <?php 
                                        $color = 'success';
                                        if($row->estatus == 'ocupado') $color = 'danger';
                                        if($row->estatus == 'reservado') $color = 'warning text-dark';
                                    ?>
                                    <span class="badge rounded-pill bg-<?= $color; ?> px-2"><?= ucfirst($row->estatus); ?></span>
                                </td>
                                <td><?= $row->nombre ? $row->nombre . ' ' . $row->paterno : '<span class="text-muted italic">Sin asignar</span>'; ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $row->carrera ?? '---'; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// --- Lógica de Datos ---

// 1. Estado Global
const statusLabels = <?= json_encode(array_column($data['statusStats'], 'estatus')); ?>;
const statusValues = <?= json_encode(array_column($data['statusStats'], 'total')); ?>;

// 2. Carreras
const careerLabels = <?= json_encode(array_column($data['careerStats'], 'carrera')); ?>;
const careerValues = <?= json_encode(array_column($data['careerStats'], 'total')); ?>;

// 3. Edificios (Cálculo de Porcentaje en JS)
const buildingsData = <?= json_encode($data['buildingStats']); ?>;
const buildingLabels = buildingsData.map(b => 'Edificio ' + b.edificio);
const buildingPercents = buildingsData.map(b => {
    return ((b.espacios_uso / b.capacidad_total) * 100).toFixed(1);
});

// --- Configuración de Gráficas ---

const fontStyle = { family: 'Outfit', size: 12 };

// Gráfica de Dona (Estado)
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusValues,
            backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d'],
            borderWidth: 0
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: fontStyle } } }
    }
});

// Gráfica de Barras Horizontales (Edificios)
new Chart(document.getElementById('chartBuildings'), {
    type: 'bar',
    data: {
        labels: buildingLabels,
        datasets: [{
            label: 'Porcentaje de Ocupación',
            data: buildingPercents,
            backgroundColor: 'rgba(98, 17, 50, 0.8)', // Guinda IPN
            borderRadius: 20
        }]
    },
    options: {
        indexAxis: 'y',
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: { callbacks: { label: (ctx) => `Ocupación: ${ctx.raw}%` } }
        },
        scales: {
            x: { max: 100, ticks: { callback: v => v + '%' }, grid: { display: false } },
            y: { grid: { display: false }, ticks: { font: fontStyle } }
        }
    }
});

// Gráfica de Barras (Carreras)
new Chart(document.getElementById('chartCareers'), {
    type: 'bar',
    data: {
        labels: careerLabels,
        datasets: [{
            data: careerValues,
            backgroundColor: 'rgba(0, 51, 102, 0.8)', // Azul ESCOM
            borderRadius: 8
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php require APPROOT . '/Views/layout/footer.php'; ?>