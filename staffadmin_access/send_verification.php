<?php
// send_verification.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require_once '../connection/connection.php';


header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : 'send';

if ($action === 'validate') {
    // Validate code
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $code = isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '';
    if (empty($email) || empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Email and code are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }
    try {
        $stmt = $db_connection->prepare("SELECT * FROM email_verifications WHERE email = ? AND verification_code = ? AND is_used = 0 AND expires_at > UTC_TIMESTAMP() ORDER BY verification_id DESC LIMIT 1");
        $stmt->execute([$email, $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired code.']);
            exit;
        }
        // Mark code as used
        $db_connection->prepare('UPDATE email_verifications SET is_used = 1 WHERE verification_id = ?')->execute([$row['verification_id']]);
        echo json_encode(['success' => true, 'message' => 'Verification successful.']);
    } catch (Exception $e) {
        error_log('Validation Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error during code validation.']);
    }
    exit;
}


// Sending verification code logic
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email address is required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Rate limiting: max 5 requests per 10 minutes per IP
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitKey = 'email_verification_' . $ip;
$maxAttempts = 5;
$timeWindow = 600; // 10 minutes
if (!isset($_SESSION[$rateLimitKey])) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'first_attempt' => time()];
}
if (time() - $_SESSION[$rateLimitKey]['first_attempt'] > $timeWindow) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'first_attempt' => time()];
}
if ($_SESSION[$rateLimitKey]['count'] >= $maxAttempts) {
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please wait 10 minutes before trying again.']);
    exit;
}

try {
    // Check if email already exists in users table (case-insensitive)
    $stmt = $db_connection->prepare('SELECT COUNT(*) FROM users WHERE LOWER(email) = LOWER(?) AND email IS NOT NULL AND email != ""');
    $stmt->execute([$email]);
    $email_exists = $stmt->fetchColumn();
    if ($email_exists) {
        echo json_encode(['success' => false, 'message' => 'This email is already registered. Please use a different email.']);
        exit;
    }

    // Generate 6-digit code as string
    $code = strval(random_int(100000, 999999));
    // Use UTC for expires_at
    $dt = new DateTime('now', new DateTimeZone('UTC'));
    $dt->modify('+10 minutes');
    $expires_at = $dt->format('Y-m-d H:i:s');

    // Mark previous codes for this email as used
    $db_connection->prepare('UPDATE email_verifications SET is_used = 1 WHERE email = ?')->execute([$email]);
    // Insert new code
    $stmt = $db_connection->prepare('INSERT INTO email_verifications (email, verification_code, expires_at, is_used) VALUES (?, ?, ?, 0)');
    $stmt->execute([$email, $code, $expires_at]);

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change if not using Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'almanongeeyan@gmail.com'; // TODO: set your email
        $mail->Password = 'ujwh jvun gupw vnwi'; // TODO: set your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('almanongeeyan@gmail.com', 'Rich Anne Tiles');
        $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Rich Anne Tiles Staff Account Verification Code';
                $mail->Body = '
                <div style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 32px 0;">
                    <div style="max-width: 480px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #e0e0e0; padding: 32px 32px 24px 32px;">
                        <div style="text-align: center; margin-bottom: 24px;">
                            <img src="cid:ralttlogo" alt="RALTT Logo" style="height: 60px; margin-bottom: 8px;"/>
                            <h2 style="margin: 0; color: #1a237e; font-weight: 700;">Rich Anne Tiles </h2>
                        </div>
                        <p style="font-size: 16px; color: #333; margin-bottom: 24px;">Dear Staff,</p>
                        <p style="font-size: 15px; color: #333;">For your security, please use the following verification code to complete your staff account registration or login:</p>
                        <div style="text-align: center; margin: 32px 0;">
                            <span style="display: inline-block; font-size: 32px; letter-spacing: 8px; color: #1565c0; font-weight: bold; background: #f1f8ff; padding: 16px 32px; border-radius: 8px; border: 1px solid #bbdefb;">' . $code . '</span>
                        </div>
                        <p style="font-size: 15px; color: #333;">This code will expire in <b>10 minutes</b>. If you did not request this code, please ignore this email or contact your system administrator immediately.</p>
                        <p style="font-size: 15px; color: #333; margin-top: 32px;">Thank you,<br><b>Rich Anne Tiles Team</b></p>
                        <hr style="margin: 32px 0 16px 0; border: none; border-top: 1px solid #eee;">
                        <div style="font-size: 12px; color: #888; text-align: center;">This is an automated message. Please do not reply directly to this email.</div>
                    </div>
                </div>';
                $mail->addEmbeddedImage('../images/userlogo.png', 'ralttlogo', 'userlogo.png');
                $mail->SMTPDebug = 2; // Show detailed debug output
                $mail->Debugoutput = function($str, $level) {
                        error_log('PHPMailer SMTP Debug: ' . $str);
                };
                $mail->send();
                $_SESSION[$rateLimitKey]['count']++;
                echo json_encode(['success' => true, 'message' => 'Verification code sent.']);
    } catch (Exception $e) {
        // Log error for debugging
        error_log('PHPMailer Error: ' . $mail->ErrorInfo . ' | Exception: ' . $e->getMessage());
        $userMessage = 'Failed to send verification email. Please check your network connection or try again later.';
        if (strpos($mail->ErrorInfo, 'SMTP connect() failed') !== false) {
            $userMessage = 'Unable to connect to mail server. Please try again later.';
        }
        // Add last SMTP debug output to the error message for diagnostics
        $debugLog = @file_get_contents(ini_get('error_log'));
        echo json_encode(['success' => false, 'message' => $userMessage, 'debug' => $debugLog]);
    }
} catch (Exception $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
}
