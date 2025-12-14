<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer

header('Content-Type: application/json');

$required = ['name','email','date','time_slot','seats'];
foreach ($required as $r) {
    if (empty($_POST[$r])) {
        echo json_encode(['success' => false, 'message' => "Missing $r"]);
        exit;
    }
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone'] ?? '');
$date = $_POST['date'];
$time_slot = $_POST['time_slot'];
$seats = (int)$_POST['seats'];

try {
    $pdo = getPDO();
    // Find first available table that fits seats
    $stmt = $pdo->prepare("SELECT id FROM tables_info WHERE capacity >= :seats ORDER BY capacity ASC");
    $stmt->execute([':seats' => $seats]);
    $table_id = null;
    while ($t = $stmt->fetch()) {
        $q = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE table_id = :tid AND date = :date AND time_slot = :time_slot AND status <> 'cancelled'");
        $q->execute([':tid' => $t['id'], ':date' => $date, ':time_slot' => $time_slot]);
        if ((int)$q->fetchColumn() === 0) {
            $table_id = $t['id'];
            break;
        }
    }

    if (!$table_id) {
        echo json_encode(['success' => false, 'message' => 'No available table']);
        exit;
    }

    // Insert reservation
    $ins = $pdo->prepare("INSERT INTO reservations (customer_name, customer_email, customer_phone, date, time_slot, seats, table_id, status) VALUES (:name,:email,:phone,:date,:time_slot,:seats,:table_id,'confirmed')");
    $ins->execute([
        ':name'=>$name,
        ':email'=>$email,
        ':phone'=>$phone,
        ':date'=>$date,
        ':time_slot'=>$time_slot,
        ':seats'=>$seats,
        ':table_id'=>$table_id
    ]);

    $reservationId = $pdo->lastInsertId();

    // Send confirmation email via PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // SMTP settings from config
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Reservation Confirmation #{$reservationId}";
        $body = "<p>Hi " . htmlspecialchars($name) . ",</p>";
        $body .= "<p>Your reservation is confirmed:</p>";
        $body .= "<ul>";
        $body .= "<li>Date: " . htmlspecialchars($date) . "</li>";
        $body .= "<li>Time: " . htmlspecialchars($time_slot) . "</li>";
        $body .= "<li>Seats: " . (int)$seats . "</li>";
        $body .= "<li>Reservation ID: " . $reservationId . "</li>";
        $body .= "</ul>";
        $body .= "<p>We look forward to seeing you!</p>";
        $body .= "<p>created by enestahiri.com</p>";

        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        // Email failed but reservation exists; we return success but include warning
        echo json_encode(['success' => true, 'warning' => 'Reservation saved but confirmation email failed: ' . $mail->ErrorInfo]);
        exit;
    }

    echo json_encode(['success' => true, 'reservation_id' => $reservationId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}