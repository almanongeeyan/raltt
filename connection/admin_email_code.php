<?php
// admin_email_code.php (reusing logic from send_verification.php)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require_once 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}
// Check if user exists and is active (case-insensitive)
$stmt = $db_connection->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?) AND LOWER(account_status) = "active" AND user_role IN ("ADMIN", "ENCODER", "CASHIER", "DRIVER")');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'No active admin/staff account found for this email.']);
    exit;
}

// Generate 6-digit code as string
$code = strval(random_int(100000, 999999));
$dt = new DateTime('now', new DateTimeZone('UTC'));
$dt->modify('+10 minutes');
$expires_at = $dt->format('Y-m-d H:i:s');

// Mark previous codes for this email as used
$db_connection->prepare('UPDATE email_verifications SET is_used = 1 WHERE email = ?')->execute([$email]);
// Insert new code
$stmt = $db_connection->prepare('INSERT INTO email_verifications (email, verification_code, expires_at, is_used) VALUES (?, ?, ?, 0)');
$stmt->execute([$email, $code, $expires_at]);

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'almanongeeyan@gmail.com'; // TODO: set your email
    $mail->Password = 'ujwh jvun gupw vnwi'; // TODO: set your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('almanongeeyan@gmail.com', 'Rich Anne Tiles');
    $mail->addAddress($email, $user['full_name'] ?? $email);
    $mail->isHTML(true);
    $mail->Subject = 'Your Admin Login Verification Code';
    $mail->Body    = 'Your admin login code is: <b>' . $code . '</b>. This code will expire in 10 minutes.';
    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Verification code sent.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
