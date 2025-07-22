<?php
require_once '../config/database.php';

// Handle date filtering
$where = '';
$params = [];
if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $where = "WHERE visit_time BETWEEN ? AND ?";
    $params[] = $_GET['from'] . " 00:00:00";
    $params[] = $_GET['to'] . " 23:59:59";
}

// Pagination for visits
$per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Total visits for pagination
$total_visits_stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_logs $where");
$total_visits_stmt->execute($params);
$total_visits_count = $total_visits_stmt->fetchColumn();
$total_pages = ceil($total_visits_count / $per_page);

// Visit history (paginated)
$history_stmt = $pdo->prepare("SELECT * FROM visit_logs $where ORDER BY visit_time DESC LIMIT $per_page OFFSET $offset");
$history_stmt->execute($params);
$visits = $history_stmt->fetchAll();

// Visits per day (for stats)
$stats_stmt = $pdo->prepare("
    SELECT DATE(visit_time) as day, COUNT(*) as count
    FROM visit_logs
    " . ($where ? $where . " AND " : "WHERE ") . "1=1
    GROUP BY day
    ORDER BY day DESC
    LIMIT 14
");
$stats_stmt->execute($params);
$stats = $stats_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$stats = array_reverse($stats, true); // for chart order
?>
<!DOCTYPE html>
<html>
<head>
    <title>Visit Statistics - Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: var(--light-bg, #f8f9fa); transition: background 0.3s, color 0.3s; }
        [data-theme="dark"] body { background: var(--dark-bg, #2c3e50); color: #f8f9fa; }
        [data-theme="dark"] .card-summary, [data-theme="dark"] .filter-section, [data-theme="dark"] .chart-container, [data-theme="dark"] .table-responsive {
            background: #232b3e !important; color: #f8f9fa;
        }
        [data-theme="dark"] .table thead { background: #1a2233 !important; color: #fff; }
        [data-theme="dark"] .table tbody tr:hover { background: #2c3e50 !important; }
        [data-theme="dark"] .table, [data-theme="dark"] .table th, [data-theme="dark"] .table td {
            color: #f8f9fa !important;
        }
        [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3, [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6 {
            color: #fff !important;
        }
        [data-theme="dark"] .text-muted { color: #b0b8c1 !important; }
        [data-theme="dark"] .form-control {
            background: #232b3e !important; color: #f8f9fa !important; border-color: #444 !important;
        }
        [data-theme="dark"] .form-control::placeholder { color: #b0b8c1 !important; }
        .dark-toggle-btn {
            background: none; border: none; color: var(--primary-color); font-size: 1.1rem; cursor: pointer; margin-left: auto; transition: color 0.3s; padding: 2px 6px; line-height: 1;
            display: flex; align-items: center; justify-content: center; height: 32px; width: 32px; border-radius: 50%;
        }
        .dark-toggle-btn i { font-size: 1.1rem; }
        [data-theme="dark"] .dark-toggle-btn { color: #f1c40f; }
        .stats-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; }
        .stats-header i { color: var(--primary-color); font-size: 2.5rem; }
        .card-row { display: flex; gap: 1.5rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .card-summary { flex: 1; min-width: 220px; display: flex; align-items: center; gap: 1rem; background: linear-gradient(135deg, #4a90e2 60%, #2c3e50 100%); color: #fff; border-radius: 10px; padding: 1.5rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .card-summary i { font-size: 2.2rem; opacity: 0.8; }
        .filter-section { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 1.5rem 2rem; margin-bottom: 2rem; }
        .filter-section form { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
        .filter-section .form-control { min-width: 180px; }
        .chart-container { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 1.5rem 2rem; margin-bottom: 2rem; }
        .table-responsive { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 1.5rem 2rem; }
        .table thead { background: var(--primary-color, #4a90e2); color: #fff; }
        .table tbody tr:hover { background: #f1f7ff; }
        @media (max-width: 900px) {
            .card-row { flex-direction: column; }
        }
        .filter-instruction { font-size: 1rem; color: var(--secondary-color); transition: color 0.3s; }
        [data-theme="dark"] .filter-instruction { color: #ffe082 !important; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="stats-header mb-4">
            <i class="fas fa-chart-bar"></i>
            <div>
                <h2 class="mb-0">Visit Statistics</h2>
                <div class="text-muted" style="font-size:1rem;">Monitor and analyze user visits to your landing page</div>
            </div>
        </div>
        <div class="card-row">
            <div class="card-summary">
                <i class="fas fa-users"></i>
                <div>
                    <div style="font-size:2rem; font-weight:700;">
                        <span id="visitorCounter" style="font-size:2rem; font-weight:700;"></span>
                    </div>
                    <div>Total Visits</div>
                </div>
            </div>
            <div class="card-summary">
                <i class="fas fa-calendar-day"></i>
                <div>
                    <div style="font-size:1.2rem; font-weight:600;">
                        <span id="maxVisitCounter" style="font-size:1.2rem; font-weight:600;"></span>
                    </div>
                    <div>Max Visits/Day</div>
                </div>
            </div>
            <div class="card-summary">
                <i class="fas fa-clock"></i>
                <div>
                    <div style="font-size:1.2rem; font-weight:600;">
                        <?= count($visits) > 0 ? $visits[0]['visit_time'] : '--' ?>
                    </div>
                    <div>Last Visit</div>
                </div>
            </div>
        </div>
        <div class="filter-section mb-4">
            <div class="mb-2 filter-instruction">
                <i class="fas fa-filter"></i> <strong>Filter by Date Range:</strong> Select a starting and ending date, then click <strong>Filter</strong> to view visits within that range.
            </div>
            <form method="get">
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            </form>
        </div>
        <div class="chart-filter-container mb-3" style="display:flex; gap:1rem; align-items:center;">
            <button class="btn btn-outline-primary" id="filter-daily">Daily</button>
            <button class="btn btn-outline-primary" id="filter-weekly">Weekly</button>
            <button class="btn btn-outline-primary" id="filter-monthly">Monthly</button>
            <button class="btn btn-outline-primary" id="filter-custom">Custom</button>
            <div id="custom-range-inputs" style="display:none; gap:0.5rem; align-items:center;">
                <input type="date" id="custom-from" class="form-control" style="max-width:150px;">
                <input type="date" id="custom-to" class="form-control" style="max-width:150px;">
                <button class="btn btn-primary" id="apply-custom-range">Apply</button>
            </div>
        </div>
        <div class="chart-container mb-4">
            <h5 class="mb-3"><i class="fas fa-chart-line"></i> Visits Per Day (last 14 days)</h5>
            <canvas id="visitsChart" height="80"></canvas>
        </div>
        <div class="table-responsive">
            <h5 class="mb-3"><i class="fas fa-history"></i> Recent Visits</h5>
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>IP Address</th>
                        <th>Device Type</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visits as $visit): ?>
                        <tr>
                            <td><?= $visit['visit_time'] ?></td>
                            <td><?= $visit['ip_address'] ?></td>
                            <td><?= htmlspecialchars($visit['device_type'] ?? 'Unknown') ?></td>
                            <td style="max-width:300px; white-space:nowrap; overflow-x:auto; text-overflow:ellipsis;">
                                <?= htmlspecialchars($visit['user_agent']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($total_pages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&from=<?= urlencode($_GET['from'] ?? '') ?>&to=<?= urlencode($_GET['to'] ?? '') ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&from=<?= urlencode($_GET['from'] ?? '') ?>&to=<?= urlencode($_GET['to'] ?? '') ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item<?= $page >= $total_pages ? ' disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&from=<?= urlencode($_GET['from'] ?? '') ?>&to=<?= urlencode($_GET['to'] ?? '') ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('visitsChart').getContext('2d');
        const visitsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($stats)) ?>,
                datasets: [{
                    label: 'Visits',
                    data: <?= json_encode(array_values($stats)) ?>,
                    backgroundColor: 'rgba(74, 144, 226, 0.7)',
                    borderColor: 'rgba(44, 62, 80, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#eee' } }
                }
            }
        });
        // Apply theme from localStorage, no toggle here
        const root = document.documentElement;
        function setTheme(theme) {
            root.setAttribute('data-theme', theme);
        }
        const savedTheme = localStorage.getItem('adminTheme') || 'light';
        setTheme(savedTheme);
        window.addEventListener('storage', function(e) {
            if (e.key === 'adminTheme') {
                setTheme(e.newValue || 'light');
            }
        });
        // Animated Moving Visitor Counter (inside card)
        function animateCounter(id, end, duration = 700) {
            const el = document.getElementById(id);
            let start = parseInt(el.textContent.replace(/,/g, '')) || 0;
            const range = end - start;
            let startTime = null;
            function animate(currentTime) {
                if (!startTime) startTime = currentTime;
                const progress = Math.min((currentTime - startTime) / duration, 1);
                el.textContent = Math.floor(progress * range + start).toLocaleString();
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    el.textContent = end.toLocaleString();
                }
            }
            requestAnimationFrame(animate);
        }
        animateCounter('visitorCounter', <?= (int)$total_visits_count ?>, 700);
        animateCounter('maxVisitCounter', <?= count($stats) > 0 ? max($stats) : 0 ?>, 700);

        // Real-time update every 5 seconds
        function updateStatsRealtime() {
            fetch('visit_stats_api.php')
                .then(response => response.json())
                .then(data => {
                    const visitorCounter = document.getElementById('visitorCounter');
                    const maxVisitCounter = document.getElementById('maxVisitCounter');
                    if (parseInt(visitorCounter.textContent.replace(/,/g, '')) !== data.total_visits) {
                        animateCounter('visitorCounter', data.total_visits, 700);
                    }
                    if (parseInt(maxVisitCounter.textContent.replace(/,/g, '')) !== data.max_visits_per_day) {
                        animateCounter('maxVisitCounter', data.max_visits_per_day, 700);
                    }
                });
        }
        setInterval(updateStatsRealtime, 5000);

        let currentMode = 'daily';
        function updateChart(mode, from = '', to = '') {
            let url = 'visit_stats_api.php?mode=' + mode;
            if (mode === 'custom' && from && to) {
                url += '&from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to);
            }
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    visitsChart.data.labels = Object.keys(data.stats);
                    visitsChart.data.datasets[0].data = Object.values(data.stats);
                    visitsChart.update();
                    // Update counters with safe fallback
                    animateCounter('visitorCounter', Number.isFinite(data.total_visits) ? data.total_visits : 0, 700);
                    animateCounter('maxVisitCounter', Number.isFinite(data.max_visits_per_day) ? data.max_visits_per_day : 0, 700);
                });
        }

        document.getElementById('filter-daily').onclick = function() {
            currentMode = 'daily';
            updateChart('daily');
            document.getElementById('custom-range-inputs').style.display = 'none';
        };
        document.getElementById('filter-weekly').onclick = function() {
            currentMode = 'weekly';
            updateChart('weekly');
            document.getElementById('custom-range-inputs').style.display = 'none';
        };
        document.getElementById('filter-monthly').onclick = function() {
            currentMode = 'monthly';
            updateChart('monthly');
            document.getElementById('custom-range-inputs').style.display = 'none';
        };
        document.getElementById('filter-custom').onclick = function() {
            currentMode = 'custom';
            document.getElementById('custom-range-inputs').style.display = 'flex';
        };
        document.getElementById('apply-custom-range').onclick = function() {
            const from = document.getElementById('custom-from').value;
            const to = document.getElementById('custom-to').value;
            if (from && to) {
                updateChart('custom', from, to);
            }
        };
    </script>
</body>
</html> 