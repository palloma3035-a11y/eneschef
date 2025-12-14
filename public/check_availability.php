<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['available' => false]);
    exit;
}

$date = $input['date'] ?? null;
$time_slot = $input['time_slot'] ?? null;
$seats = (int)($input['seats'] ?? 0);

if (!$date || !$time_slot || $seats <= 0) {
    echo json_encode(['available' => false]);
    exit;
}

try {
    $pdo = getPDO();

    // Find tables with capacity >= seats
    $stmt = $pdo->prepare("SELECT id, name, capacity FROM tables_info WHERE capacity >= :seats ORDER BY capacity ASC");
    $stmt->execute([':seats' => $seats]);
    $tables = $stmt->fetchAll();

    $available_tables = [];
    foreach ($tables as $t) {
        // Check if table is already reserved for this date and time_slot
        $q = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE table_id = :tid AND date = :date AND time_slot = :time_slot AND status <> 'cancelled'");
        $q->execute([':tid' => $t['id'], ':date' => $date, ':time_slot' => $time_slot]);
        $count = (int)$q->fetchColumn();
        if ($count === 0) {
            $available_tables[] = $t['name'];
        }
    }

    echo json_encode(['available' => count($available_tables) > 0, 'available_tables' => $available_tables]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['available' => false, 'error' => $e->getMessage()]);
}