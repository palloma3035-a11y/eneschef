<?php
require_once __DIR__ . '/../config.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        header('Location: dashboard.php');
        exit;
    } else {
        $message = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <style>body{font-family:Arial}.box{max-width:360px;margin:40px auto;background:#fff;padding:20px;border:1px solid #ddd}</style>
</head>
<body>
  <div class="box">
    <h2>Admin Login</h2>
    <?php if ($message): ?><div style="color:red"><?=htmlspecialchars($message)?></div><?php endif; ?>
    <form method="post">
      <label>Email<br><input name="email" required></label><br><br>
      <label>Password<br><input name="password" type="password" required></label><br><br>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>