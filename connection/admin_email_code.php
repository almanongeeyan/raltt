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
$password = $_POST['password'] ?? '';
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
// Check password
if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
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
        $mail->Subject = 'Rich Anne Tiles Admin Login Verification Code';
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 32px 0;">
            <div style="max-width: 480px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #e0e0e0; padding: 32px 32px 24px 32px;">
                <div style="text-align: center; margin-bottom: 24px;">
                    <img src="cid:ralttlogo" alt="RALTT Logo" style="height: 60px; margin-bottom: 8px;"/>
                    <h2 style="margin: 0; color: #1a237e; font-weight: 700;">Rich Anne Tiles</h2>
                </div>
                <p style="font-size: 16px; color: #333; margin-bottom: 24px;">Dear ' . htmlspecialchars($user['full_name'] ?? $email) . ',</p>
                <p style="font-size: 15px; color: #333;">For your security, please use the following verification code to complete your admin login process:</p>
                <div style="text-align: center; margin: 32px 0;">
                    <span style="display: inline-block; font-size: 32px; letter-spacing: 8px; color: #1565c0; font-weight: bold; background: #f1f8ff; padding: 16px 32px; border-radius: 8px; border: 1px solid #bbdefb;">' . $code . '</span>
                </div>
                <p style="font-size: 15px; color: #333;">This code will expire in <b>10 minutes</b>. If you did not request this code, please ignore this email or contact your system administrator immediately.</p>
                <p style="font-size: 15px; color: #333; margin-top: 32px;">Thank you,<br><b>Rich Anne Tiles Team</b></p>
                <hr style="margin: 32px 0 16px 0; border: none; border-top: 1px solid #eee;">
                <div style="font-size: 12px; color: #888; text-align: center;">This is an automated message. Please do not reply directly to this email.</div>
            </div>
        </div>';
        // Attach logo as inline image
        $mail->addEmbeddedImage('../images/userlogo.png', 'ralttlogo', 'userlogo.png');
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Verification code sent.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
