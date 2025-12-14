<?php
require_once __DIR__ . '/../config.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();

// Fetch reservations (most recent first)
$stmt = $pdo->query("SELECT r.*, t.name AS table_name FROM reservations r LEFT JOIN tables_info t ON r.table_id = t.id ORDER BY r.created_at DESC LIMIT 200");
$reservations = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <style>body{font-family:Arial} table{width:100%;border-collapse:collapse} th,td{padding:8px;border:1px solid #ddd}</style>
</head>
<body>
  <h1>Admin Dashboard</h1>
  <p>Welcome, <?=htmlspecialchars($_SESSION['admin_email'])?> — <a href="logout.php">Logout</a></p>

  <h2>Reservations</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Date</th><th>Time</th><th>Seats</th><th>Table</th><th>Status</th><th>Created</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($reservations as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['customer_name']) ?></td>
        <td><?= htmlspecialchars($r['customer_email']) ?></td>
        <td><?= htmlspecialchars($r['date']) ?></td>
        <td><?= htmlspecialchars($r['time_slot']) ?></td>
        <td><?= (int)$r['seats'] ?></td>
        <td><?= htmlspecialchars($r['table_name'] ?? 'Unassigned') ?></td>
        <td><?= htmlspecialchars($r['status']) ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
        <td>
          <form method="post" action="api.php" style="display:inline">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <select name="status">
              <option<?= $r['status']=='pending'?' selected':'' ?> value="pending">pending</option>
              <option<?= $r['status']=='confirmed'?' selected':'' ?> value="confirmed">confirmed</option>
              <option<?= $r['status']=='seated'?' selected':'' ?> value="seated">seated</option>
              <option<?= $r['status']=='cancelled'?' selected':'' ?> value="cancelled">cancelled</option>
            </select>
            <button type="submit">Update</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <footer>created by enestahiri.com</footer>
</body>
</html>