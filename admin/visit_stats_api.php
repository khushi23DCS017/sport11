<?php
require_once '../config/database.php';

$mode = $_GET['mode'] ?? 'daily';
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

$where = '';
$params = [];
if ($from && $to) {
    $where = "WHERE visit_time BETWEEN ? AND ?";
    $params[] = $from . " 00:00:00";
    $params[] = $to . " 23:59:59";
}

switch ($mode) {
    case 'weekly':
        $group = "YEARWEEK(visit_time, 1)";
        $label = "CONCAT(YEAR(visit_time), '-W', LPAD(WEEK(visit_time, 1), 2, '0'))";
        break;
    case 'monthly':
        $group = "DATE_FORMAT(visit_time, '%Y-%m')";
        $label = $group;
        break;
    case 'custom':
        $group = "DATE(visit_time)";
        $label = $group;
        break;
    case 'daily':
    default:
        $group = "DATE(visit_time)";
        $label = $group;
        break;
}

$sql = "SELECT $label as label, COUNT(*) as count FROM visit_logs ".($where ? $where.' AND ' : 'WHERE ')."1=1 GROUP BY $group ORDER BY label DESC LIMIT 30";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Also return total visits and max visits per day for counters
$total_visits_stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_logs $where");
$total_visits_stmt->execute($params);
$total_visits = (int)$total_visits_stmt->fetchColumn();
$max_visits_per_day = count($stats) > 0 ? max($stats) : 0;

header('Content-Type: application/json');
echo json_encode([
    'stats' => array_reverse($stats, true),
    'total_visits' => $total_visits,
    'max_visits_per_day' => $max_visits_per_day
]); 