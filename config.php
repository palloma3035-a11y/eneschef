<?php
// config.php - DB and SMTP configuration
// Copy this file to the project root and update values.

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'restaurant');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL', '/'); // Adjust if the app runs in a subfolder, e.g. '/restaurant-booking/public/'

// SMTP (PHPMailer)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'smtp_user@example.com');
define('SMTP_PASS', 'smtp_password');
define('SMTP_FROM_EMAIL', 'no-reply@example.com');
define('SMTP_FROM_NAME', 'Your Restaurant');

function getPDO(): PDO {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $opts);
}