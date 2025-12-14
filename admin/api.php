<?php
require_once __DIR__ . '/../config.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$action = $_POST['action'] ?? '';

$pdo = getPDO();

if ($action === 'update_status') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['pending','confirmed','seated','cancelled'];
    if ($id > 0 && in_array($status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE reservations SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
    }
}

header('Location: dashboard.php');
exit;